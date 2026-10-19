<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KelasController extends Controller
{
    /**
     * Bergabung ke kelas menggunakan kode kelas atau nama kelas
     */
    public function join(Request $request)
    {
        $request->validate([
            'kode_kelas' => 'required|string|max:50',
        ], [
            'kode_kelas.required' => 'Kode kelas wajib diisi',
        ]);

        $searchCode = trim($request->input('kode_kelas'));

        // Cari berdasarkan kode_kelas (case-insensitive) atau nama_kelas
        $kelas = Kelas::with('jurusan')
            ->whereRaw('LOWER(kode_kelas) = ?', [strtolower($searchCode)])
            ->orWhereRaw('LOWER(nama_kelas) = ?', [strtolower($searchCode)])
            ->first();

        if (!$kelas) {
            $message = 'Kode kelas "' . $searchCode . '" tidak ditemukan. Pastikan kode kelas yang dimasukkan sudah benar.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 404);
            }
            return redirect()->back()->with('error', $message)->withInput();
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->kelas_id === $kelas->id) {
            $message = 'Anda sudah terdaftar di kelas ' . $kelas->nama_kelas . '.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'kelas' => $kelas,
                ]);
            }
            return redirect()->back()->with('info', $message);
        }

        $user->update([
            'kelas_id' => $kelas->id,
        ]);

        $successMessage = 'Selamat! Anda berhasil bergabung ke kelas ' . $kelas->nama_kelas . ' (' . ($kelas->jurusan->nama_jurusan ?? '') . ').';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'kelas' => [
                    'id' => $kelas->id,
                    'nama_kelas' => $kelas->nama_kelas,
                    'kode_kelas' => $kelas->kode_kelas,
                    'jurusan' => $kelas->jurusan->nama_jurusan ?? '-',
                ],
            ]);
        }

        return redirect()->route('siswa.home')->with('success', $successMessage);
    }

    /**
     * Keluar dari kelas saat ini (jika salah pilih atau ingin ganti)
     */
    public function leave(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->kelas_id) {
            $message = 'Anda saat ini belum terdaftar di kelas manapun.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        $user->update([
            'kelas_id' => null,
        ]);

        $message = 'Anda telah keluar dari kelas. Silakan bergabung kembali ke kelas yang sesuai.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('siswa.home')->with('success', $message);
    }

    /**
     * Mendapatkan daftar kelas yang tersedia untuk referensi siswa
     */
    public function availableClasses(Request $request)
    {
        $kelasList = Kelas::with('jurusan')
            ->orderBy('tingkat', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->get()
            ->map(function ($k) {
                return [
                    'id' => $k->id,
                    'nama_kelas' => $k->nama_kelas,
                    'kode_kelas' => $k->kode_kelas,
                    'tingkat' => $k->tingkat,
                    'jurusan' => $k->jurusan->nama_jurusan ?? '-',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $kelasList,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\Request;

class DeteksiDiniController extends Controller
{
    public function index()
    {
        $siswa = User::whereRaw('role = ?', [2])
            ->with('kelas')
            ->get();

        $siswaBermasalah = collect();
        $kritis = 0;
        $izinBerlebih = 0;

        foreach ($siswa as $user) {
            $user->total_alpha = Presensi::where('siswa_id', $user->id)
                ->where('status', 'alpha')
                ->count();

            $user->total_izin = Presensi::where('siswa_id', $user->id)
                ->where('status', 'izin')
                ->count();

            if ($user->total_alpha >= 3) {
                $kritis++;
            }

            // "Batas izin" = izin dianggap berlebih jika sudah >= 3 hari
            if ($user->total_izin >= 3) {
                $izinBerlebih++;
            }

            if ($user->total_alpha > 0 || $user->total_izin >= 3) {
                $siswaBermasalah->push($user);
            }
        }

        $stats = [
            'kritis' => $kritis,
            'izin_berlebih' => $izinBerlebih,
            'wa_terkirim' => User::sum('peringatan_wa_terkirim'),
        ];

        return view('admin.deteksi_dini', compact('stats', 'siswaBermasalah'));
    }

    public function kirimWa(Request $request, $siswaId)
    {
        $siswa = User::whereRaw('role = ?', [2])->findOrFail($siswaId);

        if (empty($siswa->parent_phone)) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa ini belum punya nomor orang tua (parent_phone).',
            ], 422);
        }

        $totalAlpha = Presensi::where('siswa_id', $siswa->id)->where('status', 'alpha')->count();
        $totalIzin = Presensi::where('siswa_id', $siswa->id)->where('status', 'izin')->count();

        $message = "Assalamualaikum Bapak/Ibu\n\n"
            . "Peringatan Deteksi Dini Presensi:\n"
            . "*Nama:* {$siswa->name}\n"
            . "*NIS:* " . ($siswa->nis ?? '-') . "\n"
            . "*Kelas:* " . ($siswa->kelas->nama_kelas ?? '-') . "\n"
            . "*Total Alpha:* {$totalAlpha} hari\n"
            . "*Total Izin:* {$totalIzin} hari\n\n"
            . "Mohon perhatiannya agar kehadiran ananda lebih baik.\n\n"
            . "_Sistem Presensi Sekolah_";

        $fonnte = app(FonnteService::class);
        $result = $fonnte->sendMessage($siswa->parent_phone, $message);

        if (!empty($result['success'])) {
            $siswa->increment('peringatan_wa_terkirim');

            return response()->json([
                'success' => true,
                'message' => 'Peringatan WA terkirim ke ' . $siswa->parent_phone,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Gagal mengirim WA.',
        ], 500);
    }

    public function exportCsv()
    {
        $siswa = User::whereRaw('role = ?', [2])->with('kelas')->get();

        $rows = [];
        foreach ($siswa as $user) {
            $totalAlpha = Presensi::where('siswa_id', $user->id)->where('status', 'alpha')->count();
            $totalIzin = Presensi::where('siswa_id', $user->id)->where('status', 'izin')->count();

            if ($totalAlpha > 0 || $totalIzin >= 3) {
                $risiko = $totalAlpha >= 3 ? 'Sangat Tinggi' : ($totalIzin >= 3 ? 'Tinggi' : 'Sedang');
                $rows[] = [
                    $user->name,
                    $user->nis ?? '-',
                    $user->kelas->nama_kelas ?? '-',
                    $totalAlpha,
                    $totalIzin,
                    $risiko,
                    $user->parent_phone ?? '-',
                ];
            }
        }

        $filename = 'rekap-deteksi-dini-' . now()->format('Ymd-His') . '.csv';

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Nama Siswa', 'NIS', 'Kelas', 'Total Alpha', 'Total Izin', 'Tingkat Risiko', 'No. Orang Tua']);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}

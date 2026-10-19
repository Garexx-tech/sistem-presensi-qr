<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Presensi;

class SiswaPresensiSeeder extends Seeder
{
    public function run()
    {
        // 1. Ambil 2 siswa pertama yang sudah ada di database
        $siswaList = User::where('role', 3)->take(2)->get();

        if ($siswaList->isEmpty()) {
            // Fallback jika belum ada user role 3, ambil user sembarang
            $siswaList = User::take(2)->get();
        }

        if ($siswaList->isEmpty()) {
            $this->command->error("Tidak ada data user/siswa di database!");
            return;
        }

        // 2. Simulasi Presensi Real-Time Baru (Alpha / Terlambat)
        foreach ($siswaList as $index => $siswa) {
            Presensi::create([
                'siswa_id'         => $siswa->id,
                'kelas_id'         => 1, // sesuaikan ID kelas jika ada
                'tanggal_presensi' => now()->toDateString(),
                'waktu_checkin'    => now()->toTimeString(),
                'status'           => $index % 2 == 0 ? 'alpha' : 'terlambat',
                'metode'           => 'qr',
            ]);
        }

        $this->command->info("Presensi real-time berhasil ditambahkan untuk siswa asli!");
    }
}
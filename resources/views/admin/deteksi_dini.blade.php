@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-danger">Sistem Deteksi Dini Presensi</h3>
            <p class="text-muted">Pemantauan siswa berisiko tinggi (sering Alpha/Terlambat) secara real-time dari database.</p>
        </div>
        <button class="btn btn-danger">Kirim Peringatan Masal (WA)</button>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-danger text-white border-0 shadow-sm">
                <div class="card-body">
                    <small>Siswa Kategori Kritis (Alpha ≥ 3)</small>
                    <h2 class="fw-bold my-1" id="stat-kritis">{{ $stats['kritis'] ?? 0 }} Siswa</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark border-0 shadow-sm">
                <div class="card-body">
                    <small>Siswa Peringatan (Terlambat ≥ 5)</small>
                    <h2 class="fw-bold my-1" id="stat-peringatan">{{ $stats['peringatan'] ?? 0 }} Siswa</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white border-0 shadow-sm">
                <div class="card-body">
                    <small>Pesan WA Peringatan Terkirim</small>
                    <h2 class="fw-bold my-1" id="stat-wa">{{ $stats['wa_terkirim'] ?? 0 }} Pesan</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Siswa Bermasalah -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Daftar Siswa Membutuhkan Tindakan</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Total Alpha</th>
                            <th>Total Terlambat</th>
                            <th>Tingkat Risiko</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-siswa">
                        @forelse($siswaBermasalah as $siswa)
                        <tr>
                            <td>
                                <strong>{{ $siswa->name }}</strong><br>
                                <small class="text-muted">NIS/NISN: {{ $siswa->nis ?? '-' }}</small>
                            </td>
                            <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td><span class="badge bg-danger">{{ $siswa->total_alpha }} Hari</span></td>
                            <td><span class="badge bg-warning text-dark">{{ $siswa->total_terlambat }} Hari</span></td>
                            <td>
                                @if($siswa->total_alpha >= 3)
                                    <span class="badge bg-danger">Sangat Tinggi</span>
                                @elseif($siswa->total_terlambat >= 5)
                                    <span class="badge bg-warning text-dark">Tinggi</span>
                                @else
                                    <span class="badge bg-secondary">Sedang</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-success">Kirim Peringatan WA</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data siswa berisiko dari database.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Script Live Update (Auto Polling Setiap 5 Detik) -->
<script>
    setInterval(() => {
        fetch(window.location.href, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Auto update tabel & statistik tanpa reload browser
            document.getElementById('tabel-siswa').innerHTML = doc.getElementById('tabel-siswa').innerHTML;
            document.getElementById('stat-kritis').innerHTML = doc.getElementById('stat-kritis').innerHTML;
            document.getElementById('stat-peringatan').innerHTML = doc.getElementById('stat-peringatan').innerHTML;
            document.getElementById('stat-wa').innerHTML = doc.getElementById('stat-wa').innerHTML;
        })
        .catch(err => console.log('Polling error:', err));
    }, 5000); // Update otomatis tiap 5 detik
</script>
@endsection
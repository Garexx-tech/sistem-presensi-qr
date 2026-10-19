@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-danger"><i class="bi bi-shield-exclamation me-2"></i>Sistem Deteksi Dini Presensi</h4>
            <p class="text-muted mb-0">Pemantauan siswa berisiko tinggi (sering Alpha/Terlambat) sebelum notifikasi WA terkirim.</p>
        </div>
        <button class="btn btn-danger"><i class="bi bi-whatsapp me-1"></i> Kirim Peringatan Masal (WA)</button>
    </div>

    <!-- Card Statistik Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body p-3">
                    <h6 class="text-white-50">Siswa Kategori Kritis (Alpha ≥ 3)</h6>
                    <h2 class="fw-bold mb-0">3 Siswa</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body p-3">
                    <h6 class="text-dark-50">Siswa Peringatan (Terlambat ≥ 5)</h6>
                    <h2 class="fw-bold mb-0">5 Siswa</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body p-3">
                    <h6 class="text-white-50">Pesan WA Peringatan Terkirim</h6>
                    <h2 class="fw-bold mb-0">8 Pesan</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Siswa Berisiko -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold">Daftar Siswa Membutuhkan Tindakan</h6>
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
                    <tbody>
                        <tr>
                            <td><strong>Ahmad Fauzi</strong><br><small class="text-muted">NISN: 005123456</small></td>
                            <td>XII RPL 1</td>
                            <td><span class="badge bg-danger">4 Hari</span></td>
                            <td><span class="badge bg-warning text-dark">2 Hari</span></td>
                            <td><span class="badge bg-danger">Sangat Tinggi</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-success"><i class="bi bi-whatsapp me-1"></i>Kirim Peringatan WA</button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Budi Santoso</strong><br><small class="text-muted">NISN: 005123457</small></td>
                            <td>XII TKJ 2</td>
                            <td><span class="badge bg-secondary">1 Hari</span></td>
                            <td><span class="badge bg-warning text-dark">6 Hari</span></td>
                            <td><span class="badge bg-warning text-dark">Sedang</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-success"><i class="bi bi-whatsapp me-1"></i>Kirim Peringatan WA</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    // Fungsi untuk memperbarui data tabel secara otomatis
    function fetchLiveDeteksiDini() {
        fetch("{{ route('admin.deteksi_dini') }}", {
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.text())
        .then(html => {
            // Mengambil elemen parser untuk memperbarui konten tabel saja
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Perbarui area konten utama secara otomatis
            const newContent = doc.querySelector('.container-fluid') || doc.body;
            const currentContent = document.querySelector('.container-fluid') || document.body;
            
            if (newContent && currentContent) {
                currentContent.innerHTML = newContent.innerHTML;
            }
        })
        .catch(error => console.log('Live server sync error:', error));
    }

    // Jalankan pembaruan otomatis setiap 5 detik
    setInterval(fetchLiveDeteksiDini, 5000);
</script>
@endsection
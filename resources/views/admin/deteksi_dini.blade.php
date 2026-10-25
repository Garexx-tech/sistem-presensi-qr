@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-danger">Sistem Deteksi Dini Presensi</h3>
            <p class="text-muted">Pemantauan siswa berisiko tinggi (sering Alpha / Izin berlebih) secara real-time dari database.</p>
        </div>
        <a href="{{ route('admin.deteksi-dini.export-csv') }}" class="btn btn-danger">
            <i class="bi bi-download me-2"></i>Download Rekap CSV
        </a>
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
                    <small>Siswa Batas Izin Berlebih (Izin ≥ 3)</small>
                    <h2 class="fw-bold my-1" id="stat-izin">{{ $stats['izin_berlebih'] ?? 0 }} Siswa</h2>
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
                            <th>Total Izin</th>
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
                            <td><span class="badge bg-warning text-dark">{{ $siswa->total_izin }} Hari</span></td>
                            <td>
                                @if($siswa->total_alpha >= 3)
                                    <span class="badge bg-danger">Sangat Tinggi</span>
                                @elseif($siswa->total_izin >= 3)
                                    <span class="badge bg-warning text-dark">Tinggi</span>
                                @else
                                    <span class="badge bg-secondary">Sedang</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-success btn-kirim-wa"
                                        data-id="{{ $siswa->id }}"
                                        data-name="{{ $siswa->name }}"
                                        data-phone="{{ $siswa->parent_phone }}">
                                    Kirim Peringatan WA
                                </button>
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

<script>
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-kirim-wa');
    if (!btn) return;

    const id = btn.dataset.id;
    const name = btn.dataset.name;
    const phone = btn.dataset.phone;
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';
    const url = '{{ url("admin/deteksi-dini/kirim-wa") }}/' + id;

    if (!phone) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'warning', title: 'Tidak ada nomor', text: 'Siswa ini belum punya nomor orang tua (parent_phone).' });
        } else {
            alert('Siswa belum punya nomor orang tua.');
        }
        return;
    }

    const send = function () {
        btn.disabled = true;
        fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
        .then(function (r) { return r.json(); })
        .then(function (d) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: d.success ? 'success' : 'error', title: d.success ? 'Berhasil' : 'Gagal', text: d.message });
            } else {
                alert(d.message);
            }
            if (d.success) setTimeout(function () { window.location.reload(); }, 1200);
        })
        .catch(function (err) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Gagal', text: err.message });
            } else {
                alert(err.message);
            }
        })
        .finally(function () { btn.disabled = false; });
    };

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'question',
            title: 'Kirim Peringatan WA?',
            html: 'Kirim peringatan ke orang tua <strong>' + name + '</strong> (' + phone + ')?',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#198754'
        }).then(function (res) { if (res.isConfirmed) send(); });
    } else {
        if (confirm('Kirim peringatan ke orang tua ' + name + ' (' + phone + ')?')) send();
    }
});

setInterval(function () {
    fetch(window.location.href, {
        headers: { "X-Requested-With": "XMLHttpRequest" }
    })
    .then(function (response) { return response.text(); })
    .then(function (html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        var t = document.getElementById('tabel-siswa');
        var nt = doc.getElementById('tabel-siswa');
        if (t && nt) t.innerHTML = nt.innerHTML;
        ['stat-kritis', 'stat-izin', 'stat-wa'].forEach(function (id) {
            var a = document.getElementById(id);
            var b = doc.getElementById(id);
            if (a && b) a.innerHTML = b.innerHTML;
        });
    })
    .catch(function (err) { console.log('Polling error:', err); });
}, 5000);
</script>
@endsection

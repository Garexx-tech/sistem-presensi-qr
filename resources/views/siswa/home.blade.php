@extends('layouts.siswa')
@section('title', 'Dashboard')

@section('content')
    <div class="bg-primary pt-10 pb-21"></div>
    <div class="container-fluid mt-n22 px-6">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <!-- Page header -->
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="mb-2 mb-lg-0">
                            <h3 class="mb-0 text-white">Selamat Datang, {{ Auth::user()->name }}</h3>
                            <p class="text-white-50 mb-0">Dashboard Siswa - {{ now()->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards -->
        <div class="row mt-6">
            <!-- Profile Card -->
            <div class="col-xl-4 col-lg-6 col-md-12 col-12 mb-4 mb-xl-0">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <img src="{{ Avatar::create(Auth::user()->name)->toBase64() }}" 
                                 alt="Avatar" 
                                 class="rounded-circle border border-3 border-primary" 
                                 width="100" 
                                 height="100">
                        </div>
                        <h4 class="mb-1 fw-bold">{{ Auth::user()->name }}</h4>
                        <p class="text-muted mb-3 small">{{ Auth::user()->email }}</p>
                        
                        @if(Auth::user()->kelas)
                            <div class="bg-light rounded-3 p-3 text-start">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary px-2 py-1">Tingkat {{ Auth::user()->kelas->tingkat }}</span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" data-bs-toggle="modal" data-bs-target="#pindahKelasModal" title="Ganti Kelas">
                                        <i class="bi bi-arrow-repeat me-1"></i>Ganti Kelas
                                    </button>
                                </div>
                                <div class="mb-1">
                                    <i class="bi bi-door-open-fill text-primary me-2"></i>
                                    <strong class="text-dark">{{ Auth::user()->kelas->nama_kelas }}</strong>
                                    <span class="badge bg-white text-muted border ms-1">{{ Auth::user()->kelas->kode_kelas }}</span>
                                </div>
                                <div class="small text-muted mb-1">
                                    <i class="bi bi-building me-2"></i>
                                    {{ Auth::user()->kelas->jurusan->nama_jurusan ?? '-' }}
                                </div>
                                @if(Auth::user()->kelas->waliKelas)
                                    <div class="small text-muted">
                                        <i class="bi bi-person-badge me-2"></i>
                                        Wali: {{ Auth::user()->kelas->waliKelas->name }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-warning text-start mb-3">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-exclamation-triangle-fill text-warning fs-5 me-2"></i>
                                    <strong class="text-dark">Belum Terdaftar di Kelas</strong>
                                </div>
                                <p class="small text-muted mb-0">
                                    Anda belum terhubung ke kelas manapun. Masukkan kode kelas untuk mulai presensi.
                                </p>
                            </div>
                            <button type="button" class="btn btn-primary w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#joinKelasModal">
                                <i class="bi bi-door-open-fill me-1"></i> Gabung ke Kelas
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Today's Attendance Status -->
            <div class="col-xl-8 col-lg-6 col-md-12 col-12">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-calendar-check text-success me-2"></i>
                            Status Presensi Hari Ini
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $todayPresensi = \App\Models\Presensi::where('siswa_id', Auth::id())
                                ->whereDate('tanggal_presensi', now())
                                ->first();
                        @endphp

                        @if($todayPresensi)
                            <div class="alert alert-success mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill fs-2 me-3 d-none d-md-block"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">
                                            <i class="bi bi-check-circle-fill me-1 d-md-none"></i>
                                            Sudah Presensi
                                        </h6>
                                        <div class="mb-2">
                                            <span class="badge bg-success me-2 px-3 py-2">
                                                <i class="bi bi-check-circle me-1"></i>
                                                {{ ucfirst($todayPresensi->status) }}
                                            </span>
                                            <span class="badge bg-info px-3 py-2">
                                                <i class="bi bi-{{ $todayPresensi->metode == 'qr' ? 'qr-code' : 'pencil' }} me-1"></i>
                                                {{ $todayPresensi->metode == 'qr' ? 'QR Code' : 'Manual' }}
                                            </span>
                                        </div>
                                        <p class="mb-0 small">
                                            <i class="bi bi-clock me-1"></i>
                                            <strong>Waktu:</strong> 
                                            {{ $todayPresensi->created_at->format('H:i:s') }}
                                            <br>
                                            <i class="bi bi-calendar me-1"></i>
                                            <strong>Tanggal:</strong> 
                                            {{ $todayPresensi->tanggal_presensi->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif(!Auth::user()->kelas)
                            <div class="alert alert-warning mb-0 border-0 bg-warning bg-opacity-10">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-circle-fill fs-2 text-warning me-3 d-none d-md-block"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold text-dark">
                                            <i class="bi bi-exclamation-circle-fill me-1 d-md-none text-warning"></i>
                                            Belum Terdaftar di Kelas
                                        </h6>
                                        <p class="mb-2 text-muted small">
                                            Anda harus bergabung ke kelas terlebih dahulu sebelum dapat melakukan presensi kehadiran.
                                        </p>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#joinKelasModal">
                                            <i class="bi bi-door-open-fill me-1"></i>
                                            Gabung Kelas Sekarang
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill fs-2 me-3 d-none d-md-block"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">
                                            <i class="bi bi-exclamation-triangle-fill me-1 d-md-none"></i>
                                            Belum Presensi
                                        </h6>
                                        <p class="mb-2">
                                            Anda belum melakukan presensi hari ini ({{ now()->format('d M Y') }})
                                        </p>
                                        <a href="{{ route('siswa.presensi.index') }}" 
                                           class="btn btn-warning">
                                            <i class="bi bi-qr-code-scan me-1"></i>
                                            Presensi Sekarang
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Statistics -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-graph-up text-primary me-2"></i>
                            Statistik Presensi
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $stats = [
                                'hadir' => \App\Models\Presensi::where('siswa_id', Auth::id())->where('status', 'hadir')->count(),
                                'izin' => \App\Models\Presensi::where('siswa_id', Auth::id())->where('status', 'izin')->count(),
                                'sakit' => \App\Models\Presensi::where('siswa_id', Auth::id())->where('status', 'sakit')->count(),
                                'alpha' => \App\Models\Presensi::where('siswa_id', Auth::id())->where('status', 'alpha')->count(),
                            ];
                            $total = array_sum($stats);
                        @endphp

                        <div class="row g-3">
                            <!-- Hadir -->
                            <div class="col-6 col-md-3">
                                <div class="text-center p-3 bg-success bg-opacity-10 rounded-3">
                                    <i class="bi bi-check-circle-fill text-success fs-2"></i>
                                    <h3 class="mb-0 mt-2 fw-bold">{{ $stats['hadir'] }}</h3>
                                    <small class="text-muted">Hadir</small>
                                </div>
                            </div>

                            <!-- Izin -->
                            <div class="col-6 col-md-3">
                                <div class="text-center p-3 bg-warning bg-opacity-10 rounded-3">
                                    <i class="bi bi-envelope-fill text-warning fs-2"></i>
                                    <h3 class="mb-0 mt-2 fw-bold">{{ $stats['izin'] }}</h3>
                                    <small class="text-muted">Izin</small>
                                </div>
                            </div>

                            <!-- Sakit -->
                            <div class="col-6 col-md-3">
                                <div class="text-center p-3 bg-info bg-opacity-10 rounded-3">
                                    <i class="bi bi-heart-pulse-fill text-info fs-2"></i>
                                    <h3 class="mb-0 mt-2 fw-bold">{{ $stats['sakit'] }}</h3>
                                    <small class="text-muted">Sakit</small>
                                </div>
                            </div>

                            <!-- Alpha -->
                            <div class="col-6 col-md-3">
                                <div class="text-center p-3 bg-danger bg-opacity-10 rounded-3">
                                    <i class="bi bi-x-circle-fill text-danger fs-2"></i>
                                    <h3 class="mb-0 mt-2 fw-bold">{{ $stats['alpha'] }}</h3>
                                    <small class="text-muted">Alpha</small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-center">
                            <p class="mb-0 text-muted small">
                                <i class="bi bi-calendar-range me-1"></i>
                                Total Presensi: <strong>{{ $total }}</strong> hari
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance -->
        @if(Auth::user()->kelas)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-clock-history text-primary me-2"></i>
                                Riwayat Presensi Terakhir
                            </h5>
                            <span class="badge bg-primary">5 Terakhir</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $recentPresensis = \App\Models\Presensi::where('siswa_id', Auth::id())
                                ->orderBy('tanggal_presensi', 'desc')
                                ->take(5)
                                ->get();
                        @endphp

                        @if($recentPresensis->count() > 0)
                            <!-- Desktop Table View -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Hari</th>
                                            <th>Waktu</th>
                                            <th>Status</th>
                                            <th>Metode</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentPresensis as $presensi)
                                        <tr>
                                            <td>{{ $presensi->tanggal_presensi->format('d M Y') }}</td>
                                            <td>{{ $presensi->tanggal_presensi->locale('id')->dayName }}</td>
                                            <td>{{ $presensi->created_at->format('H:i:s') }}</td>
                                            <td>
                                                @if($presensi->status == 'hadir')
                                                    <span class="badge bg-success">Hadir</span>
                                                @elseif($presensi->status == 'izin')
                                                    <span class="badge bg-warning">Izin</span>
                                                @elseif($presensi->status == 'sakit')
                                                    <span class="badge bg-info">Sakit</span>
                                                @else
                                                    <span class="badge bg-danger">Alpha</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $presensi->metode == 'qr' ? 'bg-primary' : 'bg-secondary' }}">
                                                    {{ $presensi->metode == 'qr' ? 'QR Code' : 'Manual' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="d-md-none">
                                @foreach($recentPresensis as $presensi)
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1 fw-bold">
                                                    {{ $presensi->tanggal_presensi->format('d M Y') }}
                                                </h6>
                                                <small class="text-muted">
                                                    {{ $presensi->tanggal_presensi->locale('id')->dayName }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                @if($presensi->status == 'hadir')
                                                    <span class="badge bg-success">Hadir</span>
                                                @elseif($presensi->status == 'izin')
                                                    <span class="badge bg-warning">Izin</span>
                                                @elseif($presensi->status == 'sakit')
                                                    <span class="badge bg-info">Sakit</span>
                                                @else
                                                    <span class="badge bg-danger">Alpha</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $presensi->created_at->format('H:i:s') }}
                                            </small>
                                            <span class="badge {{ $presensi->metode == 'qr' ? 'bg-primary' : 'bg-secondary' }}">
                                                {{ $presensi->metode == 'qr' ? 'QR' : 'Manual' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Belum ada riwayat presensi</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Modal Gabung Kelas -->
    <div class="modal fade" id="joinKelasModal" tabindex="-1" aria-labelledby="joinKelasModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="joinKelasModalLabel">
                        <i class="bi bi-door-open-fill me-2"></i>Gabung ke Kelas
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('siswa.kelas.join') }}" method="POST" id="formJoinKelas">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="text-center mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex p-3 mb-2">
                                <i class="bi bi-mortarboard-fill fs-1"></i>
                            </div>
                            <h5 class="fw-bold mb-1">Masukkan Kode Kelas</h5>
                            <p class="text-muted small mb-0">Minta kode kelas dari guru atau wali kelas Anda untuk bergabung.</p>
                        </div>

                        <div class="mb-3">
                            <label for="input_kode_kelas" class="form-label fw-semibold">Kode Kelas <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-key-fill text-muted"></i></span>
                                <input type="text" 
                                       name="kode_kelas" 
                                       id="input_kode_kelas" 
                                       class="form-control form-control-lg text-uppercase fw-bold text-center" 
                                       placeholder="CONTOH: XI-PBP" 
                                       required 
                                       autocomplete="off"
                                       style="letter-spacing: 1px;">
                            </div>
                            <div class="form-text">Bisa menggunakan kode kelas atau nama kelas resmi.</div>
                        </div>

                        <!-- Quick Picker Accordion -->
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="fw-semibold text-muted">
                                    <i class="bi bi-list-check me-1"></i>Daftar Kelas Tersedia
                                </small>
                                <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" id="btnLoadAvailableClasses">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Muat Daftar
                                </button>
                            </div>
                            <div id="availableClassesContainer" class="mt-2" style="max-height: 180px; overflow-y: auto; display: none;">
                                <div class="text-center py-2 text-muted small" id="availableClassesLoading">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                    <span class="ms-1">Memuat daftar kelas...</span>
                                </div>
                                <div id="availableClassesList" class="d-grid gap-1"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold" id="btnSubmitJoin">
                            <i class="bi bi-check-circle me-1"></i>Gabung Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Pindah / Keluar Kelas -->
    @if(Auth::user()->kelas)
    <div class="modal fade" id="pindahKelasModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-arrow-repeat text-primary me-2"></i>Kelola Kelas Anda
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-4 text-center">
                        <small class="text-muted d-block mb-1">Kelas Anda Saat Ini:</small>
                        <h5 class="fw-bold text-primary mb-1">{{ Auth::user()->kelas->nama_kelas }}</h5>
                        <p class="small text-muted mb-0">{{ Auth::user()->kelas->jurusan->nama_jurusan ?? '-' }} (Kode: {{ Auth::user()->kelas->kode_kelas }})</p>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-2">Pindah ke Kelas Baru</h6>
                        <form action="{{ route('siswa.kelas.join') }}" method="POST">
                            @csrf
                            <div class="input-group mb-2">
                                <input type="text" 
                                       name="kode_kelas" 
                                       class="form-control text-uppercase" 
                                       placeholder="Masukkan Kode Kelas Baru" 
                                       required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-arrow-right-circle me-1"></i>Pindah
                                </button>
                            </div>
                            <small class="text-muted">Masukkan kode kelas tujuan untuk langsung berpindah.</small>
                        </form>
                    </div>

                    <hr>

                    <div>
                        <h6 class="fw-bold text-danger mb-1">Keluar dari Kelas</h6>
                        <p class="small text-muted mb-2">Jika Anda salah memilih kelas, Anda bisa keluar dari kelas ini.</p>
                        <form action="{{ route('siswa.kelas.leave') }}" method="POST" id="formLeaveKelas">
                            @csrf
                            <button type="button" class="btn btn-outline-danger btn-sm" id="btnConfirmLeave">
                                <i class="bi bi-box-arrow-right me-1"></i>Keluar dari Kelas Ini
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session("success") }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session("error") }}',
                    confirmButtonColor: '#0d6efd'
                });
            @endif

            @if(session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Informasi',
                    text: '{{ session("info") }}',
                    confirmButtonColor: '#0d6efd'
                });
            @endif

            // Tombol konfirmasi keluar kelas
            const btnLeave = document.getElementById('btnConfirmLeave');
            if (btnLeave) {
                btnLeave.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Keluar dari kelas?',
                        text: 'Anda tidak akan bisa melakukan presensi sampai bergabung ke kelas kembali.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Keluar Kelas',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('formLeaveKelas').submit();
                        }
                    });
                });
            }

            // Quick load daftar kelas
            const btnLoadClasses = document.getElementById('btnLoadAvailableClasses');
            const container = document.getElementById('availableClassesContainer');
            const list = document.getElementById('availableClassesList');
            const loading = document.getElementById('availableClassesLoading');
            let classesLoaded = false;

            if (btnLoadClasses) {
                btnLoadClasses.addEventListener('click', function() {
                    if (container.style.display === 'none') {
                        container.style.display = 'block';
                        if (!classesLoaded) {
                            fetch("{{ route('siswa.kelas.available') }}")
                                .then(res => res.json())
                                .then(data => {
                                    loading.style.display = 'none';
                                    if (data.success && data.data.length > 0) {
                                        classesLoaded = true;
                                        list.innerHTML = data.data.map(k => `
                                            <button type="button" class="btn btn-outline-light text-dark text-start border p-2 small class-item" data-code="${k.kode_kelas}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong class="text-primary">${k.nama_kelas}</strong>
                                                        <span class="text-muted small ms-1">(${k.jurusan})</span>
                                                    </div>
                                                    <span class="badge bg-primary-soft text-primary border">${k.kode_kelas}</span>
                                                </div>
                                            </button>
                                        `).join('');

                                        // Attach click handler to populate input
                                        document.querySelectorAll('.class-item').forEach(item => {
                                            item.addEventListener('click', function() {
                                                const code = this.getAttribute('data-code');
                                                const input = document.getElementById('input_kode_kelas');
                                                if (input) {
                                                    input.value = code;
                                                    input.focus();
                                                }
                                            });
                                        });
                                    } else {
                                        list.innerHTML = '<div class="text-center py-2 text-muted small">Belum ada kelas yang dibuat admin.</div>';
                                    }
                                })
                                .catch(() => {
                                    loading.style.display = 'none';
                                    list.innerHTML = '<div class="text-center py-2 text-danger small">Gagal memuat kelas.</div>';
                                });
                        }
                    } else {
                        container.style.display = 'none';
                    }
                });
            }
        });
    </script>
@endsection

@push('styles')
<style>
    .bg-success.bg-opacity-10 {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }

    .bg-warning.bg-opacity-10 {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }

    .bg-info.bg-opacity-10 {
        background-color: rgba(13, 202, 240, 0.1) !important;
    }

    .bg-danger.bg-opacity-10 {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }

    /* Mobile optimizations */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }

        h3 {
            font-size: 1.25rem;
        }

        .fs-2 {
            font-size: 1.5rem !important;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
    }

    @media (max-width: 576px) {
        h3 {
            font-size: 1.1rem;
        }

        h4 {
            font-size: 1.15rem;
        }

        h5 {
            font-size: 1rem;
        }
    }
</style>
@endpush
@extends('layouts.siswa')
@section('title', 'Presensi Wajah')

@section('content')
<div class="bg-primary pt-10 pb-21"></div>
<div class="container-fluid mt-n22 px-6">
    <div class="row">
        <div class="col-lg-12">
            <div class="mb-4">
                <h3 class="mb-0 text-white">Presensi dengan Pengenalan Wajah</h3>
                <p class="text-white-50 mb-0">Arahkan wajah ke kamera untuk check-in / check-out</p>
            </div>
        </div>
    </div>

    <div class="row mt-6">
        <div class="col-lg-8 mx-auto">

            @if($todayPresensi)
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill fs-2 me-3"></i>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1">Status Presensi Hari Ini</h5>
                        <p class="mb-2">
                            <strong>✅ Check-In:</strong>
                            {{ $todayPresensi->waktu_checkin ? $todayPresensi->waktu_checkin->format('H:i:s') : 'Belum' }}
                            |
                            <strong>✅ Check-Out:</strong>
                            {{ $todayPresensi->waktu_checkout ? $todayPresensi->waktu_checkout->format('H:i:s') : 'Belum' }}
                        </p>
                        <small class="text-muted">
                            Metode: {{ $todayPresensi->metode == 'wajah' ? 'Wajah' : ($todayPresensi->metode == 'qr' ? 'QR Code' : 'Manual') }}
                        </small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(!$hasFace)
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Wajah belum terdaftar.</strong> Klik "Daftarkan Wajah" dulu (cukup sekali).
            </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-camera-video me-2 text-primary"></i> Kamera Presensi Wajah
                        </h4>
                        <span class="badge bg-secondary" id="statusBadge">
                            <i class="bi bi-circle-fill me-1"></i> Tidak Aktif
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <div id="camera-container" class="mb-4">
                        <video id="video" autoplay playsinline muted style="width:100%; border-radius:12px; background:#000;"></video>
                        <canvas id="canvas" style="display:none;"></canvas>
                    </div>

                    <div id="scan-result" class="alert" style="display:none;"></div>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary btn-lg" id="startCameraBtn">
                            <i class="bi bi-camera me-2"></i>Nyalakan Kamera
                        </button>
                        <button type="button" class="btn btn-success btn-lg" id="registerBtn" style="display:none;">
                            <i class="bi bi-person-plus me-2"></i>Daftarkan Wajah
                        </button>
                        <button type="button" class="btn btn-warning btn-lg" id="presensiBtn" style="display:none;">
                            <i class="bi bi-check-circle me-2"></i>Presensi Sekarang
                        </button>
                        <button type="button" class="btn btn-danger btn-lg" id="stopCameraBtn" style="display:none;">
                            <i class="bi bi-stop-circle me-2"></i>Matikan Kamera
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="csrf-token-input" value="{{ csrf_token() }}">

<script>
(function () {
    const hasFace = {{ $hasFace ? 'true' : 'false' }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || document.getElementById('csrf-token-input')?.value || '';

    const registerRoute = '{{ route("siswa.presensi.wajah.register") }}';
    const recognizeRoute = '{{ route("siswa.presensi.wajah.recognize") }}';
    const submitRoute = '{{ route("siswa.presensi.wajah.submit") }}';

    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const startBtn = document.getElementById('startCameraBtn');
    const stopBtn = document.getElementById('stopCameraBtn');
    const registerBtn = document.getElementById('registerBtn');
    const presensiBtn = document.getElementById('presensiBtn');
    const statusBadge = document.getElementById('statusBadge');

    let stream = null;
    let busy = false;

    function setStatus(text, type) {
        statusBadge.innerHTML = '<i class="bi bi-circle-fill me-1"></i>' + text;
        statusBadge.className = 'badge bg-' + type;
    }

    function showAlert(type, title, text) {
        const el = document.getElementById('scan-result');
        el.style.display = 'block';
        el.className = 'alert alert-' + type;
        el.innerHTML = '<strong>' + title + '</strong> ' + (text || '');
    }

    function showLoading(msg) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ title: msg, allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        }
    }

    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                audio: false
            });
            video.srcObject = stream;
            startBtn.style.display = 'none';
            stopBtn.style.display = 'block';
            registerBtn.style.display = hasFace ? 'none' : 'block';
            presensiBtn.style.display = 'block';
            setStatus('Kamera Aktif', 'success');
        } catch (err) {
            showAlert('danger', 'Gagal membuka kamera', err.message);
        }
    }

    function stopCamera() {
        if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
        video.srcObject = null;
        startBtn.style.display = 'block';
        stopBtn.style.display = 'none';
        registerBtn.style.display = 'none';
        presensiBtn.style.display = 'none';
        setStatus('Tidak Aktif', 'secondary');
    }

    function captureImage() {
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        return canvas.toDataURL('image/jpeg', 0.85);
    }

    async function postJSON(url, payload) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload),
            credentials: 'same-origin'
        });
        const text = await res.text();
        try { return JSON.parse(text); } catch (e) { throw new Error(text.substring(0, 200)); }
    }

    async function registerFace() {
        if (busy) return;
        busy = true;
        showLoading('Mendaftarkan wajah...');
        try {
            const data = await postJSON(registerRoute, { image: captureImage() });
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, confirmButtonColor: '#198754' })
                    .then(() => window.location.reload());
            } else {
                throw new Error(data.message || 'Gagal mendaftarkan wajah');
            }
        } catch (err) {
            showAlert('danger', 'Gagal', err.message);
            Swal.close();
        } finally {
            busy = false;
        }
    }

    async function doPresensi() {
        if (busy) return;
        busy = true;
        showLoading('Mengenali wajah...');
        try {
            const data = await postJSON(recognizeRoute, { image: captureImage() });
            if (!data.success) {
                throw new Error(data.message || 'Wajah tidak dikenali');
            }
            window.sessionData = data.data;
            Swal.close();
            requestLocationAndSubmit(data.data);
        } catch (err) {
            showAlert('danger', 'Gagal', err.message);
            Swal.close();
        } finally {
            busy = false;
        }
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) ** 2 +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon / 2) ** 2;
        return Math.round(R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
    }

    function requestLocationAndSubmit(sessionData) {
        if (!navigator.geolocation) {
            showAlert('danger', 'Error', 'Browser tidak mendukung geolocation');
            return;
        }
        showLoading('Mengambil lokasi Anda...');
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;
                const distance = calculateDistance(userLat, userLng, sessionData.latitude, sessionData.longitude);

                if (distance > sessionData.radius) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lokasi Terlalu Jauh!',
                        html: `<p>Jarak Anda: <strong>${distance} m</strong><br>Radius maksimal: <strong>${sessionData.radius} m</strong></p>`,
                        confirmButtonColor: '#dc3545'
                    });
                    return;
                }

                submitFacePresensi(sessionData, userLat, userLng, distance, position.coords.accuracy);
            },
            (error) => {
                let msg = 'Gagal mengambil lokasi';
                if (error.PERMISSION_DENIED) msg = 'Izin lokasi ditolak.';
                else if (error.POSITION_UNAVAILABLE) msg = 'GPS tidak tersedia.';
                else if (error.TIMEOUT) msg = 'Timeout. Pastikan GPS aktif.';
                showAlert('danger', 'Gagal Mengambil Lokasi', msg);
                Swal.close();
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    async function submitFacePresensi(sessionData, lat, lng, distance, gpsAccuracy) {
        showLoading('Menyimpan presensi...');
        try {
            const data = await postJSON(submitRoute, {
                session_id: sessionData.session_id,
                type: sessionData.type,
                latitude: lat,
                longitude: lng,
                distance: distance,
                gps_accuracy: gpsAccuracy,
                confidence: sessionData.confidence
            });
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Presensi Berhasil!',
                    html: `<p>${data.message}</p><p class="small text-muted">${data.data.waktu} · ${data.data.tanggal} · Jarak ${distance} m</p>`,
                    confirmButtonColor: '#198754',
                    allowOutsideClick: false
                }).then(() => window.location.reload());
            } else {
                throw new Error(data.message || 'Gagal menyimpan presensi');
            }
        } catch (err) {
            showAlert('danger', 'Gagal Menyimpan', err.message);
            Swal.close();
        }
    }

    startBtn.addEventListener('click', startCamera);
    stopBtn.addEventListener('click', stopCamera);
    registerBtn.addEventListener('click', registerFace);
    presensiBtn.addEventListener('click', doPresensi);
})();
</script>
@endsection
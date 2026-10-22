@extends('layouts.auth')
@section('content')
    <div class="container d-flex flex-column">
        <div class="row align-items-center justify-content-center g-0 min-vh-100">
            <div class="col-12 col-md-8 col-lg-6 col-xxl-4 py-8 py-xl-0">
                <div class="card smooth-shadow-md">
                    <div class="card-body p-6">
                        <div class="text-center mb-4">
                            <img src="{{ \App\Models\SchoolSetting::get()->logo_url }}"
                                 alt="Logo Sekolah"
                                 class="rounded-circle shadow-sm mb-3"
                                 width="100">
                            <h4 class="fw-bold mb-1">{{ \App\Models\SchoolSetting::get()->school_name ?? 'SMKN 1 BENDO MAGETAN' }}</h4>
                            <p class="text-muted small">Buat akun siswa baru</p>
                        </div>

                        <form method="POST" action="{{ route('register.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Nama lengkap</label>
                                <input id="name" type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       name="name"
                                       value="{{ old('name') }}"
                                       autocomplete="name"
                                       required
                                       autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="nama@email.com"
                                       autocomplete="username"
                                       required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input id="password" type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password"
                                       autocomplete="new-password"
                                       required>
                                @error('password')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Konfirmasi password</label>
                                <input id="password_confirmation" type="password"
                                       class="form-control"
                                       name="password_confirmation"
                                       autocomplete="new-password"
                                       required>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fe fe-user-plus me-2"></i>Daftar sebagai siswa
                                </button>
                            </div>
                        </form>

                        <p class="text-center mb-0">
                            Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

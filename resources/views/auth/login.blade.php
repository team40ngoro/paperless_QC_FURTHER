@extends('layouts.app')
@php
    $isLocalhost = in_array(request()->getHost(), ['localhost', '127.0.0.1', '10.68.1.37']);
    $assetPath = $isLocalhost ? 'public/' : '';
@endphp
@if ($errors->has('sso'))
<div class="mb-4 p-3 rounded-lg bg-red-50 text-red-600 text-sm font-medium text-center">
{{ $errors->first('sso') }}
</div>
@endif
@section('container')
<div class="min-vh-100 d-flex align-items-center login-wrapper">
    <div class="overlay-bg"></div>

    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-md-11 col-lg-10 col-xl-9">
                <div class="login-card">

                    {{-- ====== PANEL KIRI: BRANDING ====== --}}
                    <div class="login-panel-left">
                        <img src="{{ asset($assetPath . 'dist/img/cpi-logo.png') }}" alt="CPI Logo" class="logo-img">
                        <h2 class="panel-title">Paperless Further</h2>
                        <p class="panel-desc">
                            Sistem manajemen dokumen digital untuk operasional lebih efisien dan terorganisir.
                        </p>
                        <div class="panel-brand-tag">PT. Charoen Pokphand Indonesia</div>
                    </div>

                    {{-- ====== PANEL KANAN: FORM LOGIN ====== --}}
                    <div class="login-panel-right">
                        <div class="welcome-header">
                            <h4 class="welcome-text">Welcome Back</h4>
                            <p class="subtitle">Sign in to Paperless Further</p>
                        </div>

                        @if(session('error'))
                            <div class="alert-clean">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group-clean">
                                <label for="login" class="form-label-clean">
                                    <i class="fas fa-user me-1"></i> Username atau Email
                                </label>
                                <input
                                    id="login"
                                    type="text"
                                    class="form-input-clean no-uppercase"
                                    name="login"
                                    placeholder="Masukkan username atau email"
                                    required
                                    autofocus
                                >
                            </div>

                            <div class="form-group-clean">
                                <label for="password" class="form-label-clean">
                                    <i class="fas fa-lock me-1"></i> Password
                                </label>
                                <div class="password-wrapper">
                                    <input
                                        id="password"
                                        type="password"
                                        class="form-input-clean no-uppercase"
                                        name="password"
                                        placeholder="Masukkan password"
                                        required
                                    >
                                    <button
                                        type="button"
                                        class="password-toggle"
                                        onclick="togglePassword()"
                                        title="Tampilkan/sembunyikan password"
                                    >
                                        <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn-login">
                                🍗 Sign In
                            </button>
                        </form>

                        <div class="footer-text">
                            <p>Paperless Further v2.0.3</p>
                            <div class="copyright-text">
                                <!-- &copy; {{ date('Y') }} PT. Charoen Pokphand Indonesia<br> -->
                                <span>|| All rights reserved by Tim Industry 4.0 ||</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* ============================
       WRAPPER & BACKGROUND
    ============================ */
    .login-wrapper {
        position: relative;
        background: url("{{ asset($assetPath . 'dist/img/nugget-bg.webp') }}") no-repeat center center;
        background-size: cover;
        background-attachment: fixed;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow: hidden;
    }

    .overlay-bg {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            135deg,
            rgba(0, 0, 0, 0.65),
            rgba(0, 0, 0, 0.45)
        );
        z-index: 1;
    }

    .container.position-relative {
        z-index: 2;
    }

    /* ============================
       CARD UTAMA (WIDE / 2 KOLOM)
    ============================ */
    .login-card {
        display: flex;
        flex-direction: row;
        background: rgba(255, 255, 255, 0.93);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 24px 64px rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.4);
        animation: fadeInUp 0.7s ease-out;
        min-height: 480px;
    }

    /* ============================
       PANEL KIRI - BRANDING
    ============================ */
    .login-panel-left {
        flex: 0 0 38%;
        background: linear-gradient(160deg, #f59e0b 0%, #f97316 100%);
        padding: 48px 36px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .login-panel-left .logo-img {
        width: 80px;
        height: auto;
        margin-bottom: 20px;
        filter: drop-shadow(0 4px 12px rgba(0,0,0,0.2));
    }

    .panel-title {
        color: #ffffff;
        font-weight: 700;
        font-size: 22px;
        margin-bottom: 12px;
    }

    .panel-desc {
        color: rgba(255, 255, 255, 0.88);
        font-size: 13.5px;
        line-height: 1.65;
        margin-bottom: 28px;
    }

    .panel-brand-tag {
        background: rgba(255, 255, 255, 0.22);
        border: 1px solid rgba(255, 255, 255, 0.35);
        border-radius: 30px;
        padding: 8px 20px;
        color: #ffffff;
        font-size: 12px;
        font-weight: 600;
    }

    /* ============================
       PANEL KANAN - FORM LOGIN
    ============================ */
    .login-panel-right {
        flex: 1;
        padding: 48px 44px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: rgba(255, 255, 255, 0.0);
    }

    .welcome-header {
        margin-bottom: 28px;
    }

    .welcome-text {
        color: #1f2933;
        font-weight: 700;
        font-size: 26px;
        margin-bottom: 4px;
    }

    .subtitle {
        color: #6b7280;
        font-size: 14px;
        margin: 0;
    }

    /* Alert error */
    .alert-clean {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        margin-bottom: 20px;
    }

    /* Form fields */
    .form-group-clean {
        margin-bottom: 20px;
    }

    .form-label-clean {
        display: block;
        color: #374151;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .form-input-clean {
        width: 100%;
        padding: 13px 16px;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.2s ease;
        background: #f9fafb;
        color: #1f2933;
    }

    .form-input-clean::placeholder {
        color: #9ca3af;
    }

    .form-input-clean:focus {
        outline: none;
        border-color: #f59e0b;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
    }

    /* Password toggle */
    .password-wrapper {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 4px;
        transition: color 0.2s;
    }

    .password-toggle:hover {
        color: #f59e0b;
    }

    /* Tombol Sign In */
    .btn-login {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 8px;
        box-shadow: 0 8px 20px rgba(249, 115, 22, 0.38);
        letter-spacing: 0.3px;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(249, 115, 22, 0.5);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    /* Footer */
    .footer-text {
        text-align: center;
        padding-top: 20px;
        margin-top: 20px;
        border-top: 1px solid #f3f4f6;
    }

    .footer-text p,
    .copyright-text {
        color: #9ca3af;
        font-size: 12px;
        margin: 0;
        line-height: 1.6;
    }

    /* ============================
       ANIMASI
    ============================ */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ============================
       RESPONSIVE - MOBILE
    ============================ */
    @media (max-width: 768px) {
        .login-card {
            flex-direction: column;
            min-height: unset;
        }

        .login-panel-left {
            flex: unset;
            padding: 36px 28px;
        }

        .login-panel-left .logo-img {
            width: 60px;
        }

        .panel-title {
            font-size: 18px;
        }

        .login-panel-right {
            padding: 32px 28px;
        }

        .welcome-text {
            font-size: 22px;
        }
    }
</style>

<script>
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
@endsection
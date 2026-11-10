@extends('layouts.app')
@php
    $assetPath = (isset($_SERVER['DOCUMENT_ROOT']) && realpath($_SERVER['DOCUMENT_ROOT']) === realpath(public_path())) ? '' : 'public/';
@endphp

@section('container')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="min-vh-100 d-flex align-items-center justify-content-center login-wrapper">
    <div class="overlay-bg"></div>

    <div class="container position-relative py-4">
        <div class="row justify-content-center">
            <div class="col-md-11 col-lg-10 col-xl-9">
                <div class="login-card">

                    {{-- ====== PANEL KIRI: CORPORATE BRANDING ====== --}}
                    <div class="login-panel-left">
                        <div class="panel-grid-pattern"></div>

                        <div class="brand-content-top">
                            <div class="logo-glass-box">
                                <img src="{{ asset($assetPath . 'dist/img/cpi-logo.png') }}" alt="CPI Logo" class="logo-img">
                            </div>
                            <span class="system-badge">QUALITY CONTROL SYSTEM</span>
                            <h2 class="panel-title">Paperless Further</h2>
                            <p class="panel-desc">
                                Platform manajemen dokumen mutu digital terintegrasi untuk operasional produksi yang presisi, terukur, dan siap audit.
                            </p>

                            <div class="feature-list">
                                <div class="feature-item">
                                    <div class="feature-icon"><i class="fas fa-check"></i></div>
                                    <span>Real-time Quality Control Monitoring</span>
                                </div>
                                <div class="feature-item">
                                    <div class="feature-icon"><i class="fas fa-check"></i></div>
                                    <span>End-to-End Batch Traceability</span>
                                </div>
                                <div class="feature-item">
                                    <div class="feature-icon"><i class="fas fa-check"></i></div>
                                    <span>Standardized Digital Audit &amp; Reporting</span>
                                </div>
                            </div>
                        </div>

                        <div class="brand-content-bottom">
                            <div class="panel-brand-tag">
                                <i class="fas fa-industry mr-2" style="color: #38bdf8;"></i>
                                <span>PT. Charoen Pokphand Indonesia &bull; Plant Ngoro</span>
                            </div>
                        </div>
                    </div>

                    {{-- ====== PANEL KANAN: FORM LOGIN ====== --}}
                    <div class="login-panel-right">
                        <div class="welcome-header">
                            <div class="welcome-eyebrow">AUTHENTICATION PORTAL</div>
                            <h4 class="welcome-text">Sign In to Workspace</h4>
                            <p class="subtitle">Masukkan username atau email terdaftar untuk mengakses sistem.</p>
                        </div>

                        @if ($errors->has('sso'))
                            <div class="alert-clean">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span>{{ $errors->first('sso') }}</span>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert-clean">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" id="loginForm">
                            @csrf

                            <div class="form-group-clean">
                                <label for="login" class="form-label-clean">
                                    Username / Email
                                </label>
                                <div class="input-icon-wrapper">
                                    <i class="fas fa-user input-left-icon"></i>
                                    <input
                                        id="login"
                                        type="text"
                                        class="form-input-clean no-uppercase"
                                        name="login"
                                        value="{{ old('login') }}"
                                        placeholder="Masukkan username atau email"
                                        required
                                        autofocus
                                    >
                                </div>
                            </div>

                            <div class="form-group-clean">
                                <label for="password" class="form-label-clean">
                                    Password
                                </label>
                                <div class="input-icon-wrapper">
                                    <i class="fas fa-lock input-left-icon"></i>
                                    <input
                                        id="password"
                                        type="password"
                                        class="form-input-clean form-input-password no-uppercase"
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
                                <span>Sign In</span>
                                <i class="fas fa-arrow-right ml-2 btn-arrow"></i>
                            </button>
                        </form>

                        <div class="footer-text">
                            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 8px;">
                                <span class="version-pill">Paperless Further v2.0.3</span>
                                <span class="copyright-text">Tim Industry 4.0 &bull; CPI Ngoro</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ============================
       WRAPPER & BACKGROUND
    ============================ */
    .login-wrapper {
        position: relative;
        background: url("{{ asset($assetPath . 'dist/img/nugget-background.jpeg') }}") no-repeat center center;
        background-size: cover;
        background-attachment: fixed;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        overflow: hidden;
    }

    .overlay-bg {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            135deg,
            rgba(15, 23, 42, 0.82) 0%,
            rgba(15, 23, 42, 0.68) 50%,
            rgba(30, 41, 59, 0.78) 100%
        );
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 1;
    }

    .container.position-relative {
        z-index: 2;
    }

    /* ============================
       CARD UTAMA (2 KOLOM ENTERPRISE)
    ============================ */
    .login-card {
        display: flex;
        flex-direction: row;
        background: #ffffff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 25px 60px -15px rgba(2, 6, 23, 0.55), 0 0 0 1px rgba(255, 255, 255, 0.12);
        animation: fadeInUp 0.55s cubic-bezier(0.16, 1, 0.3, 1);
        min-height: 530px;
    }

    /* ============================
       PANEL KIRI - BRANDING
    ============================ */
    .login-panel-left {
        position: relative;
        flex: 0 0 43%;
        background: linear-gradient(155deg, #0f172a 0%, #1e293b 60%, #0f172a 100%);
        padding: 44px 38px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        color: #ffffff;
        overflow: hidden;
    }

    .panel-grid-pattern {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
        background-size: 22px 22px;
        pointer-events: none;
    }

    .brand-content-top,
    .brand-content-bottom {
        position: relative;
        z-index: 2;
    }

    .logo-glass-box {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.96);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 22px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        padding: 10px;
    }

    .logo-glass-box .logo-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .system-badge {
        display: inline-block;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 1.2px;
        color: #38bdf8;
        background: rgba(56, 189, 248, 0.12);
        border: 1px solid rgba(56, 189, 248, 0.25);
        padding: 4px 10px;
        border-radius: 6px;
        margin-bottom: 12px;
    }

    .panel-title {
        color: #ffffff;
        font-weight: 800;
        font-size: 26px;
        letter-spacing: -0.4px;
        margin-bottom: 12px;
    }

    .panel-desc {
        color: #94a3b8;
        font-size: 13.5px;
        line-height: 1.65;
        margin-bottom: 28px;
    }

    .feature-list {
        display: flex;
        flex-direction: column;
        gap: 11px;
        margin-bottom: 24px;
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #e2e8f0;
        font-weight: 500;
    }

    .feature-icon {
        width: 20px;
        height: 20px;
        border-radius: 6px;
        background: rgba(16, 185, 129, 0.18);
        color: #34d399;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        flex-shrink: 0;
    }

    .panel-brand-tag {
        display: inline-flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 10px;
        padding: 9px 14px;
        color: #cbd5e1;
        font-size: 12px;
        font-weight: 500;
        width: 100%;
    }

    /* ============================
       PANEL KANAN - FORM LOGIN
    ============================ */
    .login-panel-right {
        flex: 1;
        padding: 48px 46px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: #ffffff;
    }

    .welcome-header {
        margin-bottom: 28px;
    }

    .welcome-eyebrow {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1px;
        color: #2563eb;
        margin-bottom: 6px;
    }

    .welcome-text {
        color: #0f172a;
        font-weight: 800;
        font-size: 25px;
        letter-spacing: -0.4px;
        margin-bottom: 6px;
    }

    .subtitle {
        color: #64748b;
        font-size: 13.5px;
        margin: 0;
        line-height: 1.5;
    }

    /* Alert error */
    .alert-clean {
        display: flex;
        align-items: center;
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-left: 4px solid #ef4444;
        color: #991b1b;
        padding: 12px 14px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 22px;
    }

    /* Form fields */
    .form-group-clean {
        margin-bottom: 20px;
    }

    .form-label-clean {
        display: block;
        color: #334155;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 7px;
    }

    .input-icon-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-left-icon {
        position: absolute;
        left: 15px;
        color: #94a3b8;
        font-size: 14px;
        pointer-events: none;
        transition: color 0.2s ease;
    }

    .form-input-clean {
        width: 100%;
        padding: 12.5px 16px 12.5px 42px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        background: #f8fafc;
        color: #0f172a;
    }

    .form-input-password {
        padding-right: 44px;
    }

    .form-input-clean::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }

    .form-input-clean:hover {
        border-color: #cbd5e1;
    }

    .form-input-clean:focus {
        outline: none;
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.12);
    }

    .input-icon-wrapper:focus-within .input-left-icon {
        color: #2563eb;
    }

    /* Password toggle */
    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 6px;
        border-radius: 6px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .password-toggle:hover {
        color: #0f172a;
        background: #f1f5f9;
    }

    /* Tombol Sign In */
    .btn-login {
        width: 100%;
        padding: 13.5px 20px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 14.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s ease;
        margin-top: 8px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        letter-spacing: 0.2px;
    }

    .btn-login:hover {
        background: linear-gradient(135deg, #1e293b 0%, #2563eb 100%);
        transform: translateY(-1px);
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.25);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .btn-arrow {
        font-size: 13px;
        transition: transform 0.25s ease;
    }

    .btn-login:hover .btn-arrow {
        transform: translateX(4px);
    }

    /* Footer */
    .footer-text {
        padding-top: 22px;
        margin-top: 28px;
        border-top: 1px solid #f1f5f9;
    }

    .version-pill {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 6px;
    }

    .copyright-text {
        color: #94a3b8;
        font-size: 11.5px;
        font-weight: 500;
    }

    /* ============================
       ANIMASI
    ============================ */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ============================
       RESPONSIVE - MOBILE & TABLET
    ============================ */
    @media (max-width: 768px) {
        .login-card {
            flex-direction: column;
            min-height: unset;
        }

        .login-panel-left {
            flex: unset;
            padding: 32px 26px;
        }

        .feature-list {
            display: none;
        }

        .panel-desc {
            margin-bottom: 18px;
        }

        .panel-title {
            font-size: 22px;
        }

        .login-panel-right {
            padding: 32px 26px;
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
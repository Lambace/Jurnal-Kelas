<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Jurnal Kelas - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            display: flex;
            justify-content: center;    /* Center horizontal */
            align-items: center;        /* Center vertikal */
            min-height: 100vh;
            padding: 1rem;
            margin: 0;
        }

        .container {
            position: relative;
            width: 420px;
            max-width: 100%;
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .form-container {
            position: relative;
            width: 100%;
        }

        .form-box {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        /* ===== DESKTOP: Sliding Animation (Vertical) ===== */
        @media (min-width: 641px) {
            .container {
                height: 700px;
            }

            .form-container {
                height: 100%;
                overflow: hidden;
            }

            .form-box {
                position: absolute;
                top: 0;
                left: 0;
                height: 100%;
                overflow-y: auto;
                scrollbar-width: none;
                -ms-overflow-style: none;
                transition: transform 0.6s ease-in-out, opacity 0.6s ease-in-out;
            }

            .form-box::-webkit-scrollbar {
                display: none;
            }

            .login-form {
                transform: translateY(0);
                opacity: 1;
                z-index: 2;
            }

            .register-form {
                transform: translateY(100%);
                opacity: 0;
                z-index: 1;
            }

            .container.register-mode .login-form {
                transform: translateY(-100%);
                opacity: 0;
                z-index: 1;
            }

            .container.register-mode .register-form {
                transform: translateY(0);
                opacity: 1;
                z-index: 2;
            }

            .curtain {
                display: flex;
            }
        }

        /* ===== MOBILE: Simple Show/Hide ===== */
        @media (max-width: 640px) {
            .container {
                height: auto;
                max-height: 95vh;
            }

            .form-container {
                height: auto;
            }

            .form-box {
                position: relative;
                height: auto;
            }

            .login-form {
                display: flex;
            }

            .register-form {
                display: none;
            }

            .container.register-mode .login-form {
                display: none;
            }

            .container.register-mode .register-form {
                display: flex;
            }

            .curtain {
                display: none !important;
            }
        }

        /* ===== HEADER ===== */
        .header {
            background: linear-gradient(135deg, #0a3d62 0%, #1e5f8e 100%);
            color: white;
            padding: 2rem 2rem 1.5rem;
            text-align: center;
            flex-shrink: 0;
        }

        .header h2 {
            font-size: 1.6rem;
            margin-bottom: 0.4rem;
        }

        .header p {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-bottom: 0.8rem;
        }

        .btn-toggle {
            background: transparent;
            border: 2px solid white;
            color: white;
            padding: 0.5rem 1.8rem;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .btn-toggle:hover {
            background: white;
            color: #0a3d62;
        }

        /* ===== FORM CONTENT ===== */
        .form-content {
            padding: 1.5rem 2rem 2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-content h3 {
            text-align: center;
            margin-bottom: 1.25rem;
            color: #333;
            font-size: 1.4rem;
        }

        .input-group {
            position: relative;
            margin-bottom: 1rem;
        }

        .input-group input {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.8rem;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .input-group input:focus {
            border-color: #667eea;
            background: white;
            outline: none;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .input-group .icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1rem;
        }

        .forgot-password {
            text-align: center;
            margin: 0.75rem 0;
        }

        .forgot-password a {
            color: #667eea;
            text-decoration: none;
            font-size: 0.85rem;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .error-message {
            text-align: center;
            margin: 0.5rem 0;
            padding: 0.5rem;
            background: #fef2f2;
            border-radius: 8px;
            color: #dc2626;
            font-size: 0.85rem;
        }

        .btn-submit {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, #00d2d3 0%, #00a8ff 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(0, 168, 255, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(0, 168, 255, 0.4);
        }

        /* ===== SOCIAL LOGIN (DINONAKTIFKAN) ===== */
        .social-login {
            margin-top: 1.25rem;
            text-align: center;
        }

        .social-login p {
            color: #999;
            font-size: 0.8rem;
            margin-bottom: 0.75rem;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
        }

        .social-icon {
            width: 42px;
            height: 42px;
            border: 2px solid #e1e8ed;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: not-allowed;
            transition: all 0.3s ease;
            background: #f8f9fa;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            color: #999;
            opacity: 0.6;
            filter: grayscale(100%);
        }

        .social-icon:hover {
            transform: none;
            box-shadow: none;
            background: #f8f9fa;
            border-color: #e1e8ed;
            color: #999;
        }

        /* ===== BOTTOM PANEL ===== */
        .bottom-panel {
            background: linear-gradient(135deg, #00d2d3 0%, #00a8ff 100%);
            color: white;
            padding: 1.5rem 2rem;
            text-align: center;
            flex-shrink: 0;
        }

        .bottom-panel h3 {
            font-size: 1.3rem;
            margin-bottom: 0.3rem;
        }

        .bottom-panel p {
            font-size: 0.8rem;
            opacity: 0.9;
            margin-bottom: 0.75rem;
        }

        /* ===== CURTAIN ANIMATION (Desktop Only) ===== */
        .curtain {
            position: absolute;
            left: 0;
            right: 0;
            width: 100%;
            height: 100%;
            z-index: 10;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            pointer-events: none;
        }

        .curtain-top {
            top: 0;
            background: linear-gradient(135deg, #0a3d62 0%, #1e5f8e 100%);
            transform: translateY(-100%);
            transition: transform 0.9s cubic-bezier(0.4, 0, 0.2, 1);
            color: white;
        }

        .curtain-top h2 { font-size: 1.6rem; margin-bottom: 0.5rem; }
        .curtain-top p { font-size: 0.85rem; opacity: 0.9; }

        .curtain-bottom {
            bottom: 0;
            background: linear-gradient(135deg, #00d2d3 0%, #00a8ff 100%);
            transform: translateY(100%);
            transition: transform 0.9s cubic-bezier(0.4, 0, 0.2, 1);
            color: white;
        }

        .curtain-bottom h2 { font-size: 1.6rem; margin-bottom: 0.5rem; }
        .curtain-bottom p { font-size: 0.85rem; opacity: 0.9; }

        .container.curtain-down .curtain-top {
            transform: translateY(0);
            pointer-events: auto;
        }

        .container.curtain-up .curtain-bottom {
            transform: translateY(0);
            pointer-events: auto;
        }

        .container.transitioning {
            overflow: hidden;
        }

        /* ===== MOBILE FINE TUNING ===== */
        @media (max-width: 640px) {
            body {
                padding: 0.5rem;
                align-items: center;       /* Pastikan center vertikal */
                justify-content: center;   /* Pastikan center horizontal */
            }

            .container {
                border-radius: 16px;
                width: 100%;
                max-width: 380px;          /* Batasi lebar agar rapi di HP */
                margin: 0 auto;            /* Center horizontal */
            }
            .header {
                padding: 1.5rem 1.5rem 1.25rem;
            }

            .header h2 {
                font-size: 1.4rem;
            }

            .form-content {
                padding: 1.25rem 1.5rem 1.5rem;
            }

            .form-content h3 {
                font-size: 1.25rem;
                margin-bottom: 1rem;
            }

            .input-group input {
                padding: 0.75rem 0.75rem 0.75rem 2.5rem;
                font-size: 0.85rem;
            }

            .bottom-panel {
                padding: 1.25rem 1.5rem;
            }
        }

        @media (max-width: 380px) {
            .header h2 {
                font-size: 1.2rem;
            }

            .form-content {
                padding: 1rem 1.25rem 1.25rem;
            }

            .social-icon {
                width: 38px;
                height: 38px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container" id="loginContainer">
        <!-- Curtain Elements (Desktop Only) -->
        <div class="curtain curtain-top">
            <h2>Halo, Selamat Datang</h2>
            <p>Menyiapkan akun Anda...</p>
        </div>
        <div class="curtain curtain-bottom">
            <h2>Selamat!</h2>
            <p>Menuju Halaman Login...</p>
        </div>

        <div class="form-container">
            <!-- ========== LOGIN FORM ========== -->
            <div class="form-box login-form">
                <div class="header">
                    <h2>Halo, Selamat Datang</h2>
                    <p>Belum Punya Akun?</p>
                    <button class="btn-toggle" onclick="toggleForm()">Daftar</button>
                </div>

                <div class="form-content">
                    <h3>Login</h3>

                    @if ($errors->any())
                        <div class="error-message">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ url('/login') }}">
                        @csrf

                        <div class="input-group">
                                <span class="icon">👤</span>
                                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                            </div>

                            <div class="input-group">
                                <span class="icon">🔒</span>
                                <input type="password" name="password" placeholder="Password" required>
                            </div>

                        <div class="forgot-password">
                            <a href="#">Forgot Password</a>
                        </div>

                        <button type="submit" class="btn-submit">Login</button>
                    </form>

                    <div class="social-login">
                        <p>atau login dengan media sosial</p>
                        <div class="social-icons">
                            <a href="javascript:void(0)" onclick="return false;" class="social-icon" title="Fitur segera hadir">G</a>
                            <a href="javascript:void(0)" onclick="return false;" class="social-icon" title="Fitur segera hadir">f</a>
                            <a href="javascript:void(0)" onclick="return false;" class="social-icon" title="Fitur segera hadir">♪</a>
                            <a href="javascript:void(0)" onclick="return false;" class="social-icon" title="Fitur segera hadir">📷</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========== REGISTER FORM ========== -->
            <div class="form-box register-form">
                <div class="form-content">
                    <h3>Registrasi</h3>

                    @if ($errors->any())
                        <div class="error-message">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ url('/register') }}">
                        @csrf

                        <div class="input-group">
                            <span class="icon">👤</span>
                            <input type="text" name="nama" placeholder="Nama Lengkap" value="{{ old('nama') }}" required>
                        </div>

                        <div class="input-group">
                            <span class="icon">✉</span>
                            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                        </div>

                        <div class="input-group">
                            <span class="icon">🔒</span>
                            <input type="password" name="password" placeholder="Password" required>
                        </div>

                        <div class="input-group">
                            <span class="icon">🔒</span>
                            <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required>
                        </div>

                        <button type="submit" class="btn-submit">Daftar Sekarang</button>
                    </form>

                    <div class="social-login">
                        <p>atau daftar dengan media sosial</p>
                        <div class="social-icons">
                            <a href="javascript:void(0)" onclick="return false;" class="social-icon" title="Fitur segera hadir">G</a>
                            <a href="javascript:void(0)" onclick="return false;" class="social-icon" title="Fitur segera hadir">f</a>
                            <a href="javascript:void(0)" onclick="return false;" class="social-icon" title="Fitur segera hadir">♪</a>
                            <a href="javascript:void(0)" onclick="return false;" class="social-icon" title="Fitur segera hadir"></a>
                        </div>
                    </div>
                </div>

                <div class="bottom-panel">
                    <h3>Selamat Datang!</h3>
                    <p>Sudah memiliki akun? Silakan login.</p>
                    <button class="btn-toggle" onclick="toggleForm()">Login</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let isTransitioning = false;

        function toggleForm() {
            // Mobile: Langsung toggle class (show/hide)
            if (window.innerWidth <= 640) {
                const container = document.getElementById('loginContainer');
                container.classList.toggle('register-mode');
                return;
            }

            // Desktop: Pakai animasi curtain
            if (isTransitioning) return;
            isTransitioning = true;

            const container = document.getElementById('loginContainer');
            const isRegisterMode = container.classList.contains('register-mode');

            container.classList.add('transitioning');

            if (isRegisterMode) {
                container.classList.add('curtain-up');
                setTimeout(() => {
                    container.classList.remove('register-mode');
                    setTimeout(() => {
                        container.classList.remove('curtain-up');
                        container.classList.remove('transitioning');
                        isTransitioning = false;
                    }, 150);
                }, 900);
            } else {
                container.classList.add('curtain-down');
                setTimeout(() => {
                    container.classList.add('register-mode');
                    setTimeout(() => {
                        container.classList.remove('curtain-down');
                        container.classList.remove('transitioning');
                        isTransitioning = false;
                    }, 150);
                }, 900);
            }
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - {{ \App\Models\Setting::getVal('system_title', 'Plataforma Corporativa RH') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            @php
                $loginImage = \App\Models\Setting::getVal('login_image_path');
            @endphp
            @if($loginImage)
                background: linear-gradient(rgba(15, 23, 42, 0.6), rgba(15, 23, 42, 0.8)), url('{{ $loginImage }}') no-repeat center center fixed;
                background-size: cover;
            @else
                background: linear-gradient(135deg, #064e3b 0%, #022c22 45%, #0f172a 100%);
            @endif
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Decorative circles for background abstract layout */
        body::before, body::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            z-index: 1;
            opacity: 0.15;
            pointer-events: none;
        }
        body::before {
            background: #10b981;
            top: -50px;
            right: -50px;
            filter: blur(80px);
        }
        body::after {
            background: #059669;
            bottom: -50px;
            left: -50px;
            filter: blur(80px);
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.92);
            padding: 45px 35px 35px 35px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.5) inset;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            text-align: center;
            z-index: 2;
            transition: transform 0.3s ease;
        }

        .brand-icon-wrapper {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
        }

        .logo-title {
            font-size: 26px;
            font-weight: 700;
            background: linear-gradient(to right, #047857, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .subtitle {
            font-size: 13.5px;
            color: #64748b;
            margin-bottom: 35px;
            font-weight: 500;
            line-height: 1.4;
        }

        .grupo {
            margin-bottom: 22px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 16px;
            display: flex;
            align-items: center;
            pointer-events: none;
        }

        input {
            width: 100%;
            padding: 12.5px 16px 12.5px 42px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 14px;
            outline: none;
            background: rgba(248, 250, 252, 0.7);
            color: #1e293b;
            transition: all 0.25s ease;
        }

        input::placeholder {
            color: #94a3b8;
        }

        input:focus {
            border-color: #10b981;
            background: white;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 13.5px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-1.5px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.45);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .error-box {
            background: rgba(254, 226, 226, 0.95);
            color: #991b1b;
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid #fecaca;
            font-size: 13px;
            text-align: left;
            box-shadow: 0 4px 10px rgba(153, 27, 27, 0.05);
        }

        .error-box ul {
            list-style-type: none;
        }

        .error-box li {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .footer-text {
            margin-top: 30px;
            font-size: 11.5px;
            color: #94a3b8;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="login-container">
        @php
            $systemTitle = \App\Models\Setting::getVal('system_title', 'Plataforma Corporativa RH');
            $loginLogo = \App\Models\Setting::getVal('login_logo_path');
        @endphp

        <!-- Icono de marca profesional o logo corporativo personalizado -->
        @if($loginLogo)
            <div style="margin: 0 auto 25px auto; display: flex; align-items: center; justify-content: center; height: 68px;">
                <img src="{{ $loginLogo }}" alt="Logo Corporativo" style="max-width: 180px; max-height: 68px; object-fit: contain; border-radius: 8px;">
            </div>
        @else
            <div class="brand-icon-wrapper">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                    <polyline points="9 16 11 18 15 14"></polyline>
                </svg>
            </div>
        @endif

        <div class="logo-title">{{ $systemTitle }}</div>
        <div class="subtitle">Ingresa tus credenciales para acceder al portal de vacaciones</div>

        @if($errors->any())
            <div class="error-box">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span>{{ $error }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf

            <div class="grupo">
                <label for="email">Usuario o Correo</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </span>
                    <input type="text" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="usuario o correo@empresa.com">
                </div>
            </div>

            <div class="grupo">
                <label for="password">Contraseña</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </span>
                    <input type="password" name="password" id="password" required placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <span>Entrar al Sistema</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </button>
        </form>

        <div class="footer-text">
            Control de Vacaciones &copy; {{ date('Y') }}
        </div>
    </div>

</body>
</html>

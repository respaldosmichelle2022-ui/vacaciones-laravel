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
                background: url('{{ $loginImage }}') no-repeat center center fixed;
                background-size: cover;
            @else
                background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #3b82f6 100%);
            @endif
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.85);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            text-align: center;
        }

        .logo-title {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(to right, #2563eb, #38bdf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 14px;
            color: #475569;
            margin-bottom: 30px;
            font-weight: 500;
        }

        .grupo {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            background: rgba(248, 250, 252, 0.8);
            transition: all 0.2s ease;
        }

        input:focus {
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: all 0.2s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        .error-box {
            background: rgba(254, 226, 226, 0.9);
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #fecaca;
            font-size: 13px;
            text-align: left;
        }

        .error-box ul {
            list-style-type: none;
        }
    </style>
</head>
<body>

    <div class="login-container">
        @php
            $systemTitle = \App\Models\Setting::getVal('system_title', 'Plataforma Corporativa RH');
        @endphp

        <div class="logo-title">{{ $systemTitle }}</div>
        <div class="subtitle">Ingresa tus credenciales para acceder al portal</div>

        @if($errors->any())
            <div class="error-box">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>⚠️ {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf

            <div class="grupo">
                <label for="email">Usuario o Correo Electrónico</label>
                <input type="text" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="usuario, usuario123 o correo@empresa.com">
            </div>

            <div class="grupo">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-submit">Entrar al Sistema</button>
        </form>
    </div>

</body>
</html>

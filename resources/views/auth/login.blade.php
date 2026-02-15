<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Keripik iLiL Admin</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(160deg, #060d08 0%, #0b1a10 40%, #071a0d 100%);
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #f0f0f0;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 40px 36px;
            background: linear-gradient(135deg, rgba(10,28,18,.88), rgba(8,22,14,.82));
            border: 1px solid rgba(255,255,255,.10);
            border-radius: 20px;
            backdrop-filter: blur(28px);
            box-shadow: 0 8px 40px rgba(0,0,0,.45), 0 0 80px rgba(57,217,138,.06);
        }
        .login-header { text-align: center; margin-bottom: 32px; }
        .login-header img { width: 56px; height: 56px; border-radius: 16px; margin-bottom: 12px; border: 1px solid rgba(255,255,255,.12); padding: 4px; background: rgba(255,255,255,.06); }
        .login-header h1 { font-size: 1.4rem; font-weight: 800; margin-bottom: 4px; }
        .login-header p { font-size: .85rem; color: rgba(255,255,255,.55); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: .82rem; font-weight: 600; margin-bottom: 6px; color: rgba(255,255,255,.75); }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            font-size: .95rem;
            color: #f0f0f0;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 12px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-group input:focus {
            border-color: rgba(57,217,138,.5);
            box-shadow: 0 0 0 3px rgba(57,217,138,.12);
        }
        .form-group input::placeholder { color: rgba(255,255,255,.3); }
        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            font-size: .85rem;
            color: rgba(255,255,255,.6);
        }
        .remember-row input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: #39d98a;
        }
        .btn-login {
            display: block;
            width: 100%;
            padding: 14px;
            font-size: 1rem;
            font-weight: 700;
            color: #071a0d;
            background: linear-gradient(135deg, #39d98a, #2bc47a);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: transform .15s, box-shadow .2s;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(57,217,138,.35);
        }
        .btn-login:active { transform: scale(.98); }
        .error-msg {
            background: rgba(255,59,92,.12);
            border: 1px solid rgba(255,59,92,.25);
            color: #ff6b82;
            font-size: .82rem;
            padding: 10px 14px;
            border-radius: 10px;
            margin-bottom: 18px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: .82rem;
            color: rgba(255,255,255,.45);
            text-decoration: none;
        }
        .back-link:hover { color: #39d98a; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <img src="{{ asset('assets/brand/logo.png') }}" alt="Keripik iLiL">
            <h1>Admin Panel</h1>
            <p>Masuk ke CMS Keripik iLiL</p>
        </div>

        @if ($errors->any())
            <div class="error-msg">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       placeholder="admin@keripikilil.com" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="••••••••" required>
            </div>
            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Ingat saya</label>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>

        <a href="{{ url('/') }}" class="back-link">← Kembali ke Landing Page</a>
    </div>
</body>
</html>

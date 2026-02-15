<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} — Keripik iLiL</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(160deg, #060d08 0%, #0b1a10 40%, #071a0d 100%);
            font-family: 'Inter', system-ui, sans-serif; color: #f0f0f0;
            padding: 24px;
        }
        .result-card {
            width: 100%; max-width: 480px; text-align: center;
            background: linear-gradient(135deg, rgba(10,28,18,.88), rgba(8,22,14,.82));
            border: 1px solid rgba(255,255,255,.10); border-radius: 22px;
            backdrop-filter: blur(28px); padding: 48px 32px;
            box-shadow: 0 8px 40px rgba(0,0,0,.45);
        }
        .result-icon { font-size: 4rem; margin-bottom: 16px; }
        .result-card h1 { font-size: 1.4rem; font-weight: 800; margin-bottom: 10px; }
        .result-card p { color: rgba(255,255,255,.6); margin-bottom: 28px; line-height: 1.6; }
        .btn-row { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; gap: 6px; padding: 12px 24px;
            font-size: .9rem; font-weight: 700; border-radius: 12px; border: none;
            cursor: pointer; text-decoration: none; transition: all .15s; font-family: inherit;
        }
        .btn:hover { transform: translateY(-1px); text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, #39d98a, #2bc47a); color: #071a0d; }
        .btn-primary:hover { box-shadow: 0 4px 16px rgba(57,217,138,.3); }
        .btn-ghost { background: rgba(255,255,255,.06); color: #f0f0f0; border: 1px solid rgba(255,255,255,.1); }
        .btn-ghost:hover { background: rgba(255,255,255,.1); }
        .pending-note { margin-top: 20px; font-size: .82rem; color: rgba(255,213,74,.8); background: rgba(255,213,74,.08); padding: 10px 14px; border-radius: 10px; border: 1px solid rgba(255,213,74,.15); }
    </style>
</head>
<body>
    <div class="result-card">
        <div class="result-icon">
            @if ($status === 'success') ✅ @else ❌ @endif
        </div>

        <h1>{{ $title }}</h1>
        <p>{{ $message }}</p>

        @if (request('pending'))
            <div class="pending-note">
                ⏳ Pembayaran masih menunggu konfirmasi. Kamu akan menerima notifikasi setelah pembayaran terverifikasi.
            </div>
        @endif

        <div class="btn-row">
            <a href="{{ url('/') }}" class="btn btn-primary">🏠 Kembali ke Beranda</a>
            @if ($status === 'failed')
                <a href="{{ route('checkout') }}" class="btn btn-ghost">🔄 Coba Lagi</a>
            @endif
        </div>
    </div>
</body>
</html>

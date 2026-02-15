<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Resi & Status Pesanan — Keripik iLiL</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #060d08; --bg2: #0b1a10; --surface: rgba(10,28,18,.88);
            --stroke: rgba(255,255,255,.10); --accent: #39d98a; --text: #f0f0f0; --muted: rgba(255,255,255,.55);
        }
        body {
            min-height: 100vh; background: linear-gradient(160deg, #060d08 0%, #0b1a10 40%, #071a0d 100%);
            font-family: 'Inter', system-ui, sans-serif; color: var(--text);
            display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 32px 16px;
        }
        a { color: var(--accent); text-decoration: none; }
        .container { width: 100%; max-width: 500px; text-align: center; }
        .logo { width: 60px; height: 60px; object-fit: contain; margin-bottom: 24px; }
        
        .card {
            background: var(--surface); border: 1px solid var(--stroke); border-radius: 20px;
            padding: 32px; backdrop-filter: blur(20px); box-shadow: 0 8px 40px rgba(0,0,0,.4);
            text-align: left; margin-bottom: 24px;
        }
        .card h1 { font-size: 1.4rem; font-weight: 800; margin-bottom: 8px; text-align: center; }
        .card p { color: var(--muted); font-size: .9rem; margin-bottom: 24px; text-align: center; }

        .search-box { display: flex; gap: 10px; margin-bottom: 10px; }
        .input {
            flex: 1; padding: 12px 16px; border-radius: 12px; background: rgba(255,255,255,.06);
            border: 1px solid var(--stroke); color: #fff; font-family: inherit; font-size: 1rem;
            outline: none; transition: all .2s;
        }
        .input:focus { border-color: var(--accent); box-shadow: 0 0 0 2px rgba(57,217,138,.2); }
        .btn {
            padding: 12px 20px; border-radius: 12px; background: var(--accent); color: #000;
            font-weight: 700; border: none; cursor: pointer; transition: all .2s;
        }
        .btn:hover { filter: brightness(110%); transform: translateY(-1px); }

        .result { animation: slideUp .4s cubic-bezier(0.2, 0.8, 0.2, 1); }
        @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .status-badge {
            display: inline-block; padding: 6px 12px; border-radius: 99px; font-size: .8rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .5px; margin-bottom: 16px;
        }
        .status-pending { background: rgba(255,213,74,.15); color: #ffd54a; border: 1px solid rgba(255,213,74,.3); }
        .status-paid { background: rgba(57,217,138,.15); color: #39d98a; border: 1px solid rgba(57,217,138,.3); }
        .status-processing { background: rgba(91,141,239,.15); color: #5b8def; border: 1px solid rgba(91,141,239,.3); }
        .status-shipped { background: rgba(168,85,247,.15); color: #a855f7; border: 1px solid rgba(168,85,247,.3); }
        .status-completed { background: rgba(34,197,94,.15); color: #22c55e; border: 1px solid rgba(34,197,94,.3); }
        .status-cancelled { background: rgba(255,59,92,.15); color: #ff3b5c; border: 1px solid rgba(255,59,92,.3); }

        .timeline { margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--stroke); }
        .timeline-item { position: relative; padding-left: 24px; margin-bottom: 16px; }
        .timeline-item::before {
            content: ''; position: absolute; left: 0; top: 6px; width: 10px; height: 10px;
            background: var(--accent); border-radius: 50%; box-shadow: 0 0 0 4px rgba(57,217,138,.2);
        }
        .timeline-item:last-child { margin-bottom: 0; }
        .t-title { font-weight: 700; font-size: .95rem; }
        .t-date { font-size: .8rem; color: var(--muted); margin-top: 2px; }

        .tracking-info {
            background: rgba(255,255,255,.03); border: 1px solid var(--stroke); border-radius: 12px;
            padding: 16px; margin-top: 20px;
        }
        .tracking-label { font-size: .75rem; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; }
        .tracking-val { font-size: 1.1rem; font-weight: 700; margin-top: 4px; font-family: monospace; letter-spacing: 1px; }
        
        .empty-state { text-align: center; color: var(--muted); padding: 20px; }
        .home-link { text-align: center; display: block; margin-top: 20px; color: var(--muted); font-size: .9rem; }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('assets/brand/logo.png') }}" alt="Logo" class="logo">
        
        <div class="card">
            <h1>Lacak Pesanan</h1>
            <p>Masukkan Nomor Order (ID) pesanan kamu.</p>

            <form action="{{ route('tracking') }}" method="GET">
                <div class="search-box">
                    <input type="text" name="order_number" class="input" placeholder="Contoh: ILIL-260215-ABCD" 
                           value="{{ request('order_number') }}" required>
                    <button type="submit" class="btn">Cek</button>
                </div>
            </form>

            @if(request('order_number') && !$order)
                <div class="empty-state">
                    ❌ Pesanan tidak ditemukan. Pastikan nomor order benar.
                </div>
            @endif

            @if($order)
                <div class="result">
                    <div style="margin-top: 24px"></div>
                    <div class="status-badge status-{{ $order->status }}">Status: {{ ucfirst($order->status) }}</div>
                    
                    <div>
                        <strong>{{ $order->customer_name }}</strong> <br>
                        <span style="color:var(--muted)">Total: {{ $order->formatted_total }}</span>
                    </div>

                    @if($order->status == 'shipped' || $order->status == 'completed')
                        <div class="tracking-info">
                            <div class="tracking-label">Nomor Resi ({{ $order->shipping_courier }})</div>
                            <div class="tracking-val">{{ $order->tracking_number }}</div>
                            <div style="margin-top:8px; font-size:.8rem; color:var(--muted)">
                                Dikirim pada: {{ $order->shipped_at ? $order->shipped_at->format('d M Y H:i') : '-' }}
                            </div>
                        </div>
                    @endif

                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="t-title">Pesanan Dibuat</div>
                            <div class="t-date">{{ $order->created_at->format('d M Y H:i') }}</div>
                        </div>
                        @if($order->paid_at)
                        <div class="timeline-item">
                            <div class="t-title">Pembayaran Diterima</div>
                            <div class="t-date">{{ $order->paid_at->format('d M Y H:i') }}</div>
                        </div>
                        @endif
                        @if($order->shipped_at)
                        <div class="timeline-item">
                            <div class="t-title">Pesanan Dikirim</div>
                            <div class="t-date">{{ $order->shipped_at->format('d M Y H:i') }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <a href="{{ url('/') }}" class="home-link">← Kembali ke Beranda</a>
    </div>
</body>
</html>

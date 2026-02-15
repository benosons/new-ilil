<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Keripik iLiL CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #060d08; --bg2: #0b1a10; --surface: rgba(10,28,18,.88);
            --stroke: rgba(255,255,255,.10); --accent: #39d98a; --accent2: #ffd54a;
            --danger: #ff3b5c; --info: #5b8def; --purple: #a855f7;
            --text: #f0f0f0; --muted: rgba(255,255,255,.55);
            --sidebar-w: 260px; --header-h: 60px;
        }
        body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }
        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }

        /* Layout */
        .admin-wrap { display: flex; min-height: 100vh; }
        .sidebar {
            width: var(--sidebar-w); background: linear-gradient(180deg, var(--bg2), var(--bg));
            border-right: 1px solid var(--stroke); padding: 20px 0; position: fixed; top: 0; left: 0;
            height: 100vh; overflow-y: auto; z-index: 50;
        }
        .sidebar-brand { display: flex; align-items: center; gap: 10px; padding: 0 20px 24px; border-bottom: 1px solid var(--stroke); margin-bottom: 16px; }
        .sidebar-brand img { width: 36px; height: 36px; border-radius: 12px; border: 1px solid var(--stroke); padding: 2px; background: rgba(255,255,255,.06); }
        .sidebar-brand strong { font-size: .95rem; }
        .sidebar-brand small { display: block; font-size: .72rem; color: var(--muted); }

        .nav-section { padding: 0 12px; margin-bottom: 8px; }
        .nav-section .nav-label { font-size: .68rem; text-transform: uppercase; letter-spacing: 1.2px; color: var(--muted); padding: 8px 12px 4px; font-weight: 700; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 16px; border-radius: 10px; color: rgba(255,255,255,.72); font-size: .88rem; font-weight: 500; transition: all .15s; }
        .nav-item:hover { background: rgba(255,255,255,.06); color: var(--text); text-decoration: none; }
        .nav-item.active { background: rgba(57,217,138,.12); color: var(--accent); font-weight: 700; }
        .nav-item .icon { font-size: 1.1rem; width: 22px; text-align: center; }

        .main { margin-left: var(--sidebar-w); flex: 1; min-height: 100vh; }
        .topbar {
            height: var(--header-h); display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px; border-bottom: 1px solid var(--stroke); background: rgba(6,13,8,.85);
            backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 40;
        }
        .topbar h2 { font-size: 1.05rem; font-weight: 700; }
        .topbar-right { display: flex; align-items: center; gap: 14px; }
        .topbar-user { font-size: .82rem; color: var(--muted); }

        .content { padding: 28px; }

        /* Cards */
        .card {
            background: var(--surface); border: 1px solid var(--stroke); border-radius: 16px;
            padding: 24px; backdrop-filter: blur(12px);
        }
        .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .card-header h3 { font-size: 1rem; font-weight: 700; }

        /* Stat cards */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: var(--surface); border: 1px solid var(--stroke); border-radius: 14px;
            padding: 20px; text-align: center;
        }
        .stat-card .stat-value { font-size: 1.8rem; font-weight: 800; color: var(--accent); }
        .stat-card .stat-label { font-size: .78rem; color: var(--muted); margin-top: 4px; }

        /* Table */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .88rem; }
        th { text-align: left; padding: 12px 14px; font-weight: 700; font-size: .75rem; text-transform: uppercase; letter-spacing: .8px; color: var(--muted); border-bottom: 1px solid var(--stroke); }
        td { padding: 12px 14px; border-bottom: 1px solid rgba(255,255,255,.05); vertical-align: middle; }
        tr:hover td { background: rgba(255,255,255,.02); }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; font-size: .84rem; font-weight: 600; border-radius: 10px; border: none; cursor: pointer; transition: all .15s; text-decoration: none; }
        .btn:hover { text-decoration: none; transform: translateY(-1px); }
        .btn-primary { background: linear-gradient(135deg, #39d98a, #2bc47a); color: #071a0d; }
        .btn-primary:hover { box-shadow: 0 4px 16px rgba(57,217,138,.3); }
        .btn-danger { background: rgba(255,59,92,.15); color: var(--danger); border: 1px solid rgba(255,59,92,.2); }
        .btn-danger:hover { background: rgba(255,59,92,.25); }
        .btn-sm { padding: 6px 12px; font-size: .78rem; border-radius: 8px; }
        .btn-ghost { background: rgba(255,255,255,.06); color: var(--text); border: 1px solid var(--stroke); }
        .btn-ghost:hover { background: rgba(255,255,255,.1); }
        .btn-warning { background: rgba(255,213,74,.15); color: var(--accent2); border: 1px solid rgba(255,213,74,.2); }

        /* Badges */
        .badge-status { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }
        .badge-status.pending { background: rgba(255,213,74,.15); color: #ffd54a; }
        .badge-status.paid { background: rgba(57,217,138,.15); color: #39d98a; }
        .badge-status.processing { background: rgba(91,141,239,.15); color: #5b8def; }
        .badge-status.shipped { background: rgba(168,85,247,.15); color: #a855f7; }
        .badge-status.completed { background: rgba(34,197,94,.15); color: #22c55e; }
        .badge-status.cancelled { background: rgba(255,59,92,.15); color: #ff3b5c; }

        /* Form */
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: .82rem; font-weight: 600; color: rgba(255,255,255,.75); margin-bottom: 6px; }
        .form-control {
            width: 100%; padding: 11px 16px; font-size: .92rem; color: var(--text);
            background: rgba(255,255,255,.06); border: 1px solid var(--stroke); border-radius: 10px;
            outline: none; transition: border-color .2s, box-shadow .2s; font-family: inherit;
        }
        .form-control:focus { border-color: rgba(57,217,138,.5); box-shadow: 0 0 0 3px rgba(57,217,138,.1); }
        select.form-control { appearance: auto; }
        textarea.form-control { min-height: 80px; resize: vertical; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media (max-width: 640px) { .form-row { grid-template-columns: 1fr; } }

        /* Alert */
        .alert { padding: 12px 16px; border-radius: 10px; font-size: .85rem; margin-bottom: 18px; }
        .alert-success { background: rgba(57,217,138,.12); border: 1px solid rgba(57,217,138,.2); color: #39d98a; }
        .alert-error { background: rgba(255,59,92,.12); border: 1px solid rgba(255,59,92,.2); color: #ff6b82; }

        /* Pagination */
        .pagination { display: flex; align-items: center; justify-content: center; gap: 4px; margin-top: 20px; }
        .pagination a, .pagination span { padding: 6px 12px; border-radius: 8px; font-size: .82rem; border: 1px solid var(--stroke); color: var(--muted); }
        .pagination a:hover { background: rgba(255,255,255,.06); text-decoration: none; }
        .pagination .active span { background: rgba(57,217,138,.15); color: var(--accent); border-color: rgba(57,217,138,.3); font-weight: 700; }

        /* Misc */
        .text-muted { color: var(--muted); }
        .text-accent { color: var(--accent); }
        .text-danger { color: var(--danger); }
        .mb-0 { margin-bottom: 0; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 12px; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }

        /* Logout btn */
        .logout-form { display: inline; }
        .logout-btn { background: none; border: none; color: var(--muted); font-size: .82rem; cursor: pointer; font-family: inherit; }
        .logout-btn:hover { color: var(--danger); }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="admin-wrap">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <img src="{{ asset('assets/brand/logo.png') }}" alt="iLiL">
                <div>
                    <strong>Keripik iLiL</strong>
                    <small>Content Management</small>
                </div>
            </div>

            <div class="nav-section">
                <div class="nav-label">Menu</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="icon">📊</span> Dashboard
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-label">Manajemen</div>
                <a href="{{ route('admin.products.index') }}" class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <span class="icon">📦</span> Produk
                </a>
                <a href="{{ route('admin.orders.index') }}" class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <span class="icon">🧾</span> Pesanan
                </a>
                <a href="{{ route('admin.vouchers.index') }}" class="nav-item {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
                    <span class="icon">🎟️</span> Voucher
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <span class="icon">👥</span> Users
                </a>
            </div>

            <div class="nav-section" style="margin-top: auto; padding-top: 16px; border-top: 1px solid var(--stroke);">
                <a href="{{ url('/') }}" class="nav-item" target="_blank">
                    <span class="icon">🌐</span> Lihat Website
                </a>
            </div>
        </aside>

        <!-- Main -->
        <div class="main">
            <div class="topbar">
                <h2>@yield('page_title', 'Dashboard')</h2>
                <div class="topbar-right">
                    <span class="topbar-user">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="logout-btn">Logout ↗</button>
                    </form>
                </div>
            </div>

            <div class="content">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>

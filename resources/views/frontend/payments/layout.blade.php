<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pembayaran Online') &middot; {{ config('payment.merchant.name', 'Tapak Suci Sport Academy') }}</title>
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .site-header {
            background: #0f172a;
            color: #fff;
            padding: 16px 0;
        }
        .site-header .inner {
            max-width: 860px; margin: 0 auto; padding: 0 20px;
            display: flex; justify-content: space-between; align-items: center; gap: 12px;
        }
        .site-header .brand { display: flex; align-items: center; gap: 12px; }
        .site-header .logo {
            width: 40px; height: 40px; border-radius: 10px;
            background: #fa5241; display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 18px;
        }
        .site-header h1 { font-size: 16px; margin: 0; }
        .site-header p { margin: 2px 0 0; font-size: 12px; color: #94a3b8; }
        .site-header a.logout { color: #cbd5e1; font-size: 13px; text-decoration: none; }
        main { flex: 1; }
        .container { max-width: 860px; margin: 0 auto; padding: 28px 20px 48px; }
        .card {
            background: #fff; border-radius: 16px; padding: 28px;
            box-shadow: 0 12px 34px rgba(15, 23, 42, 0.08);
        }
        .card + .card { margin-top: 20px; }
        h2.section-title { margin: 0 0 6px; font-size: 20px; }
        p.subtitle { margin: 0 0 22px; color: #64748b; font-size: 14px; }
        label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155; }
        input[type="text"] {
            width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 10px;
            font-size: 15px; outline: none; transition: border-color .15s;
        }
        input[type="text"]:focus { border-color: #fa5241; }
        .btn {
            display: inline-block; border: none; border-radius: 10px; padding: 12px 20px;
            font-size: 15px; font-weight: 700; cursor: pointer; text-decoration: none; text-align: center;
        }
        .btn-primary { background: #fa5241; color: #fff; }
        .btn-primary:hover { background: #e2432f; }
        .btn-outline { background: #fff; color: #334155; border: 1px solid #cbd5e1; }
        .btn-sm { padding: 8px 14px; font-size: 13px; border-radius: 8px; }
        .alert { padding: 14px 16px; border-radius: 10px; font-size: 14px; margin-bottom: 18px; }
        .alert-error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .alert-info { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        th, td { text-align: left; padding: 12px 10px; border-bottom: 1px solid #e2e8f0; }
        th { font-size: 12px; text-transform: uppercase; letter-spacing: .4px; color: #64748b; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-gray { background: #e2e8f0; color: #475569; }
        .qr-wrap { text-align: center; padding: 10px 0 4px; }
        .qr-box {
            display: inline-block; padding: 16px; background: #fff; border: 1px dashed #cbd5e1; border-radius: 14px;
        }
        .amount { font-size: 26px; font-weight: 800; color: #0f172a; }
        .muted { color: #64748b; font-size: 13px; }
        .site-footer { text-align: center; padding: 20px; font-size: 12px; color: #94a3b8; }
        .steps { margin: 20px 0 0; padding-left: 20px; color: #475569; font-size: 14px; line-height: 1.7; }
        .row { display: flex; gap: 10px; flex-wrap: wrap; }
    </style>
    @stack('head')
    <script src="{{ asset('js/validation-id.js') }}" defer></script>
</head>
<body>
    <header class="site-header">
        <div class="inner">
            <div class="brand">
                @if (filled($site['assets']['logo'] ?? null))
                    <img src="{{ $site['assets']['logo'] }}" alt="{{ $site['identity']['name'] ?? 'Tapak Suci Sport Academy' }}" style="width:40px;height:40px;border-radius:10px;object-fit:contain;background:#fff;">
                @else
                    <div class="logo">TS</div>
                @endif
                <div>
                    <h1>{{ config('payment.merchant.name', 'Tapak Suci Sport Academy') }}</h1>
                    <p>Portal Pembayaran Siswa</p>
                </div>
            </div>
            @if (session('payment_portal_student_id'))
                <a class="logout" href="{{ route('payments.portal.logout') }}">Ganti Siswa</a>
            @endif
        </div>
    </header>

    <main>
        <div class="container">
            @yield('content')
        </div>
    </main>

    <footer class="site-footer">
        &copy; {{ date('Y') }} {{ config('payment.merchant.name', 'Tapak Suci Sport Academy') }}. Pembayaran aman &amp; terverifikasi.
    </footer>

    @stack('scripts')
</body>
</html>

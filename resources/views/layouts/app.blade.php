<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('app.dashboard.title')) · {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    @include('partials.theme-init')
    <style>
        :root {
            --bg: #000000;
            --panel: #000000;
            --panel-2: #000000;
            --border: rgba(255, 255, 255, .16);
            --border-strong: rgba(255, 255, 255, .4);
            --text: #ffffff;
            --muted: #ffffff;
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --success: #34d399;
            --danger: #f87171;
            --warning: #fbbf24;
            --blue: #60a5fa;
            --surface: rgba(255, 255, 255, .04);
            --surface-strong: rgba(255, 255, 255, .03);
            --hover: rgba(255, 255, 255, .06);
            --input-bg: #000000;
            --code-bg: #000000;
            --shadow: 0 20px 50px rgba(0, 0, 0, .45);
            --font: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Tahoma, "Noto Naskh Arabic", "Noto Sans Arabic", sans-serif;
        }

        [data-theme="light"] {
            --bg: #ffffff;
            --panel: #ffffff;
            --panel-2: #eff6ff;
            --border: rgba(37, 99, 235, .2);
            --border-strong: rgba(37, 99, 235, .55);
            --text: #000000;
            --muted: #000000;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --success: #047857;
            --danger: #b91c1c;
            --warning: #b45309;
            --blue: #1d4ed8;
            --surface: rgba(37, 99, 235, .06);
            --surface-strong: rgba(37, 99, 235, .04);
            --hover: rgba(37, 99, 235, .07);
            --input-bg: #ffffff;
            --code-bg: #ffffff;
            --shadow: 0 20px 50px rgba(37, 99, 235, .12);
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font);
            font-size: 15px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }
        a { color: inherit; text-decoration: none; }
        code, pre, .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }

        .app-shell { display: flex; min-height: 100vh; }

        .sidebar {
            width: 260px;
            flex: 0 0 260px;
            background: var(--panel);
            border-right: 1px solid var(--border);
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            position: sticky;
            top: 0;
            height: 100vh;
        }
        .brand { display: flex; flex-direction: column; align-items: flex-start; gap: 10px; padding: 4px 10px; }
        .brand-logo-tile {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 7px 11px; border-radius: 12px;
            background: #ffffff; border: 1px solid var(--border);
            box-shadow: 0 8px 20px rgba(0, 0, 0, .45);
        }
        [data-theme="light"] .brand-logo-tile { box-shadow: 0 8px 20px rgba(37, 99, 235, .14); }
        .brand-logo-tile img { display: block; height: 34px; width: auto; }
        .brand .brand-sub { font-size: 19px; font-weight: 700; letter-spacing: -.01em; color: var(--text); margin-top: 0; }
        .nav { display: flex; flex-direction: column; gap: 4px; flex: 1; overflow-y: auto; min-height: 0; padding-inline-end: 2px; scrollbar-width: thin; }
        .nav-label { font-size: 11px; letter-spacing: .14em; text-transform: uppercase; color: var(--text); padding: 0 10px; margin: 10px 0 4px; }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 9px;
            color: var(--muted);
            font-weight: 500;
            transition: background .15s, color .15s;
        }
        .nav-link:hover { background: var(--hover); color: var(--text); }
        .nav-link.active { background: rgba(99, 102, 241, .16); color: #ffffff; }
        .nav-link .dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; opacity: .55; }

        .nav-group { display: block; }
        .nav-group summary { list-style: none; cursor: pointer; user-select: none; }
        .nav-group summary::-webkit-details-marker { display: none; }
        .nav-group summary .chevron { margin-inline-start: auto; width: 14px; height: 14px; opacity: .7; transition: transform .18s ease; }
        .nav-group[open] summary .chevron { transform: rotate(180deg); }
        .subnav {
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin: 2px 0 6px;
            margin-inline-start: 17px;
            padding-inline-start: 12px;
            border-inline-start: 1px solid var(--border);
        }
        .nav-link.sub { padding: 8px 10px; font-size: 14px; }
        .nav-link.sub .dot { width: 5px; height: 5px; opacity: .45; }

        .sidebar-footer { border-top: 1px solid var(--border); padding-top: 16px; display: flex; flex-direction: column; gap: 12px; }
        .user-chip { display: flex; align-items: center; gap: 10px; min-width: 0; border-radius: 10px; padding: 4px; margin: -4px; transition: background .15s; }
        .user-chip:hover { background: var(--hover); }
        .avatar {
            width: 38px; height: 38px; border-radius: 50%;
            display: grid; place-items: center;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            font-size: 14px; font-weight: 700; color: #ffffff; flex: 0 0 auto;
            overflow: hidden;
        }
        .avatar img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .avatar-xl { width: 78px; height: 78px; font-size: 26px; }
        .user-chip strong { display: block; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-chip span { display: block; font-size: 12px; color: var(--muted); }

        .main { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .topbar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            padding: 28px 36px 20px;
            border-bottom: 1px solid var(--border);
            flex-wrap: wrap;
        }
        .topbar h1 { margin: 0; font-size: 26px; letter-spacing: -.02em; }
        .topbar p { margin: 4px 0 0; color: var(--muted); font-size: 14px; }
        .topbar-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .utility-actions { display: flex; gap: 8px; align-items: center; margin-inline-end: 4px; }
        .content { padding: 28px 36px 48px; }

        .icon-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            height: 38px; padding: 0 12px;
            border: 1px solid var(--border); border-radius: 9px;
            background: transparent; color: var(--text);
            font-family: inherit; font-size: 13px; font-weight: 700;
            cursor: pointer; transition: background .15s, border-color .15s;
        }
        .icon-btn:hover { background: var(--hover); border-color: var(--border-strong); }
        .icon-btn svg { width: 16px; height: 16px; }
        .theme-toggle .icon-sun { display: none; }
        [data-theme="light"] .theme-toggle .icon-sun { display: block; }
        [data-theme="light"] .theme-toggle .icon-moon { display: none; }

        .card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 22px;
            box-shadow: var(--shadow);
        }
        .card + .card { margin-top: 20px; }
        .card-header { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 16px; }
        .card-header h2 { margin: 0; font-size: 18px; }
        .card-header p { margin: 4px 0 0; color: var(--muted); font-size: 13px; }

        .stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 22px; }
        .stat { background: var(--panel); border: 1px solid var(--border); border-radius: 14px; padding: 18px 20px; box-shadow: var(--shadow); }
        .stat span { display: block; color: var(--muted); font-size: 13px; text-transform: uppercase; letter-spacing: .08em; }
        .stat strong { display: block; font-size: 33px; margin-top: 8px; letter-spacing: -.03em; }

        .table-wrap { overflow-x: auto; border: 1px solid var(--border); border-radius: 12px; }
        table { width: 100%; border-collapse: collapse; min-width: 640px; }
        th, td { text-align: left; padding: 12px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        th { font-size: 12px; text-transform: uppercase; letter-spacing: .08em; color: var(--muted); background: var(--surface); }
        tbody tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: var(--hover); }
        td .muted, .muted { color: var(--muted); }
        .cell-title { font-weight: 600; }
        .cell-sub { color: var(--muted); font-size: 13px; }

        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 9px; border-radius: 999px;
            font-size: 12px; font-weight: 600; letter-spacing: .02em;
            border: 1px solid transparent; white-space: nowrap;
        }
        .badge.green { color: var(--success); background: rgba(52, 211, 153, .1); border-color: rgba(52, 211, 153, .25); }
        .badge.red { color: var(--danger); background: rgba(248, 113, 113, .1); border-color: rgba(248, 113, 113, .25); }
        .badge.amber { color: var(--warning); background: rgba(251, 191, 36, .1); border-color: rgba(251, 191, 36, .25); }
        .badge.blue { color: var(--blue); background: rgba(96, 165, 250, .1); border-color: rgba(96, 165, 250, .25); }
        .badge.slate { color: var(--text); background: var(--surface); border-color: var(--border); }
        .badge.static { color: var(--text); background: rgba(99, 102, 241, .12); border-color: rgba(99, 102, 241, .3); }

        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 9px 15px; border-radius: 9px; border: 1px solid transparent;
            font-family: inherit; font-size: 14px; font-weight: 600; cursor: pointer;
            transition: background .15s, border-color .15s, color .15s;
            white-space: nowrap;
        }
        .btn-primary { background: var(--primary); color: #ffffff; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-secondary { background: var(--panel-2); color: var(--text); border-color: var(--border); }
        .btn-secondary:hover { border-color: var(--border-strong); }
        .btn-danger { background: rgba(248, 113, 113, .12); color: var(--danger); border-color: rgba(248, 113, 113, .28); }
        .btn-danger:hover { background: rgba(248, 113, 113, .2); }
        .btn-ghost { background: transparent; color: var(--muted); border-color: var(--border); }
        .btn-ghost:hover { color: var(--text); border-color: var(--border-strong); }
        .btn-sm { padding: 6px 10px; font-size: 13px; }
        .btn-block { width: 100%; }

        .toolbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 18px; flex-wrap: wrap; }
        .toolbar form { display: flex; gap: 8px; flex-wrap: wrap; }

        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 6px; }
        .field .hint { font-size: 12px; color: var(--muted); margin-top: 4px; }
        input[type="text"], input[type="email"], input[type="password"], input[type="date"], input[type="search"], select, textarea {
            width: 100%;
            padding: 9px 12px;
            border-radius: 9px;
            border: 1px solid var(--border);
            background: var(--input-bg);
            color: var(--text);
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: rgba(99, 102, 241, .65);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
        }
        input::placeholder, textarea::placeholder { color: var(--text); opacity: 1; }
        select option { background: var(--input-bg); color: var(--text); }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0 18px; }
        .form-grid .full { grid-column: 1 / -1; }

        .item-row {
            display: grid;
            grid-template-columns: 2fr 2fr .7fr .9fr 1.1fr auto;
            gap: 12px;
            align-items: start;
            padding: 14px 0;
            border-bottom: 1px solid var(--border);
        }
        .item-row:first-child { padding-top: 0; }
        .item-row:last-child { border-bottom: 0; padding-bottom: 0; }
        .item-row .field { margin-bottom: 0; }
        .item-row .btn { margin-top: 25px; }
        .error-text { color: var(--danger); font-size: 12px; margin-top: 4px; }

        .permission-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .permission-group { border: 1px solid var(--border); border-radius: 10px; padding: 12px 14px; background: var(--surface-strong); }
        .permission-group h3 { margin: 0 0 10px; font-size: 13px; text-transform: uppercase; letter-spacing: .08em; color: var(--muted); }
        .check { display: flex; gap: 9px; align-items: flex-start; padding: 5px 0; cursor: pointer; }
        .check input { margin-top: 3px; accent-color: var(--primary); }
        .check strong { font-weight: 500; font-size: 14px; }
        .check span { display: block; font-size: 12px; color: var(--muted); }

        .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 18px; font-size: 14px; border: 1px solid; }
        .alert.success { color: var(--success); background: rgba(52, 211, 153, .08); border-color: rgba(52, 211, 153, .25); }
        .alert.error { color: var(--danger); background: rgba(248, 113, 113, .08); border-color: rgba(248, 113, 113, .25); }
        .alert ul { margin: 6px 0 0; padding-left: 18px; }

        .empty { padding: 34px 20px; text-align: center; color: var(--muted); }
        .actions { display: flex; gap: 6px; flex-wrap: wrap; }
        .inline-form { display: inline; }
        .stack { display: flex; flex-direction: column; gap: 4px; }
        .role-tags { display: flex; gap: 5px; flex-wrap: wrap; }

        .pagination { display: flex; gap: 4px; align-items: center; flex-wrap: wrap; margin-top: 18px; }
        .pagination .page {
            min-width: 34px; height: 34px; padding: 0 10px;
            display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid var(--border); border-radius: 8px;
            color: var(--muted); font-size: 13px;
        }
        .pagination a.page:hover { color: var(--text); border-color: var(--border-strong); }
        .pagination .page.active { background: rgba(99, 102, 241, .18); border-color: rgba(99, 102, 241, .45); color: #ffffff; font-weight: 600; }
        .pagination .page.disabled { opacity: .4; }
        .pagination .summary { margin-left: auto; color: var(--muted); font-size: 13px; }

        .detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-bottom: 20px; }
        .detail { padding: 12px 16px; background: var(--surface-strong); border: 1px solid var(--border); border-radius: 10px; }
        .detail dt { font-size: 12px; text-transform: uppercase; letter-spacing: .07em; color: var(--muted); margin-bottom: 4px; }
        .detail dd { margin: 0; font-size: 14px; word-break: break-word; }
        pre.code {
            background: var(--code-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px;
            overflow: auto;
            font-size: 13px;
            color: var(--text);
            margin: 0;
        }
        .two-col { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }

        .profile-hero { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
        .profile-hero strong { display: block; font-size: 20px; letter-spacing: -.01em; }
        .profile-hero .meta { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 6px; align-items: center; }
        .profile-hero .meta span { font-size: 12px; color: var(--muted); }
        .segmented { display: inline-flex; gap: 4px; padding: 4px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface-strong); flex-wrap: wrap; }
        .seg {
            padding: 7px 14px; border: 0; border-radius: 7px; background: transparent;
            color: var(--muted); font-family: inherit; font-size: 13px; font-weight: 600;
            cursor: pointer; transition: background .15s, color .15s; text-decoration: none;
        }
        .seg:hover { color: var(--text); }
        .seg.active { background: var(--primary); color: #ffffff; }
        .pref-row { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; padding: 14px 0; border-bottom: 1px solid var(--border); }
        .pref-row:first-child { padding-top: 0; }
        .pref-row:last-child { border-bottom: 0; padding-bottom: 0; }
        .pref-row strong { display: block; font-size: 14px; }
        .pref-row p { margin: 2px 0 0; font-size: 12px; color: var(--muted); }
        input[type="file"] { width: 100%; font: inherit; font-size: 13px; color: var(--text); }
        input[type="file"]::file-selector-button {
            margin-inline-end: 10px; padding: 7px 12px; border-radius: 8px; border: 1px solid var(--border);
            background: var(--panel-2); color: var(--text); font-family: inherit; font-size: 12px;
            font-weight: 600; cursor: pointer;
        }
        [data-theme="light"] .seg.active { color: #ffffff; }
        [dir="rtl"] .pref-row { text-align: right; }

        [data-theme="light"] .nav-link.active { background: rgba(37, 99, 235, .1); color: #000000; }
        [data-theme="light"] .avatar { background: linear-gradient(135deg, #2563eb, #60a5fa); }
        [data-theme="light"] .badge.green,
        [data-theme="light"] .badge.red,
        [data-theme="light"] .badge.amber,
        [data-theme="light"] .badge.blue { color: #000000; }
        [data-theme="light"] .alert.success,
        [data-theme="light"] .alert.error { color: #000000; }
        [data-theme="light"] .error-text { color: #000000; }
        [data-theme="light"] .btn-danger { color: #000000; }
        [data-theme="light"] .pagination .page.active { background: rgba(37, 99, 235, .12); border-color: rgba(37, 99, 235, .5); color: #000000; }

        [dir="rtl"] th, [dir="rtl"] td { text-align: right; }
        [dir="rtl"] .sidebar { border-right: 0; border-left: 1px solid var(--border); }
        [dir="rtl"] .alert ul { padding-left: 0; padding-right: 18px; }
        [dir="rtl"] .pagination .summary { margin-left: 0; margin-right: auto; }
        [dir="rtl"] input, [dir="rtl"] textarea, [dir="rtl"] select { text-align: right; }
        [dir="rtl"] .nav-label,
        [dir="rtl"] .stat span,
        [dir="rtl"] .brand .brand-sub,
        [dir="rtl"] th,
        [dir="rtl"] .detail dt,
        [dir="rtl"] .permission-group h3 { letter-spacing: 0; }

        @media (max-width: 1000px) {
            .stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .permission-grid, .detail-grid, .two-col { grid-template-columns: 1fr; }
            .item-row { grid-template-columns: 1fr 1fr; }
            .item-row .btn { margin-top: 0; }
        }
        @media (max-width: 860px) {
            .app-shell { flex-direction: column; }
            .sidebar { width: 100%; flex: none; height: auto; position: static; padding: 18px; border-left: 0; border-right: 0; border-bottom: 1px solid var(--border); }
            .nav { flex-direction: row; flex-wrap: wrap; }
            .nav-label { display: none; }
            .sidebar-footer { flex-direction: row; justify-content: space-between; align-items: center; }
            .sidebar-footer form { width: auto; }
            .topbar, .content { padding-left: 20px; padding-right: 20px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="app-shell">
        @php
            $settingsActive = request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('audit.*');
            $canUsers = auth()->user()->hasPermission('users.view');
            $canRoles = auth()->user()->hasPermission('roles.view');
            $canAudit = auth()->user()->hasPermission('audit.view');
            $canRfq = auth()->user()->hasPermission('rfq.view');
            $canVendors = auth()->user()->hasPermission('vendors.view');
            $canLocal = auth()->user()->hasPermission('purchases.local.view');
            $canForeign = auth()->user()->hasPermission('purchases.foreign.view');
            $canMarket = auth()->user()->hasPermission('market.view');
            $canAdministration = auth()->user()->hasAnyPermission(['departments.view', 'locations.view', 'priorities.view', 'currencies.view', 'units.view', 'markets.view', 'order_types.view', 'proposed_methods.view']);
        @endphp

        <aside class="sidebar">
            <a class="brand" href="{{ route('home') }}" aria-label="{{ config('app.name') }}">
                <span class="brand-logo-tile">
                    <img src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }} logo">
                </span>
                <span class="brand-sub">{{ __('app.brand_subtitle') }}</span>
            </a>

            <nav class="nav">
                <div class="nav-label">{{ __('app.nav.workspace') }}</div>

                @if (auth()->user()->hasPermission('dashboard.view'))
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <span class="dot"></span> {{ __('app.nav.dashboard') }}
                    </a>
                @endif

                @if ($canRfq)
                    <a class="nav-link {{ request()->routeIs('rfqs.*') ? 'active' : '' }}" href="{{ route('rfqs.index') }}">
                        <span class="dot"></span> {{ __('app.nav.rfq') }}
                    </a>
                @endif

                @if ($canVendors)
                    <a class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}" href="{{ route('vendors.index') }}">
                        <span class="dot"></span> {{ __('app.nav.vendors') }}
                    </a>
                @endif

                @if ($canLocal)
                    <a class="nav-link {{ request()->routeIs('purchasing.local') ? 'active' : '' }}" href="{{ route('purchasing.local') }}">
                        <span class="dot"></span> {{ __('app.nav.local_purchasing') }}
                    </a>
                @endif

                @if ($canForeign)
                    <a class="nav-link {{ request()->routeIs('purchasing.foreign') ? 'active' : '' }}" href="{{ route('purchasing.foreign') }}">
                        <span class="dot"></span> {{ __('app.nav.foreign_purchasing') }}
                    </a>
                @endif

                @if ($canMarket)
                    <a class="nav-link {{ request()->routeIs('purchasing.market') ? 'active' : '' }}" href="{{ route('purchasing.market') }}">
                        <span class="dot"></span> {{ __('app.nav.local_market') }}
                    </a>
                @endif

                @if ($canAdministration)
                    <a class="nav-link {{ request()->routeIs('administration.*') || request()->routeIs('markets.*') || request()->routeIs('order_types.*') || request()->routeIs('proposed_methods.*') ? 'active' : '' }}" href="{{ route('administration.index') }}">
                        <span class="dot"></span> {{ __('app.nav.administration') }}
                    </a>
                @endif

                @if ($canUsers || $canRoles || $canAudit)
                    <details class="nav-group" @if ($settingsActive) open @endif>
                        <summary class="nav-link {{ $settingsActive ? 'active' : '' }}">
                            <span class="dot"></span> {{ __('app.nav.settings') }}
                            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M6 9l6 6 6-6"></path>
                            </svg>
                        </summary>

                        <div class="subnav">
                            @if ($canUsers)
                                <a class="nav-link sub {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                    <span class="dot"></span> {{ __('app.nav.users') }}
                                </a>
                            @endif

                            @if ($canRoles)
                                <a class="nav-link sub {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                                    <span class="dot"></span> {{ __('app.nav.roles') }}
                                </a>
                            @endif

                            @if ($canAudit)
                                <a class="nav-link sub {{ request()->routeIs('audit.*') ? 'active' : '' }}" href="{{ route('audit.index') }}">
                                    <span class="dot"></span> {{ __('app.nav.audit') }}
                                </a>
                            @endif
                        </div>
                    </details>
                @endif
            </nav>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-block btn-sm">{{ __('app.nav.sign_out') }}</button>
                </form>
            </div>
        </aside>

        <div class="main">
            <header class="topbar">
                <div>
                    <h1>@yield('heading', __('app.dashboard.heading'))</h1>
                    <p>@yield('subheading', '')</p>
                </div>
                <div class="topbar-actions">
                    <div class="utility-actions">
                        <a
                            class="icon-btn"
                            href="{{ route('language.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"
                            title="{{ __('app.language.switch_title') }}"
                            aria-label="{{ __('app.language.switch_title') }}"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M3 12h18"></path>
                                <path d="M12 3a15 15 0 0 1 0 18"></path>
                                <path d="M12 3a15 15 0 0 0 0 18"></path>
                            </svg>
                            <span>{{ __('app.language.switch_code') }}</span>
                        </a>
                        @include('partials.theme-toggle')
                    </div>
                    @yield('actions')
                </div>
            </header>

            <main class="content">
                @if (session('status'))
                    <div class="alert success">{{ session('status') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert error">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert error">
                        <strong>{{ __('app.messages.fix_following') }}</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>

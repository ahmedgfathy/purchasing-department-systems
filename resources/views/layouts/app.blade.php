<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#072949">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <title>@yield('title', __('app.dashboard.title')) · {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=Noto+Sans+Arabic:wght@400;500;700&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --pms-navy: #072949;
            --pms-deep: #031b33;
            --pms-sea: #0e5f95;
            --pms-medium: #0b396a;
            --pms-gold: #f2ae2e;
            --pms-green: #007236;
            --pms-teal: #5894ac;
            --pms-line: #d6e2ef;

            --bg: #f5f9ff;
            --panel: #ffffff;
            --panel-2: #f2f7fc;
            --border: #d6e2ef;
            --border-strong: #b9cee2;
            --text: #0f2035;
            --muted: #5a6d82;
            --primary: #072949;
            --primary-hover: #0b396a;
            --success: #007236;
            --danger: #b91c1c;
            --warning: #b45309;
            --blue: #0e5f95;
            --surface: #f2f7fc;
            --surface-strong: #f7fafd;
            --hover: #edf6ff;
            --input-bg: #ffffff;
            --code-bg: #f7fafd;
            --shadow: 0 16px 34px rgba(4, 25, 46, .08);
            --font: "Roboto", "Segoe UI", Tahoma, Arial, ui-sans-serif, system-ui, "Noto Sans Arabic", sans-serif;
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            background:
                radial-gradient(at 18% 8%, rgba(242, 174, 46, .25) 0%, transparent 50%),
                radial-gradient(at 82% 12%, rgba(0, 114, 54, .16) 0%, transparent 45%),
                radial-gradient(at 50% 92%, rgba(88, 148, 172, .22) 0%, transparent 40%),
                linear-gradient(#f5f9ff 0%, #f0f6fe 30%, #eaf2fa 70%, #eef5fc 100%);
            background-attachment: fixed;
            color: var(--text);
            font-family: var(--font);
            font-size: 15px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }
        a { color: inherit; text-decoration: none; }
        code, pre, .mono { font-family: "IBM Plex Mono", ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }

        .app-shell { display: flex; min-height: 100vh; }

        .sidebar {
            width: 260px;
            flex: 0 0 260px;
            background: linear-gradient(180deg, rgba(255, 255, 255, .96) 0%, rgba(241, 248, 255, .98) 100%);
            backdrop-filter: blur(8px);
            border-right: 1px solid var(--border);
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            position: sticky;
            top: 0;
            height: 100vh;
        }
        .brand { display: flex; flex-direction: column; align-items: flex-start; gap: 8px; padding: 4px 10px; }
        .brand-logo-tile {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 7px 11px; border-radius: 12px;
            background: #ffffff; border: 1px solid var(--border);
            box-shadow: 0 8px 18px rgba(4, 25, 46, .08);
        }
        .brand-logo-tile img { display: block; height: 78px; width: auto; min-width: 105px; }
        .brand-copy { display: flex; flex-direction: column; align-items: flex-start; gap: 1px; min-width: 0; }
        .brand .brand-company { display: block; font-size: 16px; font-weight: 800; letter-spacing: 0; color: var(--pms-navy); line-height: 1.25; }
        .brand .brand-sub { font-size: 17px; font-weight: 600; letter-spacing: -.01em; color: var(--muted); line-height: 1.25; }
        .nav { display: flex; flex-direction: column; gap: 4px; flex: 1; overflow-y: auto; min-height: 0; padding-inline-end: 2px; scrollbar-width: thin; }
        .nav-icon { display: inline-flex; align-items: center; justify-content: center; width: 18px; height: 18px; flex: 0 0 18px; }
        .nav-icon svg { width: 17px; height: 17px; }
        .nav-label { font-size: 11px; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); padding: 0 10px; margin: 10px 0 4px; }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 999px;
            color: var(--muted);
            font-weight: 600;
            transition: background .15s, color .15s, box-shadow .15s;
        }
        .nav-link:hover { background: var(--hover); color: var(--pms-navy); }
        .nav-link.active {
            background: linear-gradient(135deg, #072949, #114a73);
            color: #ffffff;
            box-shadow: 0 10px 20px rgba(7, 41, 73, .28);
        }
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
            background: linear-gradient(135deg, #072949, #0e5f95);
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
            position: sticky;
            top: 0;
            z-index: 30;
            background: linear-gradient(135deg, rgba(255, 255, 255, .95), rgba(241, 248, 255, .98));
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        .topbar h1 { margin: 0; font-size: 26px; letter-spacing: -.02em; color: var(--pms-navy); }
        .topbar p { margin: 4px 0 0; color: var(--muted); font-size: 14px; }
        .topbar-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .utility-actions { display: flex; gap: 8px; align-items: center; margin-inline-end: 4px; }
        .content { padding: 28px 36px 48px; }

        .icon-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            height: 40px; padding: 0 13px;
            border: 1px solid rgba(7, 41, 73, .2); border-radius: 12px;
            background: rgba(7, 41, 73, .08); color: var(--pms-navy);
            font-family: inherit; font-size: 13px; font-weight: 700;
            cursor: pointer; transition: background .15s, border-color .15s;
        }
        .icon-btn:hover { background: rgba(7, 41, 73, .14); border-color: rgba(7, 41, 73, .33); }
        .icon-btn svg { width: 16px; height: 16px; }

        .card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 22px;
            box-shadow: var(--shadow);
        }
        .card + .card { margin-top: 20px; }
        .card-header { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 16px; }
        .card-header h2 { margin: 0; font-size: 18px; }
        .card-header p { margin: 4px 0 0; color: var(--muted); font-size: 13px; }

        .stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 22px; }
        .stat { background: var(--panel); border: 1px solid var(--border); border-radius: 16px; padding: 18px 20px; box-shadow: var(--shadow); }
        .stat span { display: block; color: var(--muted); font-size: 13px; text-transform: uppercase; letter-spacing: .08em; }
        .stat strong { display: block; font-size: 33px; margin-top: 8px; letter-spacing: -.03em; color: var(--pms-navy); }

        .table-wrap { overflow-x: auto; border: 1px solid var(--border); border-radius: 10px; background: var(--panel); }
        table { width: 100%; border-collapse: collapse; min-width: 640px; }
        th, td { text-align: left; padding: 12px 14px; border-bottom: 1px solid #e1edf6; vertical-align: middle; }
        th { font-size: 12px; text-transform: uppercase; letter-spacing: .04em; color: #34556f; background: #f4f9fd; border-bottom: 1px solid var(--border); }
        tbody tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: #edf6ff; }
        td .muted, .muted { color: var(--muted); }
        .cell-title { font-weight: 600; }
        .cell-sub { color: var(--muted); font-size: 13px; }

        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 9px; border-radius: 999px;
            font-size: 12px; font-weight: 600; letter-spacing: .02em;
            border: 1px solid transparent; white-space: nowrap;
        }
        .badge.green { color: var(--success); background: rgba(0, 114, 54, .1); border-color: rgba(0, 114, 54, .28); }
        .badge.red { color: var(--danger); background: rgba(185, 28, 28, .08); border-color: rgba(185, 28, 28, .25); }
        .badge.amber { color: #92610a; background: rgba(242, 174, 46, .14); border-color: rgba(242, 174, 46, .45); }
        .badge.blue { color: var(--blue); background: rgba(14, 95, 149, .1); border-color: rgba(14, 95, 149, .28); }
        .badge.slate { color: var(--text); background: var(--surface); border-color: var(--border); }
        .badge.static { color: var(--pms-navy); background: rgba(7, 41, 73, .1); border-color: rgba(7, 41, 73, .3); }

        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 10px 17px; border-radius: 12px; border: 1px solid transparent;
            font-family: inherit; font-size: 14px; font-weight: 700; cursor: pointer;
            transition: background .15s, border-color .15s, color .15s, box-shadow .15s;
            white-space: nowrap;
        }
        .btn-primary { background: var(--primary); color: #ffffff; }
        .btn-primary:hover { background: var(--primary-hover); box-shadow: 0 10px 20px rgba(7, 41, 73, .25); }
        .btn-secondary { background: var(--panel); color: var(--pms-navy); border-color: var(--border); }
        .btn-secondary:hover { border-color: var(--border-strong); background: var(--panel-2); }
        .btn-danger { background: rgba(185, 28, 28, .1); color: var(--danger); border-color: rgba(185, 28, 28, .28); }
        .btn-danger:hover { background: rgba(185, 28, 28, .18); }
        .btn-ghost { background: rgba(7, 41, 73, .08); color: var(--pms-navy); border-color: rgba(7, 41, 73, .14); }
        .btn-ghost:hover { background: rgba(7, 41, 73, .14); border-color: rgba(7, 41, 73, .28); }
        .btn-sm { padding: 6px 11px; font-size: 13px; }
        .btn-block { width: 100%; }

        .toolbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 18px; flex-wrap: wrap; }
        .toolbar form { display: flex; gap: 8px; flex-wrap: wrap; }

        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 6px; }
        .field .hint { font-size: 12px; color: var(--muted); margin-top: 4px; }
        input[type="text"], input[type="email"], input[type="password"], input[type="date"], input[type="search"], select, textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #c9dbeb;
            background: var(--input-bg);
            color: var(--text);
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: rgba(14, 95, 149, .65);
            box-shadow: 0 0 0 3px rgba(14, 95, 149, .15);
        }
        input::placeholder, textarea::placeholder { color: var(--muted); opacity: 1; }
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
        .alert.success { color: var(--success); background: rgba(0, 114, 54, .08); border-color: rgba(0, 114, 54, .28); }
        .alert.error { color: var(--danger); background: rgba(185, 28, 28, .08); border-color: rgba(185, 28, 28, .25); }
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
        .pagination .page.active { background: var(--primary); border-color: var(--primary); color: #ffffff; font-weight: 600; }
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
        [dir="rtl"] .pref-row { text-align: right; }

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
                <span class="brand-copy">
                    <span class="brand-company">خدمات البترول البحرية</span>
                    <span class="brand-sub">{{ __('app.brand_subtitle') }}</span>
                </span>
            </a>

            <nav class="nav">
                @if (auth()->user()->hasPermission('dashboard.view'))
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 13h7V3H3v10ZM14 21h7V11h-7v10ZM14 3h7v4h-7V3ZM3 17h7v4H3v-4Z"></path></svg></span> {{ __('app.nav.dashboard') }}
                    </a>
                @endif

                @if ($canRfq)
                    <a class="nav-link {{ request()->routeIs('rfqs.*') ? 'active' : '' }}" href="{{ route('rfqs.index') }}">
                        <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 3h12a2 2 0 0 1 2 2v14H4V5a2 2 0 0 1 2-2Z"></path><path d="M8 7h8M8 11h8M8 15h5"></path></svg></span> {{ __('app.nav.rfq') }}
                    </a>
                @endif

                @if ($canVendors)
                    <a class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}" href="{{ route('vendors.index') }}">
                        <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 20v-2a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></span> {{ __('app.nav.vendors') }}
                    </a>
                @endif

                @if ($canLocal)
                    <a class="nav-link {{ request()->routeIs('purchasing.local') ? 'active' : '' }}" href="{{ route('purchasing.local') }}">
                        <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 10 12 3l9 7v10H3V10Z"></path><path d="M9 20v-6h6v6"></path></svg></span> {{ __('app.nav.local_purchasing') }}
                    </a>
                @endif

                @if ($canForeign)
                    <a class="nav-link {{ request()->routeIs('purchasing.foreign') ? 'active' : '' }}" href="{{ route('purchasing.foreign') }}">
                        <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"></path></svg></span> {{ __('app.nav.foreign_purchasing') }}
                    </a>
                @endif

                @if ($canMarket)
                    <a class="nav-link {{ request()->routeIs('purchasing.market') ? 'active' : '' }}" href="{{ route('purchasing.market') }}">
                        <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h16v14H4z"></path><path d="M8 9h8M8 13h5"></path></svg></span> {{ __('app.nav.local_market') }}
                    </a>
                @endif

                @if ($canAdministration)
                    <a class="nav-link {{ request()->routeIs('administration.*') || request()->routeIs('markets.*') || request()->routeIs('order_types.*') || request()->routeIs('proposed_methods.*') ? 'active' : '' }}" href="{{ route('administration.index') }}">
                        <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h16v16H4z"></path><path d="M8 8h8M8 12h8M8 16h5"></path></svg></span> {{ __('app.nav.administration') }}
                    </a>
                @endif

                @if ($canUsers || $canRoles || $canAudit)
                    <details class="nav-group" @if ($settingsActive) open @endif>
                        <summary class="nav-link {{ $settingsActive ? 'active' : '' }}">
                            <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3v2M12 19v2M3 12h2M19 12h2M5.6 5.6l1.4 1.4M17 17l1.4 1.4M18.4 5.6 17 7M7 17l-1.4 1.4"></path><circle cx="12" cy="12" r="3.5"></circle></svg></span> {{ __('app.nav.settings') }}
                            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M6 9l6 6 6-6"></path>
                            </svg>
                        </summary>

                        <div class="subnav">
                            @if ($canUsers)
                                <a class="nav-link sub {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                    <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3"></circle><path d="M3 20v-1a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v1M16 5a3 3 0 0 1 0 6M17 15h1a3 3 0 0 1 3 3v1"></path></svg></span> {{ __('app.nav.users') }}
                                </a>
                            @endif

                            @if ($canRoles)
                                <a class="nav-link sub {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                                    <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 20 7v5c0 5-3.4 8-8 9-4.6-1-8-4-8-9V7l8-4Z"></path><path d="m9 12 2 2 4-4"></path></svg></span> {{ __('app.nav.roles') }}
                                </a>
                            @endif

                            @if ($canAudit)
                                <a class="nav-link sub {{ request()->routeIs('audit.*') ? 'active' : '' }}" href="{{ route('audit.index') }}">
                                    <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h16v16H4z"></path><path d="M8 8h8M8 12h5M8 16h8"></path></svg></span> {{ __('app.nav.audit') }}
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
                        <button type="button" class="icon-btn" data-fullscreen-toggle title="{{ __('app.fullscreen.toggle') }}" aria-label="{{ __('app.fullscreen.toggle') }}">
                            <svg data-fullscreen-enter viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M8 3H3v5M16 3h5v5M8 21H3v-5M21 16v5h-5"></path></svg>
                            <svg data-fullscreen-exit viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" style="display:none"><path d="M9 3v6H3M15 3v6h6M9 21v-6H3M21 15h-6v6"></path></svg>
                        </button>
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
    <div class="pwa-install" data-pwa-install hidden role="dialog" aria-modal="false" aria-labelledby="pwa-install-title">
        <div class="pwa-install__icon"><img src="{{ asset('favicon.svg') }}" alt=""></div>
        <div class="pwa-install__content"><strong id="pwa-install-title">{{ __('app.pwa.title') }}</strong><p>{{ __('app.pwa.message') }}</p><small data-pwa-help>{{ __('app.pwa.help') }}</small></div>
        <div class="pwa-install__actions"><button type="button" class="btn btn-primary btn-sm" data-pwa-install-action>{{ __('app.pwa.install') }}</button><button type="button" class="btn btn-ghost btn-sm" data-pwa-dismiss>{{ __('app.pwa.later') }}</button></div>
    </div>
    <style>
        .pwa-install { position:fixed; z-index:100; inset-inline-end:24px; bottom:24px; display:flex; align-items:center; gap:12px; max-width:420px; padding:14px; border:1px solid var(--border-strong); border-radius:14px; background:var(--panel); box-shadow:var(--shadow); }
        .pwa-install[hidden] { display:none; } .pwa-install__icon { width:42px; height:42px; flex:0 0 42px; padding:5px; border-radius:10px; background:#fff; } .pwa-install__icon img { width:100%; height:100%; display:block; } .pwa-install__content { min-width:0; } .pwa-install__content strong { display:block; font-size:14px; } .pwa-install__content p,.pwa-install__content small { display:block; margin:2px 0 0; color:var(--muted); font-size:12px; } .pwa-install__actions { display:flex; gap:6px; flex:0 0 auto; } [dir="rtl"] .pwa-install { text-align:right; }
        [data-fullscreen-toggle] svg { width:16px; height:16px; }
        body.app-expanded { overflow: hidden; }
        body.app-expanded .app-shell { height: 100vh; min-height: 100vh; }
        body.app-expanded .sidebar, body.app-expanded .main { height: 100vh; }
        body.app-expanded .main { overflow: auto; }
        @media (max-width:600px) { .pwa-install { inset-inline:12px; bottom:12px; max-width:none; } .pwa-install__actions { flex-direction:column; } }
    </style>
    <script>
        (function () {
            var fullscreenButton = document.querySelector('[data-fullscreen-toggle]');
            var enterIcon = fullscreenButton ? fullscreenButton.querySelector('[data-fullscreen-enter]') : null;
            var exitIcon = fullscreenButton ? fullscreenButton.querySelector('[data-fullscreen-exit]') : null;
            function expandedMode() { return document.body.classList.contains('app-expanded'); }
            function updateFullscreenIcon() { var active = !!document.fullscreenElement || expandedMode(); if (enterIcon) enterIcon.style.display = active ? 'none' : 'block'; if (exitIcon) exitIcon.style.display = active ? 'block' : 'none'; }
            try { if (localStorage.getItem('ppd-expanded') === '1') document.body.classList.add('app-expanded'); } catch (e) {}
            if (fullscreenButton) { fullscreenButton.addEventListener('click', function () { var active = !expandedMode(); document.body.classList.toggle('app-expanded', active); try { localStorage.setItem('ppd-expanded', active ? '1' : '0'); } catch (e) {} if (active && document.documentElement.requestFullscreen) { var request = document.documentElement.requestFullscreen(); if (request && request.catch) request.catch(function () {}); } else if (!active && document.fullscreenElement) document.exitFullscreen(); updateFullscreenIcon(); }); document.addEventListener('fullscreenchange', updateFullscreenIcon); updateFullscreenIcon(); }

            if ('serviceWorker' in navigator) window.addEventListener('load', function () { navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(function () {}); });

            var installBox = document.querySelector('[data-pwa-install]'), installButton = document.querySelector('[data-pwa-install-action]'), dismissButton = document.querySelector('[data-pwa-dismiss]'), installHelp = document.querySelector('[data-pwa-help]'), deferredPrompt = null;
            var standalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
            function showInstallBox() { if (!standalone && installBox) installBox.hidden = false; }
            window.addEventListener('beforeinstallprompt', function (event) { event.preventDefault(); deferredPrompt = event; showInstallBox(); });
            window.addEventListener('appinstalled', function () { deferredPrompt = null; if (installBox) installBox.hidden = true; });
            if (!standalone) window.setTimeout(showInstallBox, 1200);
            if (dismissButton) dismissButton.addEventListener('click', function () { installBox.hidden = true; });
            if (installButton) installButton.addEventListener('click', function () { if (!deferredPrompt) { if (installHelp) installHelp.textContent = window.isSecureContext ? '{{ __('app.pwa.browser_help') }}' : '{{ __('app.pwa.secure_help') }}'; return; } deferredPrompt.prompt(); deferredPrompt.userChoice.finally(function () { deferredPrompt = null; }); });
        })();
    </script>
</body>
</html>

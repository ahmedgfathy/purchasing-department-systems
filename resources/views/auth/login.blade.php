<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.login.title') }} · {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    @include('partials.theme-init')
    <style>
        :root {
            --bg: #000000;
            --panel: #000000;
            --border: rgba(255, 255, 255, .16);
            --border-strong: rgba(255, 255, 255, .4);
            --text: #ffffff;
            --muted: #ffffff;
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --danger: #f87171;
            --input-bg: #000000;
            --hover: rgba(255, 255, 255, .07);
            --shadow: 0 30px 80px rgba(0, 0, 0, .5);
            --font: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Tahoma, "Noto Naskh Arabic", "Noto Sans Arabic", sans-serif;
        }

        [data-theme="light"] {
            --bg: #ffffff;
            --panel: #ffffff;
            --border: rgba(37, 99, 235, .2);
            --border-strong: rgba(37, 99, 235, .55);
            --text: #000000;
            --muted: #000000;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --danger: #b91c1c;
            --input-bg: #ffffff;
            --hover: rgba(37, 99, 235, .07);
            --shadow: 0 30px 80px rgba(37, 99, 235, .14);
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; height: 100%; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .shell {
            width: 100%;
            max-width: 960px;
            display: grid;
            grid-template-columns: 1fr 420px;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        .intro {
            padding: 48px;
            background:
                radial-gradient(circle at 80% 20%, rgba(99, 102, 241, .22), transparent 55%),
                radial-gradient(circle at 10% 90%, rgba(139, 92, 246, .14), transparent 50%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 40px;
        }
        [data-theme="light"] .intro {
            background:
                radial-gradient(circle at 80% 20%, rgba(37, 99, 235, .14), transparent 55%),
                radial-gradient(circle at 10% 90%, rgba(37, 99, 235, .08), transparent 50%);
        }
        .brand { display: flex; flex-direction: column; align-items: flex-start; gap: 14px; }
        .brand-logo-tile {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 10px 16px; border-radius: 16px;
            background: #ffffff; border: 1px solid var(--border);
            box-shadow: 0 10px 26px rgba(0, 0, 0, .4);
        }
        [data-theme="light"] .brand-logo-tile { box-shadow: 0 10px 26px rgba(37, 99, 235, .16); }
        .brand-logo-tile img { display: block; height: 52px; width: auto; }
        .brand .brand-sub { display: block; font-size: 20px; font-weight: 600; letter-spacing: 0; color: var(--text); margin-top: 0; }
        .intro h1 { font-size: 40px; line-height: 1.15; letter-spacing: -.03em; margin: 0 0 16px; }
        .intro p { color: var(--muted); line-height: 1.7; margin: 0; max-width: 38ch; }
        .features { display: flex; flex-direction: column; gap: 12px; margin-top: 26px; }
        .feature { display: flex; gap: 10px; align-items: center; color: var(--text); font-size: 14px; }
        .feature .tick {
            width: 20px; height: 20px; border-radius: 50%;
            display: grid; place-items: center;
            background: rgba(52, 211, 153, .12);
            color: #34d399; font-size: 12px; flex: 0 0 auto;
        }
        [data-theme="light"] .feature .tick { color: #000000; background: rgba(37, 99, 235, .1); }
        .form-side { padding: 48px 44px; display: flex; flex-direction: column; justify-content: center; }
        .form-side h2 { margin: 0 0 6px; font-size: 26px; letter-spacing: -.02em; }
        .form-side .sub { margin: 0 0 28px; color: var(--muted); font-size: 14px; }
        .field { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 7px; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 11px 13px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--input-bg);
            color: var(--text);
            font-family: inherit;
            font-size: 15px;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .field .hint { font-size: 12px; color: var(--muted); margin-top: 5px; }
        input:focus {
            border-color: rgba(99, 102, 241, .7);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .16);
        }
        input::placeholder { color: var(--text); opacity: 1; }
        .row { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 22px; }
        .remember { display: flex; align-items: center; gap: 8px; color: var(--muted); font-size: 13px; cursor: pointer; }
        .remember input { accent-color: var(--primary); }
        .btn {
            width: 100%;
            padding: 12px 16px;
            border: 0;
            border-radius: 10px;
            background: var(--primary);
            color: #ffffff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
        }
        .btn:hover { background: var(--primary-hover); }
        .error {
            padding: 11px 14px;
            border-radius: 9px;
            background: rgba(248, 113, 113, .08);
            border: 1px solid rgba(248, 113, 113, .28);
            color: var(--danger);
            font-size: 13px;
            margin-bottom: 18px;
        }
        [data-theme="light"] .error { color: #000000; }
        .error ul { margin: 6px 0 0; padding-left: 16px; }
        .footer-note { margin-top: 26px; text-align: center; color: var(--muted); font-size: 12px; }
        .login-tools {
            position: fixed;
            top: 24px;
            inset-inline-end: 24px;
            display: flex;
            gap: 8px;
            align-items: center;
            z-index: 10;
        }
        .icon-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            height: 38px; padding: 0 12px;
            border: 1px solid var(--border); border-radius: 9px;
            background: var(--panel); color: var(--text);
            font-family: inherit; font-size: 13px; font-weight: 700;
            cursor: pointer; transition: background .15s, border-color .15s;
        }
        .icon-btn:hover { background: var(--hover); border-color: var(--border-strong); }
        .icon-btn svg { width: 16px; height: 16px; }
        .theme-toggle .icon-sun { display: none; }
        [data-theme="light"] .theme-toggle .icon-sun { display: block; }
        [data-theme="light"] .theme-toggle .icon-moon { display: none; }

        [dir="rtl"] input[type="text"], [dir="rtl"] input[type="email"], [dir="rtl"] input[type="password"] { text-align: right; }
        [dir="rtl"] .error ul { padding-left: 0; padding-right: 16px; }

        @media (max-width: 780px) {
            .shell { grid-template-columns: 1fr; max-width: 440px; }
            .intro { display: none; }
            .form-side { padding: 36px 28px; }
            .login-tools { top: 16px; inset-inline-end: 16px; }
        }
    </style>
</head>
<body>
    <div class="login-tools">
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

    <div class="shell">
        <section class="intro">
            <div class="brand">
                <span class="brand-logo-tile">
                    <img src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }} logo">
                </span>
                <span class="brand-sub">{{ __('app.login.brand_subtitle') }}</span>
            </div>

            <div>
                <h1>{{ __('app.login.hero_line_1') }}<br>{{ __('app.login.hero_line_2') }}</h1>
                <p>{{ __('app.login.hero_text') }}</p>

                <div class="features">
                    <div class="feature"><span class="tick">&#10003;</span> {{ __('app.login.feature_permissions') }}</div>
                    <div class="feature"><span class="tick">&#10003;</span> {{ __('app.login.feature_audit') }}</div>
                    <div class="feature"><span class="tick">&#10003;</span> {{ __('app.login.feature_login') }}</div>
                </div>
            </div>

            <div style="color:var(--muted);font-size:11px;">{{ __('app.login.powered_by') }}</div>
        </section>

        <section class="form-side">
            <h2>{{ __('app.login.welcome') }}</h2>
            <p class="sub">{{ __('app.login.subtitle') }}</p>

            @if ($errors->any())
                <div class="error">
                    <strong>{{ __('app.login.unable') }}</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="field">
                    <label for="employee_id">{{ __('app.login.employee_id') }}</label>
                    <input
                        id="employee_id"
                        type="text"
                        name="employee_id"
                        value="{{ old('employee_id') }}"
                        inputmode="numeric"
                        autocomplete="username"
                        autofocus
                        required
                    >
                    <div class="hint">{{ __('app.login.employee_id_hint') }}</div>
                </div>

                <div class="field">
                    <label for="password">{{ __('app.login.password') }}</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <div class="row">
                    <label class="remember">
                        <input type="checkbox" name="remember" value="1">
                        {{ __('app.login.remember') }}
                    </label>
                </div>

                <button type="submit" class="btn">{{ __('app.login.submit') }}</button>
            </form>

            <div class="footer-note">{{ __('app.login.monitored') }}</div>
        </section>
    </div>
</body>
</html>

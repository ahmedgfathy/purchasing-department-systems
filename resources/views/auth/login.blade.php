<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#072949">
    <title>{{ __('app.login.title') }} · {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=Noto+Sans+Arabic:wght@400;500;700&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --pms-navy: #072949;
            --pms-sea: #0e5f95;
            --pms-gold: #f2ae2e;
            --pms-green: #007236;

            --bg: #f5f9ff;
            --panel: #ffffff;
            --border: #d6e2ef;
            --border-strong: #b9cee2;
            --text: #0f2035;
            --muted: #5a6d82;
            --primary: #072949;
            --primary-hover: #0b396a;
            --danger: #b91c1c;
            --input-bg: #ffffff;
            --hover: #edf6ff;
            --shadow: 0 16px 34px rgba(4, 25, 46, .10);
            --font: "Roboto", "Segoe UI", Tahoma, Arial, ui-sans-serif, system-ui, "Noto Sans Arabic", sans-serif;
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; height: 100%; }
        body {
            background:
                radial-gradient(at 18% 8%, rgba(242, 174, 46, .25) 0%, transparent 50%),
                radial-gradient(at 82% 12%, rgba(0, 114, 54, .16) 0%, transparent 45%),
                radial-gradient(at 50% 92%, rgba(88, 148, 172, .22) 0%, transparent 40%),
                linear-gradient(#f5f9ff 0%, #f0f6fe 30%, #eaf2fa 70%, #eef5fc 100%);
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
            border-radius: 22px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        .intro {
            padding: 48px;
            background:
                radial-gradient(circle at 80% 20%, rgba(242, 174, 46, .18), transparent 55%),
                radial-gradient(circle at 10% 90%, rgba(14, 95, 149, .12), transparent 50%),
                linear-gradient(135deg, #f0f7ff 0%, #e8f0fa 60%, #f5f0e8 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 40px;
        }
        .brand { display: flex; flex-direction: column; align-items: flex-start; gap: 14px; }
        .brand-logo-tile {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 10px 16px; border-radius: 16px;
            background: #ffffff; border: 1px solid var(--border);
            box-shadow: 0 10px 26px rgba(4, 25, 46, .12);
        }
        .brand-logo-tile img { display: block; height: 52px; width: auto; }
        .brand .brand-sub { display: block; font-size: 20px; font-weight: 600; letter-spacing: 0; color: var(--pms-navy); margin-top: 0; }
        .intro h1 { font-size: 40px; line-height: 1.15; letter-spacing: -.03em; margin: 0 0 16px; color: var(--pms-navy); }
        .intro p { color: var(--muted); line-height: 1.7; margin: 0; max-width: 38ch; }
        .features { display: flex; flex-direction: column; gap: 12px; margin-top: 26px; }
        .feature { display: flex; gap: 10px; align-items: center; color: var(--text); font-size: 14px; }
        .feature .tick {
            width: 20px; height: 20px; border-radius: 50%;
            display: grid; place-items: center;
            background: rgba(0, 114, 54, .12);
            color: var(--pms-green); font-size: 12px; flex: 0 0 auto;
        }
        .form-side { padding: 48px 44px; display: flex; flex-direction: column; justify-content: center; }
        .form-side h2 { margin: 0 0 6px; font-size: 26px; letter-spacing: -.02em; color: var(--pms-navy); }
        .form-side .sub { margin: 0 0 28px; color: var(--muted); font-size: 14px; }
        .field { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 7px; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 11px 13px;
            border-radius: 10px;
            border: 1px solid #c9dbeb;
            background: var(--input-bg);
            color: var(--text);
            font-family: inherit;
            font-size: 15px;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .field .hint { font-size: 12px; color: var(--muted); margin-top: 5px; }
        input:focus {
            border-color: rgba(14, 95, 149, .65);
            box-shadow: 0 0 0 3px rgba(14, 95, 149, .15);
        }
        input::placeholder { color: var(--muted); opacity: 1; }
        .row { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 22px; }
        .remember { display: flex; align-items: center; gap: 8px; color: var(--muted); font-size: 13px; cursor: pointer; font-weight: 400; }
        .remember input { accent-color: var(--primary); }
        .btn {
            width: 100%;
            padding: 13px 16px;
            border: 0;
            border-radius: 12px;
            background: var(--primary);
            color: #ffffff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s, box-shadow .15s;
        }
        .btn:hover { background: var(--primary-hover); box-shadow: 0 10px 20px rgba(7, 41, 73, .25); }
        .error {
            padding: 11px 14px;
            border-radius: 10px;
            background: rgba(185, 28, 28, .06);
            border: 1px solid rgba(185, 28, 28, .25);
            color: var(--danger);
            font-size: 13px;
            margin-bottom: 18px;
        }
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
            height: 40px; padding: 0 13px;
            border: 1px solid rgba(7, 41, 73, .2); border-radius: 12px;
            background: rgba(255, 255, 255, .85); color: var(--pms-navy);
            font-family: inherit; font-size: 13px; font-weight: 700;
            cursor: pointer; transition: background .15s, border-color .15s;
            text-decoration: none;
        }
        .icon-btn:hover { background: rgba(7, 41, 73, .1); border-color: rgba(7, 41, 73, .33); }
        .icon-btn svg { width: 16px; height: 16px; }

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

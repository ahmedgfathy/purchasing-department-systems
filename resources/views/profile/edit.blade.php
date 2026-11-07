@extends('layouts.app')

@section('title', __('app.profile.title'))
@section('heading', __('app.profile.heading'))
@section('subheading', __('app.profile.subheading'))

@section('content')
    @php
        $initials = \Illuminate\Support\Str::of($user->name)
            ->explode(' ')
            ->filter()
            ->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))
            ->take(2)
            ->implode('');
    @endphp

    <div class="card">
        <div class="profile-hero">
            <div class="avatar avatar-xl">
                @if ($user->avatarUrl())
                    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}">
                @else
                    {{ $initials }}
                @endif
            </div>

            <div style="min-width:0;flex:1">
                <strong>{{ $user->name }}</strong>
                <div class="meta">
                    <span class="badge static">{{ __('app.users.employee_id_short', ['id' => $user->employee_id]) }}</span>
                    @foreach ($user->roles as $role)
                        <span class="badge slate">{{ $role->displayName() }}</span>
                    @endforeach
                    @if ($user->job_title)
                        <span>{{ $user->job_title }}</span>
                    @endif
                </div>
            </div>

            @if ($user->avatarUrl())
                <form method="POST" action="{{ route('profile.avatar.destroy') }}" onsubmit="return confirm(@js(__('app.profile.photo_remove_confirm')))">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">{{ __('app.profile.photo_remove') }}</button>
                </form>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>{{ __('app.profile.personal') }}</h2>
                <p>{{ __('app.profile.personal_sub') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="field">
                    <label for="name">{{ __('app.profile.name') }}</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="employee_id">{{ __('app.profile.employee_id') }}</label>
                    <input id="employee_id" type="text" value="{{ $user->employee_id }}" disabled>
                    <div class="hint">{{ __('app.profile.employee_id_hint') }}</div>
                </div>

                <div class="field">
                    <label for="email">{{ __('app.profile.email') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}">
                    @error('email') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="phone">{{ __('app.profile.phone') }}</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                    @error('phone') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="job_title">{{ __('app.profile.job_title') }}</label>
                    <input id="job_title" type="text" name="job_title" value="{{ old('job_title', $user->job_title) }}">
                    @error('job_title') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="avatar">{{ __('app.profile.photo') }}</label>
                    <input id="avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp">
                    <div class="hint">{{ __('app.profile.photo_hint') }}</div>
                    @error('avatar') <div class="error-text">{{ $message }}</div> @enderror
                </div>
            </div>

            <div style="display:flex;gap:8px;margin-top:8px">
                <button type="submit" class="btn btn-primary">{{ __('app.profile.save') }}</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>{{ __('app.profile.preferences') }}</h2>
                <p>{{ __('app.profile.preferences_sub') }}</p>
            </div>
        </div>

        <div class="pref-row">
            <div>
                <strong>{{ __('app.profile.theme') }}</strong>
                <p>{{ __('app.profile.theme_hint') }}</p>
            </div>
            <div class="segmented" role="group" aria-label="{{ __('app.profile.theme') }}">
                <button type="button" class="seg" data-set-theme="dark">{{ __('app.profile.dark') }}</button>
                <button type="button" class="seg" data-set-theme="light">{{ __('app.profile.light') }}</button>
            </div>
        </div>

        <div class="pref-row">
            <div>
                <strong>{{ __('app.profile.language') }}</strong>
                <p>{{ __('app.profile.language_hint') }}</p>
            </div>
            <div class="segmented" role="group" aria-label="{{ __('app.profile.language') }}">
                <a class="seg {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">English</a>
                <a class="seg {{ app()->getLocale() === 'ar' ? 'active' : '' }}" href="{{ route('language.switch', 'ar') }}">العربية</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>{{ __('app.profile.security') }}</h2>
                <p>{{ __('app.profile.security_sub') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="field full">
                    <label for="current_password">{{ __('app.profile.current_password') }}</label>
                    <input id="current_password" type="password" name="current_password" autocomplete="current-password" required>
                    @error('current_password') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="password">{{ __('app.profile.new_password') }}</label>
                    <input id="password" type="password" name="password" autocomplete="new-password" required>
                    @error('password') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">{{ __('app.profile.confirm_password') }}</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
                </div>
            </div>

            <div style="display:flex;gap:8px;margin-top:8px">
                <button type="submit" class="btn btn-primary">{{ __('app.profile.update_password') }}</button>
            </div>
        </form>
    </div>

    <script>
        (function () {
            var buttons = document.querySelectorAll('[data-set-theme]');
            var root = document.documentElement;

            function paint(theme) {
                buttons.forEach(function (button) {
                    var active = button.dataset.setTheme === theme;
                    button.classList.toggle('active', active);
                    button.setAttribute('aria-pressed', active ? 'true' : 'false');
                });
            }

            function apply(theme) {
                if (theme === 'light') {
                    root.setAttribute('data-theme', 'light');
                } else {
                    root.removeAttribute('data-theme');
                }

                try {
                    localStorage.setItem('ppd-theme', theme);
                } catch (e) {
                    // Ignore storage errors.
                }

                paint(theme);
            }

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    apply(button.dataset.setTheme);
                });
            });

            paint(root.getAttribute('data-theme') === 'light' ? 'light' : 'dark');
        })();
    </script>
@endsection

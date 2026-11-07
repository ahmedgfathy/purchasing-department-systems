@php
    $selectedRoles = array_map('intval', old('roles', $user ? $user->roles->pluck('id')->all() : []));
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="form-grid">
        <div class="field">
            <label for="name">{{ __('app.users.full_name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required>
            @error('name') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="employee_id">{{ __('app.users.employee_id') }}</label>
            <input id="employee_id" type="text" name="employee_id" value="{{ old('employee_id', $user->employee_id ?? '') }}" inputmode="numeric" required>
            <div class="hint">{{ __('app.users.employee_id_hint') }}</div>
            @error('employee_id') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="email">{{ __('app.users.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email ?? '') }}">
            @error('email') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="phone">{{ __('app.users.phone') }}</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}">
            @error('phone') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="job_title">{{ __('app.users.job_title') }}</label>
            <input id="job_title" type="text" name="job_title" value="{{ old('job_title', $user->job_title ?? '') }}">
            @error('job_title') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password">{{ $user ? __('app.users.password_keep') : __('app.users.password') }}</label>
            <input id="password" type="password" name="password" autocomplete="new-password" {{ $user ? '' : 'required' }}>
            @error('password') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">{{ __('app.users.password_confirmation') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" {{ $user ? '' : 'required' }}>
        </div>

        <div class="field full">
            <label>{{ __('app.users.roles') }}</label>
            <div class="permission-grid">
                @foreach ($roles as $role)
                    <label class="check">
                        <input
                            type="checkbox"
                            name="roles[]"
                            value="{{ $role->id }}"
                            @checked(in_array($role->id, $selectedRoles, true))
                        >
                        <span>
                            <strong>{{ $role->displayName() }}</strong>
                            <span>{{ $role->displayDescription() ?: $role->slug }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            @error('roles') <div class="error-text">{{ $message }}</div> @enderror
        </div>
    </div>

    <div style="display:flex;gap:8px;margin-top:8px">
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        <a href="{{ route('users.index') }}" class="btn btn-ghost">{{ __('app.users.cancel') }}</a>
    </div>
</form>

@php
    $selectedPermissions = array_map('intval', old('permissions', $role ? $role->permissions->pluck('id')->all() : []));
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="form-grid">
        <div class="field">
            <label for="name">{{ __('app.roles.name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name', $role->name ?? '') }}" required>
            @error('name') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="slug">{{ __('app.roles.slug') }}</label>
            <input id="slug" type="text" name="slug" value="{{ old('slug', $role->slug ?? '') }}" placeholder="{{ __('app.roles.slug_placeholder') }}" {{ $role && $role->is_system ? 'disabled' : '' }}>
            <div class="hint">{{ __('app.roles.slug_hint') }}</div>
            @error('slug') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field full">
            <label for="description">{{ __('app.roles.description') }}</label>
            <input id="description" type="text" name="description" value="{{ old('description', $role->description ?? '') }}">
            @error('description') <div class="error-text">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="field">
        <label>{{ __('app.roles.permissions') }}</label>
        <div class="permission-grid">
            @foreach ($permissions as $group => $items)
                <div class="permission-group">
                    <h3>{{ $group }}</h3>
                    @foreach ($items as $permission)
                        <label class="check">
                            <input
                                type="checkbox"
                                name="permissions[]"
                                value="{{ $permission->id }}"
                                @checked(in_array($permission->id, $selectedPermissions, true))
                            >
                            <span>
                                <strong>{{ $permission->displayName() }}</strong>
                                <span class="mono">{{ $permission->slug }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            @endforeach
        </div>
        @error('permissions') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div style="display:flex;gap:8px">
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        <a href="{{ route('roles.index') }}" class="btn btn-ghost">{{ __('app.roles.cancel') }}</a>
    </div>
</form>

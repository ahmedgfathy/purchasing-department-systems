@php
    $vendor = $vendor ?? null;
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="form-grid">
        <div class="field full">
            <label for="name">{{ __('app.vendors.name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name', $vendor->name ?? '') }}" required>
            @error('name') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="code">{{ __('app.vendors.code') }}</label>
            <input id="code" type="text" name="code" value="{{ old('code', $vendor->code ?? '') }}" placeholder="{{ __('app.vendors.code_placeholder') }}">
            <div class="hint">{{ __('app.vendors.code_hint') }}</div>
            @error('code') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="type">{{ __('app.vendors.type') }}</label>
            <select id="type" name="type" required>
                @foreach (\App\Models\Vendor::types() as $value => $label)
                    <option value="{{ $value }}" @selected(old('type', $vendor->type ?? 'local') === $value)>{{ __('app.vendors.types.'.$value) }}</option>
                @endforeach
            </select>
            @error('type') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="contact_name">{{ __('app.vendors.contact_name') }}</label>
            <input id="contact_name" type="text" name="contact_name" value="{{ old('contact_name', $vendor->contact_name ?? '') }}">
            @error('contact_name') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="email">{{ __('app.vendors.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email', $vendor->email ?? '') }}">
            @error('email') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="phone">{{ __('app.vendors.phone') }}</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone', $vendor->phone ?? '') }}">
            @error('phone') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="country">{{ __('app.vendors.country') }}</label>
            <input id="country" type="text" name="country" value="{{ old('country', $vendor->country ?? '') }}">
            @error('country') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="category">{{ __('app.vendors.category') }}</label>
            <input id="category" type="text" name="category" value="{{ old('category', $vendor->category ?? '') }}">
            @error('category') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="status">{{ __('app.vendors.status') }}</label>
            <select id="status" name="status" required>
                @foreach (\App\Models\Vendor::statuses() as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $vendor->status ?? 'active') === $value)>{{ __('app.vendors.statuses.'.$value) }}</option>
                @endforeach
            </select>
            @error('status') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field full">
            <label for="address">{{ __('app.vendors.address') }}</label>
            <input id="address" type="text" name="address" value="{{ old('address', $vendor->address ?? '') }}">
            @error('address') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field full">
            <label for="notes">{{ __('app.vendors.notes') }}</label>
            <textarea id="notes" name="notes" rows="3">{{ old('notes', $vendor->notes ?? '') }}</textarea>
            @error('notes') <div class="error-text">{{ $message }}</div> @enderror
        </div>
    </div>

    <div style="display:flex;gap:8px;margin-top:8px">
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        <a href="{{ route('vendors.index') }}" class="btn btn-ghost">{{ __('app.vendors.cancel') }}</a>
    </div>
</form>

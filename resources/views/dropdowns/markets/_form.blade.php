@php
    $market = $market ?? null;
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="form-grid">
        <div class="field">
            <label for="code">{{ __('app.markets.code') }}</label>
            <input id="code" type="text" name="code" value="{{ old('code', $market->code ?? '') }}" placeholder="{{ __('app.markets.code_placeholder') }}" required>
            <div class="hint">{{ __('app.markets.code_hint') }}</div>
            @error('code') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field full">
            <label for="name">{{ __('app.markets.name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name', $market->name ?? '') }}" required>
            @error('name') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="status">{{ __('app.markets.status') }}</label>
            <select id="status" name="status" required>
                @foreach (\App\Models\Market::statuses() as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $market->status ?? 'active') === $value)>{{ __('app.markets.statuses.'.$value) }}</option>
                @endforeach
            </select>
            @error('status') <div class="error-text">{{ $message }}</div> @enderror
        </div>
    </div>

    <div style="display:flex;gap:8px;margin-top:8px">
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        <a href="{{ route('markets.index') }}" class="btn btn-ghost">{{ __('app.markets.cancel') }}</a>
    </div>
</form>
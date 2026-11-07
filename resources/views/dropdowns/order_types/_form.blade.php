@php
    $orderType = $orderType ?? null;
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="form-grid">
        <div class="field">
            <label for="code">{{ __('app.order_types.code') }}</label>
            <input id="code" type="text" name="code" value="{{ old('code', $orderType->code ?? '') }}" placeholder="{{ __('app.order_types.code_placeholder') }}" required>
            <div class="hint">{{ __('app.order_types.code_hint') }}</div>
            @error('code') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field full">
            <label for="name">{{ __('app.order_types.name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name', $orderType->name ?? '') }}" required>
            @error('name') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="status">{{ __('app.order_types.status') }}</label>
            <select id="status" name="status" required>
                @foreach (\App\Models\OrderType::statuses() as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $orderType->status ?? 'active') === $value)>{{ __('app.order_types.statuses.'.$value) }}</option>
                @endforeach
            </select>
            @error('status') <div class="error-text">{{ $message }}</div> @enderror
        </div>
    </div>

    <div style="display:flex;gap:8px;margin-top:8px">
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        <a href="{{ route('order_types.index') }}" class="btn btn-ghost">{{ __('app.order_types.cancel') }}</a>
    </div>
</form>
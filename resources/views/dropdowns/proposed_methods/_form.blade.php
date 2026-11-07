@php
    $proposedMethod = $proposedMethod ?? null;
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="form-grid">
        <div class="field">
            <label for="code">{{ __('app.proposed_methods.code') }}</label>
            <input id="code" type="text" name="code" value="{{ old('code', $proposedMethod->code ?? '') }}" placeholder="{{ __('app.proposed_methods.code_placeholder') }}" required>
            <div class="hint">{{ __('app.proposed_methods.code_hint') }}</div>
            @error('code') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field full">
            <label for="name">{{ __('app.proposed_methods.name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name', $proposedMethod->name ?? '') }}" required>
            @error('name') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="status">{{ __('app.proposed_methods.status') }}</label>
            <select id="status" name="status" required>
                @foreach (\App\Models\ProposedMethod::statuses() as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $proposedMethod->status ?? 'active') === $value)>{{ __('app.proposed_methods.statuses.'.$value) }}</option>
                @endforeach
            </select>
            @error('status') <div class="error-text">{{ $message }}</div> @enderror
        </div>
    </div>

    <div style="display:flex;gap:8px;margin-top:8px">
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        <a href="{{ route('proposed_methods.index') }}" class="btn btn-ghost">{{ __('app.proposed_methods.cancel') }}</a>
    </div>
</form>
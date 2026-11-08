@extends('layouts.app')

@section('title', $item ? __('app.administration.edit_title') : __('app.administration.create_title'))
@section('heading', $item ? __('app.administration.edit_title') : __('app.administration.create_title'))
@section('subheading', $definition['title'])

@section('actions')<a href="{{ route('administration.lookup.index', $type) }}" class="btn btn-ghost">{{ __('app.administration.back') }}</a>@endsection

@section('content')
    <div class="card"><form method="POST" action="{{ $item ? route('administration.lookup.update', [$type, $item->id]) : route('administration.lookup.store', $type) }}">@csrf @if ($item) @method('PUT') @endif
        <div class="form-grid"><div class="field"><label for="code">{{ __('app.administration.code') }}</label><input id="code" name="code" value="{{ old('code', $item?->code) }}" required maxlength="40"><div class="hint">{{ __('app.administration.code_hint') }}</div>@error('code')<div class="error-text">{{ $message }}</div>@enderror</div><div class="field"><label for="name">{{ __('app.administration.name') }}</label><input id="name" name="name" value="{{ old('name', $item?->name) }}" required maxlength="120">@error('name')<div class="error-text">{{ $message }}</div>@enderror</div><div class="field"><label for="status">{{ __('app.administration.status') }}</label><select id="status" name="status" required>@foreach (\App\Models\RfqLookup::statuses() as $value => $label)<option value="{{ $value }}" @selected(old('status', $item?->status ?? 'active') === $value)>{{ __('app.administration.statuses.'.$value) }}</option>@endforeach</select>@error('status')<div class="error-text">{{ $message }}</div>@enderror</div></div>
        <div style="display:flex;gap:8px;margin-top:8px"><button class="btn btn-primary">{{ $item ? __('app.administration.save') : __('app.administration.create_submit') }}</button><a href="{{ route('administration.lookup.index', $type) }}" class="btn btn-ghost">{{ __('app.administration.cancel') }}</a></div>
    </form></div>
@endsection

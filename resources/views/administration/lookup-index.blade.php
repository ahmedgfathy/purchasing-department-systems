@extends('layouts.app')

@section('title', $definition['title'])
@section('heading', $definition['title'])
@section('subheading', __('app.administration.lookup_subheading', ['name' => $definition['title']]))

@section('actions')
    <a href="{{ route('administration.index') }}" class="btn btn-ghost">{{ __('app.administration.back') }}</a>
    @if (auth()->user()->hasPermission($definition['permission'].'.manage'))<a href="{{ route('administration.lookup.create', $type) }}" class="btn btn-primary">{{ __('app.administration.new') }}</a>@endif
@endsection

@section('content')
    <div class="toolbar"><form method="GET" action="{{ route('administration.lookup.index', $type) }}"><input type="search" name="q" value="{{ $search }}" placeholder="{{ __('app.administration.search_placeholder') }}" style="min-width:260px"><button class="btn btn-secondary">{{ __('app.administration.search') }}</button>@if ($search !== '')<a href="{{ route('administration.lookup.index', $type) }}" class="btn btn-ghost">{{ __('app.administration.clear') }}</a>@endif</form></div>
    @if ($items->isEmpty())<div class="empty">{{ __('app.administration.empty') }}</div>@else
        <div class="table-wrap"><table><thead><tr><th>{{ __('app.administration.code') }}</th><th>{{ __('app.administration.name') }}</th><th>{{ __('app.administration.status') }}</th><th>{{ __('app.administration.created_by') }}</th>@if (auth()->user()->hasPermission($definition['permission'].'.manage'))<th></th>@endif</tr></thead><tbody>
            @foreach ($items as $item)<tr><td class="mono">{{ $item->code }}</td><td class="cell-title">{{ $item->name }}</td><td><span class="badge {{ $item->status === 'active' ? 'green' : 'slate' }}">{{ $item->statusLabel() }}</span></td><td class="cell-sub">{{ $item->creator?->name ?? '—' }}</td>@if (auth()->user()->hasPermission($definition['permission'].'.manage'))<td><div style="display:flex;gap:6px"><a href="{{ route('administration.lookup.edit', [$type, $item->id]) }}" class="btn btn-secondary btn-sm">{{ __('app.administration.edit') }}</a><form method="POST" action="{{ route('administration.lookup.destroy', [$type, $item->id]) }}" onsubmit="return confirm('{{ __('app.administration.delete_confirm') }}')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">{{ __('app.administration.delete') }}</button></form></div></td>@endif</tr>@endforeach
        </tbody></table></div>
    @endif
    @include('partials.pagination', ['paginator' => $items])
@endsection

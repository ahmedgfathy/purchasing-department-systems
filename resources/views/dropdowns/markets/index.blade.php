@extends('layouts.app')

@section('title', __('app.markets.title'))
@section('heading', __('app.markets.heading'))
@section('subheading', __('app.markets.subheading'))

@section('actions')
    @if (auth()->user()->hasPermission('markets.manage'))
        <a href="{{ route('markets.create') }}" class="btn btn-primary">{{ __('app.markets.new') }}</a>
    @endif
@endsection

@section('content')
    <div class="toolbar">
        <form method="GET" action="{{ route('markets.index') }}">
            <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('app.markets.search_placeholder') }}" style="min-width:260px">
            <button type="submit" class="btn btn-secondary">{{ __('app.markets.search') }}</button>
            @if ($search !== '')
                <a href="{{ route('markets.index') }}" class="btn btn-ghost">{{ __('app.markets.clear') }}</a>
            @endif
        </form>
    </div>

    @if ($markets->isEmpty())
        <div class="empty">{{ __('app.markets.empty') }}</div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('app.markets.code') }}</th>
                        <th>{{ __('app.markets.name') }}</th>
                        <th>{{ __('app.markets.status') }}</th>
                        <th>{{ __('app.markets.created_by') }}</th>
                        @if (auth()->user()->hasPermission('markets.manage'))
                            <th style="width:1%"></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($markets as $market)
                        <tr>
                            <td>
                                <div class="cell-title">{{ $market->code }}</div>
                            </td>
                            <td>
                                <div class="cell-title">{{ $market->name }}</div>
                            </td>
                            <td><span class="badge {{ $market->status === \App\Models\Market::STATUS_ACTIVE ? 'green' : 'slate' }}">{{ $market->statusLabel() }}</span></td>
                            <td class="cell-sub">{{ $market->creator?->name ?? '—' }}</td>
                            @if (auth()->user()->hasPermission('markets.manage'))
                                <td>
                                    <div style="display:flex;gap:6px;align-items:center">
                                        <a href="{{ route('markets.edit', $market) }}" class="btn btn-secondary btn-sm">{{ __('app.markets.edit') }}</a>
                                        <form method="POST" action="{{ route('markets.destroy', $market) }}" onsubmit="return confirm('{{ __('app.markets.delete_confirm') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">{{ __('app.markets.delete') }}</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @include('partials.pagination', ['paginator' => $markets])
@endsection
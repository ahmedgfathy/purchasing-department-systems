@extends('layouts.app')

@section('title', __('app.order_types.title'))
@section('heading', __('app.order_types.heading'))
@section('subheading', __('app.order_types.subheading'))

@section('actions')
    @if (auth()->user()->hasPermission('order_types.manage'))
        <a href="{{ route('order_types.create') }}" class="btn btn-primary">{{ __('app.order_types.new') }}</a>
    @endif
@endsection

@section('content')
    <div class="toolbar">
        <form method="GET" action="{{ route('order_types.index') }}">
            <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('app.order_types.search_placeholder') }}" style="min-width:260px">
            <button type="submit" class="btn btn-secondary">{{ __('app.order_types.search') }}</button>
            @if ($search !== '')
                <a href="{{ route('order_types.index') }}" class="btn btn-ghost">{{ __('app.order_types.clear') }}</a>
            @endif
        </form>
    </div>

    @if ($orderTypes->isEmpty())
        <div class="empty">{{ __('app.order_types.empty') }}</div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('app.order_types.code') }}</th>
                        <th>{{ __('app.order_types.name') }}</th>
                        <th>{{ __('app.order_types.status') }}</th>
                        <th>{{ __('app.order_types.created_by') }}</th>
                        @if (auth()->user()->hasPermission('order_types.manage'))
                            <th style="width:1%"></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orderTypes as $orderType)
                        <tr>
                            <td>
                                <div class="cell-title">{{ $orderType->code }}</div>
                            </td>
                            <td>
                                <div class="cell-title">{{ $orderType->name }}</div>
                            </td>
                            <td><span class="badge {{ $orderType->status === \App\Models\OrderType::STATUS_ACTIVE ? 'green' : 'slate' }}">{{ $orderType->statusLabel() }}</span></td>
                            <td class="cell-sub">{{ $orderType->creator?->name ?? '—' }}</td>
                            @if (auth()->user()->hasPermission('order_types.manage'))
                                <td>
                                    <div style="display:flex;gap:6px;align-items:center">
                                        <a href="{{ route('order_types.edit', $orderType) }}" class="btn btn-secondary btn-sm">{{ __('app.order_types.edit') }}</a>
                                        <form method="POST" action="{{ route('order_types.destroy', $orderType) }}" onsubmit="return confirm('{{ __('app.order_types.delete_confirm') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">{{ __('app.order_types.delete') }}</button>
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

    @include('partials.pagination', ['paginator' => $orderTypes])
@endsection
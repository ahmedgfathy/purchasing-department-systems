@extends('layouts.app')

@section('title', __('app.rfq.title'))
@section('heading', __('app.rfq.heading'))
@section('subheading', __('app.rfq.subheading'))

@section('actions')
    @if (auth()->user()->hasPermission('rfq.create'))
        <a href="{{ route('rfqs.create') }}" class="btn btn-primary">{{ __('app.rfq.new') }}</a>
    @endif
@endsection

@section('content')
    <div class="toolbar">
        <form method="GET" action="{{ route('rfqs.index') }}">
            <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('app.rfq.search_placeholder') }}" style="min-width:260px">
            <select name="status" style="min-width:180px">
                <option value="">{{ __('app.rfq.all_statuses') }}</option>
                @foreach (\App\Models\Rfq::statuses() as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ __('app.rfq.statuses.'.$value) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary">{{ __('app.rfq.filter') }}</button>
            @if ($search !== '' || $status !== '')
                <a href="{{ route('rfqs.index') }}" class="btn btn-ghost">{{ __('app.rfq.clear') }}</a>
            @endif
        </form>
    </div>

    <div class="card" style="padding:0;overflow:hidden">
        @if ($rfqs->isEmpty())
            <div class="empty">{{ __('app.rfq.empty') }}</div>
        @else
            <div class="table-wrap" style="border:0;border-radius:0">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('app.rfq.col_reference') }}</th>
                            <th>{{ __('app.rfq.col_title') }}</th>
                            <th>{{ __('app.rfq.col_requested_by') }}</th>
                            <th>{{ __('app.rfq.col_needed_by') }}</th>
                            <th>{{ __('app.rfq.col_status') }}</th>
                            <th>{{ __('app.rfq.col_vendors') }}</th>
                            <th style="width:1%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rfqs as $rfq)
                            <tr>
                                <td>
                                    <div class="cell-title mono">{{ $rfq->reference }}</div>
                                    <div class="cell-sub">{{ $rfq->created_at->translatedFormat(__('app.date.short')) }}</div>
                                </td>
                                <td>
                                    <div class="cell-title">{{ $rfq->title }}</div>
                                    <div class="cell-sub">
                                        {{ trans_choice('app.rfq.items_count', $rfq->items_count, ['count' => $rfq->items_count]) }}
                                        · <span class="badge {{ $rfq->priorityColor() }}">{{ $rfq->priorityLabel() }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="cell-title">{{ $rfq->requestedBy?->name ?? '—' }}</div>
                                    <div class="cell-sub">{{ $rfq->requestedBy?->employee_id ? __('app.users.employee_id_short', ['id' => $rfq->requestedBy->employee_id]) : '—' }}</div>
                                </td>
                                <td class="cell-sub">{{ $rfq->needed_by?->translatedFormat(__('app.date.short')) ?? '—' }}</td>
                                <td><span class="badge {{ $rfq->statusColor() }}">{{ $rfq->statusLabel() }}</span></td>
                                <td class="cell-sub">{{ $rfq->vendors->count() }}</td>
                                <td>
                                    <a href="{{ route('rfqs.show', $rfq) }}" class="btn btn-secondary btn-sm">{{ __('app.rfq.view') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @include('partials.pagination', ['paginator' => $rfqs])
@endsection

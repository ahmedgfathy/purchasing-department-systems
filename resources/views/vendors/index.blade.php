@extends('layouts.app')

@section('title', __('app.vendors.title'))
@section('heading', __('app.vendors.heading'))
@section('subheading', __('app.vendors.subheading'))

@section('actions')
    @if (auth()->user()->hasPermission('vendors.manage'))
        <a href="{{ route('vendors.create') }}" class="btn btn-primary">{{ __('app.vendors.new') }}</a>
    @endif
@endsection

@section('content')
    @if ($pendingRfqs->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>{{ __('app.vendors.approvals_queue') }}</h2>
                    <p>{{ __('app.vendors.approvals_queue_sub') }}</p>
                </div>
                <span class="badge amber">{{ $pendingRfqs->count() }}</span>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('app.rfq.col_reference') }}</th>
                            <th>{{ __('app.rfq.col_title') }}</th>
                            <th>{{ __('app.rfq.col_requested_by') }}</th>
                            <th>{{ __('app.rfq.col_needed_by') }}</th>
                            <th style="width:1%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendingRfqs as $rfq)
                            <tr>
                                <td>
                                    <div class="cell-title mono">{{ $rfq->reference }}</div>
                                    <div class="cell-sub">{{ $rfq->created_at->translatedFormat(__('app.date.short')) }}</div>
                                </td>
                                <td>
                                    <div class="cell-title">{{ $rfq->title }}</div>
                                    <div class="cell-sub"><span class="badge {{ $rfq->priorityColor() }}">{{ $rfq->priorityLabel() }}</span></div>
                                </td>
                                <td class="cell-sub">{{ $rfq->requestedBy?->name ?? '—' }}</td>
                                <td class="cell-sub">{{ $rfq->needed_by?->translatedFormat(__('app.date.short')) ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('rfqs.show', $rfq) }}" class="btn btn-primary btn-sm">{{ __('app.vendors.review') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if ($readyRfqs->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>{{ __('app.vendors.assignments_queue') }}</h2>
                    <p>{{ __('app.vendors.assignments_queue_sub') }}</p>
                </div>
                <span class="badge blue">{{ $readyRfqs->count() }}</span>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('app.rfq.col_reference') }}</th>
                            <th>{{ __('app.rfq.col_title') }}</th>
                            <th>{{ __('app.rfq.col_requested_by') }}</th>
                            <th>{{ __('app.rfq.col_status') }}</th>
                            <th style="width:1%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($readyRfqs as $rfq)
                            <tr>
                                <td>
                                    <div class="cell-title mono">{{ $rfq->reference }}</div>
                                    <div class="cell-sub">{{ $rfq->created_at->translatedFormat(__('app.date.short')) }}</div>
                                </td>
                                <td>
                                    <div class="cell-title">{{ $rfq->title }}</div>
                                    <div class="cell-sub"><span class="badge {{ $rfq->priorityColor() }}">{{ $rfq->priorityLabel() }}</span></div>
                                </td>
                                <td class="cell-sub">{{ $rfq->requestedBy?->name ?? '—' }}</td>
                                <td><span class="badge {{ $rfq->statusColor() }}">{{ $rfq->statusLabel() }}</span></td>
                                <td>
                                    <a href="{{ route('rfqs.show', $rfq) }}" class="btn btn-primary btn-sm">{{ __('app.vendors.assign') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <h2>{{ __('app.vendors.directory') }}</h2>
                <p>{{ __('app.vendors.directory_sub') }}</p>
            </div>
        </div>

        <div class="toolbar">
            <form method="GET" action="{{ route('vendors.index') }}">
                <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('app.vendors.search_placeholder') }}" style="min-width:260px">
                <button type="submit" class="btn btn-secondary">{{ __('app.vendors.search') }}</button>
                @if ($search !== '')
                    <a href="{{ route('vendors.index') }}" class="btn btn-ghost">{{ __('app.vendors.clear') }}</a>
                @endif
            </form>
        </div>

        @if ($vendors->isEmpty())
            <div class="empty">{{ __('app.vendors.empty') }}</div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('app.vendors.col_vendor') }}</th>
                            <th>{{ __('app.vendors.col_type') }}</th>
                            <th>{{ __('app.vendors.col_contact') }}</th>
                            <th>{{ __('app.vendors.col_country') }}</th>
                            <th>{{ __('app.vendors.col_category') }}</th>
                            <th>{{ __('app.vendors.col_status') }}</th>
                            @if (auth()->user()->hasPermission('vendors.manage'))
                                <th style="width:1%"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vendors as $vendor)
                            <tr>
                                <td>
                                    <div class="cell-title">{{ $vendor->name }}</div>
                                    <div class="cell-sub mono">{{ $vendor->code }}</div>
                                </td>
                                <td class="cell-sub">{{ $vendor->typeLabel() }}</td>
                                <td>
                                    <div class="cell-title" style="font-weight:500">{{ $vendor->contact_name ?: '—' }}</div>
                                    <div class="cell-sub">
                                        @if ($vendor->email) {{ $vendor->email }} @endif
                                        @if ($vendor->email && $vendor->phone) · @endif
                                        @if ($vendor->phone) {{ $vendor->phone }} @endif
                                    </div>
                                </td>
                                <td class="cell-sub">{{ $vendor->country ?: '—' }}</td>
                                <td class="cell-sub">{{ $vendor->category ?: '—' }}</td>
                                <td>
                                    <span class="badge {{ $vendor->status === \App\Models\Vendor::STATUS_ACTIVE ? 'green' : 'slate' }}">{{ $vendor->statusLabel() }}</span>
                                </td>
                                @if (auth()->user()->hasPermission('vendors.manage'))
                                    <td>
                                        <div style="display:flex;gap:6px;align-items:center">
                                            <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-secondary btn-sm">{{ __('app.vendors.edit') }}</a>
                                            <form method="POST" action="{{ route('vendors.destroy', $vendor) }}" onsubmit="return confirm('{{ __('app.vendors.delete_confirm') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">{{ __('app.vendors.delete') }}</button>
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
    </div>

    @include('partials.pagination', ['paginator' => $vendors])
@endsection

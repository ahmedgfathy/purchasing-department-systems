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
            <span class="badge slate">{{ $vendors->total() }}</span>
        </div>

        <form method="GET" action="{{ route('vendors.index') }}">
            <div class="form-grid">
                <div class="field full">
                    <label for="q">{{ __('app.vendors.search_placeholder') }}</label>
                    <input id="q" type="search" name="q" value="{{ $filters['q'] }}" placeholder="{{ __('app.vendors.search_placeholder') }}">
                </div>

                <div class="field">
                    <label for="registration_status">{{ __('app.vendors.filters.registration_status') }}</label>
                    <select id="registration_status" name="registration_status">
                        <option value="">{{ __('app.vendors.filters.all') }}</option>
                        @foreach ($registrationStatuses as $status)
                            <option value="{{ $status }}" @selected($filters['registration_status'] === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="country">{{ __('app.vendors.filters.country') }}</label>
                    <select id="country" name="country">
                        <option value="">{{ __('app.vendors.filters.all') }}</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country }}" @selected($filters['country'] === $country)>{{ $country }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="category">{{ __('app.vendors.filters.category') }}</label>
                    <select id="category" name="category">
                        <option value="">{{ __('app.vendors.filters.all') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="type">{{ __('app.vendors.filters.scope') }}</label>
                    <select id="type" name="type">
                        <option value="">{{ __('app.vendors.filters.all') }}</option>
                        @foreach (\App\Models\Vendor::types() as $value => $label)
                            <option value="{{ $value }}" @selected($filters['type'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field full">
                    <label for="activity">{{ __('app.vendors.filters.activity') }}</label>
                    <input id="activity" type="text" name="activity" value="{{ $filters['activity'] }}" placeholder="{{ __('app.vendors.filters.activity_placeholder') }}">
                    <div class="hint">{{ __('app.vendors.filters.activity_hint') }}</div>
                </div>

                <div class="field full">
                    <div class="actions">
                        <label class="check">
                            <input type="checkbox" name="include_stopped" value="1" @checked($filters['include_stopped'])>
                            <strong>{{ __('app.vendors.filters.include_stopped') }}</strong>
                        </label>
                        <label class="check">
                            <input type="checkbox" name="include_struck" value="1" @checked($filters['include_struck'])>
                            <strong>{{ __('app.vendors.filters.include_struck') }}</strong>
                        </label>
                    </div>
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">{{ __('app.vendors.search') }}</button>
                <a href="{{ route('vendors.index') }}" class="btn btn-ghost">{{ __('app.vendors.clear') }}</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>{{ __('app.vendors.list') }}</h2>
                <p>{{ __('app.vendors.list_sub') }}</p>
            </div>
        </div>

        @if ($vendors->isEmpty())
            <div class="empty">{{ __('app.vendors.empty') }}</div>
        @else
            <div class="table-wrap">
                <table style="min-width:1180px">
                    <thead>
                        <tr>
                            <th>{{ __('app.vendors.fields.supplier_id') }}</th>
                            <th>{{ __('app.vendors.col_vendor') }}</th>
                            <th>{{ __('app.vendors.col_type') }}</th>
                            <th>{{ __('app.vendors.col_country') }}</th>
                            <th>{{ __('app.vendors.col_agent') }}</th>
                            <th>{{ __('app.vendors.col_phone') }}</th>
                            <th>{{ __('app.vendors.col_email') }}</th>
                            <th>{{ __('app.vendors.col_contact') }}</th>
                            <th>{{ __('app.vendors.col_category') }}</th>
                            <th>{{ __('app.vendors.col_registration_status') }}</th>
                            <th>{{ __('app.vendors.col_services') }}</th>
                            <th>{{ __('app.vendors.col_status') }}</th>
                            <th style="width:1%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vendors as $vendor)
                            <tr>
                                <td class="mono cell-sub">{{ $vendor->supplier_id ?: $vendor->code }}</td>
                                <td>
                                    <a href="{{ route('vendors.show', $vendor) }}" class="cell-title" style="color:inherit;text-decoration:none">
                                        {{ $vendor->displayName() }}
                                    </a>
                                    @if ($vendor->name_en && $vendor->name_en !== $vendor->name_ar)
                                        <div class="cell-sub">{{ $vendor->name_en }}</div>
                                    @endif
                                </td>
                                <td><span class="badge {{ $vendor->type === \App\Models\Vendor::TYPE_BOTH ? 'amber' : ($vendor->type === \App\Models\Vendor::TYPE_FOREIGN ? 'blue' : 'slate') }}">{{ $vendor->typeLabel() }}</span></td>
                                <td class="cell-sub">{{ $vendor->country ?: '—' }}</td>
                                <td class="cell-sub">{{ $vendor->agent_name ?: '—' }}</td>
                                <td>
                                    <div class="cell-sub">{{ $vendor->phone ?: '—' }}</div>
                                    @if ($vendor->mobile1 && $vendor->mobile1 !== $vendor->phone)
                                        <div class="cell-sub muted">{{ $vendor->mobile1 }}</div>
                                    @endif
                                </td>
                                <td class="cell-sub" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $vendor->email ?: '—' }}</td>
                                <td class="cell-sub">{{ $vendor->contact_name ?: '—' }}</td>
                                <td class="cell-sub">{{ $vendor->category ?: '—' }}</td>
                                <td class="cell-sub">{{ $vendor->registration_status ?: '—' }}</td>
                                <td><span class="badge static">{{ $vendor->services_count }}</span></td>
                                <td>
                                    <span class="badge {{ $vendor->status === \App\Models\Vendor::STATUS_ACTIVE ? 'green' : 'slate' }}">{{ $vendor->statusLabel() }}</span>
                                    @if ($vendor->stopped || $vendor->struck_off)
                                        <div class="cell-sub">
                                            @if ($vendor->stopped) {{ __('app.vendors.flags.stopped') }} @endif
                                            @if ($vendor->stopped && $vendor->struck_off) · @endif
                                            @if ($vendor->struck_off) {{ __('app.vendors.flags.struck_off') }} @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('vendors.show', $vendor) }}" class="btn btn-secondary btn-sm">{{ __('app.vendors.view') }}</a>
                                        @if (auth()->user()->hasPermission('vendors.manage'))
                                            <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-secondary btn-sm">{{ __('app.vendors.edit') }}</a>
                                            <form method="POST" action="{{ route('vendors.destroy', $vendor) }}" class="inline-form" onsubmit="return confirm('{{ __('app.vendors.delete_confirm') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">{{ __('app.vendors.delete') }}</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @include('partials.pagination', ['paginator' => $vendors])
@endsection

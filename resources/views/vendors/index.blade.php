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
    @include('partials.vendor-form-style')

    @php
        $manage = auth()->user()->hasPermission('vendors.manage');

        $chips = collect([
            'q' => [__('app.vendors.search'), $filters['q']],
            'registration_status' => [__('app.vendors.filters.registration_status'), $filters['registration_status']],
            'country' => [__('app.vendors.filters.country'), $filters['country']],
            'category' => [__('app.vendors.filters.category'), $filters['category']],
            'type' => [__('app.vendors.filters.scope'), $filters['type'] ? __('app.vendors.types.'.$filters['type']) : ''],
            'activity' => [__('app.vendors.filters.activity'), $filters['activity']],
        ])->filter(fn ($chip) => filled($chip[1]));

        if ($filters['include_stopped']) {
            $chips->put('include_stopped', [__('app.vendors.filters.include_stopped'), null]);
        }

        if ($filters['include_struck']) {
            $chips->put('include_struck', [__('app.vendors.filters.include_struck'), null]);
        }

        $activeCount = $chips->count();
    @endphp

    @if ($pendingRfqs->isNotEmpty())
        <div class="card">
            <div class="vhead">
                <div>
                    <h2>{{ __('app.vendors.approvals_queue') }}</h2>
                    <p>{{ __('app.vendors.approvals_queue_sub') }}</p>
                </div>
                <span class="badge amber">{{ $pendingRfqs->count() }}</span>
            </div>

            <div class="vtable-wrap">
                <table class="vtable">
                    <thead>
                        <tr>
                            <th>{{ __('app.rfq.col_reference') }}</th>
                            <th>{{ __('app.rfq.col_title') }}</th>
                            <th>{{ __('app.rfq.col_requested_by') }}</th>
                            <th>{{ __('app.rfq.col_needed_by') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendingRfqs as $rfq)
                            <tr>
                                <td>
                                    <span class="vname vmono">{{ $rfq->reference }}</span>
                                    <span class="vsub">{{ $rfq->created_at->translatedFormat(__('app.date.short')) }}</span>
                                </td>
                                <td>
                                    <span class="vname">{{ $rfq->title }}</span>
                                    <span class="vsub"><span class="badge {{ $rfq->priorityColor() }}">{{ $rfq->priorityLabel() }}</span></span>
                                </td>
                                <td class="vsub">{{ $rfq->requestedBy?->name ?? '—' }}</td>
                                <td class="vsub">{{ $rfq->needed_by?->translatedFormat(__('app.date.short')) ?? '—' }}</td>
                                <td class="is-actions">
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
            <div class="vhead">
                <div>
                    <h2>{{ __('app.vendors.assignments_queue') }}</h2>
                    <p>{{ __('app.vendors.assignments_queue_sub') }}</p>
                </div>
                <span class="badge blue">{{ $readyRfqs->count() }}</span>
            </div>

            <div class="vtable-wrap">
                <table class="vtable">
                    <thead>
                        <tr>
                            <th>{{ __('app.rfq.col_reference') }}</th>
                            <th>{{ __('app.rfq.col_title') }}</th>
                            <th>{{ __('app.rfq.col_requested_by') }}</th>
                            <th>{{ __('app.rfq.col_status') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($readyRfqs as $rfq)
                            <tr>
                                <td>
                                    <span class="vname vmono">{{ $rfq->reference }}</span>
                                    <span class="vsub">{{ $rfq->created_at->translatedFormat(__('app.date.short')) }}</span>
                                </td>
                                <td>
                                    <span class="vname">{{ $rfq->title }}</span>
                                    <span class="vsub"><span class="badge {{ $rfq->priorityColor() }}">{{ $rfq->priorityLabel() }}</span></span>
                                </td>
                                <td class="vsub">{{ $rfq->requestedBy?->name ?? '—' }}</td>
                                <td><span class="badge {{ $rfq->statusColor() }}">{{ $rfq->statusLabel() }}</span></td>
                                <td class="is-actions">
                                    <a href="{{ route('rfqs.show', $rfq) }}" class="btn btn-primary btn-sm">{{ __('app.vendors.assign') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="vfilter" id="vendor-filters">
        <form method="GET" action="{{ route('vendors.index') }}">
            <div class="vfilter__bar">
                <div class="vfilter__search">
                    <input id="q" type="search" name="q" value="{{ $filters['q'] }}" placeholder="{{ __('app.vendors.search_placeholder') }}" aria-label="{{ __('app.vendors.search_placeholder') }}">
                    <button type="submit" class="btn btn-primary">{{ __('app.vendors.search') }}</button>
                </div>

                <button type="button" class="vfilter__toggle" id="vfilter-toggle" aria-expanded="false" aria-controls="vfilter-body">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 3H2l8 9.46V19l4 2v-8.54z"/></svg>
                    {{ __('app.vendors.filters_toggle') }}
                    <span class="vfilter__count" id="vfilter-count" @if (! $activeCount) hidden @endif>{{ $activeCount }}</span>
                </button>

                @if ($activeCount)
                    <a href="{{ route('vendors.index') }}" class="btn btn-ghost btn-sm">{{ __('app.vendors.clear') }}</a>
                @endif
            </div>

            <div class="vchips">
                @foreach ($chips as $key => $chip)
                    <span class="vchip">
                        {{ $chip[0] }}
                        @if ($chip[1])<span>· {{ $chip[1] }}</span>@endif
                        <a href="{{ request()->fullUrlWithQuery([$key => null]) }}" aria-label="{{ __('app.vendors.clear') }}">&times;</a>
                    </span>
                @endforeach
            </div>

            <div class="vfilter__body" id="vfilter-body" hidden>
                <div class="vfilter__grid">
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

                    <div class="field field--full">
                        <label for="activity">{{ __('app.vendors.filters.activity') }}</label>
                        <input id="activity" type="text" name="activity" value="{{ $filters['activity'] }}" placeholder="{{ __('app.vendors.filters.activity_placeholder') }}">
                        <div class="hint">{{ __('app.vendors.filters.activity_hint') }}</div>
                    </div>
                </div>

                <div class="vfilter__foot">
                    <button type="submit" class="btn btn-primary">{{ __('app.vendors.search') }}</button>
                    <a href="{{ route('vendors.index') }}" class="btn btn-ghost">{{ __('app.vendors.clear') }}</a>

                    <div class="vflags">
                        <label class="vflag">
                            <input type="checkbox" name="include_stopped" value="1" @checked($filters['include_stopped'])>
                            <span>{{ __('app.vendors.filters.include_stopped') }}</span>
                        </label>
                        <label class="vflag">
                            <input type="checkbox" name="include_struck" value="1" @checked($filters['include_struck'])>
                            <span>{{ __('app.vendors.filters.include_struck') }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card" style="margin-top:10px">
        <div class="vhead">
            <div>
                <h2>{{ __('app.vendors.list') }}</h2>
                <p>{{ __('app.vendors.list_sub') }}</p>
            </div>
            <span class="vcount">{{ $vendors->total() }}</span>
        </div>

        @if ($vendors->isEmpty())
            <div class="empty">{{ __('app.vendors.empty') }}</div>
        @else
            <div class="vtable-wrap vtable-wrap--flush">
                <table class="vtable" style="min-width:840px">
                    <thead>
                        <tr>
                            <th>{{ __('app.vendors.col_vendor') }}</th>
                            <th>{{ __('app.vendors.col_type') }}</th>
                            <th>{{ __('app.vendors.col_contact') }}</th>
                            <th>{{ __('app.vendors.col_services') }}</th>
                            <th>{{ __('app.vendors.col_status') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vendors as $vendor)
                            <tr>
                                <td>
                                    <a href="{{ route('vendors.show', $vendor) }}" class="vname" style="color:inherit;text-decoration:none">{{ $vendor->displayName() }}</a>
                                    @if ($vendor->name_en && $vendor->name_en !== $vendor->name_ar)
                                        <span class="vsub">{{ $vendor->name_en }}</span>
                                    @endif
                                    <span class="vsub vmono">
                                        {{ $vendor->supplier_id ? '#'.$vendor->supplier_id : $vendor->code }}
                                        @if ($vendor->supplier_id && $vendor->code && (string) $vendor->code !== (string) $vendor->supplier_id)
                                            · {{ $vendor->code }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $vendor->type === \App\Models\Vendor::TYPE_BOTH ? 'amber' : ($vendor->type === \App\Models\Vendor::TYPE_FOREIGN ? 'blue' : 'slate') }}">{{ $vendor->typeLabel() }}</span>
                                    <span class="vsub">{{ $vendor->country ?: '—' }}</span>
                                </td>
                                <td>
                                    <span class="vsub">{{ $vendor->phone ?: '—' }}</span>
                                    <span class="vsub vtrunc" title="{{ $vendor->email }}">{{ $vendor->email ?: '—' }}</span>
                                </td>
                                <td><span class="badge static">{{ $vendor->services_count }}</span></td>
                                <td>
                                    <span class="badge {{ $vendor->status === \App\Models\Vendor::STATUS_ACTIVE ? 'green' : 'slate' }}">{{ $vendor->statusLabel() }}</span>
                                    @if ($vendor->stopped || $vendor->struck_off)
                                        <span class="vsub">
                                            {{ $vendor->stopped ? __('app.vendors.flags.stopped') : '' }}
                                            {{ $vendor->stopped && $vendor->struck_off ? '·' : '' }}
                                            {{ $vendor->struck_off ? __('app.vendors.flags.struck_off') : '' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="is-actions">
                                    <div class="actions">
                                        <a href="{{ route('vendors.show', $vendor) }}" class="vicon" title="{{ __('app.vendors.view') }}" aria-label="{{ __('app.vendors.view') }}">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                        @if ($manage)
                                            <a href="{{ route('vendors.edit', $vendor) }}" class="vicon" title="{{ __('app.vendors.edit') }}" aria-label="{{ __('app.vendors.edit') }}">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                            </a>
                                            <form method="POST" action="{{ route('vendors.destroy', $vendor) }}" class="inline-form" onsubmit="return confirm('{{ __('app.vendors.delete_confirm') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="vicon vicon--danger" title="{{ __('app.vendors.delete') }}" aria-label="{{ __('app.vendors.delete') }}">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                                </button>
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

    <script>
        (function () {
            var box = document.getElementById('vendor-filters');
            var toggle = document.getElementById('vfilter-toggle');
            var body = document.getElementById('vfilter-body');
            if (!box || !toggle || !body) return;

            var KEY = 'ppd-vendor-filters-open';

            function setOpen(open) {
                body.hidden = !open;
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            }

            try {
                setOpen(localStorage.getItem(KEY) === '1');
            } catch (e) {
                setOpen(false);
            }

            toggle.addEventListener('click', function () {
                var open = body.hidden;
                setOpen(open);
                try { localStorage.setItem(KEY, open ? '1' : '0'); } catch (e) {}
            });
        })();
    </script>
@endsection

@extends('layouts.app')

@section('title', $vendor->displayName())
@section('heading', $vendor->displayName())
@section('subheading', $vendor->registration_status ?: __('app.vendors.subheading'))

@section('actions')
    <a href="{{ route('vendors.index') }}" class="btn btn-ghost">{{ __('app.vendors.back') }}</a>
    @if (auth()->user()->hasPermission('vendors.manage'))
        <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-secondary">{{ __('app.vendors.edit') }}</a>
        <form method="POST" action="{{ route('vendors.destroy', $vendor) }}" class="inline-form" onsubmit="return confirm('{{ __('app.vendors.delete_confirm') }}')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">{{ __('app.vendors.delete') }}</button>
        </form>
    @endif
@endsection

@section('content')
    @php
        $manage = auth()->user()->hasPermission('vendors.manage');
        $dateShort = __('app.date.short');
    @endphp

    @include('partials.vendor-form-style')

    {{-- Identity --}}
    <div class="card vcard">
        <div class="vhead">
            <div>
                <h2>{{ $vendor->displayName() }}</h2>
                <p>
                    @if ($vendor->name_en && $vendor->name_en !== $vendor->name_ar)
                        {{ $vendor->name_en }} ·
                    @endif
                    @if ($vendor->supplier_id)
                        <span class="mono">#{{ $vendor->supplier_id }}</span>
                        @if ($vendor->code && (string) $vendor->code !== (string) $vendor->supplier_id)
                            · <span class="mono">{{ $vendor->code }}</span>
                        @endif
                    @else
                        <span class="mono">{{ $vendor->code }}</span>
                    @endif
                </p>
            </div>
            <div class="actions">
                <span class="badge {{ $vendor->type === \App\Models\Vendor::TYPE_BOTH ? 'amber' : ($vendor->type === \App\Models\Vendor::TYPE_FOREIGN ? 'blue' : 'slate') }}">{{ $vendor->typeLabel() }}</span>
                <span class="badge {{ $vendor->status === \App\Models\Vendor::STATUS_ACTIVE ? 'green' : 'slate' }}">{{ $vendor->statusLabel() }}</span>
                @if ($vendor->stopped)
                    <span class="badge red">{{ __('app.vendors.flags.stopped') }}</span>
                @endif
                @if ($vendor->struck_off)
                    <span class="badge red">{{ __('app.vendors.flags.struck_off') }}</span>
                @endif
            </div>
        </div>

        <div class="vfacts">
            @include('vendors._detail', ['label' => __('app.vendors.fields.supplier_id'), 'value' => $vendor->supplier_id])
            @include('vendors._detail', ['label' => __('app.vendors.fields.brochure_no'), 'value' => $vendor->brochure_no])
            @include('vendors._detail', ['label' => __('app.vendors.fields.brochure_type'), 'value' => $vendor->brochure_type])
            @include('vendors._detail', ['label' => __('app.vendors.col_country'), 'value' => $vendor->country])
            @include('vendors._detail', ['label' => __('app.vendors.col_category'), 'value' => $vendor->category])
            @include('vendors._detail', ['label' => __('app.vendors.fields.agent_name'), 'value' => $vendor->agent_name])
            @include('vendors._detail', ['label' => __('app.vendors.fields.registration_status'), 'value' => $vendor->registration_status])
            @include('vendors._detail', ['label' => __('app.vendors.fields.registration_status_legacy'), 'value' => $vendor->registration_status_legacy])
            @include('vendors._detail', ['label' => __('app.vendors.fields.certified_class'), 'value' => $vendor->certified_class])
            @include('vendors._detail', ['label' => __('app.vendors.fields.sab_no'), 'value' => $vendor->sab_no])
            @include('vendors._detail', ['label' => __('app.vendors.fields.free_zone'), 'value' => $vendor->free_zone])
            @include('vendors._detail', ['label' => __('app.vendors.fields.data_entry_user'), 'value' => $vendor->data_entry_user])
            @include('vendors._detail', ['label' => __('app.vendors.fields.cd_vendor'), 'value' => $vendor->cd_vendor])
        </div>
    </div>

    {{-- Contact --}}
    <details class="vendor-section" open>
        <summary class="vendor-section__header">

            <div class="vendor-section__titles">
                <h3>{{ __('app.vendors.sections.contact') }}</h3>
                <p>{{ __('app.vendors.sections.contact_sub') }}</p>
            </div>
            <svg class="vendor-section__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
        </summary>

        <div class="vendor-section__body">
            <div class="vfacts">
                @include('vendors._detail', ['label' => __('app.vendors.fields.phone1'), 'value' => $vendor->phone1])
                @include('vendors._detail', ['label' => __('app.vendors.fields.phone2'), 'value' => $vendor->phone2])
                @include('vendors._detail', ['label' => __('app.vendors.fields.phone3'), 'value' => $vendor->phone3])
                @include('vendors._detail', ['label' => __('app.vendors.fields.mobile1'), 'value' => $vendor->mobile1])
                @include('vendors._detail', ['label' => __('app.vendors.fields.mobile2'), 'value' => $vendor->mobile2])
                @include('vendors._detail', ['label' => __('app.vendors.fields.mobile3'), 'value' => $vendor->mobile3])
                @include('vendors._detail', ['label' => __('app.vendors.fields.fax1'), 'value' => $vendor->fax1])
                @include('vendors._detail', ['label' => __('app.vendors.fields.fax2'), 'value' => $vendor->fax2])
                @include('vendors._detail', ['label' => __('app.vendors.fields.email1'), 'value' => $vendor->email1])
                @include('vendors._detail', ['label' => __('app.vendors.fields.email2'), 'value' => $vendor->email2])
                @include('vendors._detail', ['label' => __('app.vendors.fields.email3'), 'value' => $vendor->email3])
                @include('vendors._detail', ['label' => __('app.vendors.fields.website'), 'value' => $vendor->website])
                @include('vendors._detail', ['label' => __('app.vendors.fields.contact_name'), 'value' => $vendor->contact_name])
                @include('vendors._detail', ['label' => __('app.vendors.fields.address'), 'value' => $vendor->address])
            </div>
        </div>
    </details>

    {{-- Agent --}}
    @if ($vendor->agent_name || $vendor->agent_phone1 || $vendor->agent_address || $vendor->agent_email1)
        <details class="vendor-section" open>
            <summary class="vendor-section__header">

                <div class="vendor-section__titles">
                    <h3>{{ __('app.vendors.sections.agent') }}</h3>
                    <p>{{ __('app.vendors.sections.agent_sub') }}</p>
                </div>
                <svg class="vendor-section__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </summary>

            <div class="vendor-section__body">
                <div class="vfacts">
                    @include('vendors._detail', ['label' => __('app.vendors.fields.agent_name'), 'value' => $vendor->agent_name])
                    @include('vendors._detail', ['label' => __('app.vendors.fields.agent_phone1'), 'value' => $vendor->agent_phone1])
                    @include('vendors._detail', ['label' => __('app.vendors.fields.agent_phone2'), 'value' => $vendor->agent_phone2])
                    @include('vendors._detail', ['label' => __('app.vendors.fields.agent_phone3'), 'value' => $vendor->agent_phone3])
                    @include('vendors._detail', ['label' => __('app.vendors.fields.agent_fax1'), 'value' => $vendor->agent_fax1])
                    @include('vendors._detail', ['label' => __('app.vendors.fields.agent_fax2'), 'value' => $vendor->agent_fax2])
                    @include('vendors._detail', ['label' => __('app.vendors.fields.agent_fax3'), 'value' => $vendor->agent_fax3])
                    @include('vendors._detail', ['label' => __('app.vendors.fields.agent_mobile1'), 'value' => $vendor->agent_mobile1])
                    @include('vendors._detail', ['label' => __('app.vendors.fields.agent_mobile2'), 'value' => $vendor->agent_mobile2])
                    @include('vendors._detail', ['label' => __('app.vendors.fields.agent_mobile3'), 'value' => $vendor->agent_mobile3])
                    @include('vendors._detail', ['label' => __('app.vendors.fields.agent_email1'), 'value' => $vendor->agent_email1])
                    @include('vendors._detail', ['label' => __('app.vendors.fields.agent_email2'), 'value' => $vendor->agent_email2])
                    @include('vendors._detail', ['label' => __('app.vendors.fields.agent_email3'), 'value' => $vendor->agent_email3])
                    @include('vendors._detail', ['label' => __('app.vendors.fields.agent_address'), 'value' => $vendor->agent_address])
                </div>
            </div>
        </details>
    @endif

    {{-- Registration & committee --}}
    <details class="vendor-section" open>
        <summary class="vendor-section__header">

            <div class="vendor-section__titles">
                <h3>{{ __('app.vendors.sections.registration') }}</h3>
                <p>{{ __('app.vendors.sections.registration_sub') }}</p>
            </div>
            <svg class="vendor-section__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
        </summary>

        <div class="vendor-section__body">
            <div class="vfacts">
                @include('vendors._detail', ['label' => __('app.vendors.fields.committee_date'), 'value' => $vendor->committee_date?->translatedFormat($dateShort)])
                @include('vendors._detail', ['label' => __('app.vendors.fields.committee_no'), 'value' => $vendor->committee_no])
                @include('vendors._detail', ['label' => __('app.vendors.fields.commercial_register'), 'value' => $vendor->commercial_register])
                @include('vendors._detail', ['label' => __('app.vendors.fields.commercial_register_expiry'), 'value' => $vendor->commercial_register_expiry?->translatedFormat($dateShort)])
                @include('vendors._detail', ['label' => __('app.vendors.fields.tax_card'), 'value' => $vendor->tax_card])
                @include('vendors._detail', ['label' => __('app.vendors.fields.tax_card_expiry'), 'value' => $vendor->tax_card_expiry?->translatedFormat($dateShort)])
                @include('vendors._detail', ['label' => __('app.vendors.fields.vat_number'), 'value' => $vendor->vat_number])
                @include('vendors._detail', ['label' => __('app.vendors.fields.vat_expiry'), 'value' => $vendor->vat_expiry?->translatedFormat($dateShort)])
                @include('vendors._detail', ['label' => __('app.vendors.fields.owner_name'), 'value' => $vendor->owner_name])
                @include('vendors._detail', ['label' => __('app.vendors.fields.capital_amount'), 'value' => $vendor->capital_amount !== null ? number_format($vendor->capital_amount) : null])
                @include('vendors._detail', ['label' => __('app.vendors.fields.capital_currency'), 'value' => $vendor->capital_currency])
            </div>
        </div>
    </details>

    {{-- Documents & notes --}}
    <details class="vendor-section" open>
        <summary class="vendor-section__header">

            <div class="vendor-section__titles">
                <h3>{{ __('app.vendors.sections.documents') }}</h3>
                <p>{{ __('app.vendors.sections.documents_sub') }}</p>
            </div>
            <svg class="vendor-section__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
        </summary>

        <div class="vendor-section__body">
            <div class="vfacts">
                @include('vendors._detail', ['label' => __('app.vendors.fields.agency_documents'), 'value' => $vendor->agency_documents])
                @include('vendors._detail', ['label' => __('app.vendors.fields.notes'), 'value' => $vendor->notes])
                @include('vendors._detail', ['label' => __('app.vendors.fields.special_notes'), 'value' => $vendor->special_notes])
                @include('vendors._detail', ['label' => __('app.vendors.fields.extra_notes'), 'value' => $vendor->extra_notes])
                @include('vendors._detail', ['label' => __('app.vendors.fields.committee_text'), 'value' => $vendor->committee_text])
            </div>
        </div>
    </details>

    {{-- Services & products (Access: Vendor VS Tasks) --}}
    <div class="card vcard" id="services">
        <div class="vhead">
            <div>
                <h2>
                    {{ __('app.vendors.services.title') }}
                    <span class="badge static">{{ $vendor->services->count() }}</span>
                </h2>
                <p>{{ __('app.vendors.services.sub') }}</p>
            </div>
        </div>

        @if ($manage)
            <details class="vendor-section" id="add-service">
                <summary class="vendor-section__header">
                    <span class="vendor-section__number">+</span>
                    <div class="vendor-section__titles">
                        <h3>{{ __('app.vendors.services.add') }}</h3>
                        <p>{{ __('app.vendors.services.activity_hint') }}</p>
                    </div>
                    <svg class="vendor-section__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                </summary>

                <div class="vendor-section__body">
                    <form method="POST" action="{{ route('vendors.services.store', $vendor) }}">
                        @csrf

                        <div class="vendor-grid">
                            <div class="vendor-grid__field vendor-grid__field--full">
                                <div class="field">
                                    <label for="activity-search">{{ __('app.vendors.services.fields.activity') }} <abbr title="required">*</abbr></label>
                                    <input id="activity-search" type="text" autocomplete="off" list="activity-options" placeholder="{{ __('app.vendors.services.activity_placeholder') }}">
                                    <datalist id="activity-options"></datalist>
                                    <input type="hidden" name="activity_id" id="activity-id" value="{{ old('activity_id') }}">
                                    <span class="hint">{{ __('app.vendors.services.activity_hint') }}</span>
                                    @error('activity_id') <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="vendor-grid__field vendor-grid__field--wide">
                                <div class="field">
                                    <label for="activity_code">{{ __('app.vendors.services.fields.activity_code') }}</label>
                                    <input id="activity_code" type="text" name="activity_code" value="{{ old('activity_code') }}">
                                    @error('activity_code') <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="vendor-grid__field vendor-grid__field--wide">
                                <div class="field">
                                    <label for="activity_name">{{ __('app.vendors.services.fields.activity_name') }}</label>
                                    <input id="activity_name" type="text" name="activity_name" value="{{ old('activity_name') }}">
                                    @error('activity_name') <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="vendor-grid__field">
                                <div class="field">
                                    <label for="registration_type">{{ __('app.vendors.services.fields.registration_type') }}</label>
                                    <input id="registration_type" type="text" name="registration_type" value="{{ old('registration_type') }}">
                                </div>
                            </div>

                            <div class="vendor-grid__field">
                                <div class="field">
                                    <label for="registration_role">{{ __('app.vendors.services.fields.registration_role') }}</label>
                                    <input id="registration_role" type="text" name="registration_role" value="{{ old('registration_role') }}">
                                </div>
                            </div>

                            <div class="vendor-grid__field">
                                <div class="field">
                                    <label for="brand">{{ __('app.vendors.services.fields.brand') }}</label>
                                    <input id="brand" type="text" name="brand" value="{{ old('brand') }}">
                                </div>
                            </div>

                            <div class="vendor-grid__field">
                                <div class="field">
                                    <label for="country_of_origin">{{ __('app.vendors.services.fields.country_of_origin') }}</label>
                                    <input id="country_of_origin" type="text" name="country_of_origin" value="{{ old('country_of_origin') }}">
                                </div>
                            </div>

                            <div class="vendor-grid__field">
                                <div class="field">
                                    <label for="origin">{{ __('app.vendors.services.fields.origin') }}</label>
                                    <input id="origin" type="text" name="origin" value="{{ old('origin') }}">
                                </div>
                            </div>

                            <div class="vendor-grid__field">
                                <div class="field">
                                    <label for="original_manufacturer">{{ __('app.vendors.services.fields.original_manufacturer') }}</label>
                                    <input id="original_manufacturer" type="text" name="original_manufacturer" value="{{ old('original_manufacturer') }}">
                                </div>
                            </div>

                            <div class="vendor-grid__field">
                                <div class="field">
                                    <label for="reviewer">{{ __('app.vendors.services.fields.reviewer') }}</label>
                                    <input id="reviewer" type="text" name="reviewer" value="{{ old('reviewer') }}">
                                </div>
                            </div>

                            <div class="vendor-grid__field vendor-grid__field--xwide">
                                <div class="field">
                                    <label for="service_notes">{{ __('app.vendors.services.fields.notes') }}</label>
                                    <input id="service_notes" type="text" name="notes" value="{{ old('notes') }}">
                                </div>
                            </div>

                            <div class="vendor-grid__field">
                                <div class="field">
                                    <label class="vflag" style="margin-top:18px">
                                        <input type="checkbox" name="selected" value="1" @checked(old('selected'))>
                                        <span>{{ __('app.vendors.services.fields.selected') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="actions" style="padding-bottom:4px">
                            <button type="submit" class="btn btn-primary btn-sm">{{ __('app.vendors.services.add') }}</button>
                        </div>
                    </form>
                </div>
            </details>
        @endif

        @if ($vendor->services->isEmpty())
            <div class="empty">{{ __('app.vendors.services.empty') }}</div>
        @else
            <div class="vtable-wrap">
                <table class="vtable" style="min-width:760px">
                    <thead>
                        <tr>
                            <th>{{ __('app.vendors.services.col_activity') }}</th>
                            <th>{{ __('app.vendors.services.fields.registration_type') }}</th>
                            <th>{{ __('app.vendors.services.fields.brand') }} / {{ __('app.vendors.services.fields.country_of_origin') }}</th>
                            <th>{{ __('app.vendors.services.fields.selected') }}</th>
                            <th>{{ __('app.vendors.services.fields.notes') }}</th>
                            @if ($manage)
                                <th></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vendor->services as $service)
                            <tr>
                                <td>
                                    <span class="vname vmono">{{ $service->activity?->code ?? '—' }}</span>
                                    <span class="vsub">{{ $service->activity?->name_ar ?: ($service->activity?->name_en ?? '') }}</span>
                                    @if ($service->activity)
                                        <span class="vsub muted">{{ $service->activity->sourceLabel() }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="vsub">{{ $service->registration_type ?: '—' }}</span>
                                    @if ($service->registration_role)
                                        <span class="vsub">{{ $service->registration_role }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="vsub">{{ $service->brand ?: '—' }}</span>
                                    @if ($service->country_of_origin || $service->origin)
                                        <span class="vsub">{{ collect([$service->country_of_origin, $service->origin])->filter()->implode(' · ') }}</span>
                                    @endif
                                    @if ($service->original_manufacturer)
                                        <span class="vsub vtrunc" title="{{ $service->original_manufacturer }}">{{ $service->original_manufacturer }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $service->selected ? 'green' : 'slate' }}">
                                        {{ $service->selected ? __('app.vendors.services.yes') : __('app.vendors.services.no') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="vsub vtrunc" title="{{ $service->notes }}">{{ $service->notes ?: '—' }}</span>
                                </td>
                                @if ($manage)
                                    <td class="is-actions">
                                        <div class="actions">
                                            <details class="vservice-edit">
                                                <summary class="vicon" title="{{ __('app.vendors.edit') }}">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                                </summary>
                                                <form method="POST" action="{{ route('vendors.services.update', [$vendor, $service]) }}" class="service-edit">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="vendor-grid">
                                                        <div class="vendor-grid__field vendor-grid__field--half">
                                                            <div class="field">
                                                                <label>{{ __('app.vendors.services.fields.registration_type') }}</label>
                                                                <input type="text" name="registration_type" value="{{ old('registration_type', $service->registration_type) }}">
                                                            </div>
                                                        </div>
                                                        <div class="vendor-grid__field vendor-grid__field--half">
                                                            <div class="field">
                                                                <label>{{ __('app.vendors.services.fields.registration_role') }}</label>
                                                                <input type="text" name="registration_role" value="{{ old('registration_role', $service->registration_role) }}">
                                                            </div>
                                                        </div>
                                                        <div class="vendor-grid__field vendor-grid__field--half">
                                                            <div class="field">
                                                                <label>{{ __('app.vendors.services.fields.brand') }}</label>
                                                                <input type="text" name="brand" value="{{ old('brand', $service->brand) }}">
                                                            </div>
                                                        </div>
                                                        <div class="vendor-grid__field vendor-grid__field--half">
                                                            <div class="field">
                                                                <label>{{ __('app.vendors.services.fields.country_of_origin') }}</label>
                                                                <input type="text" name="country_of_origin" value="{{ old('country_of_origin', $service->country_of_origin) }}">
                                                            </div>
                                                        </div>
                                                        <div class="vendor-grid__field vendor-grid__field--half">
                                                            <div class="field">
                                                                <label>{{ __('app.vendors.services.fields.origin') }}</label>
                                                                <input type="text" name="origin" value="{{ old('origin', $service->origin) }}">
                                                            </div>
                                                        </div>
                                                        <div class="vendor-grid__field vendor-grid__field--half">
                                                            <div class="field">
                                                                <label>{{ __('app.vendors.services.fields.original_manufacturer') }}</label>
                                                                <input type="text" name="original_manufacturer" value="{{ old('original_manufacturer', $service->original_manufacturer) }}">
                                                            </div>
                                                        </div>
                                                        <div class="vendor-grid__field vendor-grid__field--half">
                                                            <div class="field">
                                                                <label>{{ __('app.vendors.services.fields.reviewer') }}</label>
                                                                <input type="text" name="reviewer" value="{{ old('reviewer', $service->reviewer) }}">
                                                            </div>
                                                        </div>
                                                        <div class="vendor-grid__field vendor-grid__field--half">
                                                            <div class="field">
                                                                <label>{{ __('app.vendors.services.fields.notes') }}</label>
                                                                <input type="text" name="notes" value="{{ old('notes', $service->notes) }}">
                                                            </div>
                                                        </div>
                                                        <div class="vendor-grid__field vendor-grid__field--full">
                                                            <label class="vflag">
                                                                <input type="checkbox" name="selected" value="1" @checked(old('selected', $service->selected))>
                                                                <span>{{ __('app.vendors.services.fields.selected') }}</span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="actions">
                                                        <button type="submit" class="btn btn-primary btn-sm">{{ __('app.vendors.save') }}</button>
                                                    </div>
                                                </form>
                                            </details>

                                            <form method="POST" action="{{ route('vendors.services.destroy', [$vendor, $service]) }}" class="inline-form" onsubmit="return confirm('{{ __('app.vendors.delete_confirm') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="vicon vicon--danger" title="{{ __('app.vendors.delete') }}" aria-label="{{ __('app.vendors.delete') }}">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                                </button>
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

    <script>
        (function () {
            var searchInput = document.getElementById('activity-search');
            var hiddenInput = document.getElementById('activity-id');
            var datalist = document.getElementById('activity-options');

            if (!searchInput) return;

            var options = [];
            var timer = null;
            var loaded = false;

            function fetchActivities(term) {
                fetch('{{ route('vendors.activities') }}?term=' + encodeURIComponent(term), {
                    headers: { 'Accept': 'application/json' },
                })
                    .then(function (response) { return response.json(); })
                    .then(function (payload) {
                        options = (payload.data || []).map(function (activity) {
                            return {
                                id: activity.id,
                                label: activity.code + ' — ' + (activity.name || ''),
                            };
                        });

                        datalist.innerHTML = '';
                        options.forEach(function (option) {
                            var element = document.createElement('option');
                            element.value = option.label;
                            datalist.appendChild(element);
                        });
                    })
                    .catch(function () { /* ignore network errors in the combobox */ });
            }

            searchInput.addEventListener('focus', function () {
                if (!loaded) {
                    loaded = true;
                    fetchActivities('');
                }
            });

            searchInput.addEventListener('input', function () {
                hiddenInput.value = '';
                clearTimeout(timer);
                timer = setTimeout(function () {
                    fetchActivities(searchInput.value.trim());
                }, 250);
            });

            searchInput.addEventListener('change', function () {
                var match = options.find(function (option) {
                    return option.label === searchInput.value;
                });
                hiddenInput.value = match ? String(match.id) : '';
            });
        })();
    </script>
@endsection

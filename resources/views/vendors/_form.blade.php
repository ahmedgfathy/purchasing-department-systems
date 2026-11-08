@php
    $vendor = $vendor ?? null;
    $isEdit = $vendor !== null;

    $sections = [
        [
            'key' => 'master',
            'fields' => [
                ['name' => 'name_ar', 'type' => 'text', 'required' => true, 'span' => 6],
                ['name' => 'name_en', 'type' => 'text', 'span' => 6],
                ['name' => 'supplier_id', 'type' => 'number'],
                ['name' => 'code', 'type' => 'text', 'placeholder' => __('app.vendors.code_placeholder'), 'hint' => __('app.vendors.code_hint')],
                ['name' => 'brochure_no', 'type' => 'number'],
                ['name' => 'brochure_type', 'type' => 'select', 'options' => ['محلى' => 'محلى', 'خارجى' => 'خارجى']],
                ['name' => 'country', 'type' => 'text'],
                ['name' => 'category', 'type' => 'text'],
                ['name' => 'registration_status', 'type' => 'text', 'datalist' => 'registration-status-options'],
                ['name' => 'registration_status_legacy', 'type' => 'text'],
                ['name' => 'certified_class', 'type' => 'text'],
                ['name' => 'sab_no', 'type' => 'text'],
                ['name' => 'data_entry_user', 'type' => 'text'],
                ['name' => 'free_zone', 'type' => 'text'],
                ['name' => 'cd_vendor', 'type' => 'text'],
                ['name' => 'local_registered', 'type' => 'check'],
                ['name' => 'foreign_registered', 'type' => 'check'],
                ['name' => 'stopped', 'type' => 'check'],
                ['name' => 'struck_off', 'type' => 'check'],
            ],
        ],
        [
            'key' => 'contact',
            'fields' => [
                ['name' => 'phone1', 'type' => 'text'],
                ['name' => 'phone2', 'type' => 'text'],
                ['name' => 'phone3', 'type' => 'text'],
                ['name' => 'mobile1', 'type' => 'text'],
                ['name' => 'mobile2', 'type' => 'text'],
                ['name' => 'mobile3', 'type' => 'text'],
                ['name' => 'fax1', 'type' => 'text'],
                ['name' => 'fax2', 'type' => 'text'],
                ['name' => 'contact_name', 'type' => 'text'],
                ['name' => 'email1', 'type' => 'text', 'span' => 6],
                ['name' => 'email2', 'type' => 'text', 'span' => 6],
                ['name' => 'email3', 'type' => 'text', 'span' => 6],
                ['name' => 'website', 'type' => 'text', 'span' => 6],
                ['name' => 'address', 'type' => 'text', 'span' => 12],
            ],
        ],
        [
            'key' => 'agent',
            'fields' => [
                ['name' => 'agent_name', 'type' => 'text'],
                ['name' => 'agent_phone1', 'type' => 'text'],
                ['name' => 'agent_phone2', 'type' => 'text'],
                ['name' => 'agent_phone3', 'type' => 'text'],
                ['name' => 'agent_mobile1', 'type' => 'text'],
                ['name' => 'agent_mobile2', 'type' => 'text'],
                ['name' => 'agent_mobile3', 'type' => 'text'],
                ['name' => 'agent_fax1', 'type' => 'text'],
                ['name' => 'agent_fax2', 'type' => 'text'],
                ['name' => 'agent_fax3', 'type' => 'text'],
                ['name' => 'agent_email1', 'type' => 'text'],
                ['name' => 'agent_email2', 'type' => 'text'],
                ['name' => 'agent_email3', 'type' => 'text'],
                ['name' => 'agent_address', 'type' => 'text', 'span' => 12],
            ],
        ],
        [
            'key' => 'registration',
            'fields' => [
                ['name' => 'committee_date', 'type' => 'date'],
                ['name' => 'committee_no', 'type' => 'text'],
                ['name' => 'owner_name', 'type' => 'text'],
                ['name' => 'commercial_register', 'type' => 'text'],
                ['name' => 'commercial_register_expiry', 'type' => 'date'],
                ['name' => 'tax_card', 'type' => 'text'],
                ['name' => 'tax_card_expiry', 'type' => 'date'],
                ['name' => 'vat_number', 'type' => 'text'],
                ['name' => 'vat_expiry', 'type' => 'date'],
                ['name' => 'capital_amount', 'type' => 'number'],
                ['name' => 'capital_currency', 'type' => 'text'],
            ],
        ],
        [
            'key' => 'documents',
            'fields' => [
                ['name' => 'agency_documents', 'type' => 'textarea', 'span' => 12],
                ['name' => 'special_notes', 'type' => 'textarea', 'span' => 12],
                ['name' => 'notes', 'type' => 'textarea', 'span' => 12],
                ['name' => 'extra_notes', 'type' => 'textarea', 'span' => 12],
                ['name' => 'committee_text', 'type' => 'textarea', 'span' => 12],
            ],
        ],
    ];
@endphp

@include('partials.vendor-form-style')

<form method="POST" action="{{ $action }}" class="vendor-form">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <datalist id="registration-status-options">
        <option value="جديد"></option>
        <option value="مسجل"></option>
        <option value="غير مسجل"></option>
        <option value="تحت التسجيل"></option>
        <option value="شطب"></option>
        <option value="إستبعاد"></option>
        <option value="مغلق"></option>
        <option value="جهة طالبة"></option>
        <option value="مصنع أصلى"></option>
    </datalist>

    <div class="vendor-form__intro">
        <div>
            <h2>{{ $isEdit ? __('app.vendors.edit_heading') : __('app.vendors.create_heading') }}</h2>
            <p>{{ $isEdit ? __('app.vendors.edit_subheading') : __('app.vendors.create_subheading') }}</p>
        </div>
        <span class="vendor-form__intro-badge">{{ $isEdit ? __('app.vendors.save') : __('app.vendors.new') }}</span>
    </div>

    @foreach ($sections as $index => $section)
        <section class="vendor-section">
            <div class="vendor-section__header">
                <span class="vendor-section__number">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                <div>
                    <h3>{{ __('app.vendors.sections.'.$section['key']) }}</h3>
                    <p>{{ __('app.vendors.sections.'.$section['key'].'_sub') }}</p>
                </div>
            </div>

            <div class="vendor-grid">
                @foreach ($section['fields'] as $field)
                    @php
                        $name = $field['name'];
                        $type = $field['type'] ?? 'text';
                        $label = __('app.vendors.fields.'.$name);
                        $span = $field['span'] ?? 4;
                        $spanClass = $span === 12 ? ' vendor-grid__field--full' : ($span === 6 ? ' vendor-grid__field--wide' : '');
                        $checked = old($name, (bool) ($vendor?->getAttribute($name) ?? false));
                    @endphp

                    <div class="vendor-grid__field{{ $spanClass }}">
                        <div class="field">
                            @if ($type === 'check')
                                <label class="check">
                                    <input type="checkbox" name="{{ $name }}" value="1" @checked($checked)>
                                    <strong>{{ $label }}</strong>
                                </label>
                            @else
                                <label for="{{ $name }}">
                                    {{ $label }}
                                    @if ($field['required'] ?? false)<abbr title="required">*</abbr>@endif
                                </label>

                                @if ($type === 'textarea')
                                    <textarea id="{{ $name }}" name="{{ $name }}" rows="3" placeholder="{{ $field['placeholder'] ?? '' }}">{{ old($name, $vendor?->getAttribute($name) ?? '') }}</textarea>
                                @elseif ($type === 'select')
                                    <select id="{{ $name }}" name="{{ $name }}">
                                        <option value="">{{ __('app.vendors.filters.all') }}</option>
                                        @foreach ($field['options'] as $value => $optionLabel)
                                            <option value="{{ $value }}" @selected((string) old($name, $vendor?->getAttribute($name) ?? '') === (string) $value)>{{ $optionLabel }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($type === 'date')
                                    <input id="{{ $name }}" type="date" name="{{ $name }}" value="{{ old($name, ($vendor?->getAttribute($name))?->format('Y-m-d') ?? '') }}">
                                @else
                                    <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $vendor?->getAttribute($name) ?? '') }}" placeholder="{{ $field['placeholder'] ?? '' }}" @if ($field['required'] ?? false) required @endif @if (! empty($field['datalist'])) list="{{ $field['datalist'] }}" @endif>
                                @endif

                                @if (! empty($field['hint']))
                                    <span class="hint">{{ $field['hint'] }}</span>
                                @endif
                            @endif

                            @error($name)
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach

    <div class="vendor-form__footer">
        <span class="vendor-form__footer-note"><abbr title="required">*</abbr> {{ __('app.vendors.fields.name_ar') }}</span>
        <div class="actions">
            <a href="{{ $isEdit ? route('vendors.show', $vendor) : route('vendors.index') }}" class="btn btn-ghost">{{ __('app.vendors.cancel') }}</a>
            <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        </div>
    </div>
</form>

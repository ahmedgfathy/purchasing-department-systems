@php
    $vendor = $vendor ?? null;
    $isEdit = $vendor !== null;

    $rawValue = function (string $name) use ($vendor) {
        $value = old($name, $vendor?->getAttribute($name));

        return $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : ($value ?? '');
    };

    $checked = fn (string $name) => (bool) old($name, $vendor?->getAttribute($name) ?? false);
    $label = fn (string $name) => __('app.vendors.fields.'.$name);

    $widthClass = [
        3 => ' vendor-grid__field',
        4 => ' vendor-grid__field vendor-grid__field--wide',
        6 => ' vendor-grid__field vendor-grid__field--half',
        8 => ' vendor-grid__field vendor-grid__field--xwide',
        12 => ' vendor-grid__field vendor-grid__field--full',
    ];

    $sections = [
        [
            'key' => 'master',
            'rows' => [
                ['type' => 'text', 'name' => 'name_ar', 'w' => 6, 'required' => true],
                ['type' => 'text', 'name' => 'name_en', 'w' => 6],
                ['type' => 'number', 'name' => 'supplier_id', 'w' => 3],
                ['type' => 'text', 'name' => 'code', 'w' => 3, 'placeholder' => __('app.vendors.code_placeholder'), 'hint' => __('app.vendors.code_hint')],
                ['type' => 'number', 'name' => 'brochure_no', 'w' => 3],
                ['type' => 'select', 'name' => 'brochure_type', 'w' => 3, 'options' => ['محلى' => 'محلى', 'خارجى' => 'خارجى']],
                ['type' => 'text', 'name' => 'country', 'w' => 3],
                ['type' => 'text', 'name' => 'category', 'w' => 3],
                ['type' => 'text', 'name' => 'registration_status', 'w' => 3, 'datalist' => 'registration-status-options'],
                ['type' => 'text', 'name' => 'registration_status_legacy', 'w' => 3],
                ['type' => 'text', 'name' => 'certified_class', 'w' => 3],
                ['type' => 'text', 'name' => 'sab_no', 'w' => 3],
                ['type' => 'text', 'name' => 'data_entry_user', 'w' => 3],
                ['type' => 'text', 'name' => 'free_zone', 'w' => 3],
                ['type' => 'text', 'name' => 'cd_vendor', 'w' => 3],
                ['type' => 'flags', 'fields' => ['local_registered', 'foreign_registered', 'stopped', 'struck_off']],
            ],
        ],
        [
            'key' => 'contact',
            'rows' => [
                ['type' => 'group', 'fields' => ['phone1', 'phone2', 'phone3'], 'w' => 4, 'numeric' => true],
                ['type' => 'group', 'fields' => ['mobile1', 'mobile2', 'mobile3'], 'w' => 4, 'numeric' => true],
                ['type' => 'group', 'fields' => ['fax1', 'fax2'], 'w' => 4, 'numeric' => true],
                ['type' => 'text', 'name' => 'contact_name', 'w' => 4],
                ['type' => 'text', 'name' => 'website', 'w' => 8, 'ltr' => true],
                ['type' => 'group', 'fields' => ['email1', 'email2', 'email3'], 'w' => 12, 'ltr' => true],
                ['type' => 'text', 'name' => 'address', 'w' => 12],
            ],
        ],
        [
            'key' => 'agent',
            'rows' => [
                ['type' => 'text', 'name' => 'agent_name', 'w' => 4],
                ['type' => 'group', 'fields' => ['agent_phone1', 'agent_phone2', 'agent_phone3'], 'w' => 4, 'numeric' => true],
                ['type' => 'group', 'fields' => ['agent_mobile1', 'agent_mobile2', 'agent_mobile3'], 'w' => 4, 'numeric' => true],
                ['type' => 'group', 'fields' => ['agent_fax1', 'agent_fax2', 'agent_fax3'], 'w' => 4, 'numeric' => true],
                ['type' => 'group', 'fields' => ['agent_email1', 'agent_email2', 'agent_email3'], 'w' => 8, 'ltr' => true, 'start' => 9],
                ['type' => 'text', 'name' => 'agent_address', 'w' => 12],
            ],
        ],
        [
            'key' => 'registration',
            'rows' => [
                ['type' => 'date', 'name' => 'committee_date', 'w' => 3],
                ['type' => 'text', 'name' => 'committee_no', 'w' => 3],
                ['type' => 'text', 'name' => 'owner_name', 'w' => 3],
                ['type' => 'number', 'name' => 'capital_amount', 'w' => 3],
                ['type' => 'text', 'name' => 'commercial_register', 'w' => 3],
                ['type' => 'date', 'name' => 'commercial_register_expiry', 'w' => 3],
                ['type' => 'text', 'name' => 'tax_card', 'w' => 3],
                ['type' => 'date', 'name' => 'tax_card_expiry', 'w' => 3],
                ['type' => 'text', 'name' => 'vat_number', 'w' => 3],
                ['type' => 'date', 'name' => 'vat_expiry', 'w' => 3],
                ['type' => 'text', 'name' => 'capital_currency', 'w' => 3],
            ],
        ],
        [
            'key' => 'documents',
            'rows' => [
                ['type' => 'textarea', 'name' => 'agency_documents', 'w' => 6],
                ['type' => 'textarea', 'name' => 'special_notes', 'w' => 6],
                ['type' => 'textarea', 'name' => 'notes', 'w' => 6],
                ['type' => 'textarea', 'name' => 'extra_notes', 'w' => 6],
                ['type' => 'textarea', 'name' => 'committee_text', 'w' => 12],
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
        <details class="vendor-section" open>
            <summary class="vendor-section__header">
                <span class="vendor-section__number">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                <div class="vendor-section__titles">
                    <h3>{{ __('app.vendors.sections.'.$section['key']) }}</h3>
                    <p>{{ __('app.vendors.sections.'.$section['key'].'_sub') }}</p>
                </div>
                <svg class="vendor-section__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </summary>

            <div class="vendor-grid">
                @foreach ($section['rows'] as $row)
                    @php
                        $type = $row['type'] ?? 'text';
                        $width = $widthClass[$row['w'] ?? 3] ?? $widthClass[3];
                    @endphp

                    @if ($type === 'flags')
                        <div class="vendor-grid__field vendor-grid__field--full">
                            <div class="vflags">
                                @foreach ($row['fields'] as $flag)
                                    <label class="vflag">
                                        <input type="checkbox" name="{{ $flag }}" value="1" @checked($checked($flag))>
                                        <span>{{ $label($flag) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @elseif ($type === 'group')
                        @php
                            $first = $row['fields'][0];
                            $groupLabel = preg_replace('/\s*\d+\s*$/u', '', $label($first));
                            $count = count($row['fields']);
                        @endphp
                        <div class="{{ trim($width) }}">
                            <div class="field">
                                <label>{{ $groupLabel }}</label>
                                <div class="vgroup__row{{ $count === 2 ? ' vgroup__row--two' : '' }}">
                                    @foreach ($row['fields'] as $field)
                                        <input
                                            id="{{ $field }}"
                                            type="text"
                                            name="{{ $field }}"
                                            value="{{ $rawValue($field) }}"
                                            placeholder="{{ $loop->iteration }}"
                                            @if ($row['ltr'] ?? false) dir="ltr" @endif
                                            aria-label="{{ $label($field) }}"
                                        >
                                    @endforeach
                                </div>
                                @foreach ($row['fields'] as $field)
                                    @error($field)
                                        <div class="error-text">{{ $message }}</div>
                                    @enderror
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="{{ trim($width) }}">
                            <div class="field">
                                <label for="{{ $row['name'] }}">
                                    {{ $label($row['name']) }}
                                    @if ($row['required'] ?? false)<abbr title="required">*</abbr>@endif
                                </label>

                                @if ($type === 'textarea')
                                    <textarea id="{{ $row['name'] }}" name="{{ $row['name'] }}" rows="2" placeholder="{{ $row['placeholder'] ?? '' }}">{{ $rawValue($row['name']) }}</textarea>
                                @elseif ($type === 'select')
                                    <select id="{{ $row['name'] }}" name="{{ $row['name'] }}">
                                        <option value="">{{ __('app.vendors.filters.all') }}</option>
                                        @foreach ($row['options'] as $value => $optionLabel)
                                            <option value="{{ $value }}" @selected((string) $rawValue($row['name']) === (string) $value)>{{ $optionLabel }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($type === 'date')
                                    <input id="{{ $row['name'] }}" type="date" name="{{ $row['name'] }}" value="{{ $rawValue($row['name']) }}">
                                @else
                                    <input
                                        id="{{ $row['name'] }}"
                                        type="{{ $type }}"
                                        name="{{ $row['name'] }}"
                                        value="{{ $rawValue($row['name']) }}"
                                        placeholder="{{ $row['placeholder'] ?? '' }}"
                                        @if ($row['required'] ?? false) required @endif
                                        @if (! empty($row['datalist'])) list="{{ $row['datalist'] }}" @endif
                                        @if ($row['ltr'] ?? false) dir="ltr" @endif
                                    >
                                @endif

                                @if (! empty($row['hint']))
                                    <span class="hint">{{ $row['hint'] }}</span>
                                @endif

                                @error($row['name'])
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </details>
    @endforeach

    <div class="vendor-form__footer">
        <span class="vendor-form__footer-note"><abbr title="required">*</abbr> {{ __('app.vendors.fields.name_ar') }}</span>
        <div class="actions">
            <a href="{{ $isEdit ? route('vendors.show', $vendor) : route('vendors.index') }}" class="btn btn-ghost">{{ __('app.vendors.cancel') }}</a>
            <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        </div>
    </div>
</form>

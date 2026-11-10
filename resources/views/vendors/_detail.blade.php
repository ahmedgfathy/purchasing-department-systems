@php
    $isEmpty = ($value ?? '') === '' || $value === null;
@endphp

<div class="vfact">
    <dt>{{ $label }}</dt>
    <dd @class(['is-empty' => $isEmpty])>{{ $isEmpty ? '—' : $value }}</dd>
</div>

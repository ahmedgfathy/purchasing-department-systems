<form method="POST" action="{{ $rfq ? route('rfqs.update', $rfq) : route('rfqs.store') }}">
    @csrf
    @if ($rfq)
        @method('PUT')
    @endif

    <div class="field">
        <label for="title">{{ __('app.rfq.title_field') }}</label>
        <input id="title" type="text" name="title" value="{{ old('title', $rfq?->title) }}" required maxlength="255">
        @error('title') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="project">{{ __('app.rfq.project') }}</label>
        <input id="project" type="text" name="project" value="{{ old('project', $rfq?->project) }}" maxlength="255">
        @error('project') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="vessel">{{ __('app.rfq.vessel') }}</label>
        <input id="vessel" type="text" name="vessel" value="{{ old('vessel', $rfq?->vessel) }}" maxlength="255">
        @error('vessel') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="requesting_department">{{ __('app.rfq.requesting_department') }}</label>
        <input id="requesting_department" type="text" name="requesting_department" value="{{ old('requesting_department', $rfq?->requesting_department) }}" maxlength="255">
        @error('requesting_department') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="location">{{ __('app.rfq.location') }}</label>
        <input id="location" type="text" name="location" value="{{ old('location', $rfq?->location) }}" maxlength="255">
        @error('location') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="cost_center">{{ __('app.rfq.cost_center') }}</label>
        <input id="cost_center" type="text" name="cost_center" value="{{ old('cost_center', $rfq?->cost_center) }}" maxlength="100">
        @error('cost_center') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="contact_name">{{ __('app.rfq.contact_name') }}</label>
        <input id="contact_name" type="text" name="contact_name" value="{{ old('contact_name', $rfq?->contact_name) }}" maxlength="255">
        @error('contact_name') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="contact_code">{{ __('app.rfq.contact_code') }}</label>
        <input id="contact_code" type="text" name="contact_code" value="{{ old('contact_code', $rfq?->contact_code) }}" maxlength="100">
        @error('contact_code') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="material_req_no">{{ __('app.rfq.material_req_no') }}</label>
        <input id="material_req_no" type="text" name="material_req_no" value="{{ old('material_req_no', $rfq?->material_req_no) }}" maxlength="100">
        @error('material_req_no') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="requisition_rec_date">{{ __('app.rfq.requisition_rec_date') }}</label>
        <input id="requisition_rec_date" type="date" name="requisition_rec_date" value="{{ old('requisition_rec_date', $rfq?->requisition_rec_date?->format('Y-m-d')) }}">
        @error('requisition_rec_date') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="preparation_date">{{ __('app.rfq.preparation_date') }}</label>
        <input id="preparation_date" type="date" name="preparation_date" value="{{ old('preparation_date', $rfq?->preparation_date?->format('Y-m-d')) }}">
        @error('preparation_date') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="service_delivery_period">{{ __('app.rfq.service_delivery_period') }}</label>
        <input id="service_delivery_period" type="text" name="service_delivery_period" value="{{ old('service_delivery_period', $rfq?->service_delivery_period) }}" maxlength="255">
        @error('service_delivery_period') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <!-- Dropdown Fields -->
    <div class="field">
        <label for="market_id">{{ __('app.rfq.market') }}</label>
        <select id="market_id" name="market_id">
            <option value="">{{ __('app.rfq.select_option') }}</option>
            @foreach ($markets as $market)
                <option value="{{ $market->id }}" @selected(old('market_id', $rfq?->market_id) === $market->id)>{{ $market->name }}</option>
            @endforeach
        </select>
        @error('market_id') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="order_type_id">{{ __('app.rfq.partial_order') }}</label>
        <select id="order_type_id" name="order_type_id">
            <option value="">{{ __('app.rfq.select_option') }}</option>
            @foreach ($orderTypes as $orderType)
                <option value="{{ $orderType->id }}" @selected(old('order_type_id', $rfq?->order_type_id) === $orderType->id)>{{ $orderType->name }}</option>
            @endforeach
        </select>
        @error('order_type_id') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="proposed_method_id">{{ __('app.rfq.proposed_method') }}</label>
        <select id="proposed_method_id" name="proposed_method_id">
            <option value="">{{ __('app.rfq.select_option') }}</option>
            @foreach ($proposedMethods as $proposedMethod)
                <option value="{{ $proposedMethod->id }}" @selected(old('proposed_method_id', $rfq?->proposed_method_id) === $proposedMethod->id)>{{ $proposedMethod->name }}</option>
            @endforeach
        </select>
        @error('proposed_method_id') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="ref_order_no">{{ __('app.rfq.ref_order_no') }}</label>
        <input id="ref_order_no" type="text" name="ref_order_no" value="{{ old('ref_order_no', $rfq?->ref_order_no) }}" maxlength="255">
        @error('ref_order_no') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="estimated_budget">{{ __('app.rfq.estimated_budget') }}</label>
        <input id="estimated_budget" type="number" name="estimated_budget" value="{{ old('estimated_budget', $rfq?->estimated_budget) }}" step="0.01" min="0">
        @error('estimated_budget') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="budget_currency">{{ __('app.rfq.budget_currency') }}</label>
        <input id="budget_currency" type="text" name="budget_currency" value="{{ old('budget_currency', $rfq?->budget_currency) }}" maxlength="10" placeholder="EGP">
        @error('budget_currency') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="description">{{ __('app.rfq.description') }}</label>
        <textarea id="description" name="description" rows="3">{{ old('description', $rfq?->description) }}</textarea>
        @error('description') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="priority">{{ __('app.rfq.priority') }}</label>
        <select id="priority" name="priority" required>
            @foreach (\App\Models\Rfq::priorities() as $value => $label)
                <option value="{{ $value }}" @selected(old('priority', $rfq?->priority ?? 'normal') === $value)>{{ __('app.rfq.priorities.'.$value) }}</option>
            @endforeach
        </select>
        @error('priority') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="currency">{{ __('app.rfq.currency') }}</label>
        <select id="currency" name="currency" required>
            @foreach (\App\Models\Rfq::currencies() as $currency)
                <option value="{{ $currency }}" @selected(old('currency', $rfq?->currency ?? 'USD') === $currency)>{{ $currency }}</option>
            @endforeach
        </select>
        @error('currency') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="needed_by">{{ __('app.rfq.needed_by') }}</label>
        <input id="needed_by" type="date" name="needed_by" value="{{ old('needed_by', $rfq?->needed_by?->format('Y-m-d')) }}">
        @error('needed_by') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="notes">{{ __('app.rfq.notes') }}</label>
        <input id="notes" type="text" name="notes" value="{{ old('notes', $rfq?->notes) }}" maxlength="5000">
        @error('notes') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <!-- Items Section -->
    <div class="card">
        <div class="card-header">
            <div>
                <h2>{{ __('app.rfq.items') }}</h2>
                <p>{{ __('app.rfq.items_sub') }}</p>
            </div>
            <button type="button" id="add-item" class="btn btn-secondary btn-sm">{{ __('app.rfq.add_item') }}</button>
        </div>

        @error('items') <div class="alert error">{{ $message }}</div> @enderror

        <div id="items">
            @foreach ($oldItems as $index => $item)
                <div class="item-row">
                    <div class="field">
                        <label>{{ __('app.rfq.description') }}</label>
                        <input type="text" name="items[{{ $index }}][description]" value="{{ $item['description'] ?? '' }}" required>
                        @error('items.'.$index.'.description') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <div class="field">
                        <label>{{ __('app.rfq.url') }}</label>
                        <input type="text" name="items[{{ $index }}][url]" value="{{ $item['url'] ?? '' }}" placeholder="https://example.com">
                        @error('items.'.$index.'.url') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <div class="field">
                        <label>{{ __('app.rfq.specifications') }}</label>
                        <input type="text" name="items[{{ $index }}][specifications]" value="{{ $item['specifications'] ?? '' }}" placeholder="Enter specifications">
                        @error('items.'.$index.'.specifications') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <div class="field">
                        <label>{{ __('app.rfq.quantity') }}</label>
                        <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" step="0.01" min="0.01" required>
                        @error('items.'.$index.'.quantity') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <div class="field">
                        <label>{{ __('app.rfq.unit') }}</label>
                        <input type="text" name="items[{{ $index }}][unit]" value="{{ $item['unit'] ?? '' }}" placeholder="EA, HR, etc.">
                        @error('items.'.$index.'.unit') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <div class="field">
                        <label>{{ __('app.rfq.estimated_unit_price') }}</label>
                        <input type="number" name="items[{{ $index }}][estimated_unit_price]" value="{{ $item['estimated_unit_price'] ?? 0 }}" step="0.01" min="0">
                        @error('items.'.$index.'.estimated_unit_price') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <div class="field">
                        <label>{{ __('app.rfq.notes') }}</label>
                        <input type="text" name="items[{{ $index }}][notes]" value="{{ $item['notes'] ?? '' }}" placeholder="Additional notes">
                        @error('items.'.$index.'.notes') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <button type="button" class="btn btn-ghost btn-sm" data-remove-item>{{ __('app.rfq.remove') }}</button>
                </div>
            @endforeach
        </div>
    </div>

    <div style="display:flex;gap:8px;margin-top:8px">
        <button type="submit" class="btn btn-primary">{{ $rfq ? __('app.rfq.save_changes') : __('app.rfq.submit') }}</button>
        <a href="{{ $rfq ? route('rfqs.show', $rfq) : route('rfqs.index') }}" class="btn btn-ghost">{{ __('app.rfq.cancel') }}</a>
    </div>
</form>

<template id="item-template">
    <div class="item-row">
        <div class="field"><label>{{ __('app.rfq.description') }}</label><input type="text" name="items[__INDEX__][description]" value="" required></div>
        <div class="field"><label>{{ __('app.rfq.url') }}</label><input type="text" name="items[__INDEX__][url]" value="" placeholder="https://example.com"></div>
        <div class="field"><label>{{ __('app.rfq.specifications') }}</label><input type="text" name="items[__INDEX__][specifications]" value="" placeholder="Enter specifications"></div>
        <div class="field"><label>{{ __('app.rfq.quantity') }}</label><input type="number" name="items[__INDEX__][quantity]" value="1" step="0.01" min="0.01" required></div>
        <div class="field"><label>{{ __('app.rfq.unit') }}</label><input type="text" name="items[__INDEX__][unit]" value="" placeholder="EA, HR, etc."></div>
        <div class="field"><label>{{ __('app.rfq.estimated_unit_price') }}</label><input type="number" name="items[__INDEX__][estimated_unit_price]" value="0" step="0.01" min="0"></div>
        <div class="field"><label>{{ __('app.rfq.notes') }}</label><input type="text" name="items[__INDEX__][notes]" value="" placeholder="Additional notes"></div>
        <button type="button" class="btn btn-ghost btn-sm" data-remove-item>{{ __('app.rfq.remove') }}</button>
    </div>
</template>

<script>
    (function () {
        var list = document.getElementById('items');
        var template = document.getElementById('item-template');
        var addButton = document.getElementById('add-item');
        var nextIndex = {{ count($oldItems) }};

        function bind(row) {
            var remove = row.querySelector('[data-remove-item]');
            if (remove) remove.addEventListener('click', function () {
                if (list.querySelectorAll('.item-row').length > 1) row.remove();
            });
        }

        list.querySelectorAll('.item-row').forEach(bind);
        addButton.addEventListener('click', function () {
            var wrapper = document.createElement('div');
            wrapper.innerHTML = template.innerHTML.replaceAll('__INDEX__', String(nextIndex++)).trim();
            var row = wrapper.firstElementChild;
            list.appendChild(row);
            bind(row);
        });
    })();
</script>
<?php

namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\OrderType;
use App\Models\ProposedMethod;
use App\Models\Rfq;
use App\Models\RfqItem;
use App\Models\RfqCurrency;
use App\Models\RfqDepartment;
use App\Models\RfqLocation;
use App\Models\RfqPriority;
use App\Models\RfqUnit;
use App\Models\Vendor;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RfqController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $status = (string) $request->query('status');

        $rfqs = Rfq::query()
            ->with(['requestedBy', 'vendors', 'market', 'orderType', 'proposedMethod', 'items'])
            ->withCount('items')
            ->when(array_key_exists($status, Rfq::statuses()), fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('reference', 'ilike', "%{$search}%")
                        ->orWhere('title', 'ilike', "%{$search}%")
                        ->orWhere('description', 'ilike', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('rfqs.index', compact('rfqs', 'search', 'status'));
    }

    public function create(): View
    {
        return view('rfqs.create', [
            'markets' => Market::query()->where('status', Market::STATUS_ACTIVE)->orderBy('name')->get(),
            'orderTypes' => OrderType::query()->where('status', OrderType::STATUS_ACTIVE)->orderBy('name')->get(),
            'proposedMethods' => ProposedMethod::query()->where('status', ProposedMethod::STATUS_ACTIVE)->orderBy('name')->get(),
            ...$this->lookupData(),
            'oldItems' => [],
            'rfq' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $items = collect($request->input('items', []))
            ->filter(fn ($item) => is_array($item) && trim((string) ($item['description'] ?? '')) !== '')
            ->values()
            ->all();

        $request->merge(['items' => $items]);

        $validated = $request->validate([
            // RFQ Header fields from Excel template
            'title' => ['required', 'string', 'max:255'],
            'project' => ['nullable', 'string', 'max:255'],
            'vessel' => ['nullable', 'string', 'max:255'],
            'department_id' => ['nullable', 'integer', 'exists:rfq_departments,id'],
            'location_id' => ['nullable', 'integer', 'exists:rfq_locations,id'],
            'cost_center' => ['nullable', 'string', 'max:100'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_code' => ['nullable', 'string', 'max:100'],
            'material_req_no' => ['nullable', 'string', 'max:100'],
            'requisition_rec_date' => ['nullable', 'date'],
            'preparation_date' => ['nullable', 'date'],
            'service_delivery_period' => ['nullable', 'string', 'max:255'],
            'market_id' => ['nullable', 'integer', 'exists:markets,id'],
            'order_type_id' => ['nullable', 'integer', 'exists:order_types,id'],
            'proposed_method_id' => ['nullable', 'integer', 'exists:proposed_methods,id'],
            'ref_order_no' => ['nullable', 'string', 'max:255'],
            'estimated_budget' => ['nullable', 'numeric', 'min:0'],
            'budget_currency_id' => ['nullable', 'integer', 'exists:rfq_currencies,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'priority_id' => ['required', 'integer', 'exists:rfq_priorities,id'],
            'currency_id' => ['required', 'integer', 'exists:rfq_currencies,id'],
            'needed_by' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:5000'],
            // RFQ Items
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0.01'],
            'items.*.unit_id' => ['nullable', 'integer', 'exists:rfq_units,id'],
        ]);

        $rfq = DB::transaction(function () use ($validated, $request) {
            $rfq = Rfq::create([
                'reference' => 'RFQ-'.now()->format('Y').'-'.str_pad((string) (Rfq::max('id') + 1), 4, '0', STR_PAD_LEFT),
                'title' => $validated['title'],
                'project' => $validated['project'] ?? null,
                'vessel' => $validated['vessel'] ?? null,
                'department_id' => $validated['department_id'] ?? null,
                'requesting_department' => RfqDepartment::find($validated['department_id'] ?? null)?->name,
                'location_id' => $validated['location_id'] ?? null,
                'location' => RfqLocation::find($validated['location_id'] ?? null)?->name,
                'cost_center' => $validated['cost_center'] ?? null,
                'contact_name' => $validated['contact_name'] ?? null,
                'contact_code' => $validated['contact_code'] ?? null,
                'material_req_no' => $validated['material_req_no'] ?? null,
                'requisition_rec_date' => $validated['requisition_rec_date'] ?? null,
                'preparation_date' => $validated['preparation_date'] ?? null,
                'service_delivery_period' => $validated['service_delivery_period'] ?? null,
                'market_id' => $validated['market_id'] ?? null,
                'order_type_id' => $validated['order_type_id'] ?? null,
                'proposed_method_id' => $validated['proposed_method_id'] ?? null,
                'ref_order_no' => $validated['ref_order_no'] ?? null,
                'estimated_budget' => $validated['estimated_budget'] ?? null,
                'budget_currency_id' => $validated['budget_currency_id'] ?? null,
                'budget_currency' => RfqCurrency::find($validated['budget_currency_id'] ?? null)?->code,
                'description' => $validated['description'] ?? null,
                'status' => Rfq::STATUS_PENDING,
                'priority_id' => $validated['priority_id'],
                'priority' => RfqPriority::findOrFail($validated['priority_id'])->code,
                'currency_id' => $validated['currency_id'],
                'currency' => RfqCurrency::findOrFail($validated['currency_id'])->code,
                'needed_by' => $validated['needed_by'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'requested_by' => $request->user()->id,
            ]);

            foreach ($validated['items'] as $item) {
                $rfq->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_id' => $item['unit_id'] ?? null,
                    'unit' => RfqUnit::find($item['unit_id'] ?? null)?->code,
                ]);
            }

            return $rfq;
        });

        return redirect()
            ->route('rfqs.show', $rfq)
            ->with('status', __('app.rfq.messages.submitted', ['reference' => $rfq->reference]));
    }

    public function edit(Rfq $rfq): View|RedirectResponse
    {
        if (! in_array($rfq->status, [Rfq::STATUS_PENDING, Rfq::STATUS_REJECTED], true)) {
            return redirect()->route('rfqs.show', $rfq)->with('error', __('app.rfq.messages.not_editable'));
        }

        $rfq->load('items');

        return view('rfqs.edit', [
            'markets' => Market::query()->where('status', Market::STATUS_ACTIVE)->orderBy('name')->get(),
            'orderTypes' => OrderType::query()->where('status', OrderType::STATUS_ACTIVE)->orderBy('name')->get(),
            'proposedMethods' => ProposedMethod::query()->where('status', ProposedMethod::STATUS_ACTIVE)->orderBy('name')->get(),
            ...$this->lookupData(),
            'rfq' => $rfq,
            'oldItems' => $rfq->items->toArray(),
        ]);
    }

    public function update(Request $request, Rfq $rfq): RedirectResponse
    {
        if (! in_array($rfq->status, [Rfq::STATUS_PENDING, Rfq::STATUS_REJECTED], true)) {
            return back()->with('error', __('app.rfq.messages.not_editable'));
        }

        $items = collect($request->input('items', []))
            ->filter(fn ($item) => is_array($item) && trim((string) ($item['description'] ?? '')) !== '')
            ->values()
            ->all();
        $request->merge(['items' => $items]);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'project' => ['nullable', 'string', 'max:255'],
            'vessel' => ['nullable', 'string', 'max:255'],
            'department_id' => ['nullable', 'integer', 'exists:rfq_departments,id'],
            'location_id' => ['nullable', 'integer', 'exists:rfq_locations,id'],
            'cost_center' => ['nullable', 'string', 'max:100'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_code' => ['nullable', 'string', 'max:100'],
            'material_req_no' => ['nullable', 'string', 'max:100'],
            'requisition_rec_date' => ['nullable', 'date'],
            'preparation_date' => ['nullable', 'date'],
            'service_delivery_period' => ['nullable', 'string', 'max:255'],
            'market_id' => ['nullable', 'integer', 'exists:markets,id'],
            'order_type_id' => ['nullable', 'integer', 'exists:order_types,id'],
            'proposed_method_id' => ['nullable', 'integer', 'exists:proposed_methods,id'],
            'ref_order_no' => ['nullable', 'string', 'max:255'],
            'estimated_budget' => ['nullable', 'numeric', 'min:0'],
            'budget_currency_id' => ['nullable', 'integer', 'exists:rfq_currencies,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'priority_id' => ['required', 'integer', 'exists:rfq_priorities,id'],
            'currency_id' => ['required', 'integer', 'exists:rfq_currencies,id'],
            'needed_by' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0.01'],
            'items.*.unit_id' => ['nullable', 'integer', 'exists:rfq_units,id'],
        ]);

        DB::transaction(function () use ($rfq, $validated) {
            $rfq->update([
                'title' => $validated['title'],
                'project' => $validated['project'] ?? null,
                'vessel' => $validated['vessel'] ?? null,
                'department_id' => $validated['department_id'] ?? null,
                'requesting_department' => RfqDepartment::find($validated['department_id'] ?? null)?->name,
                'location_id' => $validated['location_id'] ?? null,
                'location' => RfqLocation::find($validated['location_id'] ?? null)?->name,
                'cost_center' => $validated['cost_center'] ?? null,
                'contact_name' => $validated['contact_name'] ?? null,
                'contact_code' => $validated['contact_code'] ?? null,
                'material_req_no' => $validated['material_req_no'] ?? null,
                'requisition_rec_date' => $validated['requisition_rec_date'] ?? null,
                'preparation_date' => $validated['preparation_date'] ?? null,
                'service_delivery_period' => $validated['service_delivery_period'] ?? null,
                'market_id' => $validated['market_id'] ?? null,
                'order_type_id' => $validated['order_type_id'] ?? null,
                'proposed_method_id' => $validated['proposed_method_id'] ?? null,
                'ref_order_no' => $validated['ref_order_no'] ?? null,
                'estimated_budget' => $validated['estimated_budget'] ?? null,
                'budget_currency_id' => $validated['budget_currency_id'] ?? null,
                'budget_currency' => RfqCurrency::find($validated['budget_currency_id'] ?? null)?->code,
                'description' => $validated['description'] ?? null,
                'status' => Rfq::STATUS_PENDING,
                'priority_id' => $validated['priority_id'],
                'priority' => RfqPriority::findOrFail($validated['priority_id'])->code,
                'currency_id' => $validated['currency_id'],
                'currency' => RfqCurrency::findOrFail($validated['currency_id'])->code,
                'needed_by' => $validated['needed_by'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'approved_by' => null,
                'approved_at' => null,
                'rejection_reason' => null,
            ]);

            $rfq->items()->delete();

            foreach ($validated['items'] as $item) {
                $rfq->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_id' => $item['unit_id'] ?? null,
                    'unit' => RfqUnit::find($item['unit_id'] ?? null)?->code,
                ]);
            }
        });

        return redirect()->route('rfqs.show', $rfq)->with('status', __('app.rfq.messages.updated', ['reference' => $rfq->reference]));
    }

    public function destroy(Rfq $rfq): RedirectResponse
    {
        if (! in_array($rfq->status, [Rfq::STATUS_PENDING, Rfq::STATUS_REJECTED], true)) {
            return back()->with('error', __('app.rfq.messages.not_deletable'));
        }

        $reference = $rfq->reference;
        $rfq->delete();

        return redirect()->route('rfqs.index')->with('status', __('app.rfq.messages.deleted', ['reference' => $reference]));
    }

    public function show(Rfq $rfq): View
    {
        $rfq->load(['market', 'orderType', 'proposedMethod', 'department', 'locationOption', 'priorityOption', 'currencyOption', 'budgetCurrencyOption', 'items.unitOption', 'vendors', 'requestedBy', 'approvedBy', 'vendorsAssignedBy']);

        $vendors = Vendor::query()
            ->where('status', Vendor::STATUS_ACTIVE)
            ->orderBy('name')
            ->get();

        return view('rfqs.show', compact('rfq', 'vendors'));
    }

    public function approve(Request $request, Rfq $rfq): RedirectResponse
    {
        if ($rfq->status !== Rfq::STATUS_PENDING) {
            return back()->with('error', __('app.rfq.messages.not_pending'));
        }

        $rfq->update([
            'status' => Rfq::STATUS_APPROVED,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('status', __('app.rfq.messages.approved', ['reference' => $rfq->reference]));
    }

    public function reject(Request $request, Rfq $rfq): RedirectResponse
    {
        if ($rfq->status !== Rfq::STATUS_PENDING) {
            return back()->with('error', __('app.rfq.messages.not_pending'));
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ]);

        $rfq->update([
            'status' => Rfq::STATUS_REJECTED,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('status', __('app.rfq.messages.rejected', ['reference' => $rfq->reference]));
    }

    public function assignVendors(Request $request, Rfq $rfq): RedirectResponse
    {
        if (! in_array($rfq->status, [Rfq::STATUS_APPROVED, Rfq::STATUS_VENDORS_ASSIGNED], true)) {
            return back()->with('error', __('app.rfq.messages.not_assignable'));
        }

        $validated = $request->validate([
            'vendors' => ['required', 'array', 'min:1'],
            'vendors.*' => ['integer', 'exists:vendors,id'],
            'assignment_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $oldVendors = $rfq->vendors()->orderBy('name')->pluck('name')->all();

        $pivot = collect($validated['vendors'])
            ->mapWithKeys(fn ($vendorId) => [
                (int) $vendorId => [
                    'assigned_by' => $request->user()->id,
                    'note' => $validated['assignment_note'] ?? null,
                ],
            ])
            ->all();

        $rfq->vendors()->sync($pivot);

        $rfq->update([
            'status' => Rfq::STATUS_VENDORS_ASSIGNED,
            'vendors_assigned_by' => $request->user()->id,
            'vendors_assigned_at' => now(),
        ]);

        $newVendors = $rfq->vendors()->orderBy('name')->pluck('name')->all();

        AuditLogger::event(
            'vendors_assigned',
            $rfq,
            ['vendors' => $oldVendors],
            ['vendors' => $newVendors],
        );

        return back()->with('status', __('app.rfq.messages.vendors_assigned', ['reference' => $rfq->reference]));
    }

    private function lookupData(): array
    {
        return [
            'departments' => RfqDepartment::query()->where('status', RfqDepartment::STATUS_ACTIVE)->orderBy('name')->get(),
            'locations' => RfqLocation::query()->where('status', RfqLocation::STATUS_ACTIVE)->orderBy('name')->get(),
            'priorities' => RfqPriority::query()->where('status', RfqPriority::STATUS_ACTIVE)->orderBy('id')->get(),
            'currencies' => RfqCurrency::query()->where('status', RfqCurrency::STATUS_ACTIVE)->orderBy('code')->get(),
            'units' => RfqUnit::query()->where('status', RfqUnit::STATUS_ACTIVE)->orderBy('name')->get(),
        ];
    }
}

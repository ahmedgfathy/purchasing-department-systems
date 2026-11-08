<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Rfq;
use App\Models\Vendor;
use App\Models\VendorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VendorController extends Controller
{
    /**
     * Vendor list with "search by anything" plus the legacy Access filters.
     */
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q')),
            'registration_status' => trim((string) $request->query('registration_status')),
            'country' => trim((string) $request->query('country')),
            'category' => trim((string) $request->query('category')),
            'type' => trim((string) $request->query('type')),
            'activity' => trim((string) $request->query('activity')),
            'include_stopped' => $request->boolean('include_stopped'),
            'include_struck' => $request->boolean('include_struck'),
        ];

        $vendors = Vendor::query()
            ->withCount('services')
            ->when($filters['q'] !== '', fn ($query, $term) => $query->search($term))
            ->when($filters['registration_status'] !== '', function ($query, $value) {
                $query->where('registration_status', $value);
            })
            ->when($filters['country'] !== '', fn ($query, $value) => $query->where('country', $value))
            ->when($filters['category'] !== '', fn ($query, $value) => $query->where('category', $value))
            ->when($filters['type'] !== '', fn ($query, $value) => $query->where('type', $value))
            ->when($filters['activity'] !== '', function ($query, $value) {
                $like = '%'.$value.'%';

                $query->whereHas('services.activity', function ($activity) use ($like) {
                    $activity->where(function ($activity) use ($like) {
                        $activity->where('code', 'ilike', $like)
                            ->orWhere('activity_code', 'ilike', $like)
                            ->orWhere('name_ar', 'ilike', $like)
                            ->orWhere('name_en', 'ilike', $like)
                            ->orWhere('product_name', 'ilike', $like);
                    });
                });
            })
            ->when(! $filters['include_stopped'], fn ($query) => $query->where('stopped', false))
            ->when(! $filters['include_struck'], fn ($query) => $query->where('struck_off', false))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        $registrationStatuses = $this->distinctValues('registration_status');
        $countries = $this->distinctValues('country');
        $categories = $this->distinctValues('category');

        $pendingRfqs = $request->user()->hasPermission('rfq.approve')
            ? Rfq::query()->where('status', Rfq::STATUS_PENDING)->with('requestedBy')->latest('id')->get()
            : collect();

        $readyRfqs = $request->user()->hasPermission('rfq.assign')
            ? Rfq::query()->where('status', Rfq::STATUS_APPROVED)->with('requestedBy')->latest('id')->get()
            : collect();

        return view('vendors.index', compact(
            'vendors',
            'filters',
            'registrationStatuses',
            'countries',
            'categories',
            'pendingRfqs',
            'readyRfqs'
        ));
    }

    public function create(): View
    {
        return view('vendors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateVendor($request);

        $vendor = new Vendor($validated + [
            'created_by' => $request->user()->id,
        ]);

        if (! $vendor->code && ! $vendor->supplier_id) {
            $vendor->code = $this->nextCode();
        }

        $vendor->save();

        return redirect()
            ->route('vendors.show', $vendor)
            ->with('status', __('app.vendors.messages.created', ['name' => $vendor->name]));
    }

    public function show(Vendor $vendor): View
    {
        $vendor->load([
            'services' => fn ($query) => $query->orderBy('id'),
            'services.activity',
            'creator',
        ]);

        return view('vendors.show', compact('vendor'));
    }

    public function edit(Vendor $vendor): View
    {
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor): RedirectResponse
    {
        $validated = $this->validateVendor($request, $vendor);

        if (! ($validated['code'] ?? null) && ! ($validated['supplier_id'] ?? null)) {
            $validated['code'] = $vendor->code ?: $this->nextCode();
        }

        $vendor->update($validated);

        return redirect()
            ->route('vendors.show', $vendor)
            ->with('status', __('app.vendors.messages.updated', ['name' => $vendor->name]));
    }

    public function destroy(Vendor $vendor): RedirectResponse
    {
        if ($vendor->rfqs()->exists()) {
            return back()->with('error', __('app.vendors.messages.in_use'));
        }

        $name = $vendor->name;
        $vendor->delete();

        return redirect()->route('vendors.index')->with('status', __('app.vendors.messages.deleted', ['name' => $name]));
    }

    /**
     * AJAX combobox source for services / products.
     */
    public function activities(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('term', ''));
        $like = '%'.$term.'%';

        $activities = Activity::query()
            ->when($term !== '', function ($query) use ($like) {
                $query->where(function ($query) use ($like) {
                    $query->where('code', 'ilike', $like)
                        ->orWhere('name_ar', 'ilike', $like)
                        ->orWhere('name_en', 'ilike', $like)
                        ->orWhere('activity_code', 'ilike', $like)
                        ->orWhere('product_name', 'ilike', $like)
                        ->orWhere('main_name_ar', 'ilike', $like);
                });
            })
            ->withCount(['vendors as services_count'])
            ->orderBy('code')
            ->limit(30)
            ->get()
            ->map(fn (Activity $activity) => [
                'id' => $activity->id,
                'code' => $activity->code,
                'name' => $activity->name_ar ?: ($activity->name_en ?: $activity->product_name),
                'source' => $activity->source,
                'services_count' => $activity->services_count,
            ]);

        return response()->json(['data' => $activities]);
    }

    /**
     * Relate a service / product to the vendor (Access: Vendor VS Tasks).
     */
    public function storeService(Request $request, Vendor $vendor): RedirectResponse
    {
        $activity = $this->resolveActivity($request);

        if ($vendor->services()->where('activity_id', $activity->id)->exists()) {
            return back()->with('error', __('app.vendors.services.messages.duplicate', [
                'activity' => $activity->displayName(),
            ]));
        }

        $vendor->services()->create(
            $this->validateService($request) + ['activity_id' => $activity->id]
        );

        return back()->with('status', __('app.vendors.services.messages.created', [
            'activity' => $activity->displayName(),
        ]));
    }

    public function updateService(Request $request, Vendor $vendor, VendorService $service): RedirectResponse
    {
        abort_unless($service->vendor_id === $vendor->id, 404);

        $activityId = $service->activity_id;

        if ($request->filled('activity_id') || $request->filled('activity_code')) {
            $activity = $this->resolveActivity($request);

            if ($vendor->services()
                ->where('activity_id', $activity->id)
                ->where('id', '!=', $service->id)
                ->exists()) {
                return back()->with('error', __('app.vendors.services.messages.duplicate', [
                    'activity' => $activity->displayName(),
                ]));
            }

            $activityId = $activity->id;
        }

        $service->update(
            $this->validateService($request) + ['activity_id' => $activityId]
        );

        return back()->with('status', __('app.vendors.services.messages.updated', [
            'activity' => Activity::find($activityId)?->displayName() ?? '',
        ]));
    }

    public function destroyService(Vendor $vendor, VendorService $service): RedirectResponse
    {
        abort_unless($service->vendor_id === $vendor->id, 404);

        $label = $service->activity?->displayName() ?? '';
        $service->delete();

        return back()->with('status', __('app.vendors.services.messages.deleted', [
            'activity' => $label,
        ]));
    }

    /**
     * Accepts an existing activity id, or resolves/creates one by code+name.
     */
    private function resolveActivity(Request $request): Activity
    {
        $validated = $request->validate([
            'activity_id' => ['nullable', 'integer', 'exists:activities,id'],
            'activity_code' => ['nullable', 'string', 'max:50'],
            'activity_name' => ['nullable', 'string', 'max:255'],
        ], [], [
            'activity_id' => __('app.vendors.services.fields.activity'),
            'activity_code' => __('app.vendors.services.fields.activity_code'),
            'activity_name' => __('app.vendors.services.fields.activity_name'),
        ]);

        if ($validated['activity_id'] ?? null) {
            return Activity::query()->findOrFail($validated['activity_id']);
        }

        $code = trim((string) ($validated['activity_code'] ?? ''));
        $name = trim((string) ($validated['activity_name'] ?? ''));

        if ($code === '' || $name === '') {
            throw ValidationException::withMessages([
                'activity_id' => __('app.vendors.services.messages.activity_required'),
            ]);
        }

        // Reuse the catalog entry when the code already exists (any source).
        return Activity::query()->where('code', $code)->first()
            ?? Activity::create([
                'source' => Activity::SOURCE_CUSTOM,
                'code' => $code,
                'name_ar' => $name,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateService(Request $request): array
    {
        $validated = $request->validate([
            'registration_type' => ['nullable', 'string', 'max:100'],
            'registration_role' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:255'],
            'country_of_origin' => ['nullable', 'string', 'max:255'],
            'origin' => ['nullable', 'string', 'max:255'],
            'original_manufacturer' => ['nullable', 'string', 'max:255'],
            'reviewer' => ['nullable', 'string', 'max:255'],
            'selected' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [], [
            'registration_type' => __('app.vendors.services.fields.registration_type'),
            'registration_role' => __('app.vendors.services.fields.registration_role'),
            'brand' => __('app.vendors.services.fields.brand'),
            'country_of_origin' => __('app.vendors.services.fields.country_of_origin'),
            'origin' => __('app.vendors.services.fields.origin'),
            'original_manufacturer' => __('app.vendors.services.fields.original_manufacturer'),
            'reviewer' => __('app.vendors.services.fields.reviewer'),
            'selected' => __('app.vendors.services.fields.selected'),
            'notes' => __('app.vendors.services.fields.notes'),
        ]);

        return $validated + [
            'selected' => $request->boolean('selected'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validateVendor(Request $request, ?Vendor $vendor = null): array
    {
        // The legacy "name" column keeps validating updates of Access rows
        // where the Arabic/English split is still empty.
        if ($vendor !== null && ! $request->filled('name')) {
            $request->merge(['name' => $vendor->name]);
        }

        $trimmed = [
            'name_ar', 'name_en', 'name', 'code', 'brochure_type', 'agent_name',
            'phone1', 'phone2', 'phone3', 'fax1', 'fax2', 'mobile1', 'mobile2', 'mobile3',
            'website', 'agent_phone1', 'agent_phone2', 'agent_phone3',
            'agent_fax1', 'agent_fax2', 'agent_fax3',
            'agent_mobile1', 'agent_mobile2', 'agent_mobile3',
            'committee_no', 'registration_status', 'registration_status_legacy',
            'certified_class', 'data_entry_user', 'free_zone', 'cd_vendor',
            'commercial_register', 'tax_card', 'vat_number', 'owner_name',
            'capital_currency', 'country', 'category', 'address', 'contact_name', 'sab_no',
        ];

        foreach ($trimmed as $field) {
            $request->merge([
                $field => trim((string) $request->input($field)) ?: null,
            ]);
        }

        foreach (['email1', 'email2', 'email3', 'agent_email1', 'agent_email2', 'agent_email3',
            'agency_documents', 'notes', 'special_notes', 'extra_notes', 'committee_text'] as $field) {
            $request->merge([
                $field => trim((string) $request->input($field)) ?: null,
            ]);
        }

        $emailRule = function (string $attribute, mixed $value, callable $fail): void {
            // Access stores messy multi-address strings; only reject obvious garbage.
            if (is_string($value) && $value !== '' && ! preg_match('/[@#]/', $value)) {
                $fail(__('app.vendors.validation.invalid_email', ['field' => $attribute]));
            }
        };

        return $request->validate([
            'name_ar' => ['required_without_all:name_en,name', 'nullable', 'string', 'max:75'],
            'name_en' => ['nullable', 'string', 'max:75'],
            'name' => ['nullable', 'string', 'max:255'],
            'supplier_id' => [
                'nullable', 'integer', 'min:1',
                Rule::unique('vendors', 'supplier_id')->ignore($vendor?->id),
            ],
            'code' => [
                'nullable', 'string', 'max:30',
                Rule::unique('vendors', 'code')->ignore($vendor?->id),
            ],
            'brochure_no' => ['nullable', 'integer', 'min:0'],
            'brochure_type' => ['nullable', Rule::in(['محلى', 'خارجى'])],
            'agent_name' => ['nullable', 'string', 'max:50'],
            'phone1' => ['nullable', 'string', 'max:50'],
            'phone2' => ['nullable', 'string', 'max:50'],
            'phone3' => ['nullable', 'string', 'max:50'],
            'fax1' => ['nullable', 'string', 'max:50'],
            'fax2' => ['nullable', 'string', 'max:50'],
            'mobile1' => ['nullable', 'string', 'max:50'],
            'mobile2' => ['nullable', 'string', 'max:50'],
            'mobile3' => ['nullable', 'string', 'max:50'],
            'email1' => ['nullable', 'string', 'max:1000', $emailRule],
            'email2' => ['nullable', 'string', 'max:1000', $emailRule],
            'email3' => ['nullable', 'string', 'max:1000', $emailRule],
            'website' => ['nullable', 'string', 'max:255'],
            'agent_phone1' => ['nullable', 'string', 'max:50'],
            'agent_phone2' => ['nullable', 'string', 'max:50'],
            'agent_phone3' => ['nullable', 'string', 'max:50'],
            'agent_fax1' => ['nullable', 'string', 'max:50'],
            'agent_fax2' => ['nullable', 'string', 'max:50'],
            'agent_fax3' => ['nullable', 'string', 'max:50'],
            'agent_mobile1' => ['nullable', 'string', 'max:50'],
            'agent_mobile2' => ['nullable', 'string', 'max:50'],
            'agent_mobile3' => ['nullable', 'string', 'max:50'],
            'agent_email1' => ['nullable', 'string', 'max:1000', $emailRule],
            'agent_email2' => ['nullable', 'string', 'max:1000', $emailRule],
            'agent_email3' => ['nullable', 'string', 'max:1000', $emailRule],
            'agent_address' => ['nullable', 'string', 'max:255'],
            'committee_date' => ['nullable', 'date'],
            'committee_no' => ['nullable', 'string', 'max:50'],
            'registration_status' => ['nullable', 'string', 'max:50'],
            'registration_status_legacy' => ['nullable', 'string', 'max:50'],
            'certified_class' => ['nullable', 'string', 'max:20'],
            'data_entry_user' => ['nullable', 'string', 'max:255'],
            'free_zone' => ['nullable', 'string', 'max:255'],
            'cd_vendor' => ['nullable', 'string', 'max:255'],
            'commercial_register' => ['nullable', 'string', 'max:50'],
            'commercial_register_expiry' => ['nullable', 'date'],
            'tax_card' => ['nullable', 'string', 'max:50'],
            'tax_card_expiry' => ['nullable', 'date'],
            'vat_number' => ['nullable', 'string', 'max:50'],
            'vat_expiry' => ['nullable', 'date'],
            'owner_name' => ['nullable', 'string', 'max:50'],
            'capital_amount' => ['nullable', 'integer', 'min:0'],
            'capital_currency' => ['nullable', 'string', 'max:50'],
            'agency_documents' => ['nullable', 'string', 'max:2000'],
            'local_registered' => ['nullable', 'boolean'],
            'foreign_registered' => ['nullable', 'boolean'],
            'stopped' => ['nullable', 'boolean'],
            'struck_off' => ['nullable', 'boolean'],
            'special_notes' => ['nullable', 'string', 'max:2000'],
            'extra_notes' => ['nullable', 'string', 'max:2000'],
            'committee_text' => ['nullable', 'string', 'max:2000'],
            'sab_no' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:80'],
            'category' => ['nullable', 'string', 'max:80'],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [], [
            'name_ar' => __('app.vendors.fields.name_ar'),
            'name_en' => __('app.vendors.fields.name_en'),
            'supplier_id' => __('app.vendors.fields.supplier_id'),
            'code' => __('app.vendors.fields.code'),
        ]) + [
            'local_registered' => $request->boolean('local_registered'),
            'foreign_registered' => $request->boolean('foreign_registered'),
            'stopped' => $request->boolean('stopped'),
            'struck_off' => $request->boolean('struck_off'),
        ];
    }

    /**
     * Distinct non-empty values used to populate the list filters.
     *
     * @return array<int, string>
     */
    private function distinctValues(string $column): array
    {
        return Vendor::query()
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->all();
    }

    private function nextCode(): string
    {
        return 'V-'.str_pad((string) (Vendor::max('id') + 1), 4, '0', STR_PAD_LEFT);
    }
}

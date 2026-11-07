<?php

namespace App\Http\Controllers;

use App\Models\Rfq;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $vendors = Vendor::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'ilike', "%{$search}%")
                        ->orWhere('code', 'ilike', "%{$search}%")
                        ->orWhere('contact_name', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%")
                        ->orWhere('category', 'ilike', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $pendingRfqs = $request->user()->hasPermission('rfq.approve')
            ? Rfq::query()->where('status', Rfq::STATUS_PENDING)->with('requestedBy')->latest('id')->get()
            : collect();

        $readyRfqs = $request->user()->hasPermission('rfq.assign')
            ? Rfq::query()->where('status', Rfq::STATUS_APPROVED)->with('requestedBy')->latest('id')->get()
            : collect();

        return view('vendors.index', compact('vendors', 'search', 'pendingRfqs', 'readyRfqs'));
    }

    public function create(): View
    {
        return view('vendors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateVendor($request);

        Vendor::create($validated + [
            'code' => $validated['code'] ?: $this->nextCode(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('vendors.index')
            ->with('status', __('app.vendors.messages.created', ['name' => $validated['name']]));
    }

    public function edit(Vendor $vendor): View
    {
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor): RedirectResponse
    {
        $validated = $this->validateVendor($request, $vendor);

        $vendor->update($validated + [
            'code' => $validated['code'] ?: $vendor->code,
        ]);

        return redirect()
            ->route('vendors.index')
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
     * @return array<string, mixed>
     */
    private function validateVendor(Request $request, ?Vendor $vendor = null): array
    {
        $request->merge([
            'code' => Str::upper(trim((string) $request->input('code'))) ?: null,
            'contact_name' => trim((string) $request->input('contact_name')) ?: null,
            'email' => Str::lower(trim((string) $request->input('email'))) ?: null,
            'phone' => trim((string) $request->input('phone')) ?: null,
            'country' => trim((string) $request->input('country')) ?: null,
            'category' => trim((string) $request->input('category')) ?: null,
            'address' => trim((string) $request->input('address')) ?: null,
            'notes' => trim((string) $request->input('notes')) ?: null,
        ]);

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'nullable', 'string', 'max:30',
                Rule::unique('vendors', 'code')->ignore($vendor?->id),
            ],
            'type' => ['required', Rule::in(array_keys(Vendor::types()))],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'country' => ['nullable', 'string', 'max:80'],
            'category' => ['nullable', 'string', 'max:80'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_keys(Vendor::statuses()))],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function nextCode(): string
    {
        return 'V-'.str_pad((string) (Vendor::max('id') + 1), 4, '0', STR_PAD_LEFT);
    }
}

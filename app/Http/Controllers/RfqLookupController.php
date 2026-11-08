<?php

namespace App\Http\Controllers;

use App\Models\RfqCurrency;
use App\Models\RfqDepartment;
use App\Models\RfqLocation;
use App\Models\RfqLookup;
use App\Models\RfqPriority;
use App\Models\RfqUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RfqLookupController extends Controller
{
    private const LOOKUPS = [
        'departments' => ['model' => RfqDepartment::class, 'label' => 'departments', 'permission' => 'departments'],
        'locations' => ['model' => RfqLocation::class, 'label' => 'locations', 'permission' => 'locations'],
        'priorities' => ['model' => RfqPriority::class, 'label' => 'priorities', 'permission' => 'priorities'],
        'currencies' => ['model' => RfqCurrency::class, 'label' => 'currencies', 'permission' => 'currencies'],
        'units' => ['model' => RfqUnit::class, 'label' => 'units', 'permission' => 'units'],
    ];

    public function administration(): View
    {
        return view('administration.index', ['lookups' => $this->availableLookups(request())]);
    }

    public function index(Request $request, string $type): View
    {
        $definition = $this->definition($type);
        $search = trim((string) $request->query('q'));
        $items = $definition['model']::query()
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query->where('code', 'ilike', "%{$search}%")->orWhere('name', 'ilike', "%{$search}%")))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('administration.lookup-index', compact('items', 'search', 'type', 'definition'));
    }

    public function create(string $type): View
    {
        return view('administration.lookup-form', ['type' => $type, 'definition' => $this->definition($type), 'item' => null]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $definition = $this->definition($type);
        $validated = $this->validateLookup($request, $definition['model']);
        $definition['model']::create($validated + ['created_by' => $request->user()->id]);
        return redirect()->route('administration.lookup.index', $type)->with('status', __('app.administration.messages.created', ['name' => $validated['name']]));
    }

    public function edit(string $type, int $id): View
    {
        $definition = $this->definition($type);
        return view('administration.lookup-form', ['type' => $type, 'definition' => $definition, 'item' => $definition['model']::findOrFail($id)]);
    }

    public function update(Request $request, string $type, int $id): RedirectResponse
    {
        $definition = $this->definition($type);
        $item = $definition['model']::findOrFail($id);
        $validated = $this->validateLookup($request, $definition['model'], $item->id);
        $item->update($validated);
        return redirect()->route('administration.lookup.index', $type)->with('status', __('app.administration.messages.updated', ['name' => $validated['name']]));
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        $definition = $this->definition($type);
        $item = $definition['model']::findOrFail($id);
        if ($this->isInUse($type, $item->id)) return back()->with('error', __('app.administration.messages.in_use'));
        $name = $item->name; $item->delete();
        return redirect()->route('administration.lookup.index', $type)->with('status', __('app.administration.messages.deleted', ['name' => $name]));
    }

    private function validateLookup(Request $request, string $model, ?int $ignore = null): array
    {
        $table = (new $model)->getTable();
        return $request->validate(['code' => ['required', 'string', 'max:40', Rule::unique($table, 'code')->ignore($ignore)], 'name' => ['required', 'string', 'max:120'], 'status' => ['required', Rule::in(array_keys(RfqLookup::statuses()))]]);
    }

    private function definition(string $type): array
    {
        abort_unless(isset(self::LOOKUPS[$type]), 404);
        return self::LOOKUPS[$type] + ['title' => __('app.administration.'.$type), 'singular' => Str::singular(__('app.administration.'.$type))];
    }

    private function availableLookups(Request $request): array
    {
        return collect(self::LOOKUPS)->filter(fn ($definition) => $request->user()->hasPermission($definition['permission'].'.view'))->mapWithKeys(fn ($definition, $type) => [$type => $this->definition($type)])->all();
    }

    private function isInUse(string $type, int $id): bool
    {
        return match ($type) {
            'departments' => \App\Models\Rfq::where('department_id', $id)->exists(),
            'locations' => \App\Models\Rfq::where('location_id', $id)->exists(),
            'priorities' => \App\Models\Rfq::where('priority_id', $id)->exists(),
            'currencies' => \App\Models\Rfq::where(fn ($q) => $q->where('currency_id', $id)->orWhere('budget_currency_id', $id))->exists(),
            'units' => \App\Models\RfqItem::where('unit_id', $id)->exists(),
            default => false,
        };
    }
}

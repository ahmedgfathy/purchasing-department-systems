<?php

namespace App\Http\Controllers;

use App\Models\OrderType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTypeController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $orderTypes = OrderType::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('code', 'ilike', "%{$search}%")
                        ->orWhere('name', 'ilike', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('dropdowns.order_types.index', compact('orderTypes', 'search'));
    }

    public function create(): View
    {
        return view('dropdowns.order_types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:order_types,code'],
            'name' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:20'],
        ]);

        OrderType::create($validated + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('order_types.index')
            ->with('status', __('app.order_types.messages.created', ['name' => $validated['name']]));
    }

    public function edit(OrderType $orderType): View
    {
        return view('dropdowns.order_types.edit', compact('orderType'));
    }

    public function update(Request $request, OrderType $orderType): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:order_types,code,'.$orderType->id],
            'name' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:20'],
        ]);

        $orderType->update($validated);

        return redirect()
            ->route('order_types.index')
            ->with('status', __('app.order_types.messages.updated', ['name' => $orderType->name]));
    }

    public function destroy(OrderType $orderType): RedirectResponse
    {
        if ($orderType->rfqs()->exists()) {
            return back()->with('error', __('app.order_types.messages.in_use'));
        }

        $name = $orderType->name;
        $orderType->delete();

        return redirect()->route('order_types.index')->with('status', __('app.order_types.messages.deleted', ['name' => $name]));
    }
}

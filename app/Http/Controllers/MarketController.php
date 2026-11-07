<?php

namespace App\Http\Controllers;

use App\Models\Market;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $markets = Market::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('code', 'ilike', "%{$search}%")
                        ->orWhere('name', 'ilike', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('dropdowns.markets.index', compact('markets', 'search'));
    }

    public function create(): View
    {
        return view('dropdowns.markets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:markets,code'],
            'name' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:20'],
        ]);

        Market::create($validated + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('markets.index')
            ->with('status', __('app.markets.messages.created', ['name' => $validated['name']]));
    }

    public function edit(Market $market): View
    {
        return view('dropdowns.markets.edit', compact('market'));
    }

    public function update(Request $request, Market $market): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:markets,code,'.$market->id],
            'name' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:20'],
        ]);

        $market->update($validated);

        return redirect()
            ->route('markets.index')
            ->with('status', __('app.markets.messages.updated', ['name' => $market->name]));
    }

    public function destroy(Market $market): RedirectResponse
    {
        if ($market->rfqs()->exists()) {
            return back()->with('error', __('app.markets.messages.in_use'));
        }

        $name = $market->name;
        $market->delete();

        return redirect()->route('markets.index')->with('status', __('app.markets.messages.deleted', ['name' => $name]));
    }
}

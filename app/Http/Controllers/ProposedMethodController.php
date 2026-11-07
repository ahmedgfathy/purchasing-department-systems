<?php

namespace App\Http\Controllers;

use App\Models\ProposedMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProposedMethodController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $proposedMethods = ProposedMethod::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('code', 'ilike', "%{$search}%")
                        ->orWhere('name', 'ilike', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('dropdowns.proposed_methods.index', compact('proposedMethods', 'search'));
    }

    public function create(): View
    {
        return view('dropdowns.proposed_methods.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:proposed_methods,code'],
            'name' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:20'],
        ]);

        ProposedMethod::create($validated + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('proposed_methods.index')
            ->with('status', __('app.proposed_methods.messages.created', ['name' => $validated['name']]));
    }

    public function edit(ProposedMethod $proposedMethod): View
    {
        return view('dropdowns.proposed_methods.edit', compact('proposedMethod'));
    }

    public function update(Request $request, ProposedMethod $proposedMethod): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:proposed_methods,code,'.$proposedMethod->id],
            'name' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:20'],
        ]);

        $proposedMethod->update($validated);

        return redirect()
            ->route('proposed_methods.index')
            ->with('status', __('app.proposed_methods.messages.updated', ['name' => $proposedMethod->name]));
    }

    public function destroy(ProposedMethod $proposedMethod): RedirectResponse
    {
        if ($proposedMethod->rfqs()->exists()) {
            return back()->with('error', __('app.proposed_methods.messages.in_use'));
        }

        $name = $proposedMethod->name;
        $proposedMethod->delete();

        return redirect()->route('proposed_methods.index')->with('status', __('app.proposed_methods.messages.deleted', ['name' => $name]));
    }
}

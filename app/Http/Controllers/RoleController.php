<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $roles = Role::query()
            ->withCount(['users', 'permissions'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'ilike', "%{$search}%")
                        ->orWhere('slug', 'ilike', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('roles.index', compact('roles', 'search'));
    }

    public function create(): View
    {
        $permissions = $this->permissionGroups();

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:roles,slug'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['slug'] ?: $validated['name']),
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        $permissionIds = $validated['permissions'] ?? [];
        $role->permissions()->sync($permissionIds);

        AuditLogger::event(
            'permissions_synced',
            $role,
            [],
            ['permissions' => $this->permissionNames($permissionIds)],
        );

        return redirect()
            ->route('roles.index')
            ->with('status', __('app.messages.role_created', ['name' => $role->displayName()]));
    }

    public function edit(Role $role): View
    {
        $permissions = $this->permissionGroups();
        $role->load('permissions');

        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable', 'string', 'max:255', 'alpha_dash',
                Rule::unique('roles', 'slug')->ignore($role->id),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $oldPermissions = $role->permissions->pluck('name')->sort()->values()->all();

        $role->fill([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // System roles keep their slug so core authorization checks stay intact.
        if (! $role->is_system) {
            $slug = $validated['slug'] ?: $validated['name'];

            if (Str::slug($slug) !== $role->slug) {
                $role->slug = $this->uniqueSlug($slug, $role->id);
            }
        }

        $role->save();

        $permissionIds = $validated['permissions'] ?? [];
        $role->permissions()->sync($permissionIds);

        $newPermissions = $this->permissionNames($permissionIds);

        if ($oldPermissions !== $newPermissions) {
            AuditLogger::event(
                'permissions_synced',
                $role,
                ['permissions' => $oldPermissions],
                ['permissions' => $newPermissions],
            );
        }

        return redirect()
            ->route('roles.index')
            ->with('status', __('app.messages.role_updated', ['name' => $role->displayName()]));
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->with('error', __('app.messages.system_role_delete'));
        }

        $name = $role->displayName();
        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('status', __('app.messages.role_deleted', ['name' => $name]));
    }

    private function permissionGroups()
    {
        return Permission::query()
            ->get()
            ->groupBy(fn (Permission $permission) => $permission->displayGroup())
            ->sortKeys();
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'role';
        $slug = $base;
        $suffix = 2;

        while (Role::where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    /**
     * @param  array<int, int>  $permissionIds
     * @return array<int, string>
     */
    private function permissionNames(array $permissionIds): array
    {
        return Permission::whereIn('id', $permissionIds)
            ->orderBy('name')
            ->get()
            ->map(fn (Permission $permission) => $permission->displayName())
            ->all();
    }
}

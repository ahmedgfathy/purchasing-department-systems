<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $users = User::query()
            ->with('roles')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'ilike', "%{$search}%")
                        ->orWhere('employee_id', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%")
                        ->orWhere('phone', 'ilike', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users', 'search'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'employee_id' => trim((string) $request->input('employee_id')),
            'email' => Str::lower(trim((string) $request->input('email'))) ?: null,
            'phone' => trim((string) $request->input('phone')) ?: null,
            'job_title' => trim((string) $request->input('job_title')) ?: null,
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'employee_id' => ['required', 'string', 'max:50', 'unique:users,employee_id'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'employee_id' => $validated['employee_id'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'job_title' => $validated['job_title'],
            'password' => $validated['password'],
        ]);

        $user->roles()->sync($validated['roles']);

        AuditLogger::event(
            'roles_synced',
            $user,
            [],
            ['roles' => $this->roleNames($validated['roles'])],
        );

        return redirect()
            ->route('users.index')
            ->with('status', __('app.messages.user_created', ['name' => $user->name]));
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();
        $user->load('roles');

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->merge([
            'employee_id' => trim((string) $request->input('employee_id')),
            'email' => Str::lower(trim((string) $request->input('email'))) ?: null,
            'phone' => trim((string) $request->input('phone')) ?: null,
            'job_title' => trim((string) $request->input('job_title')) ?: null,
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'employee_id' => [
                'required', 'string', 'max:50',
                Rule::unique('users', 'employee_id')->ignore($user->id),
            ],
            'email' => [
                'nullable', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:40'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        if ($this->wouldRemoveLastSuperAdmin($user, $validated['roles'])) {
            throw ValidationException::withMessages([
                'roles' => __('app.messages.last_super_admin_role'),
            ]);
        }

        $oldRoles = $user->roles->pluck('name')->all();

        $user->fill([
            'name' => $validated['name'],
            'employee_id' => $validated['employee_id'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'job_title' => $validated['job_title'],
        ]);

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();
        $user->roles()->sync($validated['roles']);

        $newRoles = $this->roleNames($validated['roles']);

        if ($oldRoles !== $newRoles) {
            AuditLogger::event(
                'roles_synced',
                $user,
                ['roles' => $oldRoles],
                ['roles' => $newRoles],
            );
        }

        return redirect()
            ->route('users.index')
            ->with('status', __('app.messages.user_updated', ['name' => $user->name]));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->with('error', __('app.messages.cannot_delete_self'));
        }

        $superAdminId = Role::where('slug', Role::SUPER_ADMIN)->value('id');

        if ($superAdminId && $user->roles->contains('id', $superAdminId)) {
            $superAdmins = User::whereHas('roles', fn ($query) => $query->where('slug', Role::SUPER_ADMIN))->count();

            if ($superAdmins <= 1) {
                return back()->with('error', __('app.messages.last_super_admin_delete'));
            }
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('status', __('app.messages.user_deleted', ['name' => $name]));
    }

    /**
     * @param  array<int, int>  $roleIds
     */
    private function wouldRemoveLastSuperAdmin(User $user, array $roleIds): bool
    {
        $superAdminId = Role::where('slug', Role::SUPER_ADMIN)->value('id');

        if (! $superAdminId || ! $user->roles->contains('id', $superAdminId)) {
            return false;
        }

        if (in_array((int) $superAdminId, array_map('intval', $roleIds), true)) {
            return false;
        }

        return User::whereHas('roles', fn ($query) => $query->where('slug', Role::SUPER_ADMIN))->count() <= 1;
    }

    /**
     * @param  array<int, int>  $roleIds
     * @return array<int, string>
     */
    private function roleNames(array $roleIds): array
    {
        return Role::whereIn('id', $roleIds)
            ->orderBy('name')
            ->get()
            ->map(fn (Role $role) => $role->displayName())
            ->all();
    }
}

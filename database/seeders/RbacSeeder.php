<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'View dashboard', 'slug' => 'dashboard.view', 'group' => 'Dashboard', 'description' => 'Open the dashboard.'],
            ['name' => 'View users', 'slug' => 'users.view', 'group' => 'Users', 'description' => 'List and search user accounts.'],
            ['name' => 'Create users', 'slug' => 'users.create', 'group' => 'Users', 'description' => 'Add new user accounts.'],
            ['name' => 'Update users', 'slug' => 'users.update', 'group' => 'Users', 'description' => 'Edit accounts and role assignments.'],
            ['name' => 'Delete users', 'slug' => 'users.delete', 'group' => 'Users', 'description' => 'Remove user accounts.'],
            ['name' => 'View roles', 'slug' => 'roles.view', 'group' => 'Roles', 'description' => 'List roles and their permissions.'],
            ['name' => 'Create roles', 'slug' => 'roles.create', 'group' => 'Roles', 'description' => 'Add new roles.'],
            ['name' => 'Update roles', 'slug' => 'roles.update', 'group' => 'Roles', 'description' => 'Edit roles and their permissions.'],
            ['name' => 'Delete roles', 'slug' => 'roles.delete', 'group' => 'Roles', 'description' => 'Remove custom roles.'],
            ['name' => 'View audit trail', 'slug' => 'audit.view', 'group' => 'Audit', 'description' => 'Read the application audit log.'],
            ['name' => 'View RFQ departments', 'slug' => 'departments.view', 'group' => 'RFQ dropdowns', 'description' => 'View department options.'],
            ['name' => 'Manage RFQ departments', 'slug' => 'departments.manage', 'group' => 'RFQ dropdowns', 'description' => 'Create and manage department options.'],
            ['name' => 'View RFQ locations', 'slug' => 'locations.view', 'group' => 'RFQ dropdowns', 'description' => 'View location options.'],
            ['name' => 'Manage RFQ locations', 'slug' => 'locations.manage', 'group' => 'RFQ dropdowns', 'description' => 'Create and manage location options.'],
            ['name' => 'View RFQ priorities', 'slug' => 'priorities.view', 'group' => 'RFQ dropdowns', 'description' => 'View priority options.'],
            ['name' => 'Manage RFQ priorities', 'slug' => 'priorities.manage', 'group' => 'RFQ dropdowns', 'description' => 'Create and manage priority options.'],
            ['name' => 'View RFQ currencies', 'slug' => 'currencies.view', 'group' => 'RFQ dropdowns', 'description' => 'View currency options.'],
            ['name' => 'Manage RFQ currencies', 'slug' => 'currencies.manage', 'group' => 'RFQ dropdowns', 'description' => 'Create and manage currency options.'],
            ['name' => 'View RFQ units', 'slug' => 'units.view', 'group' => 'RFQ dropdowns', 'description' => 'View unit options.'],
            ['name' => 'Manage RFQ units', 'slug' => 'units.manage', 'group' => 'RFQ dropdowns', 'description' => 'Create and manage unit options.'],
            ['name' => 'View RFQs', 'slug' => 'rfq.view', 'group' => 'RFQ', 'description' => 'View requests for quotation.'],
            ['name' => 'Create RFQs', 'slug' => 'rfq.create', 'group' => 'RFQ', 'description' => 'Submit a request for quotation.'],
            ['name' => 'Update RFQs', 'slug' => 'rfq.update', 'group' => 'RFQ', 'description' => 'Edit pending or rejected requests for quotation.'],
            ['name' => 'Delete RFQs', 'slug' => 'rfq.delete', 'group' => 'RFQ', 'description' => 'Delete pending or rejected requests for quotation.'],
            ['name' => 'Manage all RFQs', 'slug' => 'rfq.manage', 'group' => 'RFQ', 'description' => 'Edit or delete any pending or rejected RFQ.'],
            ['name' => 'Approve or reject RFQs', 'slug' => 'rfq.approve', 'group' => 'RFQ', 'description' => 'Review and approve or reject submitted RFQs.'],
            ['name' => 'Assign vendors to RFQs', 'slug' => 'rfq.assign', 'group' => 'RFQ', 'description' => 'Select vendors for approved RFQs.'],
            ['name' => 'View vendor list', 'slug' => 'vendors.view', 'group' => 'Vendors', 'description' => 'View the vendor directory and RFQ review queues.'],
            ['name' => 'Manage vendors', 'slug' => 'vendors.manage', 'group' => 'Vendors', 'description' => 'Create and update vendors in the directory.'],
            ['name' => 'View local purchasing', 'slug' => 'purchases.local.view', 'group' => 'Purchasing', 'description' => 'Open the local purchasing workspace.'],
            ['name' => 'Create local purchases', 'slug' => 'purchases.local.create', 'group' => 'Local purchasing', 'description' => 'Create local purchase records when that workflow is enabled.'],
            ['name' => 'Update local purchases', 'slug' => 'purchases.local.update', 'group' => 'Local purchasing', 'description' => 'Update local purchase records when that workflow is enabled.'],
            ['name' => 'Delete local purchases', 'slug' => 'purchases.local.delete', 'group' => 'Local purchasing', 'description' => 'Delete local purchase records when that workflow is enabled.'],
            ['name' => 'View foreign purchasing', 'slug' => 'purchases.foreign.view', 'group' => 'Purchasing', 'description' => 'Open the foreign purchasing workspace.'],
            ['name' => 'Create foreign purchases', 'slug' => 'purchases.foreign.create', 'group' => 'Foreign purchasing', 'description' => 'Create foreign purchase records when that workflow is enabled.'],
            ['name' => 'Update foreign purchases', 'slug' => 'purchases.foreign.update', 'group' => 'Foreign purchasing', 'description' => 'Update foreign purchase records when that workflow is enabled.'],
            ['name' => 'Delete foreign purchases', 'slug' => 'purchases.foreign.delete', 'group' => 'Foreign purchasing', 'description' => 'Delete foreign purchase records when that workflow is enabled.'],
            ['name' => 'View local market', 'slug' => 'market.view', 'group' => 'Purchasing', 'description' => 'Open the local market workspace.'],
            ['name' => 'Create local market records', 'slug' => 'market.create', 'group' => 'Local market', 'description' => 'Create local market records when that workflow is enabled.'],
            ['name' => 'Update local market records', 'slug' => 'market.update', 'group' => 'Local market', 'description' => 'Update local market records when that workflow is enabled.'],
            ['name' => 'Delete local market records', 'slug' => 'market.delete', 'group' => 'Local market', 'description' => 'Delete local market records when that workflow is enabled.'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
        }

        $allPermissions = Permission::pluck('id');

        $superAdmin = Role::updateOrCreate(
            ['slug' => Role::SUPER_ADMIN],
            [
                'name' => 'Super Administrator',
                'description' => 'Full, unrestricted access to every part of the application.',
                'is_system' => true,
            ],
        );
        $superAdmin->permissions()->sync($allPermissions);

        $admin = Role::updateOrCreate(
            ['slug' => Role::ADMIN],
            [
                'name' => 'Administrator',
                'description' => 'Manages users, roles and audit records.',
                'is_system' => true,
            ],
        );
        $admin->permissions()->sync($allPermissions);

        $roles = [
            [
                'slug' => 'user-manager',
                'name' => 'User Manager',
                'description' => 'Creates and maintains user accounts.',
                'permissions' => ['dashboard.view', 'users.view', 'users.create', 'users.update', 'users.delete'],
            ],
            [
                'slug' => 'auditor',
                'name' => 'Auditor',
                'description' => 'Read-only access to the audit trail.',
                'permissions' => ['dashboard.view', 'audit.view'],
            ],
            [
                'slug' => 'viewer',
                'name' => 'Viewer',
                'description' => 'Can sign in and view the dashboard only.',
                'permissions' => ['dashboard.view'],
            ],
            [
                'slug' => 'rfq-requester',
                'name' => 'RFQ Requester',
                'description' => 'Submits and maintains purchase requests before approval.',
                'permissions' => ['dashboard.view', 'rfq.view', 'rfq.create', 'rfq.update', 'rfq.delete', 'departments.view', 'locations.view', 'priorities.view', 'currencies.view', 'units.view'],
            ],
            [
                'slug' => 'vendor-list-officer',
                'name' => 'Vendor List Officer',
                'description' => 'Reviews and approves or rejects submitted RFQs.',
                'permissions' => ['dashboard.view', 'rfq.view', 'rfq.approve', 'vendors.view', 'departments.view', 'locations.view', 'priorities.view', 'currencies.view', 'units.view'],
            ],
            [
                'slug' => 'vendor-list-manager',
                'name' => 'Vendor List Manager',
                'description' => 'Assigns suitable vendors to approved RFQs.',
                'permissions' => ['dashboard.view', 'rfq.view', 'rfq.assign', 'vendors.view', 'departments.view', 'locations.view', 'priorities.view', 'currencies.view', 'units.view', 'departments.manage', 'locations.manage', 'priorities.manage', 'currencies.manage', 'units.manage'],
            ],
            [
                'slug' => 'local-purchasing',
                'name' => 'Local Purchasing',
                'description' => 'Can access the local purchasing workspace.',
                'permissions' => ['dashboard.view', 'purchases.local.view'],
            ],
            [
                'slug' => 'foreign-purchasing',
                'name' => 'Foreign Purchasing',
                'description' => 'Can access the foreign purchasing workspace.',
                'permissions' => ['dashboard.view', 'purchases.foreign.view'],
            ],
            [
                'slug' => 'local-market',
                'name' => 'Local Market',
                'description' => 'Can access the local market workspace.',
                'permissions' => ['dashboard.view', 'market.view'],
            ],
        ];

        foreach ($roles as $definition) {
            $role = Role::updateOrCreate(
                ['slug' => $definition['slug']],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'is_system' => false,
                ],
            );

            $role->permissions()->sync(
                Permission::whereIn('slug', $definition['permissions'])->pluck('id'),
            );
        }

        $adminUser = User::updateOrCreate(
            ['email' => 'admin@ppd.local'],
            [
                'name' => 'System Administrator',
                'employee_id' => '2669',
                'job_title' => 'System Administrator',
                'password' => 'Admin@12345',
                'email_verified_at' => now(),
            ],
        );
        $adminUser->roles()->sync([$superAdmin->id]);
    }
}

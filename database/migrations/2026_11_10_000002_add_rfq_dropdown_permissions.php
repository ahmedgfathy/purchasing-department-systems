<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach ([
            ['name' => 'View markets', 'slug' => 'markets.view', 'group' => 'RFQ Dropdowns', 'description' => 'View market dropdown options.'],
            ['name' => 'Manage markets', 'slug' => 'markets.manage', 'group' => 'RFQ Dropdowns', 'description' => 'Create and manage market dropdown options.'],
            ['name' => 'View order types', 'slug' => 'order_types.view', 'group' => 'RFQ Dropdowns', 'description' => 'View order type dropdown options.'],
            ['name' => 'Manage order types', 'slug' => 'order_types.manage', 'group' => 'RFQ Dropdowns', 'description' => 'Create and manage order type dropdown options.'],
            ['name' => 'View proposed methods', 'slug' => 'proposed_methods.view', 'group' => 'RFQ Dropdowns', 'description' => 'View proposed method dropdown options.'],
            ['name' => 'Manage proposed methods', 'slug' => 'proposed_methods.manage', 'group' => 'RFQ Dropdowns', 'description' => 'Create and manage proposed method dropdown options.'],
        ] as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $permission['slug']],
                $permission + ['created_at' => $now, 'updated_at' => $now],
            );
        }

        $permissionIds = DB::table('permissions')->whereIn('slug', [
            'markets.view', 'markets.manage',
            'order_types.view', 'order_types.manage',
            'proposed_methods.view', 'proposed_methods.manage',
        ])->pluck('id');

        // Admin and super-admin get all
        $adminRoleIds = DB::table('roles')->whereIn('slug', ['admin', 'super-admin'])->pluck('id');
        foreach ($adminRoleIds as $roleId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }

        // Vendor List Manager can manage all dropdowns
        $managerId = DB::table('roles')->where('slug', 'vendor-list-manager')->value('id');
        if ($managerId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $managerId,
                    'permission_id' => $permissionId,
                ]);
            }
        }

        // RFQ Requester and Vendor List Officer can view all dropdowns
        $viewerRoleIds = DB::table('roles')->whereIn('slug', ['rfq-requester', 'vendor-list-officer'])->pluck('id');
        $viewPermissionIds = DB::table('permissions')->whereIn('slug', [
            'markets.view', 'order_types.view', 'proposed_methods.view',
        ])->pluck('id');
        foreach ($viewerRoleIds as $roleId) {
            foreach ($viewPermissionIds as $permissionId) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }

    public function down(): void
    {
        $permissionIds = DB::table('permissions')->whereIn('slug', [
            'markets.view', 'markets.manage',
            'order_types.view', 'order_types.manage',
            'proposed_methods.view', 'proposed_methods.manage',
        ])->pluck('id');

        DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};

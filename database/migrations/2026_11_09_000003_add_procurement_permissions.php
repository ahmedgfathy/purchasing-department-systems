<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $permissions = [
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

    public function up(): void
    {
        $now = now();

        foreach ($this->permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $permission['slug']],
                $permission + ['created_at' => $now, 'updated_at' => $now],
            );
        }

        $permissionIds = DB::table('permissions')->whereIn('slug', array_column($this->permissions, 'slug'))->pluck('id');
        $roleIds = DB::table('roles')->whereIn('slug', ['admin', 'super-admin'])->pluck('id');

        foreach ($roleIds as $roleId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }

    public function down(): void
    {
        $permissionIds = DB::table('permissions')->whereIn('slug', array_column($this->permissions, 'slug'))->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};

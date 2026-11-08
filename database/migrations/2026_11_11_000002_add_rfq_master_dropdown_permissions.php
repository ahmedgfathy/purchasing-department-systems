<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        foreach (['departments', 'locations', 'priorities', 'currencies', 'units'] as $lookup) {
            foreach ([['view', 'View '.$lookup], ['manage', 'Manage '.$lookup]] as [$action, $name]) {
                DB::table('permissions')->updateOrInsert(['slug' => $lookup.'.'.$action], ['name' => $name, 'group' => 'RFQ dropdowns', 'description' => $name.'.', 'created_at' => $now, 'updated_at' => $now]);
            }
        }
        $ids = DB::table('permissions')->where(function ($query) {
            foreach (['departments', 'locations', 'priorities', 'currencies', 'units'] as $lookup) $query->orWhereIn('slug', [$lookup.'.view', $lookup.'.manage']);
        })->pluck('id');
        foreach (DB::table('roles')->whereIn('slug', ['admin', 'super-admin'])->pluck('id') as $roleId) foreach ($ids as $permissionId) DB::table('permission_role')->insertOrIgnore(['role_id' => $roleId, 'permission_id' => $permissionId]);
        foreach (DB::table('roles')->whereIn('slug', ['rfq-requester', 'vendor-list-officer'])->pluck('id') as $roleId) foreach ($ids as $permissionId) if (str_ends_with((string) DB::table('permissions')->where('id', $permissionId)->value('slug'), '.view')) DB::table('permission_role')->insertOrIgnore(['role_id' => $roleId, 'permission_id' => $permissionId]);
        foreach (DB::table('roles')->where('slug', 'vendor-list-manager')->pluck('id') as $roleId) foreach ($ids as $permissionId) DB::table('permission_role')->insertOrIgnore(['role_id' => $roleId, 'permission_id' => $permissionId]);
    }

    public function down(): void
    {
        $slugs = [];
        foreach (['departments', 'locations', 'priorities', 'currencies', 'units'] as $lookup) $slugs = array_merge($slugs, [$lookup.'.view', $lookup.'.manage']);
        $ids = DB::table('permissions')->whereIn('slug', $slugs)->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $ids)->delete(); DB::table('permissions')->whereIn('id', $ids)->delete();
    }
};

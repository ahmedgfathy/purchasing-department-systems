<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_id', 50)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('job_title', 120)->nullable();
            $table->string('avatar_path')->nullable();
        });

        $adminId = DB::table('users')->where('email', 'admin@ppd.local')->value('id');

        foreach (DB::table('users')->orderBy('id')->get() as $user) {
            DB::table('users')->where('id', $user->id)->update([
                'employee_id' => $user->id === $adminId ? '2669' : 'EMP-'.$user->id,
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_id', 50)->nullable(false)->change();
            $table->unique('employee_id');
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['employee_id']);
            $table->dropColumn(['employee_id', 'phone', 'job_title', 'avatar_path']);
        });
    }
};

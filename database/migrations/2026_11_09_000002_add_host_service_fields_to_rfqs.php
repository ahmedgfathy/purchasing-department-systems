<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rfq_items', function (Blueprint $table) {
            $table->string('url', 2048)->nullable()->after('description');
            $table->string('host', 120)->nullable()->after('url');
            $table->string('company_host', 160)->nullable()->after('host');
            $table->string('host_country', 100)->nullable()->after('company_host');
        });
    }

    public function down(): void
    {
        Schema::table('rfq_items', function (Blueprint $table) {
            $table->dropColumn(['url', 'host', 'company_host', 'host_country']);
        });
    }
};

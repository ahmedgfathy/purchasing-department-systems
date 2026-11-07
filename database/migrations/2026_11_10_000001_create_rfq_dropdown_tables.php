<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->string('status', 20)->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('order_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->string('status', 20)->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('proposed_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->string('status', 20)->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('rfqs', function (Blueprint $table) {
            $table->foreignId('market_id')->nullable()->after('currency')->constrained('markets')->nullOnDelete();
            $table->foreignId('order_type_id')->nullable()->after('market_id')->constrained('order_types')->nullOnDelete();
            $table->foreignId('proposed_method_id')->nullable()->after('order_type_id')->constrained('proposed_methods')->nullOnDelete();
            $table->string('project')->nullable()->after('title');
            $table->string('vessel')->nullable()->after('project');
            $table->string('requesting_department')->nullable()->after('vessel');
            $table->string('location')->nullable()->after('requesting_department');
            $table->string('cost_center')->nullable()->after('location');
            $table->string('contact_name')->nullable()->after('cost_center');
            $table->string('contact_code')->nullable()->after('contact_name');
            $table->string('material_req_no')->nullable()->after('contact_code');
            $table->date('requisition_rec_date')->nullable()->after('material_req_no');
            $table->date('preparation_date')->nullable()->after('requisition_rec_date');
            $table->string('service_delivery_period')->nullable()->after('preparation_date');
            $table->string('ref_order_no')->nullable()->after('service_delivery_period');
            $table->decimal('estimated_budget', 14, 2)->nullable()->after('ref_order_no');
            $table->text('budget_currency')->nullable()->after('estimated_budget');
        });
    }

    public function down(): void
    {
        Schema::table('rfqs', function (Blueprint $table) {
            $table->dropForeign(['market_id']);
            $table->dropForeign(['order_type_id']);
            $table->dropForeign(['proposed_method_id']);
            $table->dropColumn([
                'market_id', 'order_type_id', 'proposed_method_id',
                'project', 'vessel', 'requesting_department', 'location',
                'cost_center', 'contact_name', 'contact_code', 'material_req_no',
                'requisition_rec_date', 'preparation_date', 'service_delivery_period',
                'ref_order_no', 'estimated_budget', 'budget_currency',
            ]);
        });

        Schema::dropIfExists('proposed_methods');
        Schema::dropIfExists('order_types');
        Schema::dropIfExists('markets');
    }
};

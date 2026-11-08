<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Vendor module mapped from the legacy Microsoft Access purchasing system
     * (2026.accdb — Vendors, Tasks sub, الأنشطة and Vendor VS Tasks tables).
     */
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Vendors
            $table->unsignedBigInteger('supplier_id')->nullable()->unique()->after('id');
            $table->string('name_ar', 75)->nullable()->after('name');
            $table->string('name_en', 75)->nullable()->after('name_ar');
            $table->unsignedInteger('brochure_no')->nullable()->after('name_en');
            $table->string('brochure_type', 10)->nullable()->after('brochure_no');
            $table->string('agent_name', 50)->nullable()->after('brochure_type');

            // Phones / faxes / mobiles
            $table->string('phone1', 50)->nullable()->after('agent_name');
            $table->string('phone2', 50)->nullable()->after('phone1');
            $table->string('phone3', 50)->nullable()->after('phone2');
            $table->string('fax1', 50)->nullable()->after('phone3');
            $table->string('fax2', 50)->nullable()->after('fax1');
            $table->string('mobile1', 50)->nullable()->after('fax2');
            $table->string('mobile2', 50)->nullable()->after('mobile1');
            $table->string('mobile3', 50)->nullable()->after('mobile2');

            // Emails / website
            $table->text('email1')->nullable()->after('mobile3');
            $table->text('email2')->nullable()->after('email1');
            $table->text('email3')->nullable()->after('email2');
            $table->text('website')->nullable()->after('email3');

            // Agent contact block
            $table->string('agent_phone1', 50)->nullable()->after('website');
            $table->string('agent_phone2', 50)->nullable()->after('agent_phone1');
            $table->string('agent_phone3', 50)->nullable()->after('agent_phone2');
            $table->string('agent_fax1', 50)->nullable()->after('agent_phone3');
            $table->string('agent_fax2', 50)->nullable()->after('agent_fax1');
            $table->string('agent_fax3', 50)->nullable()->after('agent_fax2');
            $table->string('agent_mobile1', 50)->nullable()->after('agent_fax3');
            $table->string('agent_mobile2', 50)->nullable()->after('agent_mobile1');
            $table->string('agent_mobile3', 50)->nullable()->after('agent_mobile2');
            $table->text('agent_email1')->nullable()->after('agent_mobile3');
            $table->text('agent_email2')->nullable()->after('agent_email1');
            $table->text('agent_email3')->nullable()->after('agent_email2');
            $table->text('agent_address')->nullable()->after('agent_email3');

            // Registration & classification
            $table->date('committee_date')->nullable()->after('agent_address');
            $table->string('committee_no', 50)->nullable()->after('committee_date');
            $table->string('registration_status', 50)->nullable()->after('committee_no')->index();
            $table->string('registration_status_legacy', 50)->nullable()->after('registration_status');
            $table->string('certified_class', 20)->nullable()->after('registration_status_legacy');
            $table->string('data_entry_user', 255)->nullable()->after('certified_class');
            $table->string('free_zone', 255)->nullable()->after('data_entry_user');
            $table->string('cd_vendor')->nullable()->after('free_zone');

            // Documents & dates
            $table->string('commercial_register', 50)->nullable()->after('cd_vendor');
            $table->date('commercial_register_expiry')->nullable()->after('commercial_register');
            $table->string('tax_card', 50)->nullable()->after('commercial_register_expiry');
            $table->date('tax_card_expiry')->nullable()->after('tax_card');
            $table->string('vat_number', 50)->nullable()->after('tax_card_expiry');
            $table->date('vat_expiry')->nullable()->after('vat_number');
            $table->string('owner_name', 50)->nullable()->after('vat_expiry');
            $table->bigInteger('capital_amount')->nullable()->after('owner_name');
            $table->string('capital_currency', 50)->nullable()->after('capital_amount');
            $table->text('agency_documents')->nullable()->after('capital_currency');

            // Flags
            $table->boolean('local_registered')->default(false)->after('agency_documents');
            $table->boolean('foreign_registered')->default(false)->after('local_registered');
            $table->boolean('stopped')->default(false)->after('foreign_registered')->index();
            $table->boolean('struck_off')->default(false)->after('stopped')->index();
            $table->boolean('ahmed_flag')->default(false)->after('struck_off');

            // Notes
            $table->text('special_notes')->nullable()->after('ahmed_flag');
            $table->text('extra_notes')->nullable()->after('special_notes');
            $table->text('committee_text')->nullable()->after('extra_notes');
            $table->string('sab_no', 255)->nullable()->after('committee_text'); // رقم الساب

            $table->index('country');
            $table->index('category');
        });

        // Services / products catalog (Access: Tasks sub + الأنشطة)
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('source', 20)->default('task'); // task | product | custom
            $table->string('code', 50);
            $table->string('name_ar', 255);
            $table->string('name_en', 255)->nullable();
            $table->string('main_code', 50)->nullable();
            $table->string('main_name_ar', 255)->nullable();
            $table->string('main_name_en', 255)->nullable();
            $table->string('activity_code', 50)->nullable(); // كود النشاط (product rows)
            $table->string('product_name', 255)->nullable();
            $table->string('product_code', 50)->nullable();
            $table->string('product_status', 50)->nullable();
            $table->timestamps();

            $table->unique(['source', 'code']);
            $table->index('main_code');
            $table->index('activity_code');
        });

        // Vendor ↔ service/product links (Access: Vendor VS Tasks)
        Schema::create('vendor_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->string('registration_type', 100)->nullable(); // نوع التسجيل
            $table->string('registration_role', 100)->nullable(); // صفة التسجيل
            $table->string('brand', 255)->nullable(); // الماركة
            $table->string('country_of_origin', 255)->nullable(); // بلد المنشأ
            $table->string('origin', 255)->nullable(); // المنشأ
            $table->string('original_manufacturer', 255)->nullable(); // مصنع أصلى
            $table->string('reviewer', 255)->nullable(); // Ahmed Hany
            $table->boolean('selected')->default(false); // Select
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['vendor_id', 'activity_id']);
            $table->index('activity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_services');
        Schema::dropIfExists('activities');

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropUnique(['supplier_id']);
            $table->dropIndex(['country']);
            $table->dropIndex(['category']);
            $table->dropIndex(['registration_status']);
            $table->dropIndex(['stopped']);
            $table->dropIndex(['struck_off']);

            $table->dropColumn([
                'supplier_id', 'name_ar', 'name_en', 'brochure_no', 'brochure_type', 'agent_name',
                'phone1', 'phone2', 'phone3', 'fax1', 'fax2', 'mobile1', 'mobile2', 'mobile3',
                'email1', 'email2', 'email3', 'website',
                'agent_phone1', 'agent_phone2', 'agent_phone3',
                'agent_fax1', 'agent_fax2', 'agent_fax3',
                'agent_mobile1', 'agent_mobile2', 'agent_mobile3',
                'agent_email1', 'agent_email2', 'agent_email3', 'agent_address',
                'committee_date', 'committee_no', 'registration_status', 'registration_status_legacy',
                'certified_class', 'data_entry_user', 'free_zone', 'cd_vendor',
                'commercial_register', 'commercial_register_expiry',
                'tax_card', 'tax_card_expiry', 'vat_number', 'vat_expiry',
                'owner_name', 'capital_amount', 'capital_currency', 'agency_documents',
                'local_registered', 'foreign_registered', 'stopped', 'struck_off', 'ahmed_flag',
                'special_notes', 'extra_notes', 'committee_text', 'sab_no',
            ]);
        });
    }
};

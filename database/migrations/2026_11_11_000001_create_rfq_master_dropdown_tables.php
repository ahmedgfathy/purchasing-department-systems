<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private array $tables = ['rfq_departments', 'rfq_locations', 'rfq_priorities', 'rfq_currencies', 'rfq_units'];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('code', 40)->unique();
                $table->string('name', 120);
                $table->string('status', 20)->default('active')->index();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        $this->seedDefaults();

        Schema::table('rfqs', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('requesting_department')->constrained('rfq_departments')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->after('location')->constrained('rfq_locations')->nullOnDelete();
            $table->foreignId('priority_id')->nullable()->after('priority')->constrained('rfq_priorities')->nullOnDelete();
            $table->foreignId('currency_id')->nullable()->after('currency')->constrained('rfq_currencies')->nullOnDelete();
            $table->foreignId('budget_currency_id')->nullable()->after('budget_currency')->constrained('rfq_currencies')->nullOnDelete();
        });

        Schema::table('rfq_items', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('unit')->constrained('rfq_units')->nullOnDelete();
        });

        foreach (DB::table('rfqs')->get() as $rfq) {
            $departmentId = $this->migrateValue('rfq_departments', $rfq->requesting_department);
            $locationId = $this->migrateValue('rfq_locations', $rfq->location);
            $priorityId = $this->migrateValue('rfq_priorities', $rfq->priority);
            $currencyId = $this->migrateValue('rfq_currencies', $rfq->currency);
            $budgetCurrencyId = $this->migrateValue('rfq_currencies', $rfq->budget_currency);
            DB::table('rfqs')->where('id', $rfq->id)->update([
                'department_id' => $departmentId,
                'location_id' => $locationId,
                'priority_id' => $priorityId,
                'currency_id' => $currencyId,
                'budget_currency_id' => $budgetCurrencyId,
            ]);
        }

        foreach (DB::table('rfq_items')->get() as $item) {
            $unitId = $this->migrateValue('rfq_units', $item->unit);
            DB::table('rfq_items')->where('id', $item->id)->update(['unit_id' => $unitId]);
        }
    }

    private function seedDefaults(): void
    {
        $defaults = [
            'rfq_priorities' => [['code' => 'low', 'name' => 'Low'], ['code' => 'normal', 'name' => 'Normal'], ['code' => 'high', 'name' => 'High'], ['code' => 'urgent', 'name' => 'Urgent']],
            'rfq_currencies' => array_map(fn (string $code) => ['code' => $code, 'name' => $code], ['USD', 'EGP', 'EUR', 'GBP', 'SAR', 'AED']),
            'rfq_units' => [['code' => 'EA', 'name' => 'Each'], ['code' => 'HR', 'name' => 'Hour'], ['code' => 'DAY', 'name' => 'Day'], ['code' => 'KG', 'name' => 'Kilogram'], ['code' => 'SET', 'name' => 'Set']],
            'rfq_departments' => [['code' => 'PROCUREMENT', 'name' => 'Procurement'], ['code' => 'OPERATIONS', 'name' => 'Operations'], ['code' => 'MAINTENANCE', 'name' => 'Maintenance'], ['code' => 'FINANCE', 'name' => 'Finance']],
            'rfq_locations' => [['code' => 'HEAD_OFFICE', 'name' => 'Head office'], ['code' => 'WAREHOUSE', 'name' => 'Warehouse'], ['code' => 'PROJECT_SITE', 'name' => 'Project site']],
        ];
        foreach ($defaults as $table => $rows) {
            foreach ($rows as $row) DB::table($table)->insert($row + ['status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    private function migrateValue(string $table, ?string $value): ?int
    {
        $value = trim((string) $value);
        if ($value === '') return null;
        $code = strtoupper(Str::slug($value, '_'));
        $row = DB::table($table)->where('code', $code)->first();
        if ($row) return $row->id;
        return DB::table($table)->insertGetId(['code' => $code, 'name' => $value, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('rfq_items', fn (Blueprint $table) => $table->dropForeign(['unit_id']));
        Schema::table('rfqs', function (Blueprint $table) {
            foreach (['department_id', 'location_id', 'priority_id', 'currency_id', 'budget_currency_id'] as $column) $table->dropForeign([$column]);
        });
        Schema::table('rfq_items', fn (Blueprint $table) => $table->dropColumn('unit_id'));
        Schema::table('rfqs', fn (Blueprint $table) => $table->dropColumn(['department_id', 'location_id', 'priority_id', 'currency_id', 'budget_currency_id']));
        foreach (array_reverse($this->tables) as $table) Schema::dropIfExists($table);
    }
};

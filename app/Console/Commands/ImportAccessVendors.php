<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\Vendor;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

/**
 * Imports the legacy Microsoft Access purchasing data:
 *
 *  - "Tasks sub"      -> activities (source = task)
 *  - "الأنشطة"        -> activities (source = product)
 *  - "Vendors"        -> vendors (upserted by Supplier ID)
 *  - "Vendor VS Tasks" -> vendor_services (vendor x activity relations)
 *
 * mdbtools (mdb-export) is used to read the .accdb file, so the command is
 * re-runnable: every table is imported with upsert semantics.
 */
class ImportAccessVendors extends Command
{
    protected $signature = 'vendors:import-access
        {--path= : Path to the Access data file (defaults to the local 2026.accdb copy)}
        {--fresh : Delete previously imported Access vendors, services and activities first}';

    protected $description = 'Import vendors, activities and vendor services from the legacy Microsoft Access database';

    private const DEFAULT_PATH = '/home/xinreal/pms-purchasing/2026.accdb';

    private const CHUNK = 500;

    private string $path;

    public function handle(): int
    {
        $this->path = (string) ($this->option('path') ?: self::DEFAULT_PATH);

        if (! is_file($this->path)) {
            $this->error('Access data file not found: '.$this->path);

            return self::FAILURE;
        }

        if (! trim((string) shell_exec('command -v mdb-export 2>/dev/null'))) {
            $this->error('mdb-export is required. Install it with: sudo apt-get install mdbtools');

            return self::FAILURE;
        }

        try {
            if ($this->option('fresh')) {
                $this->fresh();
            }

            $this->importActivities();
            $this->importVendors();
            $this->importServices();
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Done.');

        return self::SUCCESS;
    }

    private function fresh(): void
    {
        $services = DB::table('vendor_services')->delete();
        $activities = DB::table('activities')->delete();
        $vendors = Vendor::whereNotNull('supplier_id')->delete();

        $this->info("Fresh start: removed {$services} services, {$activities} activities and {$vendors} imported vendors.");
    }

    private function importActivities(): void
    {
        /** @var array<string, Activity> $existing keyed by "source|code" */
        $existing = [];
        foreach (Activity::query()->get() as $activity) {
            $existing[$activity->source.'|'.$activity->code] = $activity;
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        // --- Tasks sub (Access sub activities, source = task) -------------
        $columns = ['كود النشاط الرئيسى', 'أسم النشاط الرئيسى A', 'أسم النشاط الرئيسى E',
            'كود النشاط الفرعى', 'أسم النشاط الفرعى A', 'أسم النشاط الفرعى E'];

        foreach ($this->readTable('Tasks sub', $columns) as $row) {
            $code = $this->text($row['كود النشاط الفرعى']);

            if ($code === null) {
                $skipped++;

                continue;
            }

            $data = [
                'source' => Activity::SOURCE_TASK,
                'code' => $code,
                'name_ar' => $this->text($row['أسم النشاط الفرعى A']) ?? $code,
                'name_en' => $this->text($row['أسم النشاط الفرعى E']),
                'main_code' => $this->text($row['كود النشاط الرئيسى']),
                'main_name_ar' => $this->text($row['أسم النشاط الرئيسى A']),
                'main_name_en' => $this->text($row['أسم النشاط الرئيسى E']),
            ];

            $activity = $existing['task|'.$code] ?? null;

            if ($activity === null) {
                $existing['task|'.$code] = Activity::create($data);
                $created++;
            } elseif ($this->fillIfDirty($activity, $data)) {
                $updated++;
            }
        }

        // --- الأنشطة (products / services, source = product) --------------
        $columns = ['ID', 'كود النشاط', 'أسم النشاط', 'كود الرئيسي', 'الرئيسي',
            'المنتج-الخدمة', 'كود المنتج', 'حالة المنتج'];

        foreach ($this->readTable('الأنشطة', $columns) as $row) {
            // Each row is a product line: the product code is unique, while the
            // activity code repeats (and is sometimes blank).
            $productCode = $this->text($row['كود المنتج']);
            $code = $productCode ?? ('P'.(int) $row['ID']);

            $data = [
                'source' => Activity::SOURCE_PRODUCT,
                'code' => $code,
                'name_ar' => $this->text($row['أسم النشاط'])
                    ?? $this->text($row['المنتج-الخدمة'])
                    ?? $code,
                'main_code' => $this->text($row['كود الرئيسي']),
                'main_name_ar' => $this->text($row['الرئيسي']),
                'activity_code' => $this->text($row['كود النشاط']),
                'product_name' => $this->text($row['المنتج-الخدمة']),
                'product_code' => $productCode,
                'product_status' => $this->text($row['حالة المنتج']),
            ];

            $activity = $existing['product|'.$code] ?? null;

            if ($activity === null) {
                $existing['product|'.$code] = Activity::create($data);
                $created++;
            } elseif ($this->fillIfDirty($activity, $data)) {
                $updated++;
            }
        }

        $this->info("Activities: {$created} created, {$updated} updated, {$skipped} skipped.");
    }

    private function importVendors(): void
    {
        $columns = ['Supplier ID', 'أسم المورد A', 'أسم المورد E', 'رقم الكراسة', 'نوع الكراسة', 'الوكيل',
            'البلد', 'التليفون 1', 'التليفون 2', 'التليفون 3', 'الفاكس 1', 'الفاكس 2',
            'E_mail 1', 'E_mail 2', 'E_mail 3', 'العنوان', 'المحمول 1', 'المحمول 2', 'المحمول 3',
            'الموقع الالكترونى', 'تليفون الوكيل 1', 'تليفون الوكيل 2', 'تليفون الوكيل 3',
            'فاكس الوكيل 1', 'فاكس الوكيل 2', 'فاكس الوكيل 3', 'عنوان الوكيل',
            'E_mail الوكيل 1', 'E_mail الوكيل 2', 'E_mail الوكيل 3',
            'محمول الوكيل 1', 'محمول الوكيل 2', 'محمول الوكيل 3',
            'تاريخ اللجنة', 'رقم اللجنة', 'موقف  التسجيل', 'ملاحظات', 'السجل التجارى',
            'القيمة المضافه', 'البطاقه الضريبيه', 'الشخص المتصل',
            'تاريخ انتهاء البطاقة الضريبية', 'تاريخ انتهاء السجل التجاري', 'تاريخ انتهاء ضريبة القيمة المضافة',
            'أسم صاحب المنشأة', 'ملاحظات خاصة', 'Ahmed', 'CD Vendor', 'committee',
            'مبلغ رأس المال', 'عملة رأس المال', 'مسجل محلي', 'مسجل خارجى', 'ملاحظات2',
            'موقف التسجيل جديد', 'موقوف', 'مشطوب', 'مستندات الوكالة', 'منطقة حرة',
            'A certified', 'مسجل البيانات', 'Categry', 'رقم الساب'];

        /** @var array<int, Vendor> $existing keyed by supplier_id */
        $existing = [];
        foreach (Vendor::query()->whereNotNull('supplier_id')->get() as $vendor) {
            $existing[(int) $vendor->supplier_id] = $vendor;
        }

        $created = 0;
        $updated = 0;
        $failed = 0;
        $rows = 0;

        foreach ($this->readTable('Vendors', $columns) as $row) {
            $supplierId = (int) $row['Supplier ID'];

            if ($supplierId <= 0) {
                continue;
            }

            $rows++;
            $legacy = $this->text($row['موقف  التسجيل']);
            $status = $this->text($row['موقف التسجيل جديد']);

            $data = [
                'supplier_id' => $supplierId,
                'name_ar' => $this->text($row['أسم المورد A']),
                'name_en' => $this->text($row['أسم المورد E']),
                'brochure_no' => $this->integer($row['رقم الكراسة']),
                'brochure_type' => $this->text($row['نوع الكراسة']),
                'agent_name' => $this->text($row['الوكيل']),
                'country' => $this->text($row['البلد']),
                'phone1' => $this->text($row['التليفون 1']),
                'phone2' => $this->text($row['التليفون 2']),
                'phone3' => $this->text($row['التليفون 3']),
                'fax1' => $this->text($row['الفاكس 1']),
                'fax2' => $this->text($row['الفاكس 2']),
                'email1' => Vendor::cleanHyperlink($row['E_mail 1']),
                'email2' => Vendor::cleanHyperlink($row['E_mail 2']),
                'email3' => Vendor::cleanHyperlink($row['E_mail 3']),
                'address' => $this->text($row['العنوان']),
                'mobile1' => $this->text($row['المحمول 1']),
                'mobile2' => $this->text($row['المحمول 2']),
                'mobile3' => $this->text($row['المحمول 3']),
                'website' => Vendor::cleanHyperlink($row['الموقع الالكترونى']),
                'agent_phone1' => $this->text($row['تليفون الوكيل 1']),
                'agent_phone2' => $this->text($row['تليفون الوكيل 2']),
                'agent_phone3' => $this->text($row['تليفون الوكيل 3']),
                'agent_fax1' => $this->text($row['فاكس الوكيل 1']),
                'agent_fax2' => $this->text($row['فاكس الوكيل 2']),
                'agent_fax3' => $this->text($row['فاكس الوكيل 3']),
                'agent_address' => $this->text($row['عنوان الوكيل']),
                'agent_email1' => Vendor::cleanHyperlink($row['E_mail الوكيل 1']),
                'agent_email2' => Vendor::cleanHyperlink($row['E_mail الوكيل 2']),
                'agent_email3' => Vendor::cleanHyperlink($row['E_mail الوكيل 3']),
                'agent_mobile1' => $this->text($row['محمول الوكيل 1']),
                'agent_mobile2' => $this->text($row['محمول الوكيل 2']),
                'agent_mobile3' => $this->text($row['محمول الوكيل 3']),
                'committee_date' => $this->date($row['تاريخ اللجنة']),
                'committee_no' => $this->text($row['رقم اللجنة']),
                'registration_status_legacy' => $legacy,
                'notes' => $this->text($row['ملاحظات']),
                'commercial_register' => $this->text($row['السجل التجارى']),
                'vat_number' => $this->text($row['القيمة المضافه']),
                'tax_card' => $this->text($row['البطاقه الضريبيه']),
                'contact_name' => $this->text($row['الشخص المتصل']),
                'tax_card_expiry' => $this->date($row['تاريخ انتهاء البطاقة الضريبية']),
                'commercial_register_expiry' => $this->date($row['تاريخ انتهاء السجل التجاري']),
                'vat_expiry' => $this->date($row['تاريخ انتهاء ضريبة القيمة المضافة']),
                'owner_name' => $this->text($row['أسم صاحب المنشأة']),
                'special_notes' => $this->text($row['ملاحظات خاصة']),
                'ahmed_flag' => $this->boolean($row['Ahmed']),
                'cd_vendor' => $this->text($row['CD Vendor']),
                'committee_text' => $this->text($row['committee']),
                'capital_amount' => $this->integer($row['مبلغ رأس المال']),
                'capital_currency' => $this->text($row['عملة رأس المال']),
                'local_registered' => $this->boolean($row['مسجل محلي']),
                'foreign_registered' => $this->boolean($row['مسجل خارجى']),
                'extra_notes' => $this->text($row['ملاحظات2']),
                'registration_status' => $status,
                // The legacy Access booleans are all zero, so the meaningful
                // "stopped / struck off" states are derived from the text
                // registration statuses used by the Access forms.
                'stopped' => $this->boolean($row['موقوف'])
                    || in_array($legacy, ['مغلق', 'موقوف لمدة'], true),
                'struck_off' => $this->boolean($row['مشطوب'])
                    || $status === 'شطب'
                    || in_array($legacy, ['إستبعاد', 'شطب'], true),
                'agency_documents' => $this->text($row['مستندات الوكالة']),
                'free_zone' => $this->text($row['منطقة حرة']),
                'certified_class' => $this->text($row['A certified']),
                'data_entry_user' => $this->text($row['مسجل البيانات']),
                'category' => $this->text($row['Categry']),
                'sab_no' => $this->text($row['رقم الساب']),
            ];

            $vendor = $existing[$supplierId] ?? new Vendor;

            try {
                $vendor->fill($data);
                $vendor->save();

                if ($vendor->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            } catch (QueryException $exception) {
                $failed++;
                $this->warn("Supplier {$supplierId}: ".$exception->getMessage());
            }
        }

        $this->info("Vendors: {$created} created, {$updated} updated, {$failed} failed ({$rows} rows read).");
    }

    private function importServices(): void
    {
        $columns = ['Supplier ID', 'كود النشاط الفرعى', 'نوع التسجيل', 'صفة التسجيل', 'ملاحظات',
            'Select', 'Ahmed Hany', 'مصنع أصلى', 'الماركة', 'بلد المنشأ', 'المنشأ'];

        /** @var array<int, int> $vendorIds supplier_id => vendor id */
        $vendorIds = DB::table('vendors')
            ->whereNotNull('supplier_id')
            ->pluck('id', 'supplier_id')
            ->map(fn ($id, $sid) => (int) $id)
            ->all();

        /** @var array<string, int> $activityIds code => activity id (task entries win) */
        $activityIds = [];
        $activityRows = Activity::query()
            ->orderByRaw("CASE source WHEN 'task' THEN 0 WHEN 'product' THEN 1 ELSE 2 END")
            ->get(['id', 'code']);

        foreach ($activityRows as $activity) {
            if (! array_key_exists($activity->code, $activityIds)) {
                $activityIds[$activity->code] = (int) $activity->id;
            }
        }

        // Pairs already related (re-runs keep existing rows).
        $seen = [];
        foreach (DB::table('vendor_services')->get(['vendor_id', 'activity_id']) as $pair) {
            $seen[$pair->vendor_id.':'.$pair->activity_id] = true;
        }

        $now = now();
        $buffer = [];
        $imported = 0;
        $duplicates = 0;
        $orphans = 0;
        $placeholders = 0;

        foreach ($this->readTable('Vendor VS Tasks', $columns) as $row) {
            $supplierId = (int) $row['Supplier ID'];
            $code = $this->text($row['كود النشاط الفرعى']);
            $vendorId = $vendorIds[$supplierId] ?? null;

            if ($vendorId === null || $code === null) {
                $orphans++;

                continue;
            }

            $activityId = $activityIds[$code] ?? null;

            if ($activityId === null) {
                // Catalog drift: keep the relation with a placeholder entry.
                $activity = Activity::create([
                    'source' => Activity::SOURCE_TASK,
                    'code' => $code,
                    'name_ar' => $code,
                ]);
                $activityId = (int) $activity->id;
                $activityIds[$code] = $activityId;
                $placeholders++;
            }

            $key = $vendorId.':'.$activityId;

            if (isset($seen[$key])) {
                $duplicates++;

                continue;
            }

            $seen[$key] = true;

            $buffer[] = [
                'vendor_id' => $vendorId,
                'activity_id' => $activityId,
                'registration_type' => $this->text($row['نوع التسجيل']),
                'registration_role' => $this->text($row['صفة التسجيل']),
                'brand' => $this->text($row['الماركة']),
                'country_of_origin' => $this->text($row['بلد المنشأ']),
                'origin' => $this->text($row['المنشأ']),
                'original_manufacturer' => $this->text($row['مصنع أصلى']),
                'reviewer' => $this->text($row['Ahmed Hany']),
                'selected' => $this->boolean($row['Select']),
                'notes' => $this->text($row['ملاحظات']),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($buffer) >= self::CHUNK) {
                DB::table('vendor_services')->insert($buffer);
                $imported += count($buffer);
                $buffer = [];
            }
        }

        if ($buffer !== []) {
            DB::table('vendor_services')->insert($buffer);
            $imported += count($buffer);
        }

        $this->info("Vendor services: {$imported} imported, {$duplicates} duplicates skipped, {$orphans} without vendor, {$placeholders} placeholder activities created.");
    }

    /**
     * Stream a table from the Access file as associative rows.
     *
     * @param  array<int, string>  $expectedColumns
     * @return \Generator<int, array<string, string|null>>
     */
    private function readTable(string $table, array $expectedColumns): \Generator
    {
        $command = 'mdb-export -b strip '.escapeshellarg($this->path).' '.escapeshellarg($table).' 2>/dev/null';
        $stream = popen($command, 'r');

        if ($stream === false) {
            throw new RuntimeException('Unable to run mdb-export for table: '.$table);
        }

        try {
            $header = fgetcsv($stream, null, ',', '"', '\\');

            if ($header === false || $header === [null]) {
                throw new RuntimeException('Unable to read table (empty output): '.$table);
            }

            $missing = array_diff($expectedColumns, $header);

            if ($missing !== []) {
                throw new RuntimeException(
                    'Columns missing from table ['.$table.']: '.implode(', ', $missing)
                );
            }

            while (($row = fgetcsv($stream, null, ',', '"', '\\')) !== false) {
                if ($row === [null] || $row === []) {
                    continue;
                }

                // Guard against ragged rows.
                $row = array_pad($row, count($header), null);

                yield array_combine($header, array_slice($row, 0, count($header)));
            }
        } finally {
            pclose($stream);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function fillIfDirty(Activity $activity, array $data): bool
    {
        $activity->fill($data);

        if (! $activity->isDirty()) {
            return false;
        }

        $activity->save();

        return true;
    }

    private function text(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function boolean(mixed $value): bool
    {
        return (string) $value === '1';
    }

    private function integer(mixed $value): ?int
    {
        $value = trim((string) $value);

        return preg_match('/^\d+$/', $value) === 1 ? (int) $value : null;
    }

    private function date(mixed $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $date = DateTime::createFromFormat('m/d/y H:i:s', $value)
            ?: DateTime::createFromFormat('m/d/Y H:i:s', $value);

        $errors = DateTime::getLastErrors();

        if ($date === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            $this->warn('Unparsable date value: '.$value);

            return null;
        }

        return $date->format('Y-m-d');
    }
}

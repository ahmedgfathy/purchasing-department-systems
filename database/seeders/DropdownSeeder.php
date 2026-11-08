<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Market;
use App\Models\OrderType;
use App\Models\ProposedMethod;
use App\Models\RfqCurrency;
use App\Models\RfqDepartment;
use App\Models\RfqLocation;
use App\Models\RfqPriority;
use App\Models\RfqUnit;

class DropdownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            [Market::class, [['code' => 'LOCAL', 'name' => 'Local'], ['code' => 'FOREIGN', 'name' => 'Foreign']]],
            [OrderType::class, [['code' => 'ACCEPTED', 'name' => 'Accepted'], ['code' => 'NOT_ACCEPTED', 'name' => 'Not Accepted']]],
            [ProposedMethod::class, [['code' => 'OPEN_TENDER', 'name' => 'Open Tender'], ['code' => 'SELECTIVE_TENDER', 'name' => 'Selective Tender'], ['code' => 'DIRECT_PURCHASE', 'name' => 'Direct Purchase'], ['code' => 'RFQ', 'name' => 'Request for Quotation']]],
        ] as [$model, $rows]) {
            foreach ($rows as $row) $model::updateOrCreate(['code' => $row['code']], $row + ['status' => 'active']);
        }

        foreach ([
            [RfqPriority::class, [['code' => 'low', 'name' => 'Low'], ['code' => 'normal', 'name' => 'Normal'], ['code' => 'high', 'name' => 'High'], ['code' => 'urgent', 'name' => 'Urgent']]],
            [RfqCurrency::class, array_map(fn (string $code) => ['code' => $code, 'name' => $code], ['USD', 'EGP', 'EUR', 'GBP', 'SAR', 'AED'])],
            [RfqUnit::class, [['code' => 'EA', 'name' => 'Each'], ['code' => 'HR', 'name' => 'Hour'], ['code' => 'DAY', 'name' => 'Day'], ['code' => 'KG', 'name' => 'Kilogram'], ['code' => 'SET', 'name' => 'Set']]],
            [RfqDepartment::class, [['code' => 'PROCUREMENT', 'name' => 'Procurement'], ['code' => 'OPERATIONS', 'name' => 'Operations'], ['code' => 'MAINTENANCE', 'name' => 'Maintenance'], ['code' => 'FINANCE', 'name' => 'Finance']]],
            [RfqLocation::class, [['code' => 'HEAD_OFFICE', 'name' => 'Head office'], ['code' => 'WAREHOUSE', 'name' => 'Warehouse'], ['code' => 'PROJECT_SITE', 'name' => 'Project site']]],
        ] as [$model, $rows]) {
            foreach ($rows as $row) $model::updateOrCreate(['code' => $row['code']], $row + ['status' => 'active']);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Market;
use App\Models\OrderType;
use App\Models\ProposedMethod;

class DropdownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Markets
        Market::insert([
            [
                'code' => 'LOCAL',
                'name' => 'Local',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'FOREIGN',
                'name' => 'Foreign',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Order Types (Partial Order)
        OrderType::insert([
            [
                'code' => 'ACCEPTED',
                'name' => 'Accepted',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'NOT_ACCEPTED',
                'name' => 'Not Accepted',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Proposed Methods (Tender Types)
        ProposedMethod::insert([
            [
                'code' => 'OPEN_TENDER',
                'name' => 'Open Tender',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SELECTIVE_TENDER',
                'name' => 'Selective Tender',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'DIRECT_PURCHASE',
                'name' => 'Direct Purchase',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'RFQ',
                'name' => 'Request for Quotation',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

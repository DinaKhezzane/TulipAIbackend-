<?php
namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InflowsSeeder extends Seeder
{
    public function run()
    {
        DB::table('inflows')->insert([
            [
                'company_id' => 1, // Assuming company_id 1 exists
                'inflow_category_id' => 1, // Assuming inflow_category_id 1 exists
                'date' => '2024-10-01', // Date in jj/mm/aaaa format, adjust as per your format
                'amount' => 15000.50,
                'revenue_name' => 'Product Sales',
                'description' => 'Revenue generated from the sales of product A.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1, // Assuming company_id 1 exists
                'inflow_category_id' => 2, // Assuming inflow_category_id 2 exists
                'date' => '2024-10-05', // Date in jj/mm/aaaa format
                'amount' => 5000.00,
                'revenue_name' => 'Consulting Income',
                'description' => 'Income from consulting services provided in Q4.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 2, // Assuming company_id 2 exists
                'inflow_category_id' => 1, // Assuming inflow_category_id 1 exists
                'date' => '2024-10-10', // Date in jj/mm/aaaa format
                'amount' => 8000.75,
                'revenue_name' => 'Product Licensing',
                'description' => 'Revenue from licensing agreement for product B.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 2, // Assuming company_id 2 exists
                'inflow_category_id' => 3, // Assuming inflow_category_id 3 exists
                'date' => '2024-10-12', // Date in jj/mm/aaaa format
                'amount' => 10000.00,
                'revenue_name' => 'Investment Income',
                'description' => 'Income from investments in real estate projects.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

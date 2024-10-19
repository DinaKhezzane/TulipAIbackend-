<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OutflowsSeeder extends Seeder
{
    public function run()
    {
        DB::table('outflows')->insert([
            [
                'company_id' => 1, // Assuming company_id 1 exists
                'outflow_category_id' => 1, // Assuming outflow_category_id 1 exists
                'date' => '2024-10-01', // Date in jj/mm/aaaa format, adjust as per your format
                'amount' => 10000.00,
                'outflow_name' => 'Salaries',
                'description' => 'Monthly employee salaries for the tech team.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1, // Assuming company_id 1 exists
                'outflow_category_id' => 2, // Assuming outflow_category_id 2 exists
                'date' => '2024-10-05', // Date in jj/mm/aaaa format
                'amount' => 5000.00,
                'outflow_name' => 'Office Rent',
                'description' => 'Rent payment for the main office building.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 2, // Assuming company_id 2 exists
                'outflow_category_id' => 3, // Assuming outflow_category_id 3 exists
                'date' => '2024-10-10', // Date in jj/mm/aaaa format
                'amount' => 3000.75,
                'outflow_name' => 'Utilities',
                'description' => 'Payment for electricity and water services.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 2, // Assuming company_id 2 exists
                'outflow_category_id' => 4, // Assuming outflow_category_id 4 exists
                'date' => '2024-10-12', // Date in jj/mm/aaaa format
                'amount' => 7000.00,
                'outflow_name' => 'Insurance',
                'description' => 'Quarterly payment for business insurance.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

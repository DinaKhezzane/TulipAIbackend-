<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OutflowCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('outflow_categories')->insert([
            ['name' => 'Operational Expenses', 'description' => 'Daily expenses required to run the business, such as utilities and rent.'],
            ['name' => 'Payroll', 'description' => 'Salaries and wages paid to employees.'],
            ['name' => 'Marketing & Advertising', 'description' => 'Spending on promotional activities and advertising campaigns.'],
            ['name' => 'Research & Development', 'description' => 'Funds allocated to developing new products or services.'],
            ['name' => 'Taxes', 'description' => 'Corporate, property, or income taxes paid to the government.'],
            ['name' => 'Loan Repayments', 'description' => 'Payments made to repay borrowed funds.'],
            ['name' => 'Capital Expenditure', 'description' => 'Money spent on acquiring or maintaining fixed assets like buildings or equipment.'],
            ['name' => 'Insurance Premiums', 'description' => 'Payments made to cover business, health, or property insurance.'],
            ['name' => 'Legal Fees', 'description' => 'Costs associated with legal services and consultations.'],
            ['name' => 'Charitable Donations', 'description' => 'Money donated to charitable organizations or causes.'],
        ]);
    }
}

<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesSeeder extends Seeder
{
    public function run()
    {
        DB::table('companies')->insert([
            [
                'manager_id' => 1, // Assuming manager_id 1 exists
                'category_id' => 1, // Assuming category_id 1 exists for 'Technology'
                'org_name' => 'Tech Innovators',
                'description' => 'A leading tech company focusing on AI and robotics.',
                'company_logo' => 'tech_innovators_logo.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'manager_id' => 2, // Assuming manager_id 2 exists
                'category_id' => 2, // Assuming category_id 2 exists for 'Finance'
                'org_name' => 'Financial Experts Inc.',
                'description' => 'A financial consultancy firm providing top-tier services.',
                'company_logo' => 'financial_experts_logo.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert 10 common corporate categories
        $categories = [
            ['name' => 'Technology', 'description' => 'All tech-related businesses.'],
            ['name' => 'Finance', 'description' => 'Businesses related to finance and banking.'],
            ['name' => 'Healthcare', 'description' => 'Healthcare services and products.'],
            ['name' => 'Education', 'description' => 'Educational institutions and services.'],
            ['name' => 'Retail', 'description' => 'Businesses that sell goods to consumers.'],
            ['name' => 'Real Estate', 'description' => 'Real estate agencies and services.'],
            ['name' => 'Manufacturing', 'description' => 'Manufacturing and production businesses.'],
            ['name' => 'Logistics', 'description' => 'Transportation and logistics services.'],
            ['name' => 'Hospitality', 'description' => 'Hotels, restaurants, and related services.'],
            ['name' => 'Consulting', 'description' => 'Consultancy services for various sectors.'],
        ];

        DB::table('categories')->insert($categories);
    }
}

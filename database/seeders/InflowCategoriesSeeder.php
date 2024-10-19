<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InflowCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('inflow_categories')->insert([
            ['name' => 'Product Sales', 'description' => 'Revenue generated from selling products.'],
            ['name' => 'Service Fees', 'description' => 'Income from providing services to clients.'],
            ['name' => 'Investment Gains', 'description' => 'Income from company investments such as stocks, bonds, or mutual funds.'],
            ['name' => 'Loan Proceeds', 'description' => 'Funds received from financial institutions as loans.'],
            ['name' => 'Government Grants', 'description' => 'Monetary aid received from government programs.'],
            ['name' => 'Crowdfunding Contributions', 'description' => 'Money raised from public crowdfunding campaigns.'],
            ['name' => 'Sponsorship Income', 'description' => 'Revenue received through partnerships or sponsorships with other businesses.'],
            ['name' => 'Dividend Income', 'description' => 'Earnings received from stock dividends.'],
            ['name' => 'Royalties', 'description' => 'Income from licensing intellectual property or trademarks.'],
            ['name' => 'Rental Income', 'description' => 'Revenue generated from leasing property or equipment.'],
        ]);
    }
}

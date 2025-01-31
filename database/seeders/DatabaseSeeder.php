<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ManagersSeeder::class);
    $this->call(CompaniesSeeder::class);
     $this->call(CategoriesTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(InflowCategoriesSeeder::class);
    
        $this->call(OutflowCategoriesSeeder::class);
        $this->call(OutflowsSeeder::class);
    $this->call(InflowsSeeder::class);
       
    }
}

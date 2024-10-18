<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'role_name' => 'Finance Manager',
                'description' => 'Full access to financial operations, including cash flow management, expense approval, and reporting.',
            ],
            [
                'role_name' => 'Assistant Accountant',
                'description' => 'Limited access for inputting and tracking expenses, but cannot approve major financial transactions.',
            ],
        ]);
    }
}

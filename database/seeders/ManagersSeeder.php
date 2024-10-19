<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ManagersSeeder extends Seeder
{
    public function run()
    {
        DB::table('managers')->insert([
            [
                'name' => 'Alice Johnson',
                'email' => 'alice.johnson@example.com',
                'password' => Hash::make('password123'), // Using Laravel's Hash facade for password encryption
                'profile_picture' => 'alice_profile.jpg', // Can be null if no profile picture
                'phone_number' => '555-123-4567',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bob Williams',
                'email' => 'bob.williams@example.com',
                'password' => Hash::make('securepass456'), // Password encryption
                'profile_picture' => 'bob_profile.jpg',
                'phone_number' => '555-987-6543',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Catherine Smith',
                'email' => 'catherine.smith@example.com',
                'password' => Hash::make('catpass789'), // Password encryption
                'profile_picture' => null, // No profile picture
                'phone_number' => '555-654-3210',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

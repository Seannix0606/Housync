<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin user
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@housesync.com',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'status' => 'active',
            'phone' => '+1234567890',
            'address' => 'HouseSync Headquarters',
            'email_verified_at' => now(),
        ]);
    }
}

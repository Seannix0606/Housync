<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LandlordSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'landlord@example.com'],
            [
                'name' => 'Sample Landlord',
                'password' => Hash::make('password'),
                'role' => 'landlord',
                'status' => 'approved',
                'phone' => '1234567890',
                'address' => 'Sample Address',
            ]
        );
    }
} 
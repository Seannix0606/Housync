<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'tenant1@example.com'],
            [
                'name' => 'Sample Tenant',
                'password' => Hash::make('password'),
                'role' => 'tenant',
                'status' => 'active',
                'phone' => '5551234567',
                'address' => 'Tenant Address 1',
            ]
        );
        User::updateOrCreate(
            ['email' => 'tenant2@example.com'],
            [
                'name' => 'Second Tenant',
                'password' => Hash::make('password'),
                'role' => 'tenant',
                'status' => 'active',
                'phone' => '5559876543',
                'address' => 'Tenant Address 2',
            ]
        );
    }
} 
<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $apartment = Apartment::first();
        $landlord = User::where('role', 'landlord')->first();
        if (!$apartment || !$landlord) return;
        $units = [
            [
                'unit_number' => 'Unit 01',
                'apartment_id' => $apartment->id,
                'unit_type' => '1 Bedroom',
                'rent_amount' => 8500.00,
                'status' => 'occupied',
                'leasing_type' => 'separate',
                'tenant_count' => 2,
                'description' => 'Modern 1-bedroom unit with city view',
                'floor_area' => 35.5,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'is_furnished' => true,
                'amenities' => ['AC', 'WiFi', 'Cable TV'],
                'notes' => 'Excellent tenants, always on time with payments',
            ],
            [
                'unit_number' => 'Unit 02',
                'apartment_id' => $apartment->id,
                'unit_type' => 'Studio',
                'rent_amount' => 6000.00,
                'status' => 'occupied',
                'leasing_type' => 'inclusive',
                'tenant_count' => 1,
                'description' => 'Compact studio perfect for professionals',
                'floor_area' => 25.0,
                'bedrooms' => 0,
                'bathrooms' => 1,
                'is_furnished' => false,
                'amenities' => ['WiFi'],
                'notes' => 'Recently renovated',
            ],
            [
                'unit_number' => 'Unit 03',
                'apartment_id' => $apartment->id,
                'unit_type' => '2 Bedroom',
                'rent_amount' => 12000.00,
                'status' => 'available',
                'leasing_type' => 'separate',
                'tenant_count' => 0,
                'description' => 'Spacious 2-bedroom unit perfect for families',
                'floor_area' => 55.0,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'is_furnished' => true,
                'amenities' => ['AC', 'WiFi', 'Cable TV', 'Washing Machine'],
                'notes' => 'Ready for immediate occupancy'
            ],
            [
                'unit_number' => 'Unit 04',
                'apartment_id' => $apartment->id,
                'unit_type' => '2 Bedroom',
                'rent_amount' => 11500.00,
                'status' => 'occupied',
                'leasing_type' => 'inclusive',
                'tenant_count' => 3,
                'description' => 'Well-maintained 2-bedroom unit',
                'floor_area' => 52.0,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'is_furnished' => true,
                'amenities' => ['AC', 'WiFi', 'Cable TV'],
                'notes' => 'Family with children'
            ],
            [
                'unit_number' => 'Unit 05',
                'apartment_id' => $apartment->id,
                'unit_type' => '1 Bedroom',
                'rent_amount' => 9000.00,
                'status' => 'available',
                'leasing_type' => 'separate',
                'tenant_count' => 0,
                'description' => 'Bright 1-bedroom with balcony',
                'floor_area' => 40.0,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'is_furnished' => false,
                'amenities' => ['WiFi', 'Balcony'],
                'notes' => 'Recently painted and cleaned'
            ],
            [
                'unit_number' => 'Unit 06',
                'apartment_id' => $apartment->id,
                'unit_type' => 'Studio',
                'rent_amount' => 7500.00,
                'status' => 'occupied',
                'leasing_type' => 'inclusive',
                'tenant_count' => 2,
                'description' => 'Cozy studio for young couples',
                'floor_area' => 28.0,
                'bedrooms' => 0,
                'bathrooms' => 1,
                'is_furnished' => true,
                'amenities' => ['AC', 'WiFi'],
                'notes' => 'Young professional couple'
            ],
            [
                'unit_number' => 'Unit 07',
                'apartment_id' => $apartment->id,
                'unit_type' => '3 Bedroom',
                'rent_amount' => 15000.00,
                'status' => 'available',
                'leasing_type' => 'separate',
                'tenant_count' => 0,
                'description' => 'Premium 3-bedroom penthouse unit',
                'floor_area' => 75.0,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'is_furnished' => true,
                'amenities' => ['AC', 'WiFi', 'Cable TV', 'Washing Machine', 'Dishwasher', 'Balcony'],
                'notes' => 'Top floor with panoramic view'
            ],
            [
                'unit_number' => 'Unit 08',
                'apartment_id' => $apartment->id,
                'unit_type' => '1 Bedroom',
                'rent_amount' => 8000.00,
                'status' => 'maintenance',
                'leasing_type' => 'inclusive',
                'tenant_count' => 0,
                'description' => '1-bedroom unit currently under renovation',
                'floor_area' => 38.0,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'is_furnished' => false,
                'amenities' => ['WiFi'],
                'notes' => 'Bathroom renovation in progress - available next month'
            ]
        ];

        foreach ($units as $unitData) {
            \App\Models\Unit::create($unitData);
        }
    }
}

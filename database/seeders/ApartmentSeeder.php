<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApartmentSeeder extends Seeder
{
    public function run()
    {
        $landlord = User::where('email', 'landlord@example.com')->first();
        if (!$landlord) return;
        Apartment::create([
            'name' => 'Sunset Apartments',
            'address' => '123 Main St',
            'description' => 'Modern apartment complex',
            'total_units' => 10,
            'landlord_id' => $landlord->id,
            'contact_person' => $landlord->name,
            'contact_phone' => $landlord->phone,
            'contact_email' => $landlord->email,
            'amenities' => ['parking', 'pool', 'gym'],
            'status' => 'active',
        ]);
        Apartment::create([
            'name' => 'Greenview Residences',
            'address' => '456 Oak Ave',
            'description' => 'Family-friendly apartments',
            'total_units' => 8,
            'landlord_id' => $landlord->id,
            'contact_person' => $landlord->name,
            'contact_phone' => $landlord->phone,
            'contact_email' => $landlord->email,
            'amenities' => ['garden', 'playground'],
            'status' => 'active',
        ]);
    }
} 
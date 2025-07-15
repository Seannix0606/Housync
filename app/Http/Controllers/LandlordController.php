<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Apartment;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LandlordController extends Controller
{
    public function dashboard()
    {
        $landlord = Auth::user();
        
        $stats = [
            'total_apartments' => $landlord->apartments()->count(),
            'total_units' => Unit::whereHas('apartment', function($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })->count(),
            'occupied_units' => Unit::whereHas('apartment', function($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })->where('status', 'occupied')->count(),
            'available_units' => Unit::whereHas('apartment', function($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })->where('status', 'available')->count(),
            'total_revenue' => Unit::whereHas('apartment', function($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })->where('status', 'occupied')->sum('rent_amount'),
        ];

        $apartments = $landlord->apartments()->with('units')->latest()->take(5)->get();
        $recentUnits = Unit::whereHas('apartment', function($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })->with('apartment')->latest()->take(10)->get();

        return view('landlord.dashboard', compact('stats', 'apartments', 'recentUnits'));
    }

    public function apartments()
    {
        $apartments = Auth::user()->apartments()->with('units')->latest()->paginate(10);
        return view('landlord.apartments', compact('apartments'));
    }

    public function createApartment()
    {
        return view('landlord.create-apartment');
    }

    public function storeApartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'total_units' => 'required|integer|min:1',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'amenities' => 'nullable|array',
        ]);

        $apartment = Auth::user()->apartments()->create([
            'name' => $request->name,
            'address' => $request->address,
            'description' => $request->description,
            'total_units' => $request->total_units,
            'contact_person' => $request->contact_person,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'amenities' => $request->amenities ?? [],
        ]);

        return redirect()->route('landlord.apartments')->with('success', 'Apartment created successfully.');
    }

    public function editApartment($id)
    {
        $apartment = Auth::user()->apartments()->findOrFail($id);
        return view('landlord.edit-apartment', compact('apartment'));
    }

    public function updateApartment(Request $request, $id)
    {
        $apartment = Auth::user()->apartments()->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'total_units' => 'required|integer|min:1',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'amenities' => 'nullable|array',
        ]);

        $apartment->update([
            'name' => $request->name,
            'address' => $request->address,
            'description' => $request->description,
            'total_units' => $request->total_units,
            'contact_person' => $request->contact_person,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'amenities' => $request->amenities ?? [],
        ]);

        return redirect()->route('landlord.apartments')->with('success', 'Apartment updated successfully.');
    }

    public function deleteApartment($id)
    {
        $apartment = Auth::user()->apartments()->findOrFail($id);
        $apartment->delete();

        return back()->with('success', 'Apartment deleted successfully.');
    }

    public function units($apartmentId = null)
    {
        $landlord = Auth::user();
        
        if ($apartmentId) {
            $apartment = $landlord->apartments()->findOrFail($apartmentId);
            $units = $apartment->units()->with('apartment')->latest()->paginate(15);
        } else {
            $units = Unit::whereHas('apartment', function($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })->with('apartment')->latest()->paginate(15);
        }

        $apartments = $landlord->apartments()->get();
        
        return view('landlord.units', compact('units', 'apartments', 'apartmentId'));
    }

    public function createUnit($apartmentId)
    {
        $apartment = Auth::user()->apartments()->findOrFail($apartmentId);
        return view('landlord.create-unit', compact('apartment'));
    }

    public function storeUnit(Request $request, $apartmentId)
    {
        $apartment = Auth::user()->apartments()->findOrFail($apartmentId);

        $request->validate([
            'unit_number' => 'required|string|max:50|unique:units,unit_number',
            'unit_type' => 'required|string|max:100',
            'rent_amount' => 'required|numeric|min:0',
            'status' => 'required|in:available,occupied,maintenance',
            'leasing_type' => 'required|in:separate,inclusive',
            'description' => 'nullable|string|max:1000',
            'floor_area' => 'nullable|numeric|min:0',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:1',
            'is_furnished' => 'boolean',
            'amenities' => 'nullable|array',
            'notes' => 'nullable|string|max:500',
        ]);

        $apartment->units()->create([
            'unit_number' => $request->unit_number,
            'unit_type' => $request->unit_type,
            'rent_amount' => $request->rent_amount,
            'status' => $request->status,
            'leasing_type' => $request->leasing_type,
            'description' => $request->description,
            'floor_area' => $request->floor_area,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
            'is_furnished' => $request->boolean('is_furnished'),
            'amenities' => $request->amenities ?? [],
            'notes' => $request->notes,
        ]);

        return redirect()->route('landlord.units', $apartmentId)->with('success', 'Unit created successfully.');
    }

    public function register()
    {
        return view('landlord.register');
    }

    public function storeRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'business_info' => 'required|string|max:1000',
        ]);

        $landlord = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'landlord',
            'status' => 'pending',
            'phone' => $request->phone,
            'address' => $request->address,
            'business_info' => $request->business_info,
        ]);

        // Also save to Firebase
        $this->saveToFirebase($landlord);

        return redirect()->route('landlord.pending')->with('success', 'Registration submitted successfully. Please wait for admin approval.');
    }

    private function saveToFirebase($landlord)
    {
        try {
            $databaseUrl = 'https://housesync-dd86e-default-rtdb.firebaseio.com/';
            
            // Prepare landlord data for Firebase
            $firebaseData = [
                'id' => $landlord->id,
                'name' => $landlord->name,
                'email' => $landlord->email,
                'phone' => $landlord->phone,
                'address' => $landlord->address,
                'business_info' => $landlord->business_info,
                'role' => $landlord->role,
                'status' => $landlord->status,
                'registered_at' => now()->toISOString(),
                'created_at' => $landlord->created_at->toISOString(),
                'updated_at' => $landlord->updated_at->toISOString(),
            ];
            
            // Save to Firebase using HTTP request
            $firebaseUrl = $databaseUrl . 'landlords/' . $landlord->id . '.json';
            $context = stream_context_create([
                'http' => [
                    'method' => 'PUT',
                    'header' => 'Content-Type: application/json',
                    'content' => json_encode($firebaseData)
                ]
            ]);
            
            $result = file_get_contents($firebaseUrl, false, $context);
            
            if ($result === false) {
                \Log::warning('Failed to save landlord to Firebase: ' . $landlord->email);
            } else {
                \Log::info('Landlord saved to Firebase successfully: ' . $landlord->email);
            }
            
        } catch (\Exception $e) {
            \Log::error('Error saving landlord to Firebase: ' . $e->getMessage());
        }
    }

    public function pending()
    {
        return view('landlord.pending');
    }

    public function rejected()
    {
        $user = Auth::user();
        return view('landlord.rejected', compact('user'));
    }
}

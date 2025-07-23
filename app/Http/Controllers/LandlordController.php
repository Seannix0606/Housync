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
    public function getSidebarCounts()
    {
        $landlord = auth()->user();
        if (!$landlord) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }
        return [
            'total_apartments' => $landlord->apartments()->count(),
            'total_units' => \App\Models\Unit::whereHas('apartment', function($q) use ($landlord) {
                $q->where('landlord_id', $landlord->id);
            })->count(),
        ];
    }

    public function dashboard()
    {
        $landlord = Auth::user();
        if (!$landlord) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }
        
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

        return view('landlord.dashboard', compact('stats', 'apartments', 'recentUnits') + ['sidebarCounts' => $this->getSidebarCounts()]);
    }

    public function apartments()
    {
        $apartments = Auth::user()->apartments()->with('units')->latest()->paginate(10);
        return view('landlord.apartments', compact('apartments') + ['sidebarCounts' => $this->getSidebarCounts()]);
    }

    public function createApartment()
    {
        return view('landlord.create-apartment', ['sidebarCounts' => $this->getSidebarCounts()]);
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

        try {
            $apartment = Auth::user()->apartments()->create([
                'name' => $request->name,
                'address' => $request->address,
                'description' => $request->description,
                'total_units' => $request->total_units,
                'contact_person' => $request->contact_person,
                'contact_phone' => $request->contact_phone,
                'contact_email' => $request->contact_email,
                'amenities' => $request->amenities ?? [],
                'status' => 'active',
            ]);

            // Firebase sync is automatically handled by the model's FirebaseSyncTrait
            
            return redirect()->route('landlord.apartments')->with('success', 'Apartment created successfully and synced to Firebase.');
        } catch (\Exception $e) {
            \Log::error('Error creating apartment: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create apartment. Please try again.');
        }
    }

    public function editApartment($id)
    {
        $apartment = Auth::user()->apartments()->findOrFail($id);
        return view('landlord.edit-apartment', compact('apartment') + ['sidebarCounts' => $this->getSidebarCounts()]);
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
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        try {
            $apartment->update([
                'name' => $request->name,
                'address' => $request->address,
                'description' => $request->description,
                'total_units' => $request->total_units,
                'contact_person' => $request->contact_person,
                'contact_phone' => $request->contact_phone,
                'contact_email' => $request->contact_email,
                'amenities' => $request->amenities ?? [],
                'status' => $request->status,
            ]);

            // Firebase sync is automatically handled by the model's FirebaseSyncTrait
            
            return redirect()->route('landlord.apartments')->with('success', 'Apartment updated successfully and synced to Firebase.');
        } catch (\Exception $e) {
            \Log::error('Error updating apartment: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update apartment. Please try again.');
        }
    }

    public function deleteApartment($id)
    {
        $apartment = Auth::user()->apartments()->findOrFail($id);
        
        try {
            // Check if apartment has units
            if ($apartment->units()->count() > 0) {
                return back()->with('error', 'Cannot delete apartment with existing units. Please remove all units first.');
            }
            
            $apartmentName = $apartment->name;
            $apartment->delete();
            
            // Firebase deletion is automatically handled by the model's FirebaseSyncTrait
            
            return back()->with('success', "Apartment '{$apartmentName}' deleted successfully and removed from Firebase.");
        } catch (\Exception $e) {
            \Log::error('Error deleting apartment: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete apartment. Please try again.');
        }
    }

    public function units($apartmentId = null)
    {
        $landlord = Auth::user();
        if (!$landlord) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }
        $request = request();

        $query = Unit::whereHas('apartment', function($q) use ($landlord) {
            $q->where('landlord_id', $landlord->id);
        })->with('apartment');

        // Filter by apartment
        if ($request->filled('apartment')) {
            $query->where('apartment_id', $request->apartment);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by search (unit number or property name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('unit_number', 'like', "%{$search}%")
                  ->orWhereHas('apartment', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $units = $query->latest()->paginate(15);
        $apartments = $landlord->apartments()->get();
        $apartmentId = $request->apartment ?? $apartmentId;
        return view('landlord.units', compact('units', 'apartments', 'apartmentId') + ['sidebarCounts' => $this->getSidebarCounts()]);
    }

    public function createUnit($apartmentId = null)
    {
        $landlord = Auth::user();
        if (!$landlord) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }
        if ($apartmentId) {
            $apartment = Auth::user()->apartments()->findOrFail($apartmentId);
            return view('landlord.create-unit', compact('apartment') + ['sidebarCounts' => $this->getSidebarCounts()]);
        } else {
            // Show property selection first
            $apartments = Auth::user()->apartments()->get();
            return view('landlord.select-property-for-unit', compact('apartments') + ['sidebarCounts' => $this->getSidebarCounts()]);
        }
    }

    public function storeUnit(Request $request, $apartmentId)
    {
        $landlord = Auth::user();
        if (!$landlord) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }
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
        return view('landlord.register', ['sidebarCounts' => $this->getSidebarCounts()]);
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

        // Firebase sync is now handled automatically by the model

        return redirect()->route('landlord.pending')->with('success', 'Registration submitted successfully. Please wait for admin approval.');
    }

    public function pending()
    {
        return view('landlord.pending', ['sidebarCounts' => $this->getSidebarCounts()]);
    }

    public function rejected()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }
        return view('landlord.rejected', compact('user') + ['sidebarCounts' => $this->getSidebarCounts()]);
    }

    public function tenants()
    {
        $landlord = auth()->user();
        if (!$landlord) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }
        // Get all units under this landlord's apartments
        $unitIds = \App\Models\Unit::whereHas('apartment', function($q) use ($landlord) {
            $q->where('landlord_id', $landlord->id);
        })->pluck('id');

        // Get all tenants assigned to these units
        $tenantAssignments = \App\Models\TenantAssignment::with(['tenant', 'unit', 'unit.apartment'])
            ->whereIn('unit_id', $unitIds)
            ->get();

        // Optionally, get unique tenants
        $tenants = $tenantAssignments->pluck('tenant')->unique('id')->values();

        // Get all tenants not currently assigned to a unit
        $unassignedTenants = \App\Models\User::where('role', 'tenant')
            ->whereDoesntHave('tenantAssignments', function($q) {
                $q->where('status', 'active');
            })
            ->get();

        // Get all available units for this landlord
        $availableUnits = \App\Models\Unit::whereHas('apartment', function($q) use ($landlord) {
            $q->where('landlord_id', $landlord->id);
        })->where('status', 'available')->with('apartment')->get();

        return view('landlord.tenants', [
            'tenantAssignments' => $tenantAssignments,
            'tenants' => $tenants,
            'sidebarCounts' => $this->getSidebarCounts(),
            'unassignedTenants' => $unassignedTenants,
            'availableUnits' => $availableUnits,
        ]);
    }

    // API endpoints for apartment management
    public function getApartmentDetails($id)
    {
        $landlord = Auth::user();
        if (!$landlord) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }
        $apartment = Auth::user()->apartments()->with('units')->findOrFail($id);
        
        return response()->json([
            'id' => $apartment->id,
            'name' => $apartment->name,
            'total_units' => $apartment->units->count(),
            'occupied_units' => $apartment->getOccupiedUnitsCount(),
            'available_units' => $apartment->getAvailableUnitsCount(),
            'maintenance_units' => $apartment->getMaintenanceUnitsCount(),
            'occupancy_rate' => $apartment->getOccupancyRate(),
            'total_revenue' => $apartment->getTotalRevenue(),
        ]);
    }

    public function getApartmentUnits($id)
    {
        $landlord = Auth::user();
        if (!$landlord) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }
        $apartment = Auth::user()->apartments()->findOrFail($id);
        $units = $apartment->units()->orderBy('unit_number')->get();
        
        return response()->json([
            'units' => $units->map(function($unit) {
                return [
                    'id' => $unit->id,
                    'unit_number' => $unit->unit_number,
                    'unit_type' => $unit->unit_type,
                    'rent_amount' => $unit->rent_amount,
                    'status' => $unit->status,
                    'bedrooms' => $unit->bedrooms,
                    'bathrooms' => $unit->bathrooms,
                    'max_occupants' => $unit->max_occupants ?? $unit->tenant_count,
                    'floor_number' => $unit->floor_number ?? 1,
                    'floor_area' => $unit->floor_area,
                    'amenities' => $unit->amenities,
                    'description' => $unit->description,
                ];
            })
        ]);
    }

    public function storeApartmentUnit(Request $request, $apartmentId)
    {
        $landlord = Auth::user();
        if (!$landlord) {
            return redirect()->route('login')->with('error', 'Please log in.');
        }
        $apartment = Auth::user()->apartments()->findOrFail($apartmentId);

        $request->validate([
            'unit_number' => [
                'required',
                'string',
                'max:50',
                // Unique per apartment
                \Illuminate\Validation\Rule::unique('units')->where(function ($query) use ($apartmentId) {
                    return $query->where('apartment_id', $apartmentId);
                }),
            ],
            'unit_type' => 'required|string|max:100',
            'rent_amount' => 'required|numeric|min:0',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:1',
            'max_occupants' => 'required|integer|min:1',
            'floor_number' => 'nullable|integer|min:1',
            'floor_area' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'amenities' => 'nullable|array',
        ]);

        try {
            $unit = $apartment->units()->create([
                'unit_number' => $request->unit_number,
                'unit_type' => $request->unit_type,
                'rent_amount' => $request->rent_amount,
                'status' => 'available',
                'leasing_type' => 'separate',
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'tenant_count' => 0,
                'max_occupants' => $request->max_occupants,
                'floor_number' => $request->floor_number ?? 1,
                'floor_area' => $request->floor_area,
                'description' => $request->description,
                'amenities' => $request->amenities ?? [],
                'is_furnished' => in_array('furnished', $request->amenities ?? []),
            ]);

            // Firebase sync is automatically handled by the model's FirebaseSyncTrait
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Unit created successfully and synced to Firebase.',
                    'unit' => $unit
                ]);
            } else {
                return redirect()
                    ->route('landlord.units', ['apartmentId' => $apartmentId])
                    ->with('success', 'Unit created successfully and synced to Firebase.');
            }
        } catch (\Exception $e) {
            \Log::error('Error creating unit: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create unit. Please try again.'
                ], 500);
            } else {
                return back()->withInput()->with('error', 'Failed to create unit. Please try again.');
            }
        }
    }
}

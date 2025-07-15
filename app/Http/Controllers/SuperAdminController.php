<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'pending_landlords' => User::pendingLandlords()->count(),
            'approved_landlords' => User::approvedLandlords()->count(),
            'total_tenants' => User::byRole('tenant')->count(),
            'total_apartments' => Apartment::count(),
        ];

        $pendingLandlords = User::pendingLandlords()->latest()->take(5)->get();
        $recentUsers = User::latest()->take(10)->get();

        return view('super-admin.dashboard', compact('stats', 'pendingLandlords', 'recentUsers'));
    }

    public function users()
    {
        $users = User::with('approvedBy')->latest()->paginate(15);
        return view('super-admin.users', compact('users'));
    }

    public function pendingLandlords()
    {
        $pendingLandlords = User::pendingLandlords()->with('approvedBy')->latest()->paginate(15);
        return view('super-admin.pending-landlords', compact('pendingLandlords'));
    }

    public function approveLandlord($id)
    {
        $landlord = User::findOrFail($id);
        
        if ($landlord->role !== 'landlord') {
            return back()->with('error', 'User is not a landlord.');
        }

        $landlord->approve(Auth::id());

        // Update Firebase
        $this->updateFirebaseLandlord($landlord);

        return back()->with('success', 'Landlord approved successfully.');
    }

    public function rejectLandlord(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $landlord = User::findOrFail($id);
        
        if ($landlord->role !== 'landlord') {
            return back()->with('error', 'User is not a landlord.');
        }

        $landlord->reject(Auth::id(), $request->reason);

        // Update Firebase
        $this->updateFirebaseLandlord($landlord);

        return back()->with('success', 'Landlord rejected successfully.');
    }

    public function createUser()
    {
        return view('super-admin.create-user');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:super_admin,landlord,tenant',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'business_info' => 'nullable|string|max:1000',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'address' => $request->address,
            'business_info' => $request->business_info,
            'status' => $request->role === 'landlord' ? 'pending' : 'active',
        ]);

        if ($request->role === 'landlord' && $request->approve_immediately) {
            $user->approve(Auth::id());
        }

        return redirect()->route('super-admin.users')->with('success', 'User created successfully.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('super-admin.edit-user', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:super_admin,landlord,tenant',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'business_info' => 'nullable|string|max:1000',
        ]);

        $user->update($request->only([
            'name', 'email', 'role', 'phone', 'address', 'business_info'
        ]));

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('super-admin.users')->with('success', 'User updated successfully.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        
        return back()->with('success', 'User deleted successfully.');
    }

    public function apartments()
    {
        $apartments = Apartment::with('landlord')->latest()->paginate(15);
        return view('super-admin.apartments', compact('apartments'));
    }

    private function updateFirebaseLandlord($landlord)
    {
        try {
            $databaseUrl = 'https://housesync-dd86e-default-rtdb.firebaseio.com/';
            
            // Prepare updated landlord data for Firebase
            $firebaseData = [
                'id' => $landlord->id,
                'name' => $landlord->name,
                'email' => $landlord->email,
                'phone' => $landlord->phone,
                'address' => $landlord->address,
                'business_info' => $landlord->business_info,
                'role' => $landlord->role,
                'status' => $landlord->status,
                'approved_at' => $landlord->approved_at ? $landlord->approved_at->toISOString() : null,
                'approved_by' => $landlord->approved_by,
                'rejection_reason' => $landlord->rejection_reason,
                'registered_at' => $landlord->created_at->toISOString(),
                'created_at' => $landlord->created_at->toISOString(),
                'updated_at' => $landlord->updated_at->toISOString(),
            ];
            
            // Update Firebase using HTTP request
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
                \Log::warning('Failed to update landlord in Firebase: ' . $landlord->email);
            } else {
                \Log::info('Landlord updated in Firebase successfully: ' . $landlord->email);
            }
            
        } catch (\Exception $e) {
            \Log::error('Error updating landlord in Firebase: ' . $e->getMessage());
        }
    }
}

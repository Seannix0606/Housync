<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\LandlordController;
use App\Http\Controllers\TenantAssignmentController;
use App\Services\FirebaseService;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', function () {
    return view('register');
})->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Landlord Registration (public)
Route::get('/landlord/register', [LandlordController::class, 'register'])->name('landlord.register');
Route::post('/landlord/register', [LandlordController::class, 'storeRegistration'])->name('landlord.register.store');

// Landlord status pages
Route::get('/landlord/pending', [LandlordController::class, 'pending'])->name('landlord.pending');
Route::get('/landlord/rejected', [LandlordController::class, 'rejected'])->name('landlord.rejected');

// Super Admin Routes
Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
    Route::get('/pending-landlords', [SuperAdminController::class, 'pendingLandlords'])->name('pending-landlords');
    Route::post('/approve-landlord/{id}', [SuperAdminController::class, 'approveLandlord'])->name('approve-landlord');
    Route::post('/reject-landlord/{id}', [SuperAdminController::class, 'rejectLandlord'])->name('reject-landlord');
    Route::get('/users/create', [SuperAdminController::class, 'createUser'])->name('create-user');
    Route::post('/users', [SuperAdminController::class, 'storeUser'])->name('store-user');
    Route::get('/users/{id}/edit', [SuperAdminController::class, 'editUser'])->name('edit-user');
    Route::put('/users/{id}', [SuperAdminController::class, 'updateUser'])->name('update-user');
    Route::delete('/users/{id}', [SuperAdminController::class, 'deleteUser'])->name('delete-user');
    Route::get('/apartments', [SuperAdminController::class, 'apartments'])->name('apartments');
});

// Landlord Routes
Route::middleware(['role:landlord'])->prefix('landlord')->name('landlord.')->group(function () {
    Route::get('/dashboard', [LandlordController::class, 'dashboard'])->name('dashboard');
    Route::get('/apartments', [LandlordController::class, 'apartments'])->name('apartments');
    Route::get('/apartments/create', [LandlordController::class, 'createApartment'])->name('create-apartment');
    Route::post('/apartments', [LandlordController::class, 'storeApartment'])->name('store-apartment');
    Route::get('/apartments/{id}/edit', [LandlordController::class, 'editApartment'])->name('edit-apartment');
    Route::put('/apartments/{id}', [LandlordController::class, 'updateApartment'])->name('update-apartment');
    Route::delete('/apartments/{id}', [LandlordController::class, 'deleteApartment'])->name('delete-apartment');
    
    Route::get('/units/{apartmentId?}', [LandlordController::class, 'units'])->name('units');
    Route::get('/units/create', [LandlordController::class, 'createUnit'])->name('create-unit');
    Route::get('/apartments/{apartmentId}/units/create', [LandlordController::class, 'createUnit'])->name('create-unit-for-apartment');
    Route::post('/apartments/{apartmentId}/units', [LandlordController::class, 'storeUnit'])->name('store-unit');
    
    // Tenant Assignment Routes
    Route::get('/tenant-assignments', [TenantAssignmentController::class, 'index'])->name('tenant-assignments');
    Route::get('/units/{unitId}/assign-tenant', [TenantAssignmentController::class, 'create'])->name('assign-tenant');
    Route::post('/units/{unitId}/assign-tenant', [TenantAssignmentController::class, 'store'])->name('store-tenant-assignment');
    Route::get('/tenant-assignments/{id}', [TenantAssignmentController::class, 'show'])->name('assignment-details');
    Route::put('/tenant-assignments/{id}/status', [TenantAssignmentController::class, 'updateStatus'])->name('update-assignment-status');
    Route::post('/tenant-assignments/{id}/verify-documents', [TenantAssignmentController::class, 'verifyDocuments'])->name('verify-documents');
    Route::get('/tenant-assignments/{id}/credentials', [TenantAssignmentController::class, 'getCredentials'])->name('get-credentials');
    Route::get('/available-units', [TenantAssignmentController::class, 'getAvailableUnits'])->name('available-units');
    
    // API endpoints for apartment management
    Route::get('/apartments/{id}/details', [LandlordController::class, 'getApartmentDetails'])->name('apartment-details');
    Route::get('/apartments/{id}/units', [LandlordController::class, 'getApartmentUnits'])->name('apartment-units');
    Route::post('/apartments/{apartmentId}/units', [LandlordController::class, 'storeApartmentUnit'])->name('store-apartment-unit');
    Route::get('/tenants', [LandlordController::class, 'tenants'])->name('tenants');
    Route::get('/tenants/assign', [TenantAssignmentController::class, 'createForLandlord'])->name('assign-tenant');
    Route::post('/tenants/assign', [TenantAssignmentController::class, 'storeForLandlord'])->name('store-tenant-assignment');
    // Edit and delete tenant assignment
    Route::get('/tenant-assignments/{id}/edit', [TenantAssignmentController::class, 'editAssignment'])->name('edit-tenant-assignment');
    Route::put('/tenant-assignments/{id}/edit', [TenantAssignmentController::class, 'updateAssignment'])->name('edit-tenant-assignment');
    Route::delete('/tenant-assignments/{id}', [TenantAssignmentController::class, 'deleteAssignment'])->name('delete-tenant-assignment');
});

// Original dashboard route - redirect based on role
Route::get('/dashboard', function () {
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login');
    }
    
    switch ($user->role) {
        case 'super_admin':
            return redirect()->route('super-admin.dashboard');
        case 'landlord':
            if ($user->status === 'approved') {
                return redirect()->route('landlord.dashboard');
            } elseif ($user->status === 'pending') {
                return redirect()->route('landlord.pending');
            } else {
                return redirect()->route('landlord.rejected');
            }
        case 'tenant':
            return redirect()->route('tenant.dashboard');
        default:
            return redirect()->route('login');
    }
})->middleware('auth')->name('dashboard');

// Tenant Routes
Route::middleware(['role:tenant'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/dashboard', [TenantAssignmentController::class, 'tenantDashboard'])->name('dashboard');
    Route::get('/upload-documents', [TenantAssignmentController::class, 'uploadDocuments'])->name('upload-documents');
    Route::post('/upload-documents', [TenantAssignmentController::class, 'storeDocuments'])->name('store-documents');
    Route::get('/download-document/{documentId}', [TenantAssignmentController::class, 'downloadDocument'])->name('download-document');
    // Password change routes
    Route::get('/change-password', [TenantAssignmentController::class, 'showChangePasswordForm'])->name('change-password');
    Route::post('/change-password', [TenantAssignmentController::class, 'updatePassword'])->name('update-password');
});

Route::get('/tenant-payments', function () {
    return view('tenant-payments');
})->name('tenant.payments');

Route::get('/tenant-maintenance', function () {
    return view('tenant-maintenance');
})->name('tenant.maintenance');

Route::get('/tenant-messages', function () {
    return view('tenant-messages');
})->name('tenant.messages');

Route::get('/tenant-lease', function () {
    return view('tenant-lease');
})->name('tenant.lease');

Route::get('/tenant-profile', function () {
    return view('tenant-profile');
})->name('tenant.profile');

// Units routes (need to be updated for role-based access)
Route::middleware(['role:super_admin,landlord'])->group(function () {
    Route::get('/units', [UnitController::class, 'index'])->name('units');
    Route::post('/units', [UnitController::class, 'store'])->name('units.store');
    Route::get('/units/filter', [UnitController::class, 'filter'])->name('units.filter');
    Route::get('/units/stats', [UnitController::class, 'getStats'])->name('units.stats');
    Route::get('/units/types', [UnitController::class, 'getUnitTypes'])->name('units.types');
});

Route::get('/tenants', function () {
    return view('tenants');
})->name('tenants');

Route::get('/billing', function () {
    return view('billing');
})->name('billing');

Route::get('/messages', function () {
    return view('messages');
})->name('messages');

Route::get('/security', function () {
    return view('security');
})->name('security');

// Authentication routes
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Firebase test route
Route::get('/test-firebase', function () {
    try {
        $firebaseService = new App\Services\FirebaseService();
        
        if ($firebaseService->isConfigured()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Firebase is configured and working!',
                'timestamp' => now()->toISOString()
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Firebase is not properly configured. Please check your environment variables.',
                'timestamp' => now()->toISOString()
            ], 500);
        }
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Firebase test failed: ' . $e->getMessage(),
            'timestamp' => now()->toISOString()
        ], 500);
    }
})->name('test-firebase');

// Firebase write test route
Route::get('/test-firebase-write', function () {
    $firebaseService = app(FirebaseService::class);
    
    if (!$firebaseService->isConfigured()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Firebase is not configured properly'
        ], 500);
    }
    
    // Test data to write
    $testData = [
        'message' => 'Hello from HouseSync!',
        'timestamp' => now()->toISOString(),
        'user' => 'test_user',
        'data' => [
            'property' => 'Test Property',
            'units' => 5,
            'location' => 'Test City'
        ]
    ];
    
    // Write data to Firebase
    $success = $firebaseService->storeData('test/housesync', $testData);
    
    if ($success) {
        return response()->json([
            'status' => 'success',
            'message' => 'Data written to Firebase successfully!',
            'data' => $testData
        ]);
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to write data to Firebase'
        ], 500);
    }
});

// Firebase read test route
Route::get('/test-firebase-read', function () {
    $firebaseService = app(FirebaseService::class);
    
    if (!$firebaseService->isConfigured()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Firebase is not configured properly'
        ], 500);
    }
    
    // Read data from Firebase
    $data = $firebaseService->getData('test/housesync');
    
    return response()->json([
        'status' => 'success',
        'message' => 'Data read from Firebase successfully!',
        'data' => $data
    ]);
});

// Firebase simple test route (no auth required)
Route::get('/test-firebase-simple', function () {
    try {
        $factory = (new \Kreait\Firebase\Factory)
            ->withDatabaseUri('https://housesync-dd86e-default-rtdb.firebaseio.com/');
        
        $database = $factory->createDatabase();
        
        // Test writing data
        $testData = [
            'message' => 'Hello from HouseSync!',
            'timestamp' => now()->toISOString(),
            'test' => true
        ];
        
        $database->getReference('test/simple')->set($testData);
        
        // Test reading data
        $snapshot = $database->getReference('test/simple')->getSnapshot();
        $readData = $snapshot->getValue();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Firebase simple test successful!',
            'written_data' => $testData,
            'read_data' => $readData
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Firebase simple test failed: ' . $e->getMessage()
        ], 500);
    }
});

// Firebase App Check configuration endpoint
Route::get('/firebase-config', function () {
    $firebaseService = app(FirebaseService::class);
    $config = $firebaseService->getAppCheckConfig();
    
    return response()->json([
        'firebase_config' => [
            'apiKey' => env('FIREBASE_WEB_API_KEY'),
            'authDomain' => env('FIREBASE_AUTH_DOMAIN'),
            'databaseURL' => env('FIREBASE_DATABASE_URL'),
            'projectId' => env('FIREBASE_PROJECT_ID'),
            'storageBucket' => env('FIREBASE_STORAGE_BUCKET'),
            'messagingSenderId' => env('FIREBASE_MESSAGING_SENDER_ID'),
            'appId' => env('FIREBASE_APP_ID')
        ],
        'app_check_config' => $config
    ]);
})->name('firebase-config');

// Firebase direct HTTP test route
Route::get('/test-firebase-http', function () {
    try {
        $databaseUrl = 'https://housesync-dd86e-default-rtdb.firebaseio.com/';
        
        // Test data
        $testData = [
            'message' => 'Hello from HTTP request!',
            'timestamp' => now()->toISOString(),
            'method' => 'direct_http'
        ];
        
        // Write data using HTTP PUT request
        $writeUrl = $databaseUrl . 'test/http_test.json';
        $context = stream_context_create([
            'http' => [
                'method' => 'PUT',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($testData)
            ]
        ]);
        
        $writeResult = file_get_contents($writeUrl, false, $context);
        
        if ($writeResult === false) {
            throw new Exception('Failed to write data');
        }
        
        // Read data using HTTP GET request
        $readUrl = $databaseUrl . 'test/http_test.json';
        $readResult = file_get_contents($readUrl);
        
        if ($readResult === false) {
            throw new Exception('Failed to read data');
        }
        
        $readData = json_decode($readResult, true);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Firebase HTTP test successful!',
            'written_data' => $testData,
            'read_data' => $readData
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Firebase HTTP test failed: ' . $e->getMessage()
        ], 500);
    }
});

// Firebase landlord data route
Route::get('/firebase-landlords', function () {
    try {
        $databaseUrl = 'https://housesync-dd86e-default-rtdb.firebaseio.com/';
        
        // Read landlord data from Firebase
        $landlordsUrl = $databaseUrl . 'landlords.json';
        $landlordsResult = file_get_contents($landlordsUrl);
        
        if ($landlordsResult === false) {
            throw new Exception('Failed to read landlord data');
        }
        
        $landlordsData = json_decode($landlordsResult, true);
        
        // Also check for users data
        $usersUrl = $databaseUrl . 'users.json';
        $usersResult = file_get_contents($usersUrl);
        $usersData = json_decode($usersResult, true);
        
        // Check all data paths
        $allDataUrl = $databaseUrl . '.json';
        $allDataResult = file_get_contents($allDataUrl);
        $allData = json_decode($allDataResult, true);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Firebase landlord data retrieved successfully!',
            'landlords' => $landlordsData,
            'users' => $usersData,
            'all_data' => $allData,
            'timestamp' => now()->toISOString()
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to retrieve landlord data: ' . $e->getMessage()
        ], 500);
    }
});

// Sync existing landlords to Firebase
Route::get('/sync-landlords-to-firebase', function () {
    try {
        $landlords = \App\Models\User::where('role', 'landlord')->get();
        $firebaseService = app(\App\Services\FirebaseService::class);

        $syncedCount = 0;
        $errors = [];

        foreach ($landlords as $landlord) {
            try {
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
                    'approved_at' => $landlord->approved_at ? $landlord->approved_at->toISOString() : null,
                    'approved_by' => $landlord->approved_by,
                    'rejection_reason' => $landlord->rejection_reason,
                    'registered_at' => $landlord->created_at->toISOString(),
                    'created_at' => $landlord->created_at->toISOString(),
                    'updated_at' => $landlord->updated_at->toISOString(),
                ];

                $success = $firebaseService->storeData('landlords/' . $landlord->id, $firebaseData);

                if ($success) {
                    $syncedCount++;
                } else {
                    $errors[] = "Failed to sync landlord: " . $landlord->email;
                }
            } catch (Exception $e) {
                $errors[] = "Error syncing landlord " . $landlord->email . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Landlord sync completed',
            'total_landlords' => $landlords->count(),
            'synced_count' => $syncedCount,
            'errors' => $errors,
            'timestamp' => now()->toISOString()
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to sync landlords: ' . $e->getMessage()
        ], 500);
    }
});

// Display landlords in a user-friendly format
Route::get('/landlords-dashboard', function () {
    try {
        $databaseUrl = 'https://housesync-dd86e-default-rtdb.firebaseio.com/';
        
        // Read landlord data from Firebase
        $landlordsUrl = $databaseUrl . 'landlords.json';
        $landlordsResult = file_get_contents($landlordsUrl);
        
        if ($landlordsResult === false) {
            throw new Exception('Failed to read landlord data');
        }
        
        $landlordsData = json_decode($landlordsResult, true);
        
        return view('landlords-dashboard', compact('landlordsData'));
        
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to retrieve landlord data: ' . $e->getMessage()
        ], 500);
    }
});

// Sync all existing data to Firebase
Route::get('/sync-all-data-to-firebase', function () {
    $firebaseService = new \App\Services\FirebaseDataService();
    
    $results = $firebaseService->syncAllData();
    
    return response()->json([
        'status' => 'success',
        'message' => 'Data sync completed',
        'results' => $results,
        'timestamp' => now()->toISOString()
    ]);
});

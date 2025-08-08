<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\LandlordController;
use App\Http\Controllers\TenantAssignmentController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SecurityController;

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
    
    // Super Admin Security Routes
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/dashboard', [SecurityController::class, 'dashboard'])->name('dashboard');
        Route::get('/logs', [SecurityController::class, 'logs'])->name('logs');
    });
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
    Route::delete('/tenant-assignments/{id}', [TenantAssignmentController::class, 'destroy'])->name('delete-tenant-assignment');
    Route::post('/tenant-assignments/{id}/verify-documents', [TenantAssignmentController::class, 'verifyDocuments'])->name('verify-documents');
    Route::post('/documents/{documentId}/verify', [TenantAssignmentController::class, 'verifyIndividualDocument'])->name('verify-individual-document');
    Route::get('/tenant-assignments/{id}/credentials', [TenantAssignmentController::class, 'getCredentials'])->name('get-credentials');
    Route::get('/available-units', [TenantAssignmentController::class, 'getAvailableUnits'])->name('available-units');
    Route::get('/download-document/{documentId}', [TenantAssignmentController::class, 'downloadDocument'])->name('download-document');
    
    // Staff Management Routes
    Route::get('/staff', [StaffController::class, 'index'])->name('staff');
    Route::get('/staff/create', [StaffController::class, 'create'])->name('create-staff');
    Route::get('/units/{unitId}/assign-staff', [StaffController::class, 'create'])->name('assign-staff');
    Route::post('/staff', [StaffController::class, 'store'])->name('store-staff');
    Route::get('/staff/{id}', [StaffController::class, 'show'])->name('staff-details');
    Route::put('/staff/{id}/status', [StaffController::class, 'updateStatus'])->name('update-staff-status');
    Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('delete-staff');
    Route::get('/staff/{id}/credentials', [StaffController::class, 'getCredentials'])->name('get-staff-credentials');
    
    // API endpoints for apartment management
    Route::get('/apartments/{id}/details', [LandlordController::class, 'getApartmentDetails'])->name('apartment-details');
    Route::get('/apartments/{id}/units', [LandlordController::class, 'getApartmentUnits'])->name('apartment-units');
    Route::post('/apartments/{apartmentId}/units', [LandlordController::class, 'storeApartmentUnit'])->name('store-apartment-unit');
    
    // Security Management Routes
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/dashboard', [SecurityController::class, 'dashboard'])->name('dashboard');
        Route::get('/cards', [SecurityController::class, 'cards'])->name('cards');
        Route::get('/cards/create', [SecurityController::class, 'createCard'])->name('create-card');
        Route::post('/cards', [SecurityController::class, 'storeCard'])->name('store-card');
        Route::get('/cards/{card}', [SecurityController::class, 'showCard'])->name('card-details');
        Route::put('/cards/{card}/status', [SecurityController::class, 'updateCardStatus'])->name('update-card-status');
        Route::get('/logs', [SecurityController::class, 'logs'])->name('logs');
    });
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
        case 'staff':
            return redirect()->route('staff.dashboard');
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
    Route::delete('/delete-document/{documentId}', [TenantAssignmentController::class, 'deleteDocument'])->name('delete-document');
    
    // Tenant Security Routes
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/dashboard', [SecurityController::class, 'dashboard'])->name('dashboard');
    });
});

// Staff Routes
Route::middleware(['role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffController::class, 'staffDashboard'])->name('dashboard');
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

Route::get('/landlord/tenants', [LandlordController::class, 'tenants'])->name('landlord.tenants');

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

// ESP32 Serial Communication Routes (No authentication required for hardware)
Route::prefix('api/esp32')->name('esp32.')->group(function () {
    Route::post('/rfid-scan', [SecurityController::class, 'handleRfidScan'])->name('rfid-scan');
    Route::get('/device-status', [SecurityController::class, 'deviceStatus'])->name('device-status');
});

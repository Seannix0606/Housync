<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;

class FirebaseDataService
{
    private $databaseUrl;
    private $maxRetries = 3;
    private $retryDelay = 1; // seconds

    public function __construct()
    {
        $this->databaseUrl = 'https://housesync-dd86e-default-rtdb.firebaseio.com/';
    }

    /**
     * Generic method to save data to Firebase with retry logic
     */
    public function saveToFirebase($path, $data, $id = null)
    {
        $attempt = 0;
        
        while ($attempt < $this->maxRetries) {
            try {
                $firebaseUrl = $this->databaseUrl . $path;
                
                if ($id) {
                    $firebaseUrl .= '/' . $id;
                }
                
                $firebaseUrl .= '.json';
                
                $context = stream_context_create([
                    'http' => [
                        'method' => 'PUT',
                        'header' => 'Content-Type: application/json',
                        'content' => json_encode($data),
                        'timeout' => 30
                    ]
                ]);
                
                $result = file_get_contents($firebaseUrl, false, $context);
                
                if ($result === false) {
                    throw new Exception("Failed to save data to Firebase: {$path}");
                }
                
                Log::info("Data saved to Firebase successfully: {$path}" . ($id ? "/{$id}" : ''));
                return true;
                
            } catch (Exception $e) {
                $attempt++;
                Log::warning("Attempt {$attempt} failed to save data to Firebase: " . $e->getMessage());
                
                if ($attempt >= $this->maxRetries) {
                    Log::error("Failed to save data to Firebase after {$this->maxRetries} attempts: {$path}");
                    return false;
                }
                
                sleep($this->retryDelay);
            }
        }
        
        return false;
    }

    /**
     * Generic method to get data from Firebase with retry logic
     */
    public function getFromFirebase($path, $id = null)
    {
        $attempt = 0;
        
        while ($attempt < $this->maxRetries) {
            try {
                $firebaseUrl = $this->databaseUrl . $path;
                
                if ($id) {
                    $firebaseUrl .= '/' . $id;
                }
                
                $firebaseUrl .= '.json';
                
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 30
                    ]
                ]);
                
                $result = file_get_contents($firebaseUrl, false, $context);
                
                if ($result === false) {
                    throw new Exception("Failed to get data from Firebase: {$path}");
                }
                
                return json_decode($result, true);
                
            } catch (Exception $e) {
                $attempt++;
                Log::warning("Attempt {$attempt} failed to get data from Firebase: " . $e->getMessage());
                
                if ($attempt >= $this->maxRetries) {
                    Log::error("Failed to get data from Firebase after {$this->maxRetries} attempts: {$path}");
                    return null;
                }
                
                sleep($this->retryDelay);
            }
        }
        
        return null;
    }

    /**
     * Generic method to delete data from Firebase with retry logic
     */
    public function deleteFromFirebase($path, $id = null)
    {
        $attempt = 0;
        
        while ($attempt < $this->maxRetries) {
            try {
                $firebaseUrl = $this->databaseUrl . $path;
                
                if ($id) {
                    $firebaseUrl .= '/' . $id;
                }
                
                $firebaseUrl .= '.json';
                
                $context = stream_context_create([
                    'http' => [
                        'method' => 'DELETE',
                        'timeout' => 30
                    ]
                ]);
                
                $result = file_get_contents($firebaseUrl, false, $context);
                
                if ($result === false) {
                    throw new Exception("Failed to delete data from Firebase: {$path}");
                }
                
                Log::info("Data deleted from Firebase successfully: {$path}" . ($id ? "/{$id}" : ''));
                return true;
                
            } catch (Exception $e) {
                $attempt++;
                Log::warning("Attempt {$attempt} failed to delete data from Firebase: " . $e->getMessage());
                
                if ($attempt >= $this->maxRetries) {
                    Log::error("Failed to delete data from Firebase after {$this->maxRetries} attempts: {$path}");
                    return false;
                }
                
                sleep($this->retryDelay);
            }
        }
        
        return false;
    }

    /**
     * Save user data to Firebase based on role
     */
    public function saveUser($user)
    {
        $userData = $this->prepareUserData($user);
        
        // Save to main users collection
        $mainSave = $this->saveToFirebase('users', $userData, $user->id);
        
        // Save to role-specific collection
        $roleSave = $this->saveToFirebase($user->role . 's', $userData, $user->id);
        
        // For landlords, also save to landlords collection for backward compatibility
        if ($user->role === 'landlord') {
            $this->saveToFirebase('landlords', $userData, $user->id);
        }
        
        return $mainSave && $roleSave;
    }

    /**
     * Prepare user data for Firebase
     */
    private function prepareUserData($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
            'phone' => $user->phone,
            'address' => $user->address,
            'business_info' => $user->business_info,
            'approved_at' => $user->approved_at ? $user->approved_at->toISOString() : null,
            'approved_by' => $user->approved_by,
            'rejection_reason' => $user->rejection_reason,
            'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toISOString() : null,
            'created_at' => $user->created_at->toISOString(),
            'updated_at' => $user->updated_at->toISOString(),
        ];
    }

    /**
     * Save apartment data to Firebase
     */
    public function saveApartment($apartment)
    {
        $apartmentData = $this->prepareApartmentData($apartment);

        // Save to main apartments collection
        $mainSave = $this->saveToFirebase('apartments', $apartmentData, $apartment->id);
        
        // Also save under landlord's apartments
        $landlordSave = $this->saveToFirebase("landlords/{$apartment->landlord_id}/apartments", $apartmentData, $apartment->id);
        
        return $mainSave && $landlordSave;
    }

    /**
     * Prepare apartment data for Firebase
     */
    private function prepareApartmentData($apartment)
    {
        return [
            'id' => $apartment->id,
            'name' => $apartment->name,
            'address' => $apartment->address,
            'description' => $apartment->description,
            'total_units' => $apartment->total_units,
            'landlord_id' => $apartment->landlord_id,
            'contact_person' => $apartment->contact_person,
            'contact_phone' => $apartment->contact_phone,
            'contact_email' => $apartment->contact_email,
            'amenities' => $apartment->amenities,
            'status' => $apartment->status,
            'created_at' => $apartment->created_at->toISOString(),
            'updated_at' => $apartment->updated_at->toISOString(),
        ];
    }

    /**
     * Save unit data to Firebase
     */
    public function saveUnit($unit)
    {
        $unitData = $this->prepareUnitData($unit);

        // Save to main units collection
        $mainSave = $this->saveToFirebase('units', $unitData, $unit->id);
        
        // Also save under apartment's units
        $apartmentSave = $this->saveToFirebase("apartments/{$unit->apartment_id}/units", $unitData, $unit->id);
        
        // Also save under landlord's units (get landlord_id from apartment)
        $apartment = \App\Models\Apartment::find($unit->apartment_id);
        $landlordSave = true;
        if ($apartment) {
            $landlordSave = $this->saveToFirebase("landlords/{$apartment->landlord_id}/units", $unitData, $unit->id);
        }
        
        return $mainSave && $apartmentSave && $landlordSave;
    }

    /**
     * Prepare unit data for Firebase
     */
    private function prepareUnitData($unit)
    {
        return [
            'id' => $unit->id,
            'apartment_id' => $unit->apartment_id,
            'unit_number' => $unit->unit_number,
            'unit_type' => $unit->unit_type,
            'rent_amount' => $unit->rent_amount,
            'status' => $unit->status,
            'leasing_type' => $unit->leasing_type,
            'tenant_count' => $unit->tenant_count,
            'description' => $unit->description,
            'floor_area' => $unit->floor_area,
            'bedrooms' => $unit->bedrooms,
            'bathrooms' => $unit->bathrooms,
            'is_furnished' => $unit->is_furnished,
            'amenities' => $unit->amenities,
            'notes' => $unit->notes,
            'created_at' => $unit->created_at->toISOString(),
            'updated_at' => $unit->updated_at->toISOString(),
        ];
    }

    /**
     * Delete user from Firebase
     */
    public function deleteUser($user)
    {
        $userId = is_object($user) ? $user->id : $user;
        $role = is_object($user) ? $user->role : null;
        
        // Delete from main users collection
        $mainDelete = $this->deleteFromFirebase('users', $userId);
        
        // Delete from role-specific collection if role is known
        $roleDelete = true;
        if ($role) {
            $roleDelete = $this->deleteFromFirebase($role . 's', $userId);
            
            // For landlords, also delete from landlords collection
            if ($role === 'landlord') {
                $this->deleteFromFirebase('landlords', $userId);
            }
        }
        
        return $mainDelete && $roleDelete;
    }

    /**
     * Delete apartment from Firebase
     */
    public function deleteApartment($apartment)
    {
        $apartmentId = is_object($apartment) ? $apartment->id : $apartment;
        $landlordId = is_object($apartment) ? $apartment->landlord_id : null;
        
        // Delete from main apartments collection
        $mainDelete = $this->deleteFromFirebase('apartments', $apartmentId);
        
        // Delete from landlord's apartments if landlord_id is known
        $landlordDelete = true;
        if ($landlordId) {
            $landlordDelete = $this->deleteFromFirebase("landlords/{$landlordId}/apartments", $apartmentId);
        }
        
        // Delete all units of this apartment
        if (is_object($apartment)) {
            $units = \App\Models\Unit::where('apartment_id', $apartmentId)->get();
            foreach ($units as $unit) {
                $this->deleteUnit($unit);
            }
        }
        
        return $mainDelete && $landlordDelete;
    }

    /**
     * Delete unit from Firebase
     */
    public function deleteUnit($unit)
    {
        $unitId = is_object($unit) ? $unit->id : $unit;
        $apartmentId = is_object($unit) ? $unit->apartment_id : null;
        
        // Delete from main units collection
        $mainDelete = $this->deleteFromFirebase('units', $unitId);
        
        // Delete from apartment's units if apartment_id is known
        $apartmentDelete = true;
        if ($apartmentId) {
            $apartmentDelete = $this->deleteFromFirebase("apartments/{$apartmentId}/units", $unitId);
        }
        
        // Delete from landlord's units
        $landlordDelete = true;
        if (is_object($unit)) {
            $apartment = \App\Models\Apartment::find($unit->apartment_id);
            if ($apartment) {
                $landlordDelete = $this->deleteFromFirebase("landlords/{$apartment->landlord_id}/units", $unitId);
            }
        }
        
        return $mainDelete && $apartmentDelete && $landlordDelete;
    }

    /**
     * Get all data from Firebase
     */
    public function getAllData()
    {
        return $this->getFromFirebase('');
    }

    /**
     * Get all users from Firebase
     */
    public function getAllUsers()
    {
        return $this->getFromFirebase('users');
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($role)
    {
        return $this->getFromFirebase($role . 's');
    }

    /**
     * Get all apartments from Firebase
     */
    public function getAllApartments()
    {
        return $this->getFromFirebase('apartments');
    }

    /**
     * Get all units from Firebase
     */
    public function getAllUnits()
    {
        return $this->getFromFirebase('units');
    }

    /**
     * Get landlord with all their data
     */
    public function getLandlordData($landlordId)
    {
        $landlord = $this->getFromFirebase('users', $landlordId);
        $apartments = $this->getFromFirebase("landlords/{$landlordId}/apartments");
        $units = $this->getFromFirebase("landlords/{$landlordId}/units");
        
        return [
            'landlord' => $landlord,
            'apartments' => $apartments,
            'units' => $units
        ];
    }

    /**
     * Sync all existing data to Firebase
     */
    public function syncAllData()
    {
        $results = [
            'users' => ['success' => 0, 'failed' => 0],
            'apartments' => ['success' => 0, 'failed' => 0],
            'units' => ['success' => 0, 'failed' => 0],
            'errors' => []
        ];

        try {
            // Sync all users
            $users = \App\Models\User::all();
            foreach ($users as $user) {
                if ($this->saveUser($user)) {
                    $results['users']['success']++;
                } else {
                    $results['users']['failed']++;
                    $results['errors'][] = "Failed to sync user: {$user->email}";
                }
            }

            // Sync all apartments
            $apartments = \App\Models\Apartment::all();
            foreach ($apartments as $apartment) {
                if ($this->saveApartment($apartment)) {
                    $results['apartments']['success']++;
                } else {
                    $results['apartments']['failed']++;
                    $results['errors'][] = "Failed to sync apartment: {$apartment->name}";
                }
            }

            // Sync all units
            $units = \App\Models\Unit::all();
            foreach ($units as $unit) {
                if ($this->saveUnit($unit)) {
                    $results['units']['success']++;
                } else {
                    $results['units']['failed']++;
                    $results['errors'][] = "Failed to sync unit: {$unit->unit_number}";
                }
            }

        } catch (Exception $e) {
            $results['errors'][] = "Sync error: " . $e->getMessage();
        }

        return $results;
    }

    /**
     * Validate Firebase connection
     */
    public function testConnection()
    {
        try {
            $testData = ['test' => 'connection', 'timestamp' => now()->toISOString()];
            $result = $this->saveToFirebase('test', $testData);
            
            if ($result) {
                $this->deleteFromFirebase('test');
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            Log::error("Firebase connection test failed: " . $e->getMessage());
            return false;
        }
    }
} 
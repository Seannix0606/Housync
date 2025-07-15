<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;

class FirebaseDataService
{
    private $databaseUrl;

    public function __construct()
    {
        $this->databaseUrl = 'https://housesync-dd86e-default-rtdb.firebaseio.com/';
    }

    /**
     * Generic method to save data to Firebase
     */
    public function saveToFirebase($path, $data, $id = null)
    {
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
                    'content' => json_encode($data)
                ]
            ]);
            
            $result = file_get_contents($firebaseUrl, false, $context);
            
            if ($result === false) {
                Log::warning("Failed to save data to Firebase: {$path}");
                return false;
            }
            
            Log::info("Data saved to Firebase successfully: {$path}");
            return true;
            
        } catch (Exception $e) {
            Log::error("Error saving data to Firebase: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generic method to get data from Firebase
     */
    public function getFromFirebase($path, $id = null)
    {
        try {
            $firebaseUrl = $this->databaseUrl . $path;
            
            if ($id) {
                $firebaseUrl .= '/' . $id;
            }
            
            $firebaseUrl .= '.json';
            
            $result = file_get_contents($firebaseUrl);
            
            if ($result === false) {
                Log::warning("Failed to get data from Firebase: {$path}");
                return null;
            }
            
            return json_decode($result, true);
            
        } catch (Exception $e) {
            Log::error("Error getting data from Firebase: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generic method to delete data from Firebase
     */
    public function deleteFromFirebase($path, $id = null)
    {
        try {
            $firebaseUrl = $this->databaseUrl . $path;
            
            if ($id) {
                $firebaseUrl .= '/' . $id;
            }
            
            $firebaseUrl .= '.json';
            
            $context = stream_context_create([
                'http' => [
                    'method' => 'DELETE'
                ]
            ]);
            
            $result = file_get_contents($firebaseUrl, false, $context);
            
            if ($result === false) {
                Log::warning("Failed to delete data from Firebase: {$path}");
                return false;
            }
            
            Log::info("Data deleted from Firebase successfully: {$path}");
            return true;
            
        } catch (Exception $e) {
            Log::error("Error deleting data from Firebase: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Save user data to Firebase
     */
    public function saveUser($user)
    {
        $userData = [
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

        return $this->saveToFirebase('users', $userData, $user->id);
    }

    /**
     * Save apartment data to Firebase
     */
    public function saveApartment($apartment)
    {
        $apartmentData = [
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
            'created_at' => $apartment->created_at->toISOString(),
            'updated_at' => $apartment->updated_at->toISOString(),
        ];

        // Save to main apartments collection
        $this->saveToFirebase('apartments', $apartmentData, $apartment->id);
        
        // Also save under landlord's apartments
        return $this->saveToFirebase("landlords/{$apartment->landlord_id}/apartments", $apartmentData, $apartment->id);
    }

    /**
     * Save unit data to Firebase
     */
    public function saveUnit($unit)
    {
        $unitData = [
            'id' => $unit->id,
            'apartment_id' => $unit->apartment_id,
            'unit_number' => $unit->unit_number,
            'unit_type' => $unit->unit_type,
            'rent_amount' => $unit->rent_amount,
            'status' => $unit->status,
            'leasing_type' => $unit->leasing_type,
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

        // Save to main units collection
        $this->saveToFirebase('units', $unitData, $unit->id);
        
        // Also save under apartment's units
        $this->saveToFirebase("apartments/{$unit->apartment_id}/units", $unitData, $unit->id);
        
        // Also save under landlord's units (get landlord_id from apartment)
        $apartment = \App\Models\Apartment::find($unit->apartment_id);
        if ($apartment) {
            return $this->saveToFirebase("landlords/{$apartment->landlord_id}/units", $unitData, $unit->id);
        }
        
        return true;
    }

    /**
     * Delete user from Firebase
     */
    public function deleteUser($userId)
    {
        return $this->deleteFromFirebase('users', $userId);
    }

    /**
     * Delete apartment from Firebase
     */
    public function deleteApartment($apartment)
    {
        // Delete from main apartments collection
        $this->deleteFromFirebase('apartments', $apartment->id);
        
        // Delete from landlord's apartments
        $this->deleteFromFirebase("landlords/{$apartment->landlord_id}/apartments", $apartment->id);
        
        // Delete all units of this apartment
        $units = \App\Models\Unit::where('apartment_id', $apartment->id)->get();
        foreach ($units as $unit) {
            $this->deleteUnit($unit);
        }
        
        return true;
    }

    /**
     * Delete unit from Firebase
     */
    public function deleteUnit($unit)
    {
        // Delete from main units collection
        $this->deleteFromFirebase('units', $unit->id);
        
        // Delete from apartment's units
        $this->deleteFromFirebase("apartments/{$unit->apartment_id}/units", $unit->id);
        
        // Delete from landlord's units
        $apartment = \App\Models\Apartment::find($unit->apartment_id);
        if ($apartment) {
            $this->deleteFromFirebase("landlords/{$apartment->landlord_id}/units", $unit->id);
        }
        
        return true;
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
            'users' => 0,
            'apartments' => 0,
            'units' => 0,
            'errors' => []
        ];

        try {
            // Sync all users
            $users = \App\Models\User::all();
            foreach ($users as $user) {
                if ($this->saveUser($user)) {
                    $results['users']++;
                } else {
                    $results['errors'][] = "Failed to sync user: {$user->email}";
                }
            }

            // Sync all apartments
            $apartments = \App\Models\Apartment::all();
            foreach ($apartments as $apartment) {
                if ($this->saveApartment($apartment)) {
                    $results['apartments']++;
                } else {
                    $results['errors'][] = "Failed to sync apartment: {$apartment->name}";
                }
            }

            // Sync all units
            $units = \App\Models\Unit::all();
            foreach ($units as $unit) {
                if ($this->saveUnit($unit)) {
                    $results['units']++;
                } else {
                    $results['errors'][] = "Failed to sync unit: {$unit->unit_number}";
                }
            }

        } catch (Exception $e) {
            $results['errors'][] = "Sync error: " . $e->getMessage();
        }

        return $results;
    }
} 
<?php

namespace App\Services;

use App\Services\FirebaseDataService;
use Illuminate\Support\Facades\Log;

trait FirebaseSyncTrait
{
    /**
     * Boot the trait
     */
    protected static function bootFirebaseSyncTrait()
    {
        // Sync to Firebase when model is created
        static::created(function ($model) {
            $model->syncToFirebase('created');
        });

        // Sync to Firebase when model is updated
        static::updated(function ($model) {
            $model->syncToFirebase('updated');
        });

        // Delete from Firebase when model is deleted
        static::deleted(function ($model) {
            $model->deleteFromFirebase();
        });
    }

    /**
     * Sync the model to Firebase
     */
    public function syncToFirebase($event = null)
    {
        if (!$this->shouldSyncToFirebase()) {
            return;
        }

        try {
            $firebaseService = app(FirebaseDataService::class);
            
            $result = match($this->getTable()) {
                'users' => $firebaseService->saveUser($this),
                'apartments' => $firebaseService->saveApartment($this),
                'units' => $firebaseService->saveUnit($this),
                default => false
            };

            if ($result) {
                Log::info("Model synced to Firebase: {$this->getTable()}#{$this->id} ({$event})");
            } else {
                Log::error("Failed to sync model to Firebase: {$this->getTable()}#{$this->id} ({$event})");
            }

        } catch (\Exception $e) {
            Log::error("Error syncing model to Firebase: {$this->getTable()}#{$this->id} - " . $e->getMessage());
        }
    }

    /**
     * Delete the model from Firebase
     */
    public function deleteFromFirebase()
    {
        if (!$this->shouldSyncToFirebase()) {
            return;
        }

        try {
            $firebaseService = app(FirebaseDataService::class);
            
            $result = match($this->getTable()) {
                'users' => $firebaseService->deleteUser($this),
                'apartments' => $firebaseService->deleteApartment($this),
                'units' => $firebaseService->deleteUnit($this),
                default => false
            };

            if ($result) {
                Log::info("Model deleted from Firebase: {$this->getTable()}#{$this->id}");
            } else {
                Log::error("Failed to delete model from Firebase: {$this->getTable()}#{$this->id}");
            }

        } catch (\Exception $e) {
            Log::error("Error deleting model from Firebase: {$this->getTable()}#{$this->id} - " . $e->getMessage());
        }
    }

    /**
     * Force sync to Firebase (bypass shouldSyncToFirebase check)
     */
    public function forceSyncToFirebase()
    {
        $originalShouldSync = $this->shouldSyncToFirebase();
        $this->firebaseSyncEnabled = true;
        
        $this->syncToFirebase('forced');
        
        $this->firebaseSyncEnabled = $originalShouldSync;
    }

    /**
     * Determine if the model should sync to Firebase
     */
    protected function shouldSyncToFirebase()
    {
        // Check if Firebase sync is globally enabled
        if (config('app.firebase_sync_enabled', true) === false) {
            return false;
        }

        // Check if sync is disabled for this specific model instance
        if (property_exists($this, 'firebaseSyncEnabled') && $this->firebaseSyncEnabled === false) {
            return false;
        }

        // Check if we're in a testing environment and sync is disabled for tests
        if (app()->environment('testing') && config('app.firebase_sync_in_tests', false) === false) {
            return false;
        }

        return true;
    }

    /**
     * Disable Firebase sync for this model instance
     */
    public function disableFirebaseSync()
    {
        $this->firebaseSyncEnabled = false;
        return $this;
    }

    /**
     * Enable Firebase sync for this model instance
     */
    public function enableFirebaseSync()
    {
        $this->firebaseSyncEnabled = true;
        return $this;
    }

    /**
     * Temporarily disable Firebase sync for a callback
     */
    public function withoutFirebaseSync(callable $callback)
    {
        $originalState = $this->firebaseSyncEnabled ?? true;
        $this->firebaseSyncEnabled = false;
        
        try {
            return $callback($this);
        } finally {
            $this->firebaseSyncEnabled = $originalState;
        }
    }

    /**
     * Get Firebase sync status
     */
    public function getFirebaseSyncStatus()
    {
        return [
            'enabled' => $this->shouldSyncToFirebase(),
            'global_enabled' => config('app.firebase_sync_enabled', true),
            'model_enabled' => $this->firebaseSyncEnabled ?? true,
            'environment' => app()->environment(),
            'testing_sync' => config('app.firebase_sync_in_tests', false),
        ];
    }
} 
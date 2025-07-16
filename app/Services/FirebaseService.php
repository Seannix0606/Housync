<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FirebaseService
{
    protected $auth;
    protected $database;
    protected $storage;
    protected $messaging;

    public function __construct()
    {
        $this->auth = app('firebase.auth');
        $this->database = app('firebase.database');
        $this->storage = app('firebase.storage');
        $this->messaging = app('firebase.messaging');
    }

    /**
     * Check if Firebase is properly configured
     */
    public function isConfigured(): bool
    {
        return $this->auth !== null && $this->database !== null;
    }

    /**
     * Create a custom token for user authentication with role-based claims
     */
    public function createCustomToken(string $uid, array $claims = []): ?string
    {
        try {
            if (!$this->auth) {
                Log::error('Firebase Auth not initialized');
                return null;
            }

            // Get user from database to include role information
            $user = User::find($uid);
            if ($user) {
                $claims['role'] = $user->role;
                $claims['status'] = $user->status;
                $claims['email'] = $user->email;
                $claims['name'] = $user->name;
                
                // Add landlord-specific claims
                if ($user->role === 'landlord') {
                    $claims['landlord_id'] = $user->id;
                    $claims['is_approved'] = $user->status === 'approved';
                }
            }

            return $this->auth->createCustomToken($uid, $claims);
        } catch (\Exception $e) {
            Log::error('Failed to create custom token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify Firebase ID token and validate user permissions
     */
    public function verifyIdToken(string $idToken): ?array
    {
        try {
            if (!$this->auth) {
                Log::error('Firebase Auth not initialized');
                return null;
            }

            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            $claims = $verifiedIdToken->claims()->toArray();
            
            // Validate user exists in our database
            $user = User::find($claims['uid'] ?? null);
            if (!$user) {
                Log::warning('Firebase token verified but user not found in database');
                return null;
            }

            // Check if user status is still valid
            if ($user->role === 'landlord' && $user->status !== 'approved') {
                Log::warning('Firebase token verified but landlord not approved');
                return null;
            }

            return $claims;
        } catch (\Exception $e) {
            Log::error('Failed to verify ID token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user by UID with role validation
     */
    public function getUser(string $uid): ?array
    {
        try {
            if (!$this->auth) {
                Log::error('Firebase Auth not initialized');
                return null;
            }

            $user = $this->auth->getUser($uid);
            
            // Validate against our database
            $dbUser = User::find($uid);
            if (!$dbUser) {
                Log::warning('Firebase user exists but not found in database');
                return null;
            }

            return [
                'uid' => $user->uid,
                'email' => $user->email,
                'displayName' => $user->displayName,
                'photoURL' => $user->photoUrl,
                'emailVerified' => $user->emailVerified,
                'disabled' => $user->disabled,
                'role' => $dbUser->role,
                'status' => $dbUser->status,
                'metadata' => [
                    'createdAt' => $user->metadata->createdAt,
                    'lastSignInAt' => $user->metadata->lastSignInAt,
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get user: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a new user with role-based custom claims
     */
    public function createUser(array $properties): ?array
    {
        try {
            if (!$this->auth) {
                Log::error('Firebase Auth not initialized');
                return null;
            }

            $userRecord = $this->auth->createUser($properties);
            
            // Set custom claims based on role
            if (isset($properties['role'])) {
                $claims = [
                    'role' => $properties['role'],
                    'status' => $properties['status'] ?? 'active'
                ];
                
                $this->auth->setCustomUserClaims($userRecord->uid, $claims);
            }

            return [
                'uid' => $userRecord->uid,
                'email' => $userRecord->email,
                'displayName' => $userRecord->displayName,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to create user: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update user with role validation
     */
    public function updateUser(string $uid, array $properties): ?array
    {
        try {
            if (!$this->auth) {
                Log::error('Firebase Auth not initialized');
                return null;
            }

            // Validate user exists in our database
            $dbUser = User::find($uid);
            if (!$dbUser) {
                Log::error('Cannot update Firebase user: User not found in database');
                return null;
            }

            $userRecord = $this->auth->updateUser($uid, $properties);
            
            // Update custom claims if role or status changed
            if (isset($properties['role']) || isset($properties['status'])) {
                $claims = [
                    'role' => $properties['role'] ?? $dbUser->role,
                    'status' => $properties['status'] ?? $dbUser->status
                ];
                
                $this->auth->setCustomUserClaims($uid, $claims);
            }

            return [
                'uid' => $userRecord->uid,
                'email' => $userRecord->email,
                'displayName' => $userRecord->displayName,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to update user: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete user with proper cleanup
     */
    public function deleteUser(string $uid): bool
    {
        try {
            if (!$this->auth) {
                Log::error('Firebase Auth not initialized');
                return false;
            }

            // Delete user data from database first
            $this->deleteUserData($uid);
            
            // Then delete from Firebase Auth
            $this->auth->deleteUser($uid);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Store data in Firebase Realtime Database with authentication
     */
    public function storeData(string $path, array $data): bool
    {
        try {
            if (!$this->database) {
                Log::error('Firebase Database not initialized');
                return false;
            }

            // Add metadata for security tracking
            $data['updated_at'] = now()->toISOString();
            $data['updated_by'] = Auth::id();

            $this->database->getReference($path)->set($data);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to store data: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get data from Firebase Realtime Database with role validation
     */
    public function getData(string $path): ?array
    {
        try {
            if (!$this->database) {
                Log::error('Firebase Database not initialized');
                return null;
            }

            $snapshot = $this->database->getReference($path)->getSnapshot();
            $data = $snapshot->getValue();
            
            // Log data access for security audit
            Log::info('Firebase data accessed', [
                'path' => $path,
                'user_id' => Auth::id(),
                'user_role' => Auth::user()?->role,
                'timestamp' => now()->toISOString()
            ]);
            
            return $data;
        } catch (\Exception $e) {
            Log::error('Failed to get data: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete user data from Firebase database
     */
    private function deleteUserData(string $uid): bool
    {
        try {
            if (!$this->database) {
                return false;
            }

            // Delete from all user-related paths
            $paths = [
                "users/{$uid}",
                "landlords/{$uid}",
                "super_admins/{$uid}",
                "tenants/{$uid}"
            ];

            foreach ($paths as $path) {
                $this->database->getReference($path)->remove();
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete user data: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate user permissions for specific data access
     */
    public function validateUserPermissions(string $uid, string $resource, string $action = 'read'): bool
    {
        try {
            $user = User::find($uid);
            if (!$user) {
                return false;
            }

            // Super admins have full access
            if ($user->role === 'super_admin') {
                return true;
            }

            // Landlords can only access their own data
            if ($user->role === 'landlord' && $user->status === 'approved') {
                // Check if resource belongs to this landlord
                return $this->isResourceOwnedByLandlord($resource, $uid);
            }

            // Tenants have limited access
            if ($user->role === 'tenant') {
                return $this->isTenantAllowedAccess($resource, $uid, $action);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to validate permissions: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if resource is owned by landlord
     */
    private function isResourceOwnedByLandlord(string $resource, string $landlordId): bool
    {
        // Parse resource path to determine ownership
        $parts = explode('/', $resource);
        
        if (count($parts) >= 2) {
            $resourceType = $parts[0];
            $resourceId = $parts[1];
            
            switch ($resourceType) {
                case 'apartments':
                    return \App\Models\Apartment::where('id', $resourceId)
                        ->where('landlord_id', $landlordId)
                        ->exists();
                        
                case 'units':
                    return \App\Models\Unit::whereHas('apartment', function($query) use ($landlordId) {
                        $query->where('landlord_id', $landlordId);
                    })->where('id', $resourceId)->exists();
            }
        }
        
        return false;
    }

    /**
     * Check if tenant is allowed to access resource
     */
    private function isTenantAllowedAccess(string $resource, string $tenantId, string $action): bool
    {
        // Tenants can only read their own data
        if ($action !== 'read') {
            return false;
        }

        $parts = explode('/', $resource);
        if (count($parts) >= 2 && $parts[0] === 'tenants' && $parts[1] === $tenantId) {
            return true;
        }

        return false;
    }

    /**
     * Generate secure Firebase token for current user
     */
    public function generateTokenForCurrentUser(): ?string
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        return $this->createCustomToken($user->id);
    }

    /**
     * Revoke all tokens for a user (useful for logout or security incidents)
     */
    public function revokeUserTokens(string $uid): bool
    {
        try {
            if (!$this->auth) {
                Log::error('Firebase Auth not initialized');
                return false;
            }

            $this->auth->revokeRefreshTokens($uid);
            Log::info('All tokens revoked for user', ['uid' => $uid]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to revoke tokens: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify Firebase App Check token
     */
    public function verifyAppCheckToken(string $appCheckToken): bool
    {
        try {
            if (!class_exists('\Kreait\Firebase\AppCheck\AppCheck')) {
                Log::warning('Firebase App Check not available - install firebase/php-jwt package');
                return true; // Allow requests if App Check is not configured
            }

            // Get App Check instance
            $factory = app('firebase.factory');
            if (!$factory) {
                Log::error('Firebase factory not available');
                return false;
            }

            $appCheck = $factory->createAppCheck();
            
            // Verify the App Check token
            $verifiedToken = $appCheck->verifyToken($appCheckToken);
            
            if ($verifiedToken) {
                Log::info('App Check token verified successfully');
                return true;
            } else {
                Log::warning('App Check token verification failed');
                return false;
            }
        } catch (\Exception $e) {
            Log::error('App Check token verification error: ' . $e->getMessage());
            
            // In development, you might want to allow requests even if App Check fails
            if (app()->environment('local', 'development')) {
                Log::warning('App Check verification failed in development - allowing request');
                return true;
            }
            
            return false;
        }
    }

    /**
     * Get App Check configuration for frontend
     */
    public function getAppCheckConfig(): array
    {
        return [
            'project_id' => config('firebase.project_id'),
            'recaptcha_site_key' => env('FIREBASE_RECAPTCHA_SITE_KEY'),
            'debug_mode' => app()->environment('local', 'development'),
            'debug_token' => env('FIREBASE_APP_CHECK_DEBUG_TOKEN', false)
        ];
    }
} 
<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

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
     * Create a custom token for user authentication
     */
    public function createCustomToken(string $uid, array $claims = []): ?string
    {
        try {
            if (!$this->auth) {
                Log::error('Firebase Auth not initialized');
                return null;
            }

            return $this->auth->createCustomToken($uid, $claims);
        } catch (\Exception $e) {
            Log::error('Failed to create custom token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify Firebase ID token
     */
    public function verifyIdToken(string $idToken): ?array
    {
        try {
            if (!$this->auth) {
                Log::error('Firebase Auth not initialized');
                return null;
            }

            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            return $verifiedIdToken->claims()->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to verify ID token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user by UID
     */
    public function getUser(string $uid): ?array
    {
        try {
            if (!$this->auth) {
                Log::error('Firebase Auth not initialized');
                return null;
            }

            $user = $this->auth->getUser($uid);
            return [
                'uid' => $user->uid,
                'email' => $user->email,
                'displayName' => $user->displayName,
                'photoURL' => $user->photoUrl,
                'emailVerified' => $user->emailVerified,
                'disabled' => $user->disabled,
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
     * Create a new user
     */
    public function createUser(array $properties): ?array
    {
        try {
            if (!$this->auth) {
                Log::error('Firebase Auth not initialized');
                return null;
            }

            $userRecord = $this->auth->createUser($properties);
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
     * Update user
     */
    public function updateUser(string $uid, array $properties): ?array
    {
        try {
            if (!$this->auth) {
                Log::error('Firebase Auth not initialized');
                return null;
            }

            $userRecord = $this->auth->updateUser($uid, $properties);
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
     * Delete user
     */
    public function deleteUser(string $uid): bool
    {
        try {
            if (!$this->auth) {
                Log::error('Firebase Auth not initialized');
                return false;
            }

            $this->auth->deleteUser($uid);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Store data in Firebase Realtime Database
     */
    public function storeData(string $path, array $data): bool
    {
        try {
            if (!$this->database) {
                Log::error('Firebase Database not initialized');
                return false;
            }

            $this->database->getReference($path)->set($data);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to store data: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get data from Firebase Realtime Database
     */
    public function getData(string $path): ?array
    {
        try {
            if (!$this->database) {
                Log::error('Firebase Database not initialized');
                return null;
            }

            $snapshot = $this->database->getReference($path)->getSnapshot();
            return $snapshot->getValue();
        } catch (\Exception $e) {
            Log::error('Failed to get data: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update data in Firebase Realtime Database
     */
    public function updateData(string $path, array $data): bool
    {
        try {
            if (!$this->database) {
                Log::error('Firebase Database not initialized');
                return false;
            }

            $this->database->getReference($path)->update($data);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update data: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete data from Firebase Realtime Database
     */
    public function deleteData(string $path): bool
    {
        try {
            if (!$this->database) {
                Log::error('Firebase Database not initialized');
                return false;
            }

            $this->database->getReference($path)->remove();
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete data: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send push notification
     */
    public function sendNotification(string $token, string $title, string $body, array $data = []): bool
    {
        try {
            if (!$this->messaging) {
                Log::error('Firebase Messaging not initialized');
                return false;
            }

            $message = [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
            ];

            $this->messaging->send($message);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to multiple tokens
     */
    public function sendMulticastNotification(array $tokens, string $title, string $body, array $data = []): array
    {
        try {
            if (!$this->messaging) {
                Log::error('Firebase Messaging not initialized');
                return ['success' => false, 'results' => []];
            }

            $message = [
                'tokens' => $tokens,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
            ];

            $response = $this->messaging->sendMulticast($message);
            
            return [
                'success' => true,
                'successCount' => $response->successCount(),
                'failureCount' => $response->failureCount(),
                'results' => $response->getItems(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send multicast notification: ' . $e->getMessage());
            return ['success' => false, 'results' => []];
        }
    }
} 
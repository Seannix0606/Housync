<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

class FirebaseAuthMiddleware
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $requiredRole = null): Response
    {
        // Check if Firebase is configured
        if (!$this->firebaseService->isConfigured()) {
            Log::error('Firebase not configured for authentication');
            return response()->json(['error' => 'Service temporarily unavailable'], 503);
        }

        // Get Firebase token from request
        $firebaseToken = $this->extractFirebaseToken($request);
        
        if (!$firebaseToken) {
            return response()->json(['error' => 'Firebase authentication token required'], 401);
        }

        // Verify Firebase token
        $tokenClaims = $this->firebaseService->verifyIdToken($firebaseToken);
        
        if (!$tokenClaims) {
            return response()->json(['error' => 'Invalid Firebase token'], 401);
        }

        // Get user ID from token
        $uid = $tokenClaims['uid'] ?? null;
        if (!$uid) {
            return response()->json(['error' => 'Invalid token format'], 401);
        }

        // Validate user exists in our database and is active
        $user = \App\Models\User::find($uid);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Check user status
        if ($user->role === 'landlord' && $user->status !== 'approved') {
            return response()->json(['error' => 'Account not approved'], 403);
        }

        // Check role requirements
        if ($requiredRole && !$this->checkUserRole($user, $requiredRole)) {
            return response()->json(['error' => 'Insufficient permissions'], 403);
        }

        // Validate resource access permissions
        if (!$this->validateResourceAccess($request, $user)) {
            return response()->json(['error' => 'Access denied to requested resource'], 403);
        }

        // Set user in request for later use
        $request->attributes->set('firebase_user', $user);
        $request->attributes->set('firebase_token_claims', $tokenClaims);

        // Log successful authentication
        Log::info('Firebase authentication successful', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return $next($request);
    }

    /**
     * Extract Firebase token from request
     */
    private function extractFirebaseToken(Request $request): ?string
    {
        // Check Authorization header
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            return substr($authHeader, 7);
        }

        // Check custom Firebase header
        $firebaseHeader = $request->header('X-Firebase-Token');
        if ($firebaseHeader) {
            return $firebaseHeader;
        }

        // Check query parameter (less secure, only for testing)
        if (app()->environment('local', 'testing')) {
            return $request->query('firebase_token');
        }

        return null;
    }

    /**
     * Check if user has required role
     */
    private function checkUserRole(\App\Models\User $user, string $requiredRole): bool
    {
        $roles = explode(',', $requiredRole);
        
        // Super admin has access to everything
        if ($user->role === 'super_admin') {
            return true;
        }

        return in_array($user->role, $roles);
    }

    /**
     * Validate resource access permissions
     */
    private function validateResourceAccess(Request $request, \App\Models\User $user): bool
    {
        $path = $request->path();
        $method = $request->method();
        
        // Super admin has full access
        if ($user->role === 'super_admin') {
            return true;
        }

        // Parse resource from URL
        $resource = $this->parseResourceFromPath($path);
        
        if (!$resource) {
            return true; // Allow access to non-resource endpoints
        }

        // Validate permissions using Firebase service
        $action = $this->mapHttpMethodToAction($method);
        
        return $this->firebaseService->validateUserPermissions(
            $user->id,
            $resource,
            $action
        );
    }

    /**
     * Parse resource information from request path
     */
    private function parseResourceFromPath(string $path): ?string
    {
        // Remove API prefix if present
        $path = preg_replace('/^api\//', '', $path);
        
        // Match patterns like /apartments/123, /units/456, etc.
        if (preg_match('/^(apartments|units|users|landlords|tenants)\/(\d+)/', $path, $matches)) {
            return $matches[1] . '/' . $matches[2];
        }
        
        // Match collection endpoints like /apartments, /units
        if (preg_match('/^(apartments|units|users|landlords|tenants)$/', $path, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Map HTTP method to action
     */
    private function mapHttpMethodToAction(string $method): string
    {
        return match(strtoupper($method)) {
            'GET' => 'read',
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'read'
        };
    }

    /**
     * Generate Firebase token for current Laravel user (helper method)
     */
    public static function generateTokenForUser(\App\Models\User $user): ?string
    {
        $firebaseService = app(FirebaseService::class);
        return $firebaseService->createCustomToken($user->id);
    }

    /**
     * Revoke Firebase tokens for user (helper method)
     */
    public static function revokeUserTokens(string $uid): bool
    {
        $firebaseService = app(FirebaseService::class);
        return $firebaseService->revokeUserTokens($uid);
    }
} 
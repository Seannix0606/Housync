<?php

namespace App\Services;

use App\Models\User;
use App\Models\Unit;
use App\Models\TenantAssignment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantAssignmentService
{
    /**
     * Assign a tenant to a unit and create their account
     */
    public function assignTenantToUnit($unitId, $tenantData, $landlordId)
    {
        try {
            // Check if unit is available
            $unit = Unit::findOrFail($unitId);
            if ($unit->status !== 'available') {
                throw new \Exception('Unit is not available for assignment.');
            }

            // Use provided email and password if present, otherwise generate
            $email = $tenantData['email'] ?? $this->generateTenantEmail($tenantData['name']);
            $password = $tenantData['password'] ?? $this->generatePassword();

            // Create tenant user account
            $tenant = User::create([
                'name' => $tenantData['name'],
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'tenant',
                'status' => 'active',
                'phone' => $tenantData['phone'] ?? null,
                'address' => $tenantData['address'] ?? null,
                'must_change_password' => true,
            ]);

            // Create tenant assignment
            $assignment = TenantAssignment::create([
                'unit_id' => $unitId,
                'tenant_id' => $tenant->id,
                'landlord_id' => $landlordId,
                'lease_start_date' => $tenantData['lease_start_date'],
                'lease_end_date' => $tenantData['lease_end_date'],
                'rent_amount' => $tenantData['rent_amount'],
                'security_deposit' => $tenantData['security_deposit'] ?? 0,
                'status' => 'pending',
                'notes' => $tenantData['notes'] ?? null,
                'generated_password' => $password, // Store the generated password temporarily
            ]);

            // Update unit status
            $unit->update([
                'status' => 'occupied',
                'tenant_count' => 1,
            ]);

            return [
                'success' => true,
                'tenant' => $tenant,
                'assignment' => $assignment,
                'credentials' => [
                    'email' => $email,
                    'password' => $password,
                ],
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate a unique email for the tenant
     */
    private function generateTenantEmail($name)
    {
        $baseName = Str::slug($name);
        $email = $baseName . '@housesync.tenant';
        
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $baseName . $counter . '@housesync.tenant';
            $counter++;
        }
        
        return $email;
    }

    /**
     * Generate a secure password
     */
    private function generatePassword()
    {
        return Str::random(8);
    }

    /**
     * Get tenant assignments for a landlord
     */
    public function getLandlordAssignments($landlordId, $filters = [])
    {
        $query = TenantAssignment::with(['tenant', 'unit.apartment'])
            ->where('landlord_id', $landlordId);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['documents_uploaded'])) {
            $query->where('documents_uploaded', $filters['documents_uploaded']);
        }

        if (isset($filters['documents_verified'])) {
            $query->where('documents_verified', $filters['documents_verified']);
        }

        return $query->latest()->paginate(15);
    }

    /**
     * Get tenant assignment details
     */
    public function getAssignmentDetails($assignmentId, $landlordId = null)
    {
        $query = TenantAssignment::with(['tenant', 'unit.apartment', 'documents']);
        
        if ($landlordId) {
            $query->where('landlord_id', $landlordId);
        }
        
        return $query->findOrFail($assignmentId);
    }

    /**
     * Update assignment status
     */
    public function updateAssignmentStatus($assignmentId, $status, $landlordId)
    {
        $assignment = TenantAssignment::where('landlord_id', $landlordId)
            ->findOrFail($assignmentId);

        $assignment->update(['status' => $status]);

        // Update unit status if assignment is terminated
        if ($status === 'terminated') {
            $assignment->unit->update([
                'status' => 'available',
                'tenant_count' => 0,
            ]);
        }

        return $assignment;
    }

    /**
     * Mark documents as uploaded
     */
    public function markDocumentsUploaded($assignmentId, $tenantId)
    {
        $assignment = TenantAssignment::where('tenant_id', $tenantId)
            ->findOrFail($assignmentId);

        $assignment->update(['documents_uploaded' => true]);

        return $assignment;
    }

    /**
     * Verify documents
     */
    public function verifyDocuments($assignmentId, $verifiedBy, $notes = null)
    {
        $assignment = TenantAssignment::findOrFail($assignmentId);
        
        $assignment->update([
            'documents_verified' => true,
            'verification_notes' => $notes,
        ]);

        // Update assignment status to active if documents are verified
        if ($assignment->status === 'pending') {
            $assignment->update(['status' => 'active']);
        }

        return $assignment;
    }

    /**
     * Get statistics for landlord
     */
    public function getLandlordStats($landlordId)
    {
        $assignments = TenantAssignment::where('landlord_id', $landlordId);

        return [
            'total_assignments' => $assignments->count(),
            'active_assignments' => $assignments->where('status', 'active')->count(),
            'pending_assignments' => $assignments->where('status', 'pending')->count(),
            'pending_documents' => $assignments->where('documents_uploaded', false)->count(),
            'documents_uploaded' => $assignments->where('documents_uploaded', true)->count(),
            'documents_verified' => $assignments->where('documents_verified', true)->count(),
            'total_revenue' => $assignments->where('status', 'active')->sum('rent_amount'),
        ];
    }
} 
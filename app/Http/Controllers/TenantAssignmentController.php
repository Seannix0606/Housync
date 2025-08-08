<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\TenantAssignment;
use App\Models\TenantDocument;
use App\Services\TenantAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TenantAssignmentController extends Controller
{
    protected $assignmentService;

    public function __construct(TenantAssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    /**
     * Show tenant assignments for landlord
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'documents_uploaded', 'documents_verified']);
        $assignments = $this->assignmentService->getLandlordAssignments(Auth::id(), $filters);
        $stats = $this->assignmentService->getLandlordStats(Auth::id());

        return view('landlord.tenant-assignments', compact('assignments', 'stats', 'filters'));
    }

    /**
     * Show form to assign tenant to unit
     */
    public function create($unitId)
    {
        $unit = Unit::whereHas('apartment', function($query) {
            $query->where('landlord_id', Auth::id());
        })->with('apartment')->findOrFail($unitId);

        if ($unit->status !== 'available') {
            return back()->with('error', 'This unit is not available for assignment.');
        }

        return view('landlord.assign-tenant', compact('unit'));
    }

    /**
     * Assign tenant to unit
     */
    public function store(Request $request, $unitId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'lease_start_date' => 'required|date|after_or_equal:today',
            'lease_end_date' => 'required|date|after:lease_start_date',
            'rent_amount' => 'required|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $result = $this->assignmentService->assignTenantToUnit(
            $unitId,
            $request->all(),
            Auth::id()
        );

        if ($result['success']) {
            return redirect()->route('landlord.tenant-assignments')
                ->with('success', 'Tenant assigned successfully!')
                ->with('credentials', $result['credentials']);
        } else {
            return back()->withInput()->with('error', $result['message']);
        }
    }

    /**
     * Reassign a previously vacated tenant (existing tenant) to a new unit
     */
    public function reassign(Request $request, $assignmentId)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'lease_start_date' => 'required|date|after_or_equal:today',
            'lease_end_date' => 'required|date|after:lease_start_date',
            'rent_amount' => 'required|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Fetch the vacated assignment within landlord scope
        $assignment = TenantAssignment::where('landlord_id', Auth::id())
            ->with('tenant')
            ->findOrFail($assignmentId);

        if ($assignment->status !== 'terminated') {
            return back()->with('error', 'Only vacated tenants can be reassigned.');
        }

        // Get the old unit to free it up
        $oldUnit = $assignment->unit;
        
        // Ensure target unit belongs to landlord and is available
        $newUnit = Unit::whereHas('apartment', function($q) {
            $q->where('landlord_id', Auth::id());
        })->findOrFail($request->unit_id);

        if ($newUnit->status !== 'available') {
            return back()->with('error', 'Selected unit is not available.');
        }

        // Free up the old unit (make it available again)
        $oldUnit->update([
            'status' => 'available',
            'tenant_count' => 0,
        ]);

        // Update the existing assignment with new unit and details
        $assignment->update([
            'unit_id' => $newUnit->id,
            'lease_start_date' => $request->lease_start_date,
            'lease_end_date' => $request->lease_end_date,
            'rent_amount' => $request->rent_amount,
            'security_deposit' => $request->security_deposit ?? 0,
            'status' => 'active',
            'notes' => $request->notes ?? null,
            'documents_uploaded' => false, // Reset document status for new assignment
            'documents_verified' => false,
        ]);

        // Update new unit status to occupied
        $newUnit->update([
            'status' => 'occupied',
            'tenant_count' => 1,
        ]);

        return redirect()->route('landlord.tenant-assignments')
            ->with('success', 'Tenant reassigned successfully. Credentials remain the same.');
    }

    /**
     * Show tenant assignment details
     */
    public function show($id)
    {
        $assignment = $this->assignmentService->getAssignmentDetails($id, Auth::id());
        return view('landlord.assignment-details', compact('assignment'));
    }

    /**
     * Update assignment status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,terminated',
        ]);

        $assignment = $this->assignmentService->updateAssignmentStatus(
            $id,
            $request->status,
            Auth::id()
        );

        return back()->with('success', 'Assignment status updated successfully.');
    }

    /**
     * Show tenant dashboard
     */
    public function tenantDashboard()
    {
        $tenant = Auth::user();
        $assignment = $tenant->tenantAssignments()->with(['unit.apartment', 'documents'])->first();

        if (!$assignment) {
            return view('tenant.no-assignment');
        }

        return view('tenant.dashboard', compact('assignment'));
    }

    /**
     * Show document upload form for tenant
     */
    public function uploadDocuments()
    {
        $tenant = Auth::user();
        $assignment = $tenant->tenantAssignments()->with(['unit.apartment', 'documents'])->first();

        if (!$assignment) {
            return redirect()->route('tenant.dashboard')->with('error', 'No assignment found.');
        }

        return view('tenant.upload-documents', compact('assignment'));
    }

    /**
     * Store uploaded documents
     */
    public function storeDocuments(Request $request)
    {
        $request->validate([
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'document_types.*' => 'required|string',
        ]);

        $tenant = Auth::user();
        $assignment = $tenant->tenantAssignments()->first();

        if (!$assignment) {
            return back()->with('error', 'No assignment found.');
        }

        try {
            foreach ($request->file('documents') as $index => $file) {
                $documentType = $request->document_types[$index];
                
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('tenant-documents', $fileName, 'public');

                TenantDocument::create([
                    'tenant_assignment_id' => $assignment->id,
                    'document_type' => $documentType,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'verification_status' => 'pending',
                ]);
            }

            // Mark documents as uploaded and update assignment status
            $assignment->update([
                'documents_uploaded' => true,
                'documents_verified' => false, // New documents are always pending verification
            ]);

            return redirect()->route('tenant.dashboard')
                ->with('success', 'Documents uploaded successfully. They will be reviewed by your landlord.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to upload documents. Please try again.');
        }
    }

    /**
     * Verify documents (landlord only)
     */
    public function verifyDocuments(Request $request, $assignmentId)
    {
        $request->validate([
            'verification_notes' => 'nullable|string|max:1000',
        ]);

        $assignment = TenantAssignment::where('landlord_id', Auth::id())
            ->findOrFail($assignmentId);

        $this->assignmentService->verifyDocuments(
            $assignmentId,
            Auth::id(),
            $request->verification_notes
        );

        return back()->with('success', 'Documents verified successfully.');
    }

    /**
     * Verify individual document (landlord only)
     */
    public function verifyIndividualDocument(Request $request, $documentId)
    {
        $request->validate([
            'verification_notes' => 'nullable|string|max:1000',
        ]);

        $document = TenantDocument::with('tenantAssignment')->findOrFail($documentId);
        
        // Check if landlord has access to this document
        if ($document->tenantAssignment->landlord_id !== Auth::id()) {
            abort(403, 'Unauthorized access to document.');
        }

        // Update the individual document
        $document->update([
            'verification_status' => 'verified',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'verification_notes' => $request->verification_notes,
        ]);

        // Check if all documents for this assignment are verified
        $assignment = $document->tenantAssignment;
        $pendingDocuments = $assignment->documents()->where('verification_status', 'pending')->count();
        
        if ($pendingDocuments === 0) {
            // All documents verified, update assignment status
            $assignment->update([
                'documents_verified' => true,
                'verification_notes' => 'All documents verified',
            ]);

            // Update assignment status to active if it was pending
            if ($assignment->status === 'pending') {
                $assignment->update(['status' => 'active']);
            }
        }

        return back()->with('success', 'Document verified successfully.');
    }

    /**
     * Download document
     */
    public function downloadDocument($documentId)
    {
        $document = TenantDocument::with('tenantAssignment')->findOrFail($documentId);
        
        // Check if user has access to this document
        $user = Auth::user();
        if ($user->isLandlord()) {
            if ($document->tenantAssignment->landlord_id !== $user->id) {
                abort(403);
            }
        } elseif ($user->isTenant()) {
            if ($document->tenantAssignment->tenant_id !== $user->id) {
                abort(403);
            }
        } else {
            abort(403);
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->response($document->file_path, $document->file_name);
    }

    /**
     * Delete document (tenant only)
     */
    public function deleteDocument($documentId)
    {
        $document = TenantDocument::with('tenantAssignment')->findOrFail($documentId);
        
        // Check if user is the tenant who uploaded this document
        if ($document->tenantAssignment->tenant_id !== Auth::id()) {
            abort(403, 'Unauthorized access to document.');
        }

        // Allow deletion of any document (tenant's own documents)
        // No restriction on verification status

        try {
            // Delete the file from storage
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Delete the document record
            $document->delete();

            // Update assignment status based on remaining documents
            $assignment = $document->tenantAssignment;
            $remainingDocuments = $assignment->documents()->count();
            
            if ($remainingDocuments === 0) {
                // No documents left, mark as not uploaded
                $assignment->update([
                    'documents_uploaded' => false,
                    'documents_verified' => false,
                ]);
            } else {
                // Check if all remaining documents are verified
                $pendingDocuments = $assignment->documents()->where('verification_status', 'pending')->count();
                $allVerified = $pendingDocuments === 0;
                
                $assignment->update([
                    'documents_uploaded' => true,
                    'documents_verified' => $allVerified,
                ]);
            }

            return back()->with('success', 'Document deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete document. Please try again.');
        }
    }

    /**
     * Get tenant credentials
     */
    public function getCredentials($id)
    {
        $assignment = TenantAssignment::where('landlord_id', Auth::id())
            ->with('tenant')
            ->findOrFail($id);

        return response()->json([
            'email' => $assignment->tenant->email,
            'password' => $assignment->generated_password ?? 'Password not available'
        ]);
    }

    /**
     * Get available units for assignment
     */
    public function getAvailableUnits()
    {
        $units = Unit::whereHas('apartment', function($query) {
            $query->where('landlord_id', Auth::id());
        })->where('status', 'available')
        ->with('apartment')
        ->get();

        return response()->json($units);
    }

    /**
     * Delete tenant assignment (landlord only)
     */
    public function destroy($id)
    {
        try {
            $assignment = TenantAssignment::where('landlord_id', Auth::id())
                ->with(['tenant', 'unit', 'documents'])
                ->findOrFail($id);

            // Delete all associated documents first
            foreach ($assignment->documents as $document) {
                // Delete the file from storage
                if (Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                }
                // Delete the document record
                $document->delete();
            }

            // Update the unit status back to available
            $assignment->unit->update(['status' => 'available']);

            // Delete the tenant user account (optional - you may want to keep it)
            // Uncomment the line below if you want to delete the tenant user account
            // $assignment->tenant->delete();

            // Delete the assignment
            $assignment->delete();

            return redirect()->route('landlord.tenant-assignments')
                ->with('success', 'Tenant assignment deleted successfully. Unit is now available for new assignments.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete tenant assignment. Please try again.');
        }
    }
} 
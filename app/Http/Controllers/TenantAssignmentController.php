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
            'lease_start_date' => 'required|date|after:today',
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

            // Mark documents as uploaded
            $this->assignmentService->markDocumentsUploaded($assignment->id, $tenant->id);

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

        return Storage::disk('public')->download($document->file_path, $document->file_name);
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
} 
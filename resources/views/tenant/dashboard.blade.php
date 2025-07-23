@extends('layouts.app')

@section('title', 'Tenant Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
                <h4 class="page-title">Welcome, {{ optional(auth()->user())->name ?? 'Guest' }}!</h4>
            </div>
        </div>
    </div>

    <!-- Assignment Status -->
    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Assignment Information -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Your Assignment Details</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Unit Number:</strong></td>
                                    <td>{{ $assignment->unit->unit_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Apartment:</strong></td>
                                    <td>{{ $assignment->unit->apartment->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Unit Type:</strong></td>
                                    <td>{{ $assignment->unit->unit_type }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Bedrooms:</strong></td>
                                    <td>{{ $assignment->unit->bedrooms }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Bathrooms:</strong></td>
                                    <td>{{ $assignment->unit->bathrooms }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Lease Start:</strong></td>
                                    <td>{{ $assignment->lease_start_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Lease End:</strong></td>
                                    <td>{{ $assignment->lease_end_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Monthly Rent:</strong></td>
                                    <td>₱{{ number_format($assignment->rent_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Security Deposit:</strong></td>
                                    <td>₱{{ number_format($assignment->security_deposit, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Assignment Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $assignment->status_badge_class }}">
                                            {{ ucfirst($assignment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($assignment->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Additional Notes:</h6>
                            <p class="text-muted">{{ $assignment->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <!-- Document Status -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Document Status</h5>
                    
                    <div class="text-center mb-3">
                        <span class="badge bg-{{ $assignment->documents_status_badge_class }} fs-6">
                            {{ ucfirst($assignment->documents_status) }}
                        </span>
                    </div>

                    @if(!$assignment->documents_uploaded)
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">Documents Required</h6>
                            <p class="mb-2">Please upload the required documents to complete your assignment.</p>
                            <a href="{{ route('tenant.upload-documents') }}" class="btn btn-warning btn-sm">
                                Upload Documents
                            </a>
                        </div>
                    @elseif(!$assignment->documents_verified)
                        <div class="alert alert-info">
                            <h6 class="alert-heading">Documents Under Review</h6>
                            <p class="mb-0">Your documents have been uploaded and are being reviewed by your landlord.</p>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <h6 class="alert-heading">Documents Verified</h6>
                            <p class="mb-0">All your documents have been verified and approved.</p>
                        </div>
                    @endif

                    @if($assignment->verification_notes)
                        <div class="mt-3">
                            <h6>Verification Notes:</h6>
                            <p class="text-muted small">{{ $assignment->verification_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    
                    <div class="d-grid gap-2">
                        @if(!$assignment->documents_uploaded)
                            <a href="{{ route('tenant.upload-documents') }}" class="btn btn-primary">
                                <i class="mdi mdi-upload me-1"></i> Upload Documents
                            </a>
                        @endif
                        
                        <a href="#" class="btn btn-outline-secondary">
                            <i class="mdi mdi-file-document me-1"></i> View Documents
                        </a>
                        
                        <a href="#" class="btn btn-outline-info">
                            <i class="mdi mdi-message me-1"></i> Contact Landlord
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents List -->
    @if($assignment->documents->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Uploaded Documents</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-centered">
                            <thead>
                                <tr>
                                    <th>Document Type</th>
                                    <th>File Name</th>
                                    <th>Uploaded</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignment->documents as $document)
                                <tr>
                                    <td>{{ $document->document_type_label }}</td>
                                    <td>{{ $document->file_name }}</td>
                                    <td>{{ $document->uploaded_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $document->verification_status_badge_class }}">
                                            {{ ucfirst($document->verification_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('tenant.download-document', $document->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="mdi mdi-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Required Documents Info -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Required Documents</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Essential Documents:</h6>
                            <ul>
                                <li><strong>Government ID:</strong> Passport, Driver's License, or any valid government-issued ID</li>
                                <li><strong>Proof of Income:</strong> Recent payslips, employment contract, or business registration</li>
                                <li><strong>Bank Statement:</strong> Last 3 months of bank statements</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Additional Documents:</h6>
                            <ul>
                                <li><strong>Character Reference:</strong> Letter from employer, colleague, or community leader</li>
                                <li><strong>Rental History:</strong> Previous rental agreements or landlord references (if applicable)</li>
                                <li><strong>Other:</strong> Any additional documents that may be required</li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <h6 class="alert-heading">Document Guidelines:</h6>
                        <ul class="mb-0">
                            <li>All documents should be clear and legible</li>
                            <li>Accepted formats: PDF, JPG, JPEG, PNG</li>
                            <li>Maximum file size: 5MB per document</li>
                            <li>Documents will be reviewed within 2-3 business days</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if(auth()->user()->must_change_password)
<!-- Change Password Modal -->
<div id="changePasswordModal" class="modal" tabindex="-1" style="display:block; background:rgba(30,41,59,0.5); position:fixed; top:0; left:0; width:100vw; height:100vh; z-index:2000;">
    <div class="modal-dialog" style="max-width:400px; margin:10vh auto; background:white; border-radius:1rem; box-shadow:0 8px 32px rgba(0,0,0,0.2); padding:2rem; position:relative;">
        <h4 style="font-weight:700; color:#1e293b; margin-bottom:1rem;">Change Your Password</h4>
        <p style="color:#64748b;">For your security, please change your password before using the portal.</p>
        <a href="{{ route('tenant.change-password') }}" class="btn btn-primary w-100 mt-3">Change Password Now</a>
    </div>
</div>
<style>
body { overflow: hidden !important; }
</style>
@endif
@endsection 
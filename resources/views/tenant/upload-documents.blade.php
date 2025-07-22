@extends('layouts.app')

@section('title', 'Upload Documents')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Upload Documents</li>
                    </ol>
                </div>
                <h4 class="page-title">Upload Required Documents</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Document Upload Form</h5>
                    
                    <form method="POST" action="{{ route('tenant.store-documents') }}" enctype="multipart/form-data" id="documentForm">
                        @csrf
                        
                        <div id="documentFields">
                            <!-- Document fields will be added here dynamically -->
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-primary" onclick="addDocumentField()">
                                    <i class="mdi mdi-plus me-1"></i> Add Another Document
                                </button>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('tenant.dashboard') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-upload me-1"></i> Upload Documents
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <!-- Document Guidelines -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Document Guidelines</h5>
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Accepted Formats:</h6>
                        <ul class="mb-0">
                            <li>PDF files</li>
                            <li>JPG/JPEG images</li>
                            <li>PNG images</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h6 class="alert-heading">File Size Limit:</h6>
                        <p class="mb-0">Maximum 5MB per document</p>
                    </div>

                    <h6>Required Documents:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Government ID</strong><br>
                            <small class="text-muted">Passport, Driver's License, or any valid government-issued ID</small>
                        </li>
                        <li class="mb-2">
                            <strong>Proof of Income</strong><br>
                            <small class="text-muted">Recent payslips, employment contract, or business registration</small>
                        </li>
                        <li class="mb-2">
                            <strong>Bank Statement</strong><br>
                            <small class="text-muted">Last 3 months of bank statements</small>
                        </li>
                        <li class="mb-2">
                            <strong>Character Reference</strong><br>
                            <small class="text-muted">Letter from employer, colleague, or community leader</small>
                        </li>
                        <li class="mb-2">
                            <strong>Rental History</strong><br>
                            <small class="text-muted">Previous rental agreements or landlord references (if applicable)</small>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Assignment Info -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Your Assignment</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Unit:</strong></td>
                                <td>{{ $assignment->unit->unit_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Apartment:</strong></td>
                                <td>{{ $assignment->unit->apartment->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Monthly Rent:</strong></td>
                                <td>₱{{ number_format($assignment->rent_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Lease Period:</strong></td>
                                <td>{{ $assignment->lease_start_date->format('M d, Y') }} - {{ $assignment->lease_end_date->format('M d, Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Document Type Options -->
<template id="documentTypeOptions">
    <option value="">Select Document Type</option>
    <option value="government_id">Government ID</option>
    <option value="proof_of_income">Proof of Income</option>
    <option value="employment_contract">Employment Contract</option>
    <option value="bank_statement">Bank Statement</option>
    <option value="character_reference">Character Reference</option>
    <option value="rental_history">Rental History</option>
    <option value="other">Other Document</option>
</template>

@endsection

@push('scripts')
<script>
let documentFieldCount = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Add initial document field
    addDocumentField();
});

function addDocumentField() {
    documentFieldCount++;
    
    const container = document.getElementById('documentFields');
    const template = document.getElementById('documentTypeOptions');
    
    const fieldDiv = document.createElement('div');
    fieldDiv.className = 'row mb-3 document-field';
    fieldDiv.innerHTML = `
        <div class="col-md-4">
            <label class="form-label">Document Type <span class="text-danger">*</span></label>
            <select name="document_types[]" class="form-select" required>
                ${template.innerHTML}
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">File <span class="text-danger">*</span></label>
            <input type="file" name="documents[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
            <small class="text-muted">Max size: 5MB</small>
        </div>
        <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <div>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeDocumentField(this)">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(fieldDiv);
}

function removeDocumentField(button) {
    const fieldDiv = button.closest('.document-field');
    fieldDiv.remove();
}

// Form validation
document.getElementById('documentForm').addEventListener('submit', function(e) {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    const maxSize = 5 * 1024 * 1024; // 5MB in bytes
    
    for (let input of fileInputs) {
        if (input.files.length > 0) {
            const file = input.files[0];
            
            if (file.size > maxSize) {
                e.preventDefault();
                alert(`File "${file.name}" is too large. Maximum size is 5MB.`);
                return false;
            }
            
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                e.preventDefault();
                alert(`File "${file.name}" is not an accepted format. Please use PDF, JPG, JPEG, or PNG.`);
                return false;
            }
        }
    }
});
</script>
@endpush 
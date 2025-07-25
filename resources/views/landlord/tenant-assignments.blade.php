@extends('layouts.landlord-app')

@section('title', 'Tenant Assignments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('landlord.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tenant Assignments</li>
                    </ol>
                </div>
                <h4 class="page-title">Tenant Assignments</h4>
            </div>
        </div>
    </div>

    <!-- Credentials Alert -->
    @if(session('credentials'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center mb-3">
                <i class="mdi mdi-check-circle me-2" style="font-size: 1.5rem; color: #28a745;"></i>
                <h5 class="alert-heading mb-0">✅ Tenant Assigned Successfully!</h5>
            </div>
            
            <p class="mb-3">A new tenant account has been created. Please share these credentials with the tenant:</p>
            
            <!-- Credentials Display -->
            <div class="credentials-box p-3 mb-3" style="background: #f8f9fa; border: 2px solid #28a745; border-radius: 8px;">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label fw-bold text-primary">📧 Email Address:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="assignedTenantEmail" value="{{ session('credentials')['email'] }}" readonly style="background: white; font-weight: bold; font-size: 1.1rem;">
                            <button class="btn btn-outline-primary" type="button" onclick="copyText('assignedTenantEmail')" title="Copy email">
                                <i class="mdi mdi-content-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label fw-bold text-primary">🔑 Password:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="assignedTenantPassword" value="{{ session('credentials')['password'] }}" readonly style="background: white; font-weight: bold; font-size: 1.1rem; color: #dc3545;">
                            <button class="btn btn-outline-primary" type="button" onclick="copyText('assignedTenantPassword')" title="Copy password">
                                <i class="mdi mdi-content-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="d-flex gap-2 mb-3">
                <button type="button" class="btn btn-primary" onclick="copyAllCredentials()" title="Copy both email and password">
                    <i class="mdi mdi-content-copy me-1"></i> Copy All Credentials
                </button>
                <button type="button" class="btn btn-outline-success" onclick="printCredentials()" title="Print credentials">
                    <i class="mdi mdi-printer me-1"></i> Print
                </button>
            </div>
            
            <!-- Important Notice -->
            <div class="alert alert-warning mb-0" style="border-left: 4px solid #ffc107;">
                <h6 class="alert-heading"><i class="mdi mdi-alert-circle me-1"></i> Important:</h6>
                <ul class="mb-0">
                    <li><strong>Save these credentials securely</strong> - They won't be shown again</li>
                    <li>The tenant must use these credentials to log in and upload required documents</li>
                    <li>Assignment status will remain "Pending" until documents are verified</li>
                </ul>
            </div>
            
            <button type="button" class="btn-close" data-bs-dismiss="alert" style="position: absolute; top: 10px; right: 10px;"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0" title="Total Assignments">Total Assignments</h5>
                            <h3 class="mt-3 mb-3">{{ $stats['total_assignments'] }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-primary rounded">
                                <i class="mdi mdi-account-multiple font-20 text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0" title="Active Assignments">Active Assignments</h5>
                            <h3 class="mt-3 mb-3">{{ $stats['active_assignments'] }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-success rounded">
                                <i class="mdi mdi-check-circle font-20 text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0" title="Pending Documents">Pending Documents</h5>
                            <h3 class="mt-3 mb-3">{{ $stats['pending_documents'] }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-warning rounded">
                                <i class="mdi mdi-file-document font-20 text-warning"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0" title="Total Revenue">Total Revenue</h5>
                            <h3 class="mt-3 mb-3">₱{{ number_format($stats['total_revenue'], 2) }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-info rounded">
                                <i class="mdi mdi-currency-php font-20 text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('landlord.tenant-assignments') }}" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="documents_uploaded" class="form-label">Documents Uploaded</label>
                            <select name="documents_uploaded" id="documents_uploaded" class="form-select">
                                <option value="">All</option>
                                <option value="1" {{ request('documents_uploaded') == '1' ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ request('documents_uploaded') == '0' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="documents_verified" class="form-label">Documents Verified</label>
                            <select name="documents_verified" id="documents_verified" class="form-select">
                                <option value="">All</option>
                                <option value="1" {{ request('documents_verified') == '1' ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ request('documents_verified') == '0' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2 align-items-end">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('landlord.tenant-assignments') }}" class="btn btn-secondary">Clear</a>
                            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#assignTenantModal">
                                <i class="mdi mdi-account-plus me-1"></i> Assign Tenant
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Tenant Modal -->
    <div class="modal fade" id="assignTenantModal" tabindex="-1" aria-labelledby="assignTenantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignTenantModalLabel">Assign Tenant to Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="#" id="assignTenantForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="unit_id" class="form-label">Select Unit <span class="text-danger">*</span></label>
                                <select class="form-select" id="unit_id" name="unit_id" required>
                                    <option value="">-- Select an Available Unit --</option>
                                    @foreach(\App\Models\Unit::available()->whereHas('apartment', function($q){ $q->where('landlord_id', auth()->id()); })->get() as $unit)
                                        <option value="{{ $unit->id }}">
                                            {{ $unit->unit_number }} ({{ $unit->apartment->name ?? 'N/A' }}) - ₱{{ number_format($unit->rent_amount, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Tenant Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="col-md-6">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="lease_start_date" class="form-label">Lease Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="lease_start_date" name="lease_start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="lease_end_date" class="form-label">Lease End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="lease_end_date" name="lease_end_date" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="rent_amount" class="form-label">Monthly Rent <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="rent_amount" name="rent_amount" required>
                            </div>
                            <div class="col-md-6">
                                <label for="security_deposit" class="form-label">Security Deposit</label>
                                <input type="number" step="0.01" class="form-control" id="security_deposit" name="security_deposit">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>
                        <div class="alert alert-info mt-3">
                            <h6 class="alert-heading">Workflow</h6>
                            <ul class="mb-0">
                                <li>A new tenant account will be automatically created</li>
                                <li>Login credentials will be generated and shown after assignment</li>
                                <li>The tenant will receive access to their dashboard</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="assignTenantSubmit" disabled>
                            <i class="mdi mdi-account-plus me-1"></i> Assign Tenant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-striped dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Tenant</th>
                                    <th>Unit</th>
                                    <th>Apartment</th>
                                    <th>Lease Period</th>
                                    <th>Rent</th>
                                    <th>Status</th>
                                    <th>Documents</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignments as $assignment)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <span class="avatar-title bg-soft-primary rounded-circle">
                                                    {{ substr($assignment->tenant->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="font-14 mb-0">{{ $assignment->tenant->name }}</h5>
                                                <small class="text-muted">{{ $assignment->tenant->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $assignment->unit->unit_number }}</span>
                                    </td>
                                    <td>{{ $assignment->unit->apartment->name }}</td>
                                    <td>
                                        <div>
                                            <small class="text-muted">Start: {{ $assignment->lease_start_date->format('M d, Y') }}</small><br>
                                            <small class="text-muted">End: {{ $assignment->lease_end_date->format('M d, Y') }}</small>
                                        </div>
                                    </td>
                                    <td>₱{{ number_format($assignment->rent_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $assignment->status_badge_class }}">
                                            {{ ucfirst($assignment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $assignment->documents_status_badge_class }}">
                                            {{ ucfirst($assignment->documents_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" title="View Details">
                                                <i class="mdi mdi-eye"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('landlord.assignment-details', $assignment->id) }}">
                                                    <i class="mdi mdi-eye me-1"></i> View Details
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="viewCredentials({{ $assignment->id }}, '{{ $assignment->tenant->email }}')" title="View Login Credentials">
                                                    <i class="mdi mdi-key me-1"></i> View Credentials
                                                </a></li>
                                                @if($assignment->status === 'pending')
                                                <li><a class="dropdown-item" href="#" onclick="updateStatus({{ $assignment->id }}, 'active')" title="Activate Assignment">
                                                    <i class="mdi mdi-check me-1"></i> Activate
                                                </a></li>
                                                @endif
                                                @if($assignment->status === 'active')
                                                <li><a class="dropdown-item" href="#" onclick="updateStatus({{ $assignment->id }}, 'terminated')" title="Terminate Assignment">
                                                    <i class="mdi mdi-close me-1"></i> Terminate
                                                </a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No tenant assignments found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $assignments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Form -->
<form id="statusForm" method="POST" style="display: none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="status" id="statusInput">
</form>

<!-- Credentials Modal -->
<div class="modal fade" id="credentialsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tenant Login Credentials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading">Login Information</h6>
                    <p class="mb-2">Share these credentials with the tenant:</p>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Email:</strong><br>
                            <code id="tenantEmail"></code>
                        </div>
                        <div class="col-md-6">
                            <strong>Password:</strong><br>
                            <code id="tenantPassword"></code>
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <h6 class="alert-heading">Important Notes:</h6>
                    <ul class="mb-0">
                        <li>The tenant should change their password after first login</li>
                        <li>These credentials are for initial access only</li>
                        <li>Keep these credentials secure and private</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="copyCredentials()">
                    <i class="mdi mdi-content-copy me-1"></i> Copy Credentials
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function updateStatus(assignmentId, status) {
    if (confirm('Are you sure you want to update this assignment status?')) {
        const form = document.getElementById('statusForm');
        const statusInput = document.getElementById('statusInput');
        
        form.action = `/landlord/tenant-assignments/${assignmentId}/status`;
        statusInput.value = status;
        form.submit();
    }
}

function viewCredentials(assignmentId, email) {
    // Fetch credentials from the server
    fetch(`/landlord/tenant-assignments/${assignmentId}/credentials`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('tenantEmail').textContent = data.email;
            document.getElementById('tenantPassword').textContent = data.password;
            
            const modal = new bootstrap.Modal(document.getElementById('credentialsModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error fetching credentials:', error);
            alert('Error fetching credentials. Please try again.');
        });
}

function copyCredentials() {
    const email = document.getElementById('tenantEmail').textContent;
    const password = document.getElementById('tenantPassword').textContent;
    const credentials = `Email: ${email}\nPassword: ${password}`;
    
    navigator.clipboard.writeText(credentials).then(function() {
        alert('Credentials copied to clipboard!');
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Could not copy to clipboard. Please copy manually.');
    });
}

function copyAssignedCredentials() {
    const email = document.getElementById('assignedTenantEmail').value;
    const password = document.getElementById('assignedTenantPassword').value;
    const credentials = `Email: ${email}\nPassword: ${password}`;
    navigator.clipboard.writeText(credentials).then(function() {
        alert('Credentials copied to clipboard!');
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Could not copy to clipboard. Please copy manually.');
    });
}

function copyText(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    element.setSelectionRange(0, 99999); // For mobile devices
    
    navigator.clipboard.writeText(element.value).then(function() {
        // Show success feedback
        const btn = element.nextElementSibling;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="mdi mdi-check"></i>';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-primary');
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        // Fallback for older browsers
        document.execCommand('copy');
        alert('Copied to clipboard!');
    });
}

function copyAllCredentials() {
    const email = document.getElementById('assignedTenantEmail').value;
    const password = document.getElementById('assignedTenantPassword').value;
    const credentials = `Tenant Login Credentials:
Email: ${email}
Password: ${password}

Please use these credentials to log in and upload your required documents.`;
    
    navigator.clipboard.writeText(credentials).then(function() {
        // Show success message
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="mdi mdi-check me-1"></i> Copied!';
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
        }, 3000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Could not copy to clipboard. Please copy manually.');
    });
}

function printCredentials() {
    const email = document.getElementById('assignedTenantEmail').value;
    const password = document.getElementById('assignedTenantPassword').value;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Tenant Login Credentials</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .header { text-align: center; color: #28a745; margin-bottom: 30px; }
                .credentials { background: #f8f9fa; padding: 20px; border: 2px solid #28a745; border-radius: 8px; margin: 20px 0; }
                .credential-item { margin: 15px 0; }
                .label { font-weight: bold; color: #333; }
                .value { font-size: 1.2rem; color: #dc3545; font-weight: bold; margin-left: 10px; }
                .footer { margin-top: 30px; color: #666; font-size: 0.9rem; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>🏠 HouseSync - Tenant Login Credentials</h2>
                <p>Generated on: ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}</p>
            </div>
            
            <div class="credentials">
                <div class="credential-item">
                    <span class="label">📧 Email Address:</span>
                    <span class="value">${email}</span>
                </div>
                <div class="credential-item">
                    <span class="label">🔑 Password:</span>
                    <span class="value">${password}</span>
                </div>
            </div>
            
            <div class="footer">
                <h4>Important Instructions:</h4>
                <ul>
                    <li>Use these credentials to log in to your tenant dashboard</li>
                    <li>Upload all required documents after logging in</li>
                    <li>Your assignment will be activated once documents are verified</li>
                    <li>Keep these credentials secure and private</li>
                </ul>
                <p><strong>Login URL:</strong> ${window.location.origin}/login</p>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Set up modal form to update action URL with selected unit
const assignTenantForm = document.getElementById('assignTenantForm');
const unitSelect = document.getElementById('unit_id');
const assignTenantSubmit = document.getElementById('assignTenantSubmit');
if (assignTenantForm && unitSelect && assignTenantSubmit) {
    unitSelect.addEventListener('change', function() {
        if (this.value) {
            const baseAction = "/landlord/units/";
            assignTenantForm.action = baseAction + this.value + "/assign-tenant";
            assignTenantSubmit.disabled = false;
        } else {
            assignTenantForm.action = '#';
            assignTenantSubmit.disabled = true;
        }
    });
    // Prevent form submission if no unit is selected
    assignTenantForm.addEventListener('submit', function(e) {
        if (!unitSelect.value) {
            e.preventDefault();
            alert('Please select a unit to assign.');
        }
    });
}
// Set minimum date for lease start date to today
const today = new Date().toISOString().split('T')[0];
document.getElementById('lease_start_date').min = today;
document.getElementById('lease_start_date').addEventListener('change', function() {
    const startDate = this.value;
    const endDateInput = document.getElementById('lease_end_date');
    endDateInput.min = startDate;
    if (endDateInput.value && endDateInput.value < startDate) {
        endDateInput.value = '';
    }
});
</script>
@endpush 
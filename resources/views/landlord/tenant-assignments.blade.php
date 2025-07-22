@extends('layouts.app')

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
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading">✅ Tenant Assigned Successfully!</h6>
                    <p class="mb-2">A new tenant account has been created. Please share these credentials with the tenant:</p>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Email:</strong> {{ session('credentials')['email'] }}
                        </div>
                        <div class="col-md-6">
                            <strong>Password:</strong> {{ session('credentials')['password'] }}
                        </div>
                    </div>
                    <hr>
                    <p class="mb-0"><strong>Important:</strong> Save these credentials securely. The tenant will need them to log in and upload required documents.</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
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
                    <form method="GET" action="{{ route('landlord.tenant-assignments') }}" class="row g-3">
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
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('landlord.tenant-assignments') }}" class="btn btn-secondary">Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
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
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('landlord.assignment-details', $assignment->id) }}">
                                                    <i class="mdi mdi-eye me-1"></i> View Details
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="viewCredentials({{ $assignment->id }}, '{{ $assignment->tenant->email }}')">
                                                    <i class="mdi mdi-key me-1"></i> View Credentials
                                                </a></li>
                                                @if($assignment->status === 'pending')
                                                <li><a class="dropdown-item" href="#" onclick="updateStatus({{ $assignment->id }}, 'active')">
                                                    <i class="mdi mdi-check me-1"></i> Activate
                                                </a></li>
                                                @endif
                                                @if($assignment->status === 'active')
                                                <li><a class="dropdown-item" href="#" onclick="updateStatus({{ $assignment->id }}, 'terminated')">
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
</script>
@endpush 
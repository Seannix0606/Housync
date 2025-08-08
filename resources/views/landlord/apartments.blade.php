@extends('layouts.landlord-app')

@section('title', 'My Properties')

@section('content')
<div class="container-fluid">
    <div class="content-header">
        <div>
            <h1>My Properties</h1>
            <p style="color: #64748b; margin-top: 0.5rem;">Manage your property portfolio</p>
        </div>
        <div class="user-profile">
            <div class="user-avatar">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="user-info">
                <h3>{{ auth()->user()->name }}</h3>
                <p>Property Manager</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $apartments->count() }}</div>
            <div class="stat-label">Total Properties</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $totalUnits ?? 0 }}</div>
            <div class="stat-label">Total Units</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $occupiedUnits ?? 0 }}</div>
            <div class="stat-label">Occupied Units</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">₱{{ number_format($monthlyRevenue ?? 0, 0) }}</div>
            <div class="stat-label">Monthly Revenue</div>
        </div>
    </div>

    <!-- Properties Section -->
    <div class="page-section">
        <div class="section-header">
            <div>
                <h2 class="section-title">Property Portfolio</h2>
                <p class="section-subtitle">View and manage all your properties</p>
            </div>
            <a href="{{ route('landlord.create-apartment') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Property
            </a>
        </div>

        @if($apartments->count() > 0)
            <div class="properties-grid">
                @foreach($apartments as $apartment)
                    <div class="property-card">
                        <div class="property-header">
                            <div>
                                <h3 class="property-title">{{ $apartment->name }}</h3>
                                <p style="font-size: 0.875rem; color: #64748b;">ID: #{{ $apartment->id }}</p>
                            </div>
                            <span class="property-status status-active">Active</span>
                        </div>
                        <div class="property-info">
                            <div class="info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span title="{{ $apartment->address }}">{{ Str::limit($apartment->address ?? 'No address', 30) }}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-door-open"></i>
                                <span>{{ $apartment->units->count() }} Units</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ $apartment->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-users"></i>
                                <span>{{ $apartment->units->where('status', 'occupied')->count() }} Occupied</span>
                            </div>
                        </div>
                        <div class="property-stats">
                            <div class="stat-item">
                                <div class="stat-item-value">{{ $apartment->units->count() }}</div>
                                <div class="stat-item-label">Total Units</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-item-value">{{ $apartment->units->where('status', 'occupied')->count() }}</div>
                                <div class="stat-item-label">Occupied</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-item-value">
                                    @if($apartment->units->count() > 0)
                                        {{ round(($apartment->units->where('status', 'occupied')->count() / $apartment->units->count()) * 100) }}%
                                    @else
                                        0%
                                    @endif
                                </div>
                                <div class="stat-item-label">Occupancy</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-item-value">₱{{ number_format($apartment->units->where('status', 'occupied')->sum('rent_amount') ?? 0, 0) }}</div>
                                <div class="stat-item-label">Revenue</div>
                            </div>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('landlord.edit-apartment', $apartment->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="viewApartmentDetails({{ $apartment->id }})">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($apartments->hasPages())
                <div class="pagination">
                    {{ $apartments->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3 class="empty-title">No Properties Yet</h3>
                <p class="empty-text">Start building your property portfolio by adding your first property.</p>
                <a href="{{ route('landlord.create-apartment') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Your First Property
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Apartment Details Modal -->
<div class="modal fade" id="apartmentDetailsModal" tabindex="-1" aria-labelledby="apartmentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="apartmentDetailsModalLabel">Property Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="apartmentDetailsContent">
                <div class="text-center py-4">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading property details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editApartmentBtn" style="display: none;">
                    <i class="fas fa-edit"></i> Edit Property
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function viewApartmentDetails(apartmentId) {
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('apartmentDetailsModal'));
    const modalTitle = document.getElementById('apartmentDetailsModalLabel');
    const modalContent = document.getElementById('apartmentDetailsContent');
    const editBtn = document.getElementById('editApartmentBtn');
    
    // Reset modal content
    modalContent.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading property details...</p>
        </div>
    `;
    editBtn.style.display = 'none';
    
    modal.show();
    
    // Fetch apartment details
    fetch(`/landlord/apartments/${apartmentId}/details`)
        .then(response => response.json())
        .then(data => {
            modalTitle.textContent = `${data.name} - Details`;
            
            // Calculate additional stats
            const availableUnits = data.available_units || 0;
            const maintenanceUnits = data.maintenance_units || 0;
            const occupancyRate = data.occupancy_rate || 0;
            
            // Create the details HTML
            modalContent.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Property Information</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Property Name</label>
                            <p class="mb-1">${data.name}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Property ID</label>
                            <p class="mb-1">#${data.id}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Total Units</label>
                            <p class="mb-1">${data.total_units}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Monthly Revenue</label>
                            <p class="mb-1 text-success fw-bold">₱${Number(data.total_revenue).toLocaleString()}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Occupancy Statistics</h6>
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <h4 class="text-success mb-0">${data.occupied_units}</h4>
                                    <small class="text-muted">Occupied</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <h4 class="text-warning mb-0">${availableUnits}</h4>
                                    <small class="text-muted">Available</small>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <h4 class="text-danger mb-0">${maintenanceUnits}</h4>
                                    <small class="text-muted">Maintenance</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <h4 class="text-primary mb-0">${occupancyRate}%</h4>
                                    <small class="text-muted">Occupancy</small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Occupancy Rate</label>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar ${occupancyRate >= 80 ? 'bg-success' : occupancyRate >= 50 ? 'bg-warning' : 'bg-danger'}" 
                                     style="width: ${occupancyRate}%">${occupancyRate}%</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-12">
                        <h6 class="fw-bold mb-3">Quick Actions</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="/landlord/apartments/${data.id}/units/create" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add Unit
                            </a>
                            <a href="/landlord/units/${data.id}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-door-open"></i> View Units
                            </a>
                            <a href="/landlord/tenant-assignments?apartment_id=${data.id}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-users"></i> View Tenants
                            </a>
                        </div>
                    </div>
                </div>
            `;
            
            // Show edit button and set up click handler
            editBtn.style.display = 'inline-block';
            editBtn.onclick = function() {
                window.location.href = `/landlord/apartments/${data.id}/edit`;
            };
        })
        .catch(error => {
            console.error('Error fetching apartment details:', error);
            modalContent.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-danger">Error Loading Details</h5>
                    <p class="text-muted">Failed to load property details. Please try again.</p>
                    <button class="btn btn-primary" onclick="viewApartmentDetails(${apartmentId})">Retry</button>
                </div>
            `;
        });
}
</script>
@endpush 
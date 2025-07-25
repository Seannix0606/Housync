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
                            <a href="{{ route('landlord.apartment-details', $apartment->id) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <a href="{{ route('landlord.apartment-units', $apartment->id) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-door-open"></i> Manage Units
                            </a>
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
@endsection 
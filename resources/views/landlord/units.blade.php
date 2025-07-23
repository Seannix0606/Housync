<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Units - Housesync</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        body.modal-open {
            overflow: hidden;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles - Orange Theme */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #ea580c 0%, #dc2626 100%);
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 2rem 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .sidebar-header p {
            font-size: 0.875rem;
            opacity: 0.8;
        }

        .sidebar-nav {
            flex: 1;
            padding: 1.5rem 0;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            position: relative;
        }

        .nav-item:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #fb923c;
        }

        .nav-item.active {
            background-color: #f97316;
            color: white;
            border-left-color: #fb923c;
        }

        .nav-item i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        .badge-count {
            background-color: #ef4444;
            color: white;
            border-radius: 9999px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: auto;
        }

        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 0.875rem;
            background: rgba(255,255,255,0.1);
            border: none;
            border-radius: 0.5rem;
            color: white;
            text-decoration: none;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .logout-btn i {
            margin-right: 0.5rem;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .content-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
        }

        .user-profile {
            display: flex;
            align-items: center;
            background: white;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f97316, #ea580c);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 0.75rem;
        }

        .user-info h3 {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e293b;
        }

        .user-info p {
            font-size: 0.75rem;
            color: #64748b;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid #f97316;
            text-align: center;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Page Content */
        .page-section {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }

        .section-subtitle {
            color: #64748b;
            font-size: 1rem;
            margin-top: 0.25rem;
        }

        /* Search and Filters */
        .filters-section {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .form-label.required {
            color: #dc2626;
        }

        .form-control {
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        /* Units Grid */
        .units-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .unit-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .unit-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-color: #f97316;
        }

        .unit-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .unit-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .unit-property {
            font-size: 0.875rem;
            color: #64748b;
        }

        .unit-status {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-available {
            background: #d1fae5;
            color: #059669;
        }

        .status-occupied {
            background: #fef3c7;
            color: #d97706;
        }

        .status-maintenance {
            background: #fee2e2;
            color: #dc2626;
        }

        .unit-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #64748b;
        }

        .info-item i {
            width: 16px;
            text-align: center;
            color: #f97316;
        }

        .unit-details {
            background: #f8fafc;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            font-size: 0.875rem;
            color: #64748b;
        }

        .detail-value {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e293b;
        }

        .rent-amount {
            font-size: 1.25rem;
            font-weight: 700;
            color: #f97316;
            text-align: center;
            margin-bottom: 1rem;
        }

        /* Action Buttons */
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: #f97316;
            color: white;
        }

        .btn-primary:hover {
            background: #ea580c;
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }

        .btn-group {
            display: flex;
            gap: 0.5rem;
        }

        /* Alert Styles */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-success {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }

        .alert-error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-icon {
            font-size: 4rem;
            color: #94a3b8;
            margin-bottom: 1rem;
        }

        .empty-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .empty-text {
            color: #64748b;
            margin-bottom: 2rem;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination a,
        .pagination span {
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            text-decoration: none;
            color: #374151;
            font-size: 0.875rem;
        }

        .pagination a:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        .pagination .active span {
            background: #f97316;
            border-color: #f97316;
            color: white;
        }

        /* Modal Styles */
        #addUnitModal {
            display:none;
            position:fixed;
            top:0;
            left:0;
            width:100vw;
            height:100vh;
            background:rgba(30,41,59,0.4);
            z-index:2000;
            align-items:center;
            justify-content:center;
            overflow-y:auto; /* allow scrolling inside the overlay */
        }

        #addUnitModal .modal-content {
            background:white;
            border-radius:1rem;
            max-width:600px;
            width:95%;
            margin:2rem auto;
            padding:2rem;
            position:relative;
            box-shadow:0 8px 32px rgba(0,0,0,0.2);
            max-height:90vh; /* limit height */
            overflow-y:auto; /* scroll inside modal if needed */
        }

        #addUnitModal .modal-header {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:1rem;
            padding-bottom:0.5rem;
            border-bottom:1px solid #f1f5f9;
        }

        #addUnitModal .modal-header h2 {
            font-size:1.25rem;
            font-weight:700;
            color:#1e293b;
        }

        #addUnitModal .modal-header .close-button {
            background:none;
            border:none;
            font-size:1.5rem;
            color:#64748b;
            cursor:pointer;
        }

        #addUnitModal .form-group {
            margin-bottom:1rem;
        }

        #addUnitModal .form-label {
            font-size:0.875rem;
            font-weight:500;
            color:#1e293b;
            margin-bottom:0.5rem;
        }

        #addUnitModal .form-label.required {
            color:#dc2626;
        }

        #addUnitModal .form-control {
            padding:0.75rem;
            border:1px solid #d1d5db;
            border-radius:0.5rem;
            font-size:0.875rem;
        }

        #addUnitModal .form-control:focus {
            outline:none;
            border-color:#f97316;
            box-shadow:0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        #addUnitModal .form-actions {
            display:flex;
            justify-content:flex-end;
            gap:1rem;
            margin-top:1.5rem;
        }

        #addUnitModal .form-actions .btn {
            padding:0.5rem 1rem;
        }

        #addUnitModal .amenities-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }
        #addUnitModal .amenity-item {
            display: inline-flex;
            align-items: center;
            padding: 0.6rem 1.1rem;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 999px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.97rem;
            font-weight: 500;
            color: #1e293b;
            box-shadow: 0 1px 2px rgba(30,41,59,0.03);
            width: auto;
            position: relative;
            margin: 0;
        }
        #addUnitModal .amenity-item input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }
        #addUnitModal .amenity-item i {
            color: #f97316;
            width: 18px;
            font-size: 1.1rem;
            margin-right: 0.5rem;
        }
        #addUnitModal .amenity-item input[type="checkbox"]:checked + span {
            color: #ea580c;
        }
        #addUnitModal .amenity-item input[type="checkbox"]:checked + span, #addUnitModal .amenity-item:has(input[type="checkbox"]:checked) {
            border-color: #f97316;
            background: #fef7ed;
            color: #ea580c;
        }
        #addUnitModal .amenity-item span {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0;
        }
        #addUnitModal .furnished-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.2rem;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Landlord Portal</h2>
                <p>Property Manager</p>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('landlord.dashboard') }}" class="nav-item{{ request()->routeIs('landlord.dashboard') ? ' active' : '' }}">
                    <i class="fas fa-home"></i> My Dashboard
                </a>
                <a href="{{ route('landlord.apartments') }}" class="nav-item{{ request()->routeIs('landlord.apartments') ? ' active' : '' }}">
                    <i class="fas fa-building"></i> My Properties
                    @if(isset($sidebarCounts['total_apartments']))
                        <span class="badge-count">{{ $sidebarCounts['total_apartments'] }}</span>
                    @endif
                </a>
                <a href="{{ route('landlord.units') }}" class="nav-item{{ request()->routeIs('landlord.units') ? ' active' : '' }}">
                    <i class="fas fa-door-open"></i> My Units
                    @if(isset($sidebarCounts['total_units']))
                        <span class="badge-count">{{ $sidebarCounts['total_units'] }}</span>
                    @endif
                </a>
                <a href="{{ route('landlord.tenants') }}" class="nav-item{{ request()->routeIs('landlord.tenants') ? ' active' : '' }}">
                    <i class="fas fa-users"></i> Tenants
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-credit-card"></i> Payments
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-tools"></i> Maintenance
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="{{ route('logout') }}" class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="content-header">
                <div>
                    <h1>My Units</h1>
                    <p style="color: #64748b; margin-top: 0.5rem;">Manage all your rental units</p>
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
                    <div class="stat-value">{{ $units->count() }}</div>
                    <div class="stat-label">Total Units</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $units->where('status', 'available')->count() }}</div>
                    <div class="stat-label">Available Units</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $units->where('status', 'occupied')->count() }}</div>
                    <div class="stat-label">Occupied Units</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">₱{{ number_format($units->where('status', 'occupied')->sum('rent_amount') ?? 0, 0) }}</div>
                    <div class="stat-label">Monthly Revenue</div>
                </div>
            </div>

            <!-- Units Section -->
            <div class="page-section">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">All Units</h2>
                        <p class="section-subtitle">View and manage your rental units across all properties</p>
                    </div>
                    <button class="btn btn-primary" onclick="openAddUnitModal()">
                        <i class="fas fa-plus"></i> Add New Unit
                    </button>
                </div>

                <!-- Search and Filters -->
                <form method="GET" action="{{ route('landlord.units') }}">
                    <div class="filters-section">
                        <div class="form-group">
                            <label class="form-label">Search Units</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search by unit number, property..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Property</label>
                            <select name="apartment" class="form-control">
                                <option value="">All Properties</option>
                                @foreach($apartments ?? [] as $apartment)
                                    <option value="{{ $apartment->id }}" {{ request('apartment') == $apartment->id ? 'selected' : '' }}>
                                        {{ $apartment->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>

                @if($units->count() > 0)
                    <div class="units-grid">
                        @foreach($units as $unit)
                            <div class="unit-card">
                                <div class="unit-header">
                                    <div>
                                        <h3 class="unit-title">{{ $unit->unit_number }}</h3>
                                        <p class="unit-property">{{ $unit->apartment->name ?? 'Unknown Property' }}</p>
                                    </div>
                                    <span class="unit-status status-{{ $unit->status }}">
                                        {{ ucfirst($unit->status) }}
                                    </span>
                                </div>

                                <div class="unit-info">
                                    <div class="info-item">
                                        <i class="fas fa-bed"></i>
                                        <span>{{ $unit->bedrooms ?? 0 }} Bedrooms</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-bath"></i>
                                        <span>{{ $unit->bathrooms ?? 1 }} Bathrooms</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-layer-group"></i>
                                        <span>Floor {{ $unit->floor_number ?? 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-users"></i>
                                        <span>Max {{ $unit->max_occupants ?? 'N/A' }}</span>
                                    </div>
                                </div>

                                <div class="unit-details">
                                    <div class="detail-row">
                                        <span class="detail-label">Unit Type:</span>
                                        <span class="detail-value">{{ str_replace('_', ' ', ucfirst($unit->unit_type ?? 'N/A')) }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Floor Area:</span>
                                        <span class="detail-value">{{ $unit->floor_area ?? 'N/A' }} sq ft</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Created:</span>
                                        <span class="detail-value">{{ $unit->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>

                                <div class="rent-amount">
                                    ₱{{ number_format($unit->rent_amount ?? 0, 0) }}/month
                                </div>

                                <div class="btn-group">
                                    <a href="#" class="btn btn-primary btn-sm" onclick="editUnit({{ $unit->id }})">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="#" class="btn btn-secondary btn-sm" onclick="viewUnitDetails({{ $unit->id }})">
                                        <i class="fas fa-eye"></i> Details
                                    </a>
                                    @if($unit->status === 'occupied')
                                        <a href="#" class="btn btn-danger btn-sm" onclick="vacateUnit({{ $unit->id }})">
                                            <i class="fas fa-user-minus"></i> Vacate
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($units->hasPages())
                        <div class="pagination">
                            {{ $units->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <h3 class="empty-title">No Units Found</h3>
                        <p class="empty-text">
                            @if(request()->hasAny(['search', 'status', 'apartment']))
                                No units match your search criteria. Try adjusting your filters.
                            @else
                                You haven't added any units yet. Start by adding units to your properties.
                            @endif
                        </p>
                        @if(request()->hasAny(['search', 'status', 'apartment']))
                            <a href="{{ route('landlord.units') }}" class="btn btn-primary">
                                <i class="fas fa-refresh"></i> Clear Filters
                            </a>
                        @else
                            <a href="{{ route('landlord.apartments') }}" class="btn btn-primary">
                                <i class="fas fa-building"></i> Go to Properties
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Unit Modal -->
    <div id="addUnitModal">
        <div class="modal-content">
            <button onclick="closeAddUnitModal()" style="position:absolute; top:1rem; right:1rem; background:none; border:none; font-size:1.5rem; color:#64748b; cursor:pointer;">&times;</button>
            <h2 style="font-size:1.25rem; font-weight:700; margin-bottom:1rem; color:#1e293b;">Add New Unit</h2>
            <form method="POST" action="#" id="addUnitForm">
                @csrf
                <div class="form-group">
                    <label class="form-label required">Property</label>
                    <select name="apartmentId" id="apartmentIdSelect" class="form-control" required onchange="updateFormAction()">
                        <option value="">Select Property</option>
                        @foreach($apartments as $apartment)
                            <option value="{{ $apartment->id }}">{{ $apartment->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label required">Unit Number</label>
                    <input type="text" name="unit_number" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label required">Unit Type</label>
                    <input type="text" name="unit_type" class="form-control" required placeholder="e.g., Studio, 1BR, 2BR">
                </div>
                <div class="form-group">
                    <label class="form-label required">Rent Amount (₱)</label>
                    <input type="number" name="rent_amount" class="form-control" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label class="form-label required">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label required">Leasing Type</label>
                    <select name="leasing_type" class="form-control" required>
                        <option value="separate">Separate Bills</option>
                        <option value="inclusive">All Inclusive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" placeholder="Describe the unit..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Floor Area (sq ft)</label>
                    <input type="number" name="floor_area" class="form-control" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label class="form-label required">Bedrooms</label>
                    <input type="number" name="bedrooms" class="form-control" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label required">Bathrooms</label>
                    <input type="number" name="bathrooms" class="form-control" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Max Occupants</label>
                    <input type="number" name="max_occupants" class="form-control" min="1">
                </div>
                <div class="form-group">
                    <label class="form-label">Floor Number</label>
                    <input type="number" name="floor_number" class="form-control" min="1">
                </div>
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" placeholder="Additional notes..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Furnished</label>
                    <div class="furnished-row">
                        <input type="checkbox" name="is_furnished" value="1" id="is_furnished">
                        <label for="is_furnished" style="margin:0; cursor:pointer;">Yes</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Amenities</label>
                    <div class="amenities-grid">
                        <label class="amenity-item">
                            <input type="checkbox" id="ac" name="amenities[]" value="ac">
                            <span><i class="fas fa-wind"></i> Air Conditioning</span>
                        </label>
                        <label class="amenity-item">
                            <input type="checkbox" id="balcony" name="amenities[]" value="balcony">
                            <span><i class="fas fa-umbrella-beach"></i> Balcony</span>
                        </label>
                        <label class="amenity-item">
                            <input type="checkbox" id="laundry" name="amenities[]" value="laundry">
                            <span><i class="fas fa-tshirt"></i> Laundry</span>
                        </label>
                        <label class="amenity-item">
                            <input type="checkbox" id="wifi" name="amenities[]" value="wifi">
                            <span><i class="fas fa-wifi"></i> WiFi</span>
                        </label>
                        <label class="amenity-item">
                            <input type="checkbox" id="parking" name="amenities[]" value="parking">
                            <span><i class="fas fa-parking"></i> Parking</span>
                        </label>
                        <label class="amenity-item">
                            <input type="checkbox" id="pet" name="amenities[]" value="pet">
                            <span><i class="fas fa-dog"></i> Pet Friendly</span>
                        </label>
                        <label class="amenity-item">
                            <input type="checkbox" id="elevator" name="amenities[]" value="elevator">
                            <span><i class="fas fa-arrow-up"></i> Elevator</span>
                        </label>
                        <label class="amenity-item">
                            <input type="checkbox" id="security" name="amenities[]" value="security">
                            <span><i class="fas fa-shield-alt"></i> Security</span>
                        </label>
                    </div>
                </div>
                <div class="form-actions" style="display:flex; justify-content:flex-end; gap:1rem; margin-top:1.5rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeAddUnitModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Unit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editUnit(unitId) {
            // Implement edit unit functionality
            alert('Edit unit: ' + unitId + '\nThis would open the edit unit form.');
        }

        function viewUnitDetails(unitId) {
            // Implement view unit details functionality
            alert('View unit details: ' + unitId + '\nThis would show detailed unit information.');
        }

        function assignTenant(unitId) {
            // Redirect to tenant assignment form
            window.location.href = '/landlord/units/' + unitId + '/assign-tenant';
        }

        function vacateUnit(unitId) {
            if (confirm('Are you sure you want to vacate this unit? This will remove the current tenant.')) {
                // Implement vacate unit functionality
                alert('Vacate unit: ' + unitId + '\nThis would remove the current tenant.');
            }
        }

        function openAddUnitModal() {
            document.getElementById('addUnitModal').style.display = 'flex';
            document.body.classList.add('modal-open');
        }
        function closeAddUnitModal() {
            document.getElementById('addUnitModal').style.display = 'none';
            document.body.classList.remove('modal-open');
        }
        function updateFormAction() {
            var select = document.getElementById('apartmentIdSelect');
            var form = document.getElementById('addUnitForm');
            var apartmentId = select.value;
            if (apartmentId) {
                form.action = "{{ url('/landlord/apartments') }}/" + apartmentId + "/units";
            } else {
                form.action = "#";
            }
        }
    </script>
</body>
</html> 
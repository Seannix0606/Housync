<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landlord Dashboard - Housesync</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Landlord Panel</h2>
                <p>{{ auth()->user()->name }}</p>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('landlord.dashboard') }}" class="nav-item active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('landlord.apartments') }}" class="nav-item">
                    <i class="fas fa-building"></i> My Apartments
                    @if(isset($stats['total_apartments']))
                        <span class="badge-count">{{ $stats['total_apartments'] }}</span>
                    @endif
                </a>
                <a href="{{ route('landlord.units') }}" class="nav-item">
                    <i class="fas fa-door-open"></i> My Units
                    @if(isset($stats['total_units']))
                        <span class="badge-count">{{ $stats['total_units'] }}</span>
                    @endif
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-users"></i> Tenants
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-dollar-sign"></i> Payments
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-tools"></i> Maintenance
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

        <div class="main-content">
            <div class="content-header">
                <h1>Dashboard Overview</h1>
                <p>Welcome back, {{ auth()->user()->name }}!</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats['total_apartments'] ?? 0 }}</h3>
                        <p>Total Apartments</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon units">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats['total_units'] ?? 0 }}</h3>
                        <p>Total Units</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon occupied">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats['occupied_units'] ?? 0 }}</h3>
                        <p>Occupied Units</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon available">
                        <i class="fas fa-door-closed"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats['available_units'] ?? 0 }}</h3>
                        <p>Available Units</p>
                    </div>
                </div>
                
                <div class="stat-card revenue">
                    <div class="stat-icon revenue">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>₱{{ number_format($stats['total_revenue'] ?? 0, 2) }}</h3>
                        <p>Monthly Revenue</p>
                    </div>
                </div>
            </div>

            @if(isset($apartments) && $apartments->count() > 0)
                <div class="section">
                    <div class="section-header">
                        <h2>My Apartments</h2>
                        <a href="{{ route('landlord.apartments') }}" class="btn btn-primary">View All</a>
                    </div>
                    <div class="apartments-grid">
                        @foreach($apartments as $apartment)
                            <div class="apartment-card">
                                <div class="apartment-header">
                                    <h3>{{ $apartment->name }}</h3>
                                    <span class="status-badge status-{{ $apartment->status }}">
                                        {{ ucfirst($apartment->status) }}
                                    </span>
                                </div>
                                <div class="apartment-info">
                                    <p><i class="fas fa-map-marker-alt"></i> {{ Str::limit($apartment->address, 50) }}</p>
                                    <p><i class="fas fa-door-open"></i> {{ $apartment->units->count() }} units</p>
                                    <p><i class="fas fa-users"></i> {{ $apartment->getOccupiedUnitsCount() }} occupied</p>
                                </div>
                                <div class="apartment-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Occupancy</span>
                                        <span class="stat-value">{{ $apartment->getOccupancyRate() }}%</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Revenue</span>
                                        <span class="stat-value">₱{{ number_format($apartment->getTotalRevenue(), 2) }}</span>
                                    </div>
                                </div>
                                <div class="apartment-actions">
                                    <a href="{{ route('landlord.edit-apartment', $apartment->id) }}" class="btn btn-sm btn-outline">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="{{ route('landlord.units', $apartment->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-door-open"></i> View Units
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="section">
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h3>No Apartments Yet</h3>
                        <p>Start by adding your first apartment to manage your properties.</p>
                        <a href="{{ route('landlord.create-apartment') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add First Apartment
                        </a>
                    </div>
                </div>
            @endif

            @if(isset($recentUnits) && $recentUnits->count() > 0)
                <div class="section">
                    <div class="section-header">
                        <h2>Recent Units</h2>
                        <a href="{{ route('landlord.units') }}" class="btn btn-primary">View All</a>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Unit Number</th>
                                    <th>Apartment</th>
                                    <th>Type</th>
                                    <th>Rent</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentUnits as $unit)
                                    <tr>
                                        <td>{{ $unit->unit_number }}</td>
                                        <td>{{ $unit->apartment->name ?? 'N/A' }}</td>
                                        <td>{{ $unit->unit_type }}</td>
                                        <td>₱{{ number_format($unit->rent_amount, 2) }}</td>
                                        <td>
                                            <span class="status-badge status-{{ $unit->status }}">
                                                {{ ucfirst($unit->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }
        
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background-color: #1f2937;
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #374151;
        }
        
        .sidebar-header h2 {
            margin: 0;
            font-size: 18px;
        }
        
        .sidebar-header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #9ca3af;
        }
        
        .sidebar-nav {
            flex: 1;
            padding: 20px 0;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #d1d5db;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        
        .nav-item:hover, .nav-item.active {
            background-color: #374151;
            color: white;
        }
        
        .nav-item i {
            margin-right: 10px;
            width: 16px;
        }
        
        .badge-count {
            background-color: #3b82f6;
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 12px;
            margin-left: auto;
        }
        
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid #374151;
        }
        
        .logout-btn {
            display: flex;
            align-items: center;
            color: #d1d5db;
            text-decoration: none;
            padding: 10px;
            border-radius: 6px;
            transition: background-color 0.2s;
        }
        
        .logout-btn:hover {
            background-color: #374151;
        }
        
        .logout-btn i {
            margin-right: 8px;
        }
        
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            background-color: #f9fafb;
            min-height: 100vh;
        }
        
        .content-header {
            margin-bottom: 30px;
        }
        
        .content-header h1 {
            margin: 0;
            color: #1f2937;
        }
        
        .content-header p {
            margin: 5px 0 0 0;
            color: #6b7280;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }
        
        .stat-card.revenue {
            grid-column: span 2;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            background-color: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .stat-icon.units {
            background-color: #8b5cf6;
        }
        
        .stat-icon.occupied {
            background-color: #10b981;
        }
        
        .stat-icon.available {
            background-color: #f59e0b;
        }
        
        .stat-icon.revenue {
            background-color: #ef4444;
        }
        
        .stat-info h3 {
            margin: 0;
            font-size: 24px;
            color: #1f2937;
        }
        
        .stat-info p {
            margin: 5px 0 0 0;
            color: #6b7280;
        }
        
        .section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .section-header h2 {
            margin: 0;
            color: #1f2937;
        }
        
        .apartments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        
        .apartment-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            transition: box-shadow 0.2s;
        }
        
        .apartment-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .apartment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .apartment-header h3 {
            margin: 0;
            color: #1f2937;
        }
        
        .apartment-info {
            margin-bottom: 15px;
        }
        
        .apartment-info p {
            margin: 5px 0;
            color: #6b7280;
            display: flex;
            align-items: center;
        }
        
        .apartment-info i {
            margin-right: 8px;
            width: 16px;
            color: #9ca3af;
        }
        
        .apartment-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f9fafb;
            border-radius: 6px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-label {
            display: block;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 2px;
        }
        
        .stat-value {
            font-weight: 600;
            color: #1f2937;
        }
        
        .apartment-actions {
            display: flex;
            gap: 10px;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-inactive {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-occupied {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .status-available {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-maintenance {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th,
        .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .data-table th {
            background-color: #f9fafb;
            color: #374151;
            font-weight: 600;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2563eb;
        }
        
        .btn-outline {
            background-color: transparent;
            color: #6b7280;
            border: 1px solid #d1d5db;
        }
        
        .btn-outline:hover {
            background-color: #f9fafb;
            border-color: #9ca3af;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 14px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-icon {
            font-size: 48px;
            color: #9ca3af;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: #6b7280;
            margin-bottom: 20px;
        }
        
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
    
    @include('partials.firebase-scripts')
</body>
</html> 
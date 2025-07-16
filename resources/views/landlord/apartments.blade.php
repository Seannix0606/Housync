<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Apartments - Housesync</title>
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
                <a href="{{ route('landlord.dashboard') }}" class="nav-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('landlord.apartments') }}" class="nav-item active">
                    <i class="fas fa-building"></i> My Apartments
                </a>
                <a href="{{ route('landlord.units') }}" class="nav-item">
                    <i class="fas fa-door-open"></i> My Units
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
                <h1>My Apartments</h1>
                <p>Manage your properties</p>
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

            <div class="section">
                <div class="section-header">
                    <h2>Apartments ({{ $apartments->count() }})</h2>
                    <a href="{{ route('landlord.create-apartment') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Apartment
                    </a>
                </div>

                @if($apartments->count() > 0)
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
                                    <p><i class="fas fa-map-marker-alt"></i> {{ $apartment->address }}</p>
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
                                    <form method="POST" action="{{ route('landlord.delete-apartment', $apartment->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this apartment? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="pagination-container">
                        {{ $apartments->links() }}
                    </div>
                @else
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
                @endif
            </div>
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
        
        .btn-danger {
            background-color: #dc2626;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #b91c1c;
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
        
        .pagination-container {
            padding: 20px;
            display: flex;
            justify-content: center;
        }
    </style>
</body>
</html> 
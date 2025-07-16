<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Units - Housesync</title>
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
                <a href="{{ route('landlord.apartments') }}" class="nav-item">
                    <i class="fas fa-building"></i> My Apartments
                </a>
                <a href="{{ route('landlord.units') }}" class="nav-item active">
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
                <h1>My Units</h1>
                <p>Manage your rental units</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="section">
                <div class="section-header">
                    <h2>Units</h2>
                    <div class="header-actions">
                        @if(isset($apartments) && $apartments->count() > 0)
                            <select id="apartmentFilter" class="filter-select">
                                <option value="">All Apartments</option>
                                @foreach($apartments as $apartment)
                                    <option value="{{ $apartment->id }}" {{ $apartmentId == $apartment->id ? 'selected' : '' }}>
                                        {{ $apartment->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>

                @if($units->count() > 0)
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Unit Number</th>
                                    <th>Apartment</th>
                                    <th>Type</th>
                                    <th>Rent</th>
                                    <th>Status</th>
                                    <th>Tenants</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($units as $unit)
                                    <tr>
                                        <td>
                                            <strong>{{ $unit->unit_number }}</strong>
                                        </td>
                                        <td>{{ $unit->apartment->name ?? 'N/A' }}</td>
                                        <td>{{ $unit->unit_type }}</td>
                                        <td>₱{{ number_format($unit->rent_amount, 2) }}</td>
                                        <td>
                                            <span class="status-badge status-{{ $unit->status }}">
                                                {{ ucfirst($unit->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $unit->tenant_count }}</td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="#" class="btn btn-sm btn-outline">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="#" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="pagination-container">
                        {{ $units->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <h3>No Units Found</h3>
                        @if(isset($apartments) && $apartments->count() > 0)
                            <p>Add units to your apartments to start managing them.</p>
                            <a href="{{ route('landlord.apartments') }}" class="btn btn-primary">
                                <i class="fas fa-building"></i> Go to Apartments
                            </a>
                        @else
                            <p>You need to create an apartment first before adding units.</p>
                            <a href="{{ route('landlord.create-apartment') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Apartment
                            </a>
                        @endif
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
        
        .header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .filter-select {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background-color: white;
            color: #374151;
            font-size: 14px;
        }
        
        .filter-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
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
        
        .btn-info {
            background-color: #3b82f6;
            color: white;
        }
        
        .btn-info:hover {
            background-color: #2563eb;
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
        
        .pagination-container {
            padding: 20px;
            display: flex;
            justify-content: center;
        }
    </style>

    <script>
        document.getElementById('apartmentFilter')?.addEventListener('change', function() {
            const apartmentId = this.value;
            if (apartmentId) {
                window.location.href = `/landlord/units/${apartmentId}`;
            } else {
                window.location.href = '/landlord/units';
            }
        });
    </script>
</body>
</html> 
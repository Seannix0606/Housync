<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Apartments - Housesync</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Super Admin</h2>
                <p>{{ auth()->user()->name }}</p>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('super-admin.dashboard') }}" class="nav-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('super-admin.pending-landlords') }}" class="nav-item">
                    <i class="fas fa-user-clock"></i> Pending Landlords
                </a>
                <a href="{{ route('super-admin.users') }}" class="nav-item">
                    <i class="fas fa-users"></i> All Users
                </a>
                <a href="{{ route('super-admin.apartments') }}" class="nav-item active">
                    <i class="fas fa-building"></i> Apartments
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
                <h1>Apartment Management</h1>
                <p>Manage all apartments in the system</p>
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

            <div class="actions-bar">
                <div class="search-filters">
                    <form method="GET" action="{{ route('super-admin.apartments') }}" class="search-form">
                        <div class="search-group">
                            <input type="text" name="search" placeholder="Search apartments..." value="{{ request('search') }}" class="search-input">
                            <select name="status" class="filter-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            <select name="landlord" class="filter-select">
                                <option value="">All Landlords</option>
                                @foreach(\App\Models\User::where('role', 'landlord')->get() as $landlord)
                                    <option value="{{ $landlord->id }}" {{ request('landlord') == $landlord->id ? 'selected' : '' }}>
                                        {{ $landlord->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="section">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Apartment</th>
                                <th>Landlord</th>
                                <th>Address</th>
                                <th>Units</th>
                                <th>Occupancy</th>
                                <th>Revenue</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($apartments as $apartment)
                                <tr>
                                    <td>
                                        <div class="apartment-info">
                                            <strong>{{ $apartment->name }}</strong>
                                            @if($apartment->description)
                                                <br><small class="text-muted">{{ Str::limit($apartment->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="landlord-info">
                                            <strong>{{ $apartment->landlord->name }}</strong>
                                            <br><small class="text-muted">{{ $apartment->landlord->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="address-info">
                                            {{ $apartment->address }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="units-info">
                                            <strong>{{ $apartment->units->count() }}</strong> total
                                            <br><small class="text-muted">{{ $apartment->total_units }} planned</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="occupancy-info">
                                            @php
                                                $occupiedUnits = $apartment->getOccupiedUnitsCount();
                                                $totalUnits = $apartment->units->count();
                                                $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100) : 0;
                                            @endphp
                                            <div class="occupancy-bar">
                                                <div class="occupancy-fill" style="width: {{ $occupancyRate }}%"></div>
                                            </div>
                                            <small>{{ $occupiedUnits }}/{{ $totalUnits }} ({{ $occupancyRate }}%)</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="revenue-info">
                                            <strong>${{ number_format($apartment->getTotalRevenue(), 2) }}</strong>
                                            <br><small class="text-muted">monthly</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $apartment->status }}">
                                            {{ ucfirst($apartment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-primary" onclick="viewApartment({{ $apartment->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info" onclick="viewUnits({{ $apartment->id }})">
                                                <i class="fas fa-th-large"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No apartments found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($apartments->hasPages())
                    <div class="pagination-container">
                        {{ $apartments->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Apartment Details Modal -->
    <div id="apartmentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Apartment Details</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <div id="apartmentDetails"></div>
            </div>
        </div>
    </div>

    <!-- Units Modal -->
    <div id="unitsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Units Overview</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <div id="unitsDetails"></div>
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
        
        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 20px;
        }
        
        .search-form {
            flex: 1;
        }
        
        .search-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .search-input, .filter-select {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .search-input {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-select {
            min-width: 120px;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
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
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .data-table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
        }
        
        .data-table tr:hover {
            background-color: #f9fafb;
        }
        
        .apartment-info strong,
        .landlord-info strong,
        .units-info strong,
        .revenue-info strong {
            color: #1f2937;
        }
        
        .text-muted {
            color: #6b7280;
        }
        
        .text-center {
            text-align: center;
        }
        
        .occupancy-bar {
            width: 100%;
            height: 8px;
            background-color: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 4px;
        }
        
        .occupancy-fill {
            height: 100%;
            background-color: #10b981;
            transition: width 0.3s ease;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .badge-active {
            background-color: #10b981;
            color: white;
        }
        
        .badge-inactive {
            background-color: #6b7280;
            color: white;
        }
        
        .badge-maintenance {
            background-color: #f59e0b;
            color: white;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2563eb;
        }
        
        .btn-info {
            background-color: #06b6d4;
            color: white;
        }
        
        .btn-info:hover {
            background-color: #0891b2;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            margin-right: 5px;
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
        
        .pagination-container nav {
            display: flex;
            gap: 5px;
        }
        
        .pagination-container a,
        .pagination-container span {
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            color: #374151;
        }
        
        .pagination-container a:hover {
            background-color: #f3f4f6;
        }
        
        .pagination-container .active span {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 8px;
            width: 80%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .modal-header h2 {
            margin: 0;
            color: #1f2937;
        }
        
        .close {
            color: #6b7280;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #374151;
        }
        
        .modal-body {
            padding: 20px;
        }
    </style>

    <script>
        function viewApartment(apartmentId) {
            // In a real application, you would make an AJAX call to get apartment details
            const modal = document.getElementById('apartmentModal');
            const details = document.getElementById('apartmentDetails');
            
            // Mock data - replace with actual AJAX call
            details.innerHTML = `
                <div class="apartment-detail">
                    <h3>Apartment Details</h3>
                    <p><strong>ID:</strong> ${apartmentId}</p>
                    <p><strong>Contact Person:</strong> John Doe</p>
                    <p><strong>Contact Phone:</strong> (555) 123-4567</p>
                    <p><strong>Contact Email:</strong> john@example.com</p>
                    <p><strong>Amenities:</strong> Pool, Gym, Parking, Laundry</p>
                    <p><strong>Description:</strong> Modern apartment complex with all amenities.</p>
                </div>
            `;
            
            modal.style.display = 'block';
        }
        
        function viewUnits(apartmentId) {
            const modal = document.getElementById('unitsModal');
            const details = document.getElementById('unitsDetails');
            
            // Mock data - replace with actual AJAX call
            details.innerHTML = `
                <div class="units-overview">
                    <h3>Units Overview</h3>
                    <div class="unit-stats">
                        <div class="stat-item">
                            <strong>Total Units:</strong> 24
                        </div>
                        <div class="stat-item">
                            <strong>Occupied:</strong> 18
                        </div>
                        <div class="stat-item">
                            <strong>Available:</strong> 6
                        </div>
                        <div class="stat-item">
                            <strong>Under Maintenance:</strong> 0
                        </div>
                    </div>
                </div>
            `;
            
            modal.style.display = 'block';
        }
        
        // Close modal when clicking the X or outside the modal
        window.onclick = function(event) {
            const apartmentModal = document.getElementById('apartmentModal');
            const unitsModal = document.getElementById('unitsModal');
            
            if (event.target === apartmentModal) {
                apartmentModal.style.display = 'none';
            }
            if (event.target === unitsModal) {
                unitsModal.style.display = 'none';
            }
        }
        
        // Close modal when clicking the close button
        document.addEventListener('DOMContentLoaded', function() {
            const closeButtons = document.querySelectorAll('.close');
            closeButtons.forEach(function(button) {
                button.onclick = function() {
                    this.closest('.modal').style.display = 'none';
                }
            });
        });
    </script>
</body>
</html> 
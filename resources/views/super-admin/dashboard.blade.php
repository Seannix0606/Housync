<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - Housesync</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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
                <a href="{{ route('super-admin.dashboard') }}" class="nav-item active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('super-admin.pending-landlords') }}" class="nav-item">
                    <i class="fas fa-user-clock"></i> Pending Landlords
                    @if($stats['pending_landlords'] > 0)
                        <span class="badge">{{ $stats['pending_landlords'] }}</span>
                    @endif
                </a>
                <a href="{{ route('super-admin.users') }}" class="nav-item">
                    <i class="fas fa-users"></i> All Users
                </a>
                <a href="{{ route('super-admin.apartments') }}" class="nav-item">
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
                <h1>Dashboard Overview</h1>
                <p>Welcome back, {{ auth()->user()->name }}!</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats['total_users'] }}</h3>
                        <p>Total Users</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon pending">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats['pending_landlords'] }}</h3>
                        <p>Pending Landlords</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon approved">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats['approved_landlords'] }}</h3>
                        <p>Approved Landlords</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats['total_apartments'] }}</h3>
                        <p>Total Apartments</p>
                    </div>
                </div>
            </div>

            @if($pendingLandlords->count() > 0)
                <div class="section">
                    <div class="section-header">
                        <h2>Pending Landlord Approvals</h2>
                        <a href="{{ route('super-admin.pending-landlords') }}" class="btn btn-primary">View All</a>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingLandlords as $landlord)
                                    <tr>
                                        <td>{{ $landlord->name }}</td>
                                        <td>{{ $landlord->email }}</td>
                                        <td>{{ $landlord->phone }}</td>
                                        <td>{{ $landlord->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('super-admin.approve-landlord', $landlord->id) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                            </form>
                                            <button class="btn btn-danger btn-sm" onclick="rejectLandlord({{ $landlord->id }})">Reject</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="section">
                <div class="section-header">
                    <h2>Recent Users</h2>
                    <a href="{{ route('super-admin.users') }}" class="btn btn-primary">View All</a>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsers as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $user->status }}">{{ ucfirst($user->status) }}</span>
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
        
        .badge {
            background-color: #ef4444;
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
        
        .stat-icon.pending {
            background-color: #f59e0b;
        }
        
        .stat-icon.approved {
            background-color: #10b981;
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
        
        .table-container {
            overflow-x: auto;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th,
        .data-table td {
            padding: 12px 20px;
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
            transition: background-color 0.2s;
        }
        
        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2563eb;
        }
        
        .btn-success {
            background-color: #10b981;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #059669;
        }
        
        .btn-danger {
            background-color: #ef4444;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #dc2626;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 14px;
            margin-right: 5px;
        }
        
        .badge-super_admin {
            background-color: #7c3aed;
        }
        
        .badge-landlord {
            background-color: #3b82f6;
        }
        
        .badge-tenant {
            background-color: #10b981;
        }
        
        .badge-pending {
            background-color: #f59e0b;
        }
        
        .badge-approved {
            background-color: #10b981;
        }
        
        .badge-active {
            background-color: #10b981;
        }
        
        .badge-rejected {
            background-color: #ef4444;
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
    </style>
</body>
</html> 
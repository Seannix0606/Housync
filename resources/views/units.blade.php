<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Units Management - HouSync</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/units.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-content">
                <div class="nav-item" onclick="window.location.href='{{ route('dashboard') }}'">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </div>
                <div class="nav-item active">
                    <i class="fas fa-building"></i>
                    <span>Units</span>
                </div>
                <div class="nav-item" onclick="window.location.href='{{ route('tenants') }}'">
                    <i class="fas fa-users"></i>
                    <span>Tenants</span>
                </div>
                <div class="nav-item">
                    <i class="fas fa-credit-card"></i>
                    <span>Billing</span>
                </div>
                <div class="nav-item">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                </div>
                <div class="nav-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Security Logs</span>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <button class="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <div class="header-center">
                    <h1 class="app-title">HouSync</h1>
                </div>
                <div class="header-right">
                    <button class="header-btn">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="header-btn">
                        <i class="fas fa-bell"></i>
                    </button>
                    <div class="user-profile">
                        <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face" alt="Ann Lee" class="profile-avatar">
                        <span class="profile-name">Ann Lee</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </header>

            <!-- Units Content -->
            <div class="units-content">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="page-title-section">
                        <h2>Units Management</h2>
                        <p>Manage all property units, tenants, and availability</p>
                    </div>
                    <div class="page-actions">
                        <button class="btn btn-outline">
                            <i class="fas fa-filter"></i>
                            Filter
                        </button>
                        <button class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add Unit
                        </button>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="units-summary">
                    <div class="summary-card">
                        <div class="summary-icon total">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="summary-info">
                            <h3>Total Units</h3>
                            <span class="summary-value">30</span>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-icon occupied">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="summary-info">
                            <h3>Occupied</h3>
                            <span class="summary-value">23</span>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-icon available">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div class="summary-info">
                            <h3>Available</h3>
                            <span class="summary-value">7</span>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-icon maintenance">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div class="summary-info">
                            <h3>Maintenance</h3>
                            <span class="summary-value">0</span>
                        </div>
                    </div>
                </div>

                <!-- Units Grid -->
                <div class="units-section">
                    <div class="section-header">
                        <h3>All Units</h3>
                        <div class="view-controls">
                            <button class="view-btn active" data-view="grid">
                                <i class="fas fa-th"></i>
                            </button>
                            <button class="view-btn" data-view="list">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>

                    <div class="units-grid" id="unitsGrid">
                        <!-- Unit Card 1 -->
                        <div class="unit-card occupied">
                            <div class="unit-header">
                                <div class="unit-number">Unit 01</div>
                                <div class="unit-status occupied">Occupied</div>
                            </div>
                            <div class="unit-details">
                                <div class="unit-info">
                                    <div class="info-item">
                                        <i class="fas fa-user"></i>
                                        <span>Owner: Maria Santos</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-users"></i>
                                        <span>Tenants: 2 people</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-home"></i>
                                        <span>Type: 1 Bedroom</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-peso-sign"></i>
                                        <span>Rent: ₱8,500/month</span>
                                    </div>
                                </div>
                                <div class="unit-actions">
                                    <button class="action-btn">View Details</button>
                                </div>
                            </div>
                        </div>

                        <!-- Unit Card 2 -->
                        <div class="unit-card occupied">
                            <div class="unit-header">
                                <div class="unit-number">Unit 02</div>
                                <div class="unit-status occupied">Occupied</div>
                            </div>
                            <div class="unit-details">
                                <div class="unit-info">
                                    <div class="info-item">
                                        <i class="fas fa-user"></i>
                                        <span>Owner: Juan Dela Cruz</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-users"></i>
                                        <span>Tenants: 1 person</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-home"></i>
                                        <span>Type: Studio</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-peso-sign"></i>
                                        <span>Rent: ₱6,000/month</span>
                                    </div>
                                </div>
                                <div class="unit-actions">
                                    <button class="action-btn">View Details</button>
                                </div>
                            </div>
                        </div>

                        <!-- Unit Card 3 -->
                        <div class="unit-card available">
                            <div class="unit-header">
                                <div class="unit-number">Unit 03</div>
                                <div class="unit-status available">Available</div>
                            </div>
                            <div class="unit-details">
                                <div class="unit-info">
                                    <div class="info-item">
                                        <i class="fas fa-user"></i>
                                        <span>Owner: Ana Reyes</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-users"></i>
                                        <span>Tenants: 0 people</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-home"></i>
                                        <span>Type: 2 Bedroom</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-peso-sign"></i>
                                        <span>Rent: ₱12,000/month</span>
                                    </div>
                                </div>
                                <div class="unit-actions">
                                    <button class="action-btn primary">Add Tenant</button>
                                </div>
                            </div>
                        </div>

                        <!-- Unit Card 4 -->
                        <div class="unit-card occupied">
                            <div class="unit-header">
                                <div class="unit-number">Unit 04</div>
                                <div class="unit-status occupied">Occupied</div>
                            </div>
                            <div class="unit-details">
                                <div class="unit-info">
                                    <div class="info-item">
                                        <i class="fas fa-user"></i>
                                        <span>Owner: Carlos Mendoza</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-users"></i>
                                        <span>Tenants: 3 people</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-home"></i>
                                        <span>Type: 2 Bedroom</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-peso-sign"></i>
                                        <span>Rent: ₱11,500/month</span>
                                    </div>
                                </div>
                                <div class="unit-actions">
                                    <button class="action-btn">View Details</button>
                                </div>
                            </div>
                        </div>

                        <!-- Unit Card 5 -->
                        <div class="unit-card available">
                            <div class="unit-header">
                                <div class="unit-number">Unit 05</div>
                                <div class="unit-status available">Available</div>
                            </div>
                            <div class="unit-details">
                                <div class="unit-info">
                                    <div class="info-item">
                                        <i class="fas fa-user"></i>
                                        <span>Owner: Lisa Garcia</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-users"></i>
                                        <span>Tenants: 0 people</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-home"></i>
                                        <span>Type: 1 Bedroom</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-peso-sign"></i>
                                        <span>Rent: ₱9,000/month</span>
                                    </div>
                                </div>
                                <div class="unit-actions">
                                    <button class="action-btn primary">Add Tenant</button>
                                </div>
                            </div>
                        </div>

                        <!-- Unit Card 6 -->
                        <div class="unit-card occupied">
                            <div class="unit-header">
                                <div class="unit-number">Unit 06</div>
                                <div class="unit-status occupied">Occupied</div>
                            </div>
                            <div class="unit-details">
                                <div class="unit-info">
                                    <div class="info-item">
                                        <i class="fas fa-user"></i>
                                        <span>Owner: Roberto Cruz</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-users"></i>
                                        <span>Tenants: 2 people</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-home"></i>
                                        <span>Type: Studio</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-peso-sign"></i>
                                        <span>Rent: ₱7,500/month</span>
                                    </div>
                                </div>
                                <div class="unit-actions">
                                    <button class="action-btn">View Details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Menu toggle functionality
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
        });

        // View toggle functionality
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const view = this.dataset.view;
                const grid = document.getElementById('unitsGrid');
                
                if (view === 'list') {
                    grid.classList.add('list-view');
                } else {
                    grid.classList.remove('list-view');
                }
            });
        });
    </script>
</body>
</html> 
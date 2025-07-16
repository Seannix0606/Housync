<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Apartments - Housesync</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                            <div class="apartment-card" onclick="openApartmentModal({{ $apartment->id }})">
                                <div class="apartment-image">
                                    <div class="image-placeholder">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="status-badge status-{{ $apartment->status }}">
                                        {{ ucfirst($apartment->status) }}
                                    </div>
                                </div>
                                
                                <div class="apartment-content">
                                    <div class="apartment-header">
                                        <h3>{{ $apartment->name }}</h3>
                                        <div class="apartment-rating">
                                            <i class="fas fa-star"></i>
                                            <span>4.8</span>
                                        </div>
                                    </div>
                                    
                                    <div class="apartment-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ Str::limit($apartment->address, 50) }}</span>
                                    </div>
                                    
                                    <div class="apartment-details">
                                        <div class="detail-item">
                                            <i class="fas fa-door-open"></i>
                                            <span>{{ $apartment->units->count() }} units</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-users"></i>
                                            <span>{{ $apartment->getOccupiedUnitsCount() }} occupied</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-percentage"></i>
                                            <span>{{ $apartment->getOccupancyRate() }}% occupancy</span>
                                        </div>
                                    </div>
                                    
                                    <div class="apartment-amenities">
                                        @if($apartment->amenities && count($apartment->amenities) > 0)
                                            @foreach(array_slice($apartment->amenities, 0, 3) as $amenity)
                                                <span class="amenity-tag">{{ ucfirst(str_replace('_', ' ', $amenity)) }}</span>
                                            @endforeach
                                            @if(count($apartment->amenities) > 3)
                                                <span class="amenity-tag more">+{{ count($apartment->amenities) - 3 }} more</span>
                                            @endif
                                        @endif
                                    </div>
                                    
                                    <div class="apartment-footer">
                                        <div class="apartment-price">
                                            <span class="price">₱{{ number_format($apartment->getTotalRevenue(), 2) }}</span>
                                            <span class="period">/ month</span>
                                        </div>
                                        <div class="apartment-actions" onclick="event.stopPropagation()">
                                            <button class="action-btn edit-btn" onclick="editApartment({{ $apartment->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete-btn" onclick="deleteApartment({{ $apartment->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
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

    <!-- Apartment Management Modal -->
    <div id="apartmentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Manage Apartment</h2>
                <span class="close" onclick="closeApartmentModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="apartment-overview">
                    <div class="overview-stats">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-door-open"></i>
                            </div>
                            <div class="stat-info">
                                <h3 id="totalUnits">0</h3>
                                <p>Total Units</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon occupied">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <h3 id="occupiedUnits">0</h3>
                                <p>Occupied</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon available">
                                <i class="fas fa-key"></i>
                            </div>
                            <div class="stat-info">
                                <h3 id="availableUnits">0</h3>
                                <p>Available</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="unit-management">
                    <div class="section-header">
                        <h3>Units & Rooms</h3>
                        <button class="btn btn-primary" onclick="openAddUnitModal()">
                            <i class="fas fa-plus"></i> Add Unit
                        </button>
                    </div>
                    
                    <div id="unitsList" class="units-list">
                        <!-- Units will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Unit Modal -->
    <div id="addUnitModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Unit</h2>
                <span class="close" onclick="closeAddUnitModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addUnitForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="unitNumber">Unit Number *</label>
                            <input type="text" id="unitNumber" name="unit_number" required placeholder="e.g., 101, A1, 2B">
                        </div>
                        <div class="form-group">
                            <label for="floorNumber">Floor Number *</label>
                            <input type="number" id="floorNumber" name="floor_number" required min="1" placeholder="e.g., 1, 2, 3">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="unitType">Unit Type *</label>
                            <select id="unitType" name="unit_type" required>
                                <option value="">Select unit type</option>
                                <option value="studio">Studio</option>
                                <option value="1_bedroom">1 Bedroom</option>
                                <option value="2_bedroom">2 Bedroom</option>
                                <option value="3_bedroom">3 Bedroom</option>
                                <option value="4_bedroom">4+ Bedroom</option>
                                <option value="penthouse">Penthouse</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="rentAmount">Monthly Rent *</label>
                            <input type="number" id="rentAmount" name="rent_amount" required min="0" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bedrooms">Bedrooms *</label>
                            <input type="number" id="bedrooms" name="bedrooms" required min="0" placeholder="0">
                        </div>
                        <div class="form-group">
                            <label for="bathrooms">Bathrooms *</label>
                            <input type="number" id="bathrooms" name="bathrooms" required min="1" placeholder="1">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="maxOccupants">Maximum Occupants *</label>
                            <input type="number" id="maxOccupants" name="max_occupants" required min="1" placeholder="2">
                        </div>
                        <div class="form-group">
                            <label for="floorArea">Floor Area (sq ft)</label>
                            <input type="number" id="floorArea" name="floor_area" min="0" step="0.01" placeholder="500">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="unitDescription">Description</label>
                        <textarea id="unitDescription" name="description" rows="3" placeholder="Describe the unit features and amenities"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Unit Amenities</label>
                        <div class="amenities-grid">
                            <div class="amenity-item">
                                <input type="checkbox" id="furnished" name="amenities[]" value="furnished">
                                <label for="furnished"><i class="fas fa-couch"></i> Furnished</label>
                            </div>
                            <div class="amenity-item">
                                <input type="checkbox" id="balcony_unit" name="amenities[]" value="balcony">
                                <label for="balcony_unit"><i class="fas fa-home"></i> Balcony</label>
                            </div>
                            <div class="amenity-item">
                                <input type="checkbox" id="ac_unit" name="amenities[]" value="air_conditioning">
                                <label for="ac_unit"><i class="fas fa-snowflake"></i> A/C</label>
                            </div>
                            <div class="amenity-item">
                                <input type="checkbox" id="kitchen" name="amenities[]" value="kitchen">
                                <label for="kitchen"><i class="fas fa-utensils"></i> Kitchen</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeAddUnitModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Unit</button>
                    </div>
                </form>
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
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
            padding: 20px;
        }
        
        .apartment-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid #f0f0f0;
        }
        
        .apartment-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .apartment-image {
            position: relative;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .image-placeholder {
            color: white;
            font-size: 48px;
            opacity: 0.7;
        }
        
        .apartment-content {
            padding: 20px;
        }
        
        .apartment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        
        .apartment-header h3 {
            margin: 0;
            color: #1f2937;
            font-size: 18px;
            font-weight: 600;
        }
        
        .apartment-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #fbbf24;
            font-size: 14px;
            font-weight: 500;
        }
        
        .apartment-location {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 16px;
        }
        
        .apartment-location i {
            color: #9ca3af;
        }
        
        .apartment-details {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 16px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #6b7280;
            font-size: 13px;
        }
        
        .detail-item i {
            color: #9ca3af;
            width: 14px;
        }
        
        .apartment-amenities {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
        }
        
        .amenity-tag {
            background: #f3f4f6;
            color: #6b7280;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .amenity-tag.more {
            background: #e5e7eb;
            color: #4b5563;
        }
        
        .apartment-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 16px;
            border-top: 1px solid #f3f4f6;
        }
        
        .apartment-price {
            display: flex;
            align-items: baseline;
            gap: 4px;
        }
        
        .price {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .period {
            font-size: 14px;
            color: #6b7280;
        }
        
        .apartment-actions {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }
        
        .edit-btn {
            background: #f3f4f6;
            color: #6b7280;
        }
        
        .edit-btn:hover {
            background: #3b82f6;
            color: white;
        }
        
        .delete-btn {
            background: #f3f4f6;
            color: #6b7280;
        }
        
        .delete-btn:hover {
            background: #dc2626;
            color: white;
        }
        
        .status-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 500;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
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
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background-color: white;
            margin: 2% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 30px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .modal-header h2 {
            margin: 0;
            color: #1f2937;
            font-size: 24px;
        }
        
        .close {
            color: #9ca3af;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.2s;
        }
        
        .close:hover {
            color: #1f2937;
        }
        
        .modal-body {
            padding: 30px;
        }
        
        .apartment-overview {
            margin-bottom: 30px;
        }
        
        .overview-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background-color: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        .stat-icon.occupied {
            background-color: #10b981;
        }
        
        .stat-icon.available {
            background-color: #f59e0b;
        }
        
        .stat-info h3 {
            margin: 0;
            font-size: 28px;
            color: #1f2937;
        }
        
        .stat-info p {
            margin: 4px 0 0 0;
            color: #6b7280;
            font-size: 14px;
        }
        
        .unit-management {
            border-top: 1px solid #e5e7eb;
            padding-top: 30px;
        }
        
        .units-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .unit-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.2s;
        }
        
        .unit-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .unit-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        
        .unit-number {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .unit-status {
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .unit-status.available {
            background: #d1fae5;
            color: #065f46;
        }
        
        .unit-status.occupied {
            background: #fef3c7;
            color: #92400e;
        }
        
        .unit-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 16px;
        }
        
        .unit-detail {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #6b7280;
            font-size: 14px;
        }
        
        .unit-detail i {
            color: #9ca3af;
            width: 14px;
        }
        
        .unit-rent {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 12px;
        }
        
        .unit-actions {
            display: flex;
            gap: 8px;
        }
        
        .unit-action-btn {
            padding: 6px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: white;
            color: #6b7280;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .unit-action-btn:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }
        
        .unit-action-btn.edit:hover {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        
        .unit-action-btn.delete:hover {
            background: #dc2626;
            color: white;
            border-color: #dc2626;
        }
        
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 5% auto;
            }
            
            .modal-header {
                padding: 20px;
            }
            
            .modal-body {
                padding: 20px;
            }
            
            .overview-stats {
                grid-template-columns: 1fr;
            }
            
            .units-list {
                grid-template-columns: 1fr;
            }
            
            .apartments-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    
    <script>
        let currentApartmentId = null;
        
        // Modal Functions
        function openApartmentModal(apartmentId) {
            currentApartmentId = apartmentId;
            document.getElementById('apartmentModal').style.display = 'block';
            loadApartmentDetails(apartmentId);
        }
        
        function closeApartmentModal() {
            document.getElementById('apartmentModal').style.display = 'none';
            currentApartmentId = null;
        }
        
        function openAddUnitModal() {
            document.getElementById('addUnitModal').style.display = 'block';
        }
        
        function closeAddUnitModal() {
            document.getElementById('addUnitModal').style.display = 'none';
            document.getElementById('addUnitForm').reset();
        }
        
        // Load apartment details
        function loadApartmentDetails(apartmentId) {
            // This would typically fetch data from the server
            // For now, we'll use the data from the page
            const apartmentCards = document.querySelectorAll('.apartment-card');
            let apartmentData = null;
            
            // Find the apartment data (in a real app, this would be an API call)
            fetch(`/landlord/apartments/${apartmentId}/details`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalTitle').textContent = `Manage ${data.name}`;
                    document.getElementById('totalUnits').textContent = data.total_units || 0;
                    document.getElementById('occupiedUnits').textContent = data.occupied_units || 0;
                    document.getElementById('availableUnits').textContent = data.available_units || 0;
                    
                    loadUnits(apartmentId);
                })
                .catch(error => {
                    console.error('Error loading apartment details:', error);
                    // Fallback to basic info
                    document.getElementById('modalTitle').textContent = 'Manage Apartment';
                });
        }
        
        // Load units for the apartment
        function loadUnits(apartmentId) {
            fetch(`/landlord/apartments/${apartmentId}/units`)
                .then(response => response.json())
                .then(data => {
                    const unitsList = document.getElementById('unitsList');
                    
                    if (data.units && data.units.length > 0) {
                        unitsList.innerHTML = data.units.map(unit => `
                            <div class="unit-card">
                                <div class="unit-header">
                                    <div class="unit-number">${unit.unit_number}</div>
                                    <div class="unit-status ${unit.status}">${unit.status}</div>
                                </div>
                                <div class="unit-details">
                                    <div class="unit-detail">
                                        <i class="fas fa-bed"></i>
                                        <span>${unit.bedrooms} bed</span>
                                    </div>
                                    <div class="unit-detail">
                                        <i class="fas fa-bath"></i>
                                        <span>${unit.bathrooms} bath</span>
                                    </div>
                                    <div class="unit-detail">
                                        <i class="fas fa-users"></i>
                                        <span>Max ${unit.max_occupants || 'N/A'}</span>
                                    </div>
                                    <div class="unit-detail">
                                        <i class="fas fa-layer-group"></i>
                                        <span>Floor ${unit.floor_number || 'N/A'}</span>
                                    </div>
                                </div>
                                <div class="unit-rent">₱${parseFloat(unit.rent_amount).toLocaleString()}/month</div>
                                <div class="unit-actions">
                                    <button class="unit-action-btn edit" onclick="editUnit(${unit.id})">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="unit-action-btn delete" onclick="deleteUnit(${unit.id})">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        unitsList.innerHTML = `
                            <div class="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                                <div class="empty-icon" style="font-size: 48px; color: #9ca3af; margin-bottom: 20px;">
                                    <i class="fas fa-door-open"></i>
                                </div>
                                <h3 style="color: #1f2937; margin-bottom: 10px;">No Units Yet</h3>
                                <p style="color: #6b7280; margin-bottom: 20px;">Start by adding your first unit to this apartment.</p>
                                <button class="btn btn-primary" onclick="openAddUnitModal()">
                                    <i class="fas fa-plus"></i> Add First Unit
                                </button>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading units:', error);
                    document.getElementById('unitsList').innerHTML = '<p>Error loading units. Please try again.</p>';
                });
        }
        
        // Edit/Delete apartment functions
        function editApartment(apartmentId) {
            window.location.href = `/landlord/apartments/${apartmentId}/edit`;
        }
        
        function deleteApartment(apartmentId) {
            if (confirm('Are you sure you want to delete this apartment? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/landlord/apartments/${apartmentId}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Unit management functions
        function editUnit(unitId) {
            // Implement edit unit functionality
            console.log('Edit unit:', unitId);
        }
        
        function deleteUnit(unitId) {
            if (confirm('Are you sure you want to delete this unit?')) {
                // Implement delete unit functionality
                console.log('Delete unit:', unitId);
            }
        }
        
        // Add unit form submission
        document.getElementById('addUnitForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('apartment_id', currentApartmentId);
            
            fetch(`/landlord/apartments/${currentApartmentId}/units`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeAddUnitModal();
                    loadUnits(currentApartmentId);
                    loadApartmentDetails(currentApartmentId);
                } else {
                    alert('Error adding unit: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding unit. Please try again.');
            });
        });
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const apartmentModal = document.getElementById('apartmentModal');
            const addUnitModal = document.getElementById('addUnitModal');
            
            if (event.target == apartmentModal) {
                closeApartmentModal();
            }
            if (event.target == addUnitModal) {
                closeAddUnitModal();
            }
        }
    </script>
    
    <!-- Firebase App Check Scripts -->
    @include('partials.firebase-scripts')
</body>
</html> 
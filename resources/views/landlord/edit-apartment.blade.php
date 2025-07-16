<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Apartment - Housesync</title>
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
                <h1>Edit Apartment</h1>
                <p>Update apartment information</p>
            </div>

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-container">
                <form method="POST" action="{{ route('landlord.update-apartment', $apartment->id) }}" class="apartment-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-building"></i> Basic Information</h3>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Apartment Name *</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $apartment->name) }}" required 
                                       placeholder="e.g., Sunset Apartments, Downtown Complex">
                            </div>
                            <div class="form-group">
                                <label for="total_units">Total Units *</label>
                                <input type="number" id="total_units" name="total_units" value="{{ old('total_units', $apartment->total_units) }}" 
                                       required min="1" placeholder="e.g., 24">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Complete Address *</label>
                            <textarea id="address" name="address" rows="3" required 
                                      placeholder="Enter the complete address including street, city, state, and postal code">{{ old('address', $apartment->address) }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4" 
                                      placeholder="Describe the apartment complex, its features, and location benefits">{{ old('description', $apartment->description) }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select id="status" name="status" required>
                                <option value="active" {{ old('status', $apartment->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $apartment->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="maintenance" {{ old('status', $apartment->status) == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-phone"></i> Contact Information</h3>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" id="contact_person" name="contact_person" 
                                       value="{{ old('contact_person', $apartment->contact_person) }}" 
                                       placeholder="Property manager or contact person name">
                            </div>
                            <div class="form-group">
                                <label for="contact_phone">Contact Phone</label>
                                <input type="tel" id="contact_phone" name="contact_phone" 
                                       value="{{ old('contact_phone', $apartment->contact_phone) }}" 
                                       placeholder="+1 (555) 123-4567">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_email">Contact Email</label>
                            <input type="email" id="contact_email" name="contact_email" 
                                   value="{{ old('contact_email', $apartment->contact_email) }}" 
                                   placeholder="property@example.com">
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-header">
                            <h3><i class="fas fa-star"></i> Amenities</h3>
                        </div>
                        
                        <div class="amenities-grid">
                            @php
                                $selectedAmenities = old('amenities', $apartment->amenities ?? []);
                                $amenityOptions = [
                                    'parking' => ['icon' => 'fas fa-car', 'label' => 'Parking'],
                                    'elevator' => ['icon' => 'fas fa-arrows-alt-v', 'label' => 'Elevator'],
                                    'security' => ['icon' => 'fas fa-shield-alt', 'label' => 'Security'],
                                    'laundry' => ['icon' => 'fas fa-tshirt', 'label' => 'Laundry'],
                                    'gym' => ['icon' => 'fas fa-dumbbell', 'label' => 'Gym'],
                                    'pool' => ['icon' => 'fas fa-swimming-pool', 'label' => 'Pool'],
                                    'garden' => ['icon' => 'fas fa-seedling', 'label' => 'Garden'],
                                    'wifi' => ['icon' => 'fas fa-wifi', 'label' => 'WiFi'],
                                    'air_conditioning' => ['icon' => 'fas fa-snowflake', 'label' => 'Air Conditioning'],
                                    'balcony' => ['icon' => 'fas fa-home', 'label' => 'Balcony'],
                                    'storage' => ['icon' => 'fas fa-box', 'label' => 'Storage'],
                                    'playground' => ['icon' => 'fas fa-child', 'label' => 'Playground']
                                ];
                            @endphp
                            
                            @foreach($amenityOptions as $value => $amenity)
                                <div class="amenity-item">
                                    <input type="checkbox" id="{{ $value }}" name="amenities[]" value="{{ $value }}" 
                                           {{ in_array($value, $selectedAmenities) ? 'checked' : '' }}>
                                    <label for="{{ $value }}">
                                        <i class="{{ $amenity['icon'] }}"></i> {{ $amenity['label'] }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('landlord.apartments') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Apartment
                        </button>
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
        
        .form-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .apartment-form {
            padding: 30px;
        }
        
        .form-section {
            margin-bottom: 40px;
        }
        
        .form-section:last-child {
            margin-bottom: 0;
        }
        
        .section-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f3f4;
        }
        
        .section-header h3 {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-header i {
            color: #3b82f6;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            flex: 1;
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            font-family: 'Inter', sans-serif;
            color: #1f2937;
            background: #ffffff;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #9ca3af;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .form-group select {
            cursor: pointer;
        }
        
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .amenity-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .amenity-item:hover {
            border-color: #3b82f6;
            background-color: #f8fafc;
        }
        
        .amenity-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #3b82f6;
        }
        
        .amenity-item label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #1f2937;
            cursor: pointer;
            margin: 0;
        }
        
        .amenity-item label i {
            color: #6b7280;
            width: 16px;
        }
        
        .amenity-item input[type="checkbox"]:checked + label {
            color: #3b82f6;
        }
        
        .amenity-item input[type="checkbox"]:checked + label i {
            color: #3b82f6;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2563eb;
        }
        
        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #4b5563;
        }
        
        .alert {
            padding: 16px;
            margin-bottom: 24px;
            border-radius: 8px;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .alert-error {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        
        .alert ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .alert li {
            margin-bottom: 4px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .amenities-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
    
    @include('partials.firebase-scripts')
</body>
</html> 
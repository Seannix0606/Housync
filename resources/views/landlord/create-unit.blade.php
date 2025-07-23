<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Unit - Housesync</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Use styles from landlord/units.blade.php and landlord/create-apartment.blade.php for consistency */
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .dashboard-container { display: flex; min-height: 100vh; }
        .sidebar { width: 280px; background: linear-gradient(180deg, #ea580c 0%, #dc2626 100%); color: white; display: flex; flex-direction: column; position: fixed; height: 100vh; left: 0; top: 0; z-index: 1000; }
        .sidebar-header { padding: 2rem 1.5rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h2 { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; }
        .sidebar-header p { font-size: 0.875rem; opacity: 0.8; }
        .sidebar-nav { flex: 1; padding: 1.5rem 0; }
        .nav-item { display: flex; align-items: center; padding: 0.875rem 1.5rem; color: rgba(255,255,255,0.8); text-decoration: none; transition: all 0.2s; border-left: 3px solid transparent; position: relative; }
        .nav-item:hover { background-color: rgba(255,255,255,0.1); color: white; border-left-color: #fb923c; }
        .nav-item.active { background-color: #f97316; color: white; border-left-color: #fb923c; }
        .nav-item i { width: 20px; margin-right: 0.75rem; font-size: 1rem; }
        .sidebar-footer { padding: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); }
        .logout-btn { display: flex; align-items: center; width: 100%; padding: 0.875rem; background: rgba(255,255,255,0.1); border: none; border-radius: 0.5rem; color: white; text-decoration: none; transition: all 0.2s; }
        .logout-btn:hover { background: rgba(255,255,255,0.2); color: white; }
        .logout-btn i { margin-right: 0.5rem; }
        .main-content { flex: 1; margin-left: 280px; padding: 2rem; }
        .content-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .content-header h1 { font-size: 2rem; font-weight: 700; color: #1e293b; }
        .user-profile { display: flex; align-items: center; background: white; padding: 0.75rem 1rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #f97316, #ea580c); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; margin-right: 0.75rem; }
        .user-info h3 { font-size: 0.875rem; font-weight: 600; color: #1e293b; }
        .user-info p { font-size: 0.75rem; color: #64748b; }
        .page-section { background: white; border-radius: 1rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .section-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9; }
        .section-title { font-size: 1.5rem; font-weight: 700; color: #1e293b; }
        .section-subtitle { color: #64748b; font-size: 1rem; margin-top: 0.25rem; }
        .form-container { max-width: 800px; margin: 0 auto; }
        .form-section { margin-bottom: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: 0.75rem; border-left: 4px solid #f97316; }
        .form-section-title { font-size: 1.125rem; font-weight: 600; color: #1e293b; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .form-section-title i { color: #f97316; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; }
        .form-group { display: flex; flex-direction: column; }
        .form-group.full-width { grid-column: 1 / -1; }
        .form-label { font-size: 0.875rem; font-weight: 500; color: #1e293b; margin-bottom: 0.5rem; }
        .form-label.required::after { content: ' *'; color: #ef4444; }
        .form-control { padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s; background: white; }
        .form-control:focus { outline: none; border-color: #f97316; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1); }
        .form-control.error { border-color: #ef4444; }
        .form-control.error:focus { box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1); }
        textarea.form-control { resize: vertical; min-height: 100px; }
        select.form-control { cursor: pointer; }
        .form-help { font-size: 0.75rem; color: #64748b; margin-top: 0.25rem; }
        .form-error { font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem; }
        .amenities-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem; }
        .amenity-item { display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; background: white; border: 1px solid #e2e8f0; border-radius: 0.5rem; cursor: pointer; transition: all 0.2s; }
        .amenity-item:hover { border-color: #f97316; background: #fef7ed; }
        .amenity-item input[type="checkbox"] { width: 1rem; height: 1rem; accent-color: #f97316; }
        .amenity-item label { font-size: 0.875rem; color: #1e293b; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; }
        .amenity-item i { color: #f97316; width: 16px; }
        .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-primary { background: #f97316; color: white; }
        .btn-primary:hover { background: #ea580c; color: white; }
        .btn-secondary { background: #6b7280; color: white; }
        .btn-secondary:hover { background: #4b5563; }
        .btn-lg { padding: 1rem 2rem; font-size: 1rem; }
        .form-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #f1f5f9; }
        .alert { padding: 1rem 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; }
        .alert-success { background: #d1fae5; border: 1px solid #a7f3d0; color: #047857; }
        .alert-error { background: #fee2e2; border: 1px solid #fecaca; color: #dc2626; }
        @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } .amenities-grid { grid-template-columns: 1fr; } .form-actions { flex-direction: column; } }
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
            <div class="content-header">
                <div>
                    <h1>Add New Unit</h1>
                    <p style="color: #64748b; margin-top: 0.5rem;">Add a new unit to {{ $apartment->name ?? 'your property' }}</p>
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
            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> Please fix the following errors:
                    <ul style="margin-left: 1rem; margin-top: 0.5rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="page-section">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Unit Information</h2>
                        <p class="section-subtitle">Fill in the details for the new unit</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('landlord.store-unit', $apartment->id) }}" class="form-container">
                    @csrf
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-door-open"></i>
                            Basic Unit Details
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label required">Unit Number</label>
                                <input type="text" name="unit_number" class="form-control @error('unit_number') error @enderror" value="{{ old('unit_number') }}" required>
                                @error('unit_number')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Unit Type</label>
                                <input type="text" name="unit_type" class="form-control @error('unit_type') error @enderror" value="{{ old('unit_type') }}" required placeholder="e.g., Studio, 1BR, 2BR">
                                @error('unit_type')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Rent Amount (₱)</label>
                                <input type="number" name="rent_amount" class="form-control @error('rent_amount') error @enderror" value="{{ old('rent_amount') }}" min="0" step="0.01" required>
                                @error('rent_amount')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Status</label>
                                <select name="status" class="form-control @error('status') error @enderror" required>
                                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                                @error('status')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Leasing Type</label>
                                <select name="leasing_type" class="form-control @error('leasing_type') error @enderror" required>
                                    <option value="separate" {{ old('leasing_type') == 'separate' ? 'selected' : '' }}>Separate Bills</option>
                                    <option value="inclusive" {{ old('leasing_type') == 'inclusive' ? 'selected' : '' }}>All Inclusive</option>
                                </select>
                                @error('leasing_type')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control @error('description') error @enderror" placeholder="Describe the unit...">{{ old('description') }}</textarea>
                                @error('description')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-cogs"></i>
                            Unit Details
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Floor Area (sq ft)</label>
                                <input type="number" name="floor_area" class="form-control @error('floor_area') error @enderror" value="{{ old('floor_area') }}" min="0" step="0.01">
                                @error('floor_area')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Bedrooms</label>
                                <input type="number" name="bedrooms" class="form-control @error('bedrooms') error @enderror" value="{{ old('bedrooms') }}" min="0" required>
                                @error('bedrooms')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Bathrooms</label>
                                <input type="number" name="bathrooms" class="form-control @error('bathrooms') error @enderror" value="{{ old('bathrooms') }}" min="1" required>
                                @error('bathrooms')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Max Occupants</label>
                                <input type="number" name="max_occupants" class="form-control @error('max_occupants') error @enderror" value="{{ old('max_occupants') }}" min="1">
                                @error('max_occupants')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Floor Number</label>
                                <input type="number" name="floor_number" class="form-control @error('floor_number') error @enderror" value="{{ old('floor_number') }}" min="1">
                                @error('floor_number')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control @error('notes') error @enderror" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                                @error('notes')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Furnished</label>
                                <input type="checkbox" name="is_furnished" value="1" {{ old('is_furnished') ? 'checked' : '' }}> Yes
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Amenities</label>
                            <div class="amenities-grid">
                                <div class="amenity-item">
                                    <input type="checkbox" id="ac" name="amenities[]" value="ac" {{ in_array('ac', old('amenities', [])) ? 'checked' : '' }}>
                                    <label for="ac"><i class="fas fa-wind"></i> Air Conditioning</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" id="balcony" name="amenities[]" value="balcony" {{ in_array('balcony', old('amenities', [])) ? 'checked' : '' }}>
                                    <label for="balcony"><i class="fas fa-umbrella-beach"></i> Balcony</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" id="laundry" name="amenities[]" value="laundry" {{ in_array('laundry', old('amenities', [])) ? 'checked' : '' }}>
                                    <label for="laundry"><i class="fas fa-tshirt"></i> Laundry</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" id="wifi" name="amenities[]" value="wifi" {{ in_array('wifi', old('amenities', [])) ? 'checked' : '' }}>
                                    <label for="wifi"><i class="fas fa-wifi"></i> WiFi</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" id="parking" name="amenities[]" value="parking" {{ in_array('parking', old('amenities', [])) ? 'checked' : '' }}>
                                    <label for="parking"><i class="fas fa-parking"></i> Parking</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" id="pet" name="amenities[]" value="pet" {{ in_array('pet', old('amenities', [])) ? 'checked' : '' }}>
                                    <label for="pet"><i class="fas fa-dog"></i> Pet Friendly</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" id="elevator" name="amenities[]" value="elevator" {{ in_array('elevator', old('amenities', [])) ? 'checked' : '' }}>
                                    <label for="elevator"><i class="fas fa-arrow-up"></i> Elevator</label>
                                </div>
                                <div class="amenity-item">
                                    <input type="checkbox" id="security" name="amenities[]" value="security" {{ in_array('security', old('amenities', [])) ? 'checked' : '' }}>
                                    <label for="security"><i class="fas fa-shield-alt"></i> Security</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <a href="{{ route('landlord.units', $apartment->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Create Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 
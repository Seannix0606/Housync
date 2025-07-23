<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Tenant - Housesync</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
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
        .badge-count { background-color: #ef4444; color: white; border-radius: 9999px; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 600; margin-left: auto; }
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
        .form-label.required { color: #dc2626; }
        .form-control { padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.95rem; transition: all 0.2s; background: white; }
        .form-control:focus { outline: none; border-color: #f97316; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1); }
        .form-control.error { border-color: #ef4444; }
        .form-control.error:focus { box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1); }
        textarea.form-control { resize: vertical; min-height: 100px; }
        select.form-control { cursor: pointer; }
        .form-error { font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem; }
        .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-primary { background: #f97316; color: white; }
        .btn-primary:hover { background: #ea580c; color: white; }
        .btn-secondary { background: #6b7280; color: white; }
        .btn-secondary:hover { background: #4b5563; }
        .alert { padding: 1rem 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; }
        .alert-success { background: #d1fae5; border: 1px solid #a7f3d0; color: #047857; }
        .alert-error { background: #fee2e2; border: 1px solid #fecaca; color: #dc2626; }
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
                    <h1>Assign Tenant</h1>
                    <p style="color: #64748b; margin-top: 0.5rem;">Assign a tenant to an available unit</p>
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
            <div class="page-section">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Create Tenant to Assign to Unit</h2>
                        <p class="section-subtitle">Fill in the details to create a new tenant and assign them to a unit</p>
                    </div>
                </div>
                <pre>{!! print_r(session()->all(), true) !!}</pre>
                <form method="POST" action="{{ route('landlord.store-tenant-assignment') }}">
                    @csrf
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title">Select Unit</h5>
                            <div class="mb-3">
                                <label for="unit_id" class="form-label required">Unit</label>
                                <select name="unit_id" id="unit_id" class="form-control" required>
                                    <option value="">-- Select Unit --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">
                                            {{ $unit->apartment->name }} - Unit {{ $unit->unit_number }} ({{ $unit->unit_type }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title">Tenant Information</h5>
                            <div class="mb-3">
                                <label for="name" class="form-label required">Full Name</label>
                                <input type="text" class="form-control @error('name') error @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone') error @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Current Address</label>
                                <textarea class="form-control @error('address') error @enderror" 
                                          id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label required">Email Address</label>
                                <input type="email" class="form-control @error('email') error @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label required">Password</label>
                                <input type="password" class="form-control @error('password') error @enderror" 
                                       id="password" name="password" required minlength="8">
                                @error('password')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label required">Confirm Password</label>
                                <input type="password" class="form-control @error('password_confirmation') error @enderror" 
                                       id="password_confirmation" name="password_confirmation" required minlength="8">
                                @error('password_confirmation')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title">Lease Information</h5>
                            <div class="mb-3">
                                <label for="lease_start_date" class="form-label required">Lease Start Date</label>
                                <input type="date" class="form-control @error('lease_start_date') error @enderror" 
                                       id="lease_start_date" name="lease_start_date" value="{{ old('lease_start_date') }}" required>
                                @error('lease_start_date')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="lease_end_date" class="form-label required">Lease End Date</label>
                                <input type="date" class="form-control @error('lease_end_date') error @enderror" 
                                       id="lease_end_date" name="lease_end_date" value="{{ old('lease_end_date') }}" required>
                                @error('lease_end_date')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="rent_amount" class="form-label required">Monthly Rent</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" step="0.01" class="form-control @error('rent_amount') error @enderror" 
                                           id="rent_amount" name="rent_amount" value="{{ old('rent_amount') }}" required>
                                </div>
                                @error('rent_amount')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="security_deposit" class="form-label">Security Deposit</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" step="0.01" class="form-control @error('security_deposit') error @enderror" 
                                           id="security_deposit" name="security_deposit" value="{{ old('security_deposit', 0) }}">
                                </div>
                                @error('security_deposit')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control @error('notes') error @enderror" 
                                          id="notes" name="notes" rows="3" placeholder="Any additional information about this assignment...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <h6 class="alert-heading">Required Documents</h6>
                                <p class="mb-0">The tenant will need to upload the following documents:</p>
                                <ul class="mb-0 mt-2">
                                    <li>Government ID (Passport, Driver's License, etc.)</li>
                                    <li>Proof of Income (Payslip, Employment Contract, etc.)</li>
                                    <li>Bank Statement (Last 3 months)</li>
                                    <li>Character Reference</li>
                                    <li>Rental History (if applicable)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('landlord.tenants') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Create Tenant
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date for lease start date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('lease_start_date').min = today;
    // Update lease end date minimum when start date changes
    document.getElementById('lease_start_date').addEventListener('change', function() {
        const startDate = this.value;
        const endDateInput = document.getElementById('lease_end_date');
        endDateInput.min = startDate;
        // If end date is before start date, clear it
        if (endDateInput.value && endDateInput.value < startDate) {
            endDateInput.value = '';
        }
    });
});
</script>
</body>
</html> 
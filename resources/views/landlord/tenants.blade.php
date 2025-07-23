<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenants - Housesync</title>
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
        .tenants-table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; }
        .tenants-table th, .tenants-table td { padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; text-align: left; }
        .tenants-table th { color: #64748b; font-size: 0.95rem; font-weight: 600; background: #f8fafc; }
        .tenants-table td { font-size: 0.97rem; }
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .status-active { background: #d1fae5; color: #059669; }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-terminated { background: #fee2e2; color: #dc2626; }
        .empty-state { text-align: center; padding: 4rem 2rem; color: #64748b; }
        .assign-modal-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 160px;
            padding: 0.65rem 1.5rem;
            margin-bottom: 0;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 2rem;
            background: #f97316;
            color: white;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(249,115,22,0.08);
            cursor: pointer;
            gap: 0.5rem;
        }
        .assign-modal-btn.secondary {
            background: #f3f4f6;
            color: #1e293b;
            border: 1.5px solid #e2e8f0;
        }
        .assign-modal-btn:hover {
            background: #ea580c;
            color: white;
            box-shadow: 0 4px 16px rgba(249,115,22,0.13);
        }
        .assign-modal-btn.secondary:hover {
            background: #f97316;
            color: white;
            border-color: #f97316;
        }
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
                <a href="{{ route('landlord.tenants') }}" class="nav-item active">
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
                    <h1>Tenants</h1>
                    <p style="color: #64748b; margin-top: 0.5rem;">View all tenants under your properties and units</p>
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
                        <h2 class="section-title">All Tenants</h2>
                        <p class="section-subtitle">Below is a list of all tenants assigned to your properties</p>
                    </div>
                    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                        <button class="assign-modal-btn" onclick="openAssignExistingTenantModal()">
                            <i class="fas fa-user-check"></i> Assign Existing Tenant
                        </button>
                        <a href="{{ route('landlord.assign-tenant') }}" class="assign-modal-btn secondary">
                            <i class="fas fa-user-plus"></i> Create New Tenant
                        </a>
                        <a href="#" class="assign-modal-btn" style="background:#2563eb;">
                            <i class="fas fa-envelope"></i> Invite Tenant
                        </a>
                    </div>
                </div>
                @if($tenantAssignments->count() > 0)
                <table class="tenants-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Unit</th>
                            <th>Property</th>
                            <th>Lease Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenantAssignments as $assignment)
                            <tr>
                                <td>{{ $assignment->tenant->name ?? 'N/A' }}</td>
                                <td>{{ $assignment->tenant->email ?? 'N/A' }}</td>
                                <td>{{ $assignment->unit->unit_number ?? 'N/A' }}</td>
                                <td>{{ $assignment->unit->apartment->name ?? 'N/A' }}</td>
                                <td>
                                    @if($assignment->status === 'active')
                                        <span class="status-badge status-active">Active</span>
                                    @elseif($assignment->status === 'pending')
                                        <span class="status-badge status-pending">Pending</span>
                                    @else
                                        <span class="status-badge status-terminated">Terminated</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" title="Edit Tenant" onclick="openEditTenantModal({{ $assignment->id }}, '{{ addslashes($assignment->tenant->name ?? 'N/A') }}', '{{ addslashes($assignment->tenant->email ?? 'N/A') }}', '{{ $assignment->unit->unit_number ?? 'N/A' }}', '{{ addslashes($assignment->unit->apartment->name ?? 'N/A') }}', '{{ $assignment->lease_start_date }}', '{{ $assignment->lease_end_date }}', '{{ $assignment->rent_amount }}', `{{ addslashes($assignment->notes) }}`)"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('landlord.delete-tenant-assignment', $assignment->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('WARNING: Deleting this tenant will permanently remove their account if they have no other assignments. Are you sure you want to proceed?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Revoke Access"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state">
                    <i class="fas fa-users" style="font-size: 2.5rem; color: #f97316;"></i>
                    <div style="margin-top: 1rem;">No tenants found for your properties yet.</div>
                </div>
                @endif
            </div>
        </div>
    </div>
<!-- Assign Existing Tenant Modal -->
<div id="assignExistingTenantModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(30,41,59,0.4); z-index:2000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:1rem; max-width:700px; width:95%; margin:auto; padding:2rem; position:relative; box-shadow:0 8px 32px rgba(0,0,0,0.2); max-height:90vh; overflow-y:auto;">
        <button onclick="closeAssignExistingTenantModal()" style="position:absolute; top:1rem; right:1rem; background:none; border:none; font-size:1.5rem; color:#64748b; cursor:pointer;">&times;</button>
        <h2 style="font-size:1.25rem; font-weight:700; margin-bottom:1rem; color:#1e293b;">Assign Existing Tenant</h2>
        <table class="tenants-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($unassignedTenants as $tenant)
                    <tr>
                        <td>{{ $tenant->name }}</td>
                        <td>{{ $tenant->email }}</td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="showAssignToUnitForm('{{ $tenant->id }}', '{{ $tenant->name }}')">Assign to Unit</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align:center; color:#64748b;">No unassigned tenants found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div id="assignToUnitFormContainer" style="display:none; margin-top:2rem;">
            <h3 style="font-size:1.1rem; font-weight:600; margin-bottom:1rem;">Assign <span id="selectedTenantName"></span> to Unit</h3>
            <form>
                <input type="hidden" id="assignTenantId" name="tenant_id" value="">
                <div class="mb-3">
                    <label for="unitSelect" class="form-label">Select Unit</label>
                    <select id="unitSelect" class="form-control">
                        <option value="">-- Select Unit --</option>
                        @foreach($availableUnits as $unit)
                            <option value="{{ $unit->id }}">
                                {{ $unit->apartment->name }} - Unit {{ $unit->unit_number }} ({{ $unit->unit_type }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="leaseStart" class="form-label">Lease Start Date</label>
                    <input type="date" id="leaseStart" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="leaseEnd" class="form-label">Lease End Date</label>
                    <input type="date" id="leaseEnd" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="rentAmount" class="form-label">Monthly Rent</label>
                    <input type="number" id="rentAmount" class="form-control" placeholder="₱">
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" onclick="hideAssignToUnitForm()">Cancel</button>
                    <button type="button" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Tenant Modal -->
<div id="editTenantModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(30,41,59,0.4); z-index:3000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:1rem; max-width:500px; width:95%; margin:auto; padding:2rem; position:relative; box-shadow:0 8px 32px rgba(0,0,0,0.2); max-height:90vh; overflow-y:auto;">
        <button onclick="closeEditTenantModal()" style="position:absolute; top:1rem; right:1rem; background:none; border:none; font-size:1.5rem; color:#64748b; cursor:pointer;">&times;</button>
        <h2 style="font-size:1.25rem; font-weight:700; margin-bottom:1rem; color:#1e293b;">Edit Tenant Assignment</h2>
        <form id="editTenantForm" method="POST">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <div class="mb-3">
                <label class="form-label">Tenant Name</label>
                <input type="text" id="editTenantName" class="form-control" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" id="editTenantEmail" class="form-control" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Unit</label>
                <input type="text" id="editTenantUnit" class="form-control" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Property</label>
                <input type="text" id="editTenantProperty" class="form-control" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Lease Start Date</label>
                <input type="date" name="lease_start_date" id="editLeaseStart" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Lease End Date</label>
                <input type="date" name="lease_end_date" id="editLeaseEnd" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Monthly Rent</label>
                <input type="number" name="rent_amount" id="editRentAmount" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" id="editNotes" class="form-control"></textarea>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" onclick="closeEditTenantModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
<script>
function openAssignExistingTenantModal() {
    document.getElementById('assignExistingTenantModal').style.display = 'flex';
    document.body.classList.add('modal-open');
    hideAssignToUnitForm();
}
function closeAssignExistingTenantModal() {
    document.getElementById('assignExistingTenantModal').style.display = 'none';
    document.body.classList.remove('modal-open');
}
function showAssignToUnitForm(tenantId, tenantName) {
    document.getElementById('assignToUnitFormContainer').style.display = 'block';
    document.getElementById('selectedTenantName').textContent = tenantName;
    document.getElementById('assignTenantId').value = tenantId;
}
function hideAssignToUnitForm() {
    document.getElementById('assignToUnitFormContainer').style.display = 'none';
    document.getElementById('selectedTenantName').textContent = '';
    document.getElementById('assignTenantId').value = '';
}
function openEditTenantModal(id, name, email, unit, property, leaseStart, leaseEnd, rent, notes) {
    document.getElementById('editTenantModal').style.display = 'flex';
    document.body.classList.add('modal-open');
    document.getElementById('editTenantName').value = name;
    document.getElementById('editTenantEmail').value = email;
    document.getElementById('editTenantUnit').value = unit;
    document.getElementById('editTenantProperty').value = property;
    document.getElementById('editLeaseStart').value = leaseStart.substring(0, 10);
    document.getElementById('editLeaseEnd').value = leaseEnd.substring(0, 10);
    document.getElementById('editRentAmount').value = rent;
    document.getElementById('editNotes').value = notes;
    // Set form action
    document.getElementById('editTenantForm').action = '/landlord/tenant-assignments/' + id + '/edit';
}
function closeEditTenantModal() {
    document.getElementById('editTenantModal').style.display = 'none';
    document.body.classList.remove('modal-open');
}
</script>
</body>
</html> 
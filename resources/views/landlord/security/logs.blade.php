@extends('layouts.landlord-app')

@section('title', 'Security Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Security Logs</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('landlord.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('landlord.security.dashboard') }}">Security</a></li>
                        <li class="breadcrumb-item active">Logs</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Filter Logs</h4>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="access_result" class="form-label">Access Result</label>
                            <select class="form-select" id="access_result" name="access_result">
                                <option value="">All Results</option>
                                <option value="granted" {{ request('access_result') == 'granted' ? 'selected' : '' }}>Granted</option>
                                <option value="denied" {{ request('access_result') == 'denied' ? 'selected' : '' }}>Denied</option>
                                <option value="unknown_card" {{ request('access_result') == 'unknown_card' ? 'selected' : '' }}>Unknown Card</option>
                                <option value="inactive_card" {{ request('access_result') == 'inactive_card' ? 'selected' : '' }}>Inactive Card</option>
                                <option value="time_restricted" {{ request('access_result') == 'time_restricted' ? 'selected' : '' }}>Time Restricted</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="apartment_id" class="form-label">Apartment</label>
                            <select class="form-select" id="apartment_id" name="apartment_id">
                                <option value="">All Apartments</option>
                                @foreach($apartments as $apartment)
                                    <option value="{{ $apartment->id }}" {{ request('apartment_id') == $apartment->id ? 'selected' : '' }}>
                                        {{ $apartment->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="device_id" class="form-label">Device</label>
                            <select class="form-select" id="device_id" name="device_id">
                                <option value="">All Devices</option>
                                @foreach($devices as $device)
                                    <option value="{{ $device }}" {{ request('device_id') == $device ? 'selected' : '' }}>
                                        {{ $device }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-filter"></i> Apply Filters
                            </button>
                            <a href="{{ route('landlord.security.logs') }}" class="btn btn-outline-secondary">
                                <i class="mdi mdi-filter-off"></i> Clear Filters
                            </a>
                            <button type="button" class="btn btn-outline-info" onclick="exportLogs()">
                                <i class="mdi mdi-download"></i> Export CSV
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="refreshLogs()">
                                <i class="mdi mdi-refresh"></i> Refresh
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="header-title">Security Events</h4>
                            <p class="text-muted mb-0">{{ $logs->total() }} total events found</p>
                        </div>
                        <div class="col-auto">
                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="markAllAsRead()">Mark All as Read</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="deleteOldLogs()">Delete Old Logs</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($logs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>Card/Tenant</th>
                                        <th>Location</th>
                                        <th>Action</th>
                                        <th>Result</th>
                                        <th>Device</th>
                                        <th>Unit</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($logs as $log)
                                        <tr class="
                                            @if($log->access_result == 'denied' || $log->access_result == 'unknown_card') table-danger
                                            @elseif($log->access_result == 'granted') table-success
                                            @elseif($log->access_result == 'time_restricted') table-warning
                                            @endif
                                        ">
                                            <td>
                                                <div>
                                                    <strong>{{ $log->scanned_at->format('H:i:s') }}</strong>
                                                    <br><small class="text-muted">{{ $log->scanned_at->format('M d, Y') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($log->tenant)
                                                    <div>
                                                        <strong>{{ $log->tenant->name }}</strong>
                                                        <br><small class="text-muted">{{ $log->card_uid }}</small>
                                                    </div>
                                                @else
                                                    <div>
                                                        <span class="text-muted">Unknown Tenant</span>
                                                        <br><small class="text-muted">{{ $log->card_uid ?: 'No UID' }}</small>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $log->scanner_location ?: $log->location ?: 'Unknown' }}</strong>
                                                    @if($log->apartment)
                                                        <br><small class="text-muted">{{ $log->apartment->name }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $log->action_type_badge_class }}">
                                                    {{ ucfirst(str_replace('_', ' ', $log->action_type)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $log->access_result_badge_class }}">
                                                    {{ ucfirst(str_replace('_', ' ', $log->access_result)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $log->device_id ?: '-' }}</small>
                                            </td>
                                            <td>
                                                @if($log->unit)
                                                    <strong>{{ $log->unit->unit_number }}</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->notes || $log->additional_data)
                                                    <button class="btn btn-sm btn-outline-info" type="button" 
                                                            data-bs-toggle="modal" data-bs-target="#logDetailsModal{{ $log->id }}">
                                                        <i class="mdi mdi-information"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} results
                            </div>
                            <div>
                                {{ $logs->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="mdi mdi-file-document-multiple-outline display-4 text-muted"></i>
                            <h4 class="mt-3 text-muted">No Security Logs</h4>
                            <p class="text-muted">No security events found for the selected criteria.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Log Details Modals -->
@foreach($logs as $log)
    @if($log->notes || $log->additional_data)
        <div class="modal fade" id="logDetailsModal{{ $log->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Security Log Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Basic Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <th>Timestamp:</th>
                                        <td>{{ $log->scanned_at->format('M d, Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Card UID:</th>
                                        <td>{{ $log->card_uid ?: 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tenant:</th>
                                        <td>{{ $log->tenant ? $log->tenant->name : 'Unknown' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Location:</th>
                                        <td>{{ $log->scanner_location ?: $log->location ?: 'Unknown' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Device:</th>
                                        <td>{{ $log->device_id ?: 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Access Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <th>Action:</th>
                                        <td>
                                            <span class="badge bg-{{ $log->action_type_badge_class }}">
                                                {{ ucfirst(str_replace('_', ' ', $log->action_type)) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Result:</th>
                                        <td>
                                            <span class="badge bg-{{ $log->access_result_badge_class }}">
                                                {{ ucfirst(str_replace('_', ' ', $log->access_result)) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Valid Scan:</th>
                                        <td>
                                            <span class="badge bg-{{ $log->is_valid_scan ? 'success' : 'danger' }}">
                                                {{ $log->is_valid_scan ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($log->notes)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Notes</h6>
                                    <div class="alert alert-info">{{ $log->notes }}</div>
                                </div>
                            </div>
                        @endif

                        @if($log->additional_data)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Additional Data</h6>
                                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($log->additional_data, JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection

@section('scripts')
<script>
function refreshLogs() {
    location.reload();
}

function exportLogs() {
    // Create a form with current filters and submit for CSV export
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '{{ route("landlord.security.logs") }}';
    
    // Add current filter parameters
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    
    params.forEach((value, key) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function markAllAsRead() {
    // Implementation for marking logs as read
    alert('Feature coming soon: Mark all logs as read');
}

function deleteOldLogs() {
    if (confirm('Are you sure you want to delete logs older than 90 days? This action cannot be undone.')) {
        // Implementation for deleting old logs
        alert('Feature coming soon: Delete old logs');
    }
}

// Auto-refresh every 30 seconds
setInterval(function() {
    if (!document.hidden) {
        location.reload();
    }
}, 30000);

// Set default dates if not set
document.addEventListener('DOMContentLoaded', function() {
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    
    if (!dateFrom.value) {
        // Default to last 7 days
        const sevenDaysAgo = new Date();
        sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);
        dateFrom.value = sevenDaysAgo.toISOString().split('T')[0];
    }
    
    if (!dateTo.value) {
        // Default to today
        const today = new Date();
        dateTo.value = today.toISOString().split('T')[0];
    }
});
</script>
@endsection

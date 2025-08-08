@extends('layouts.landlord-app')

@section('title', 'Security Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Security Dashboard</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('landlord.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Security</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate" title="Total Cards">Total RFID Cards</h5>
                            <h3 class="my-2 py-1">{{ $stats['total_cards'] }}</h3>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div id="total-cards-chart" data-colors="#727cf5"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate" title="Active Cards">Active Cards</h5>
                            <h3 class="my-2 py-1">{{ $stats['active_cards'] }}</h3>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div id="active-cards-chart" data-colors="#0acf97"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate" title="Access Granted Today">Access Granted (24h)</h5>
                            <h3 class="my-2 py-1">{{ $stats['access_granted_today'] }}</h3>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div id="granted-chart" data-colors="#0acf97"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate" title="Access Denied Today">Access Denied (24h)</h5>
                            <h3 class="my-2 py-1">{{ $stats['access_denied_today'] }}</h3>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div id="denied-chart" data-colors="#fa5c7c"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- RFID Cards Quick Actions -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Quick Actions</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('landlord.security.create-card') }}" class="btn btn-primary">
                            <i class="mdi mdi-card-plus"></i> Add New RFID Card
                        </a>
                        <a href="{{ route('landlord.security.cards') }}" class="btn btn-outline-primary">
                            <i class="mdi mdi-card-multiple"></i> Manage Cards
                        </a>
                        <a href="{{ route('landlord.security.logs') }}" class="btn btn-outline-secondary">
                            <i class="mdi mdi-file-document-multiple"></i> View Security Logs
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alert Statistics -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Security Alerts (24h)</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h3 class="text-warning">{{ $stats['unknown_cards_today'] }}</h3>
                                <p class="text-muted mb-0">Unknown Cards</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h3 class="text-danger">{{ $stats['access_denied_today'] }}</h3>
                                <p class="text-muted mb-0">Access Denied</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Recent Activity (Last 24 Hours)</h4>
                </div>
                <div class="card-body">
                    @if($recentLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Time</th>
                                        <th>Card/Tenant</th>
                                        <th>Location</th>
                                        <th>Result</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentLogs->take(15) as $log)
                                        <tr>
                                            <td class="text-muted">
                                                {{ $log->scanned_at->format('H:i') }}
                                                <br><small>{{ $log->scanned_at->format('M d') }}</small>
                                            </td>
                                            <td>
                                                @if($log->tenant)
                                                    <strong>{{ $log->tenant->name }}</strong>
                                                    <br><small class="text-muted">{{ $log->card_uid }}</small>
                                                @else
                                                    <span class="text-muted">{{ $log->card_uid ?: 'Unknown' }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $log->scanner_location ?: $log->location ?: 'Unknown' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $log->access_result_badge_class }}">
                                                    {{ ucfirst(str_replace('_', ' ', $log->access_result)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($log->unit)
                                                    {{ $log->unit->unit_number }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('landlord.security.logs') }}" class="btn btn-link">
                                View All Logs <i class="mdi mdi-arrow-right"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-shield-check-outline h1 text-muted"></i>
                            <h5 class="text-muted mt-2">No recent activity</h5>
                            <p class="text-muted">Security events will appear here when RFID cards are scanned.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- RFID Cards Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="header-title">RFID Cards Overview</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('landlord.security.cards') }}" class="btn btn-sm btn-outline-primary">
                                View All Cards
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($rfidCards->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Card UID</th>
                                        <th>Tenant</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th>Last Used</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rfidCards->take(10) as $card)
                                        <tr>
                                            <td>
                                                <strong>{{ $card->card_uid }}</strong>
                                                @if($card->card_number)
                                                    <br><small class="text-muted">{{ $card->card_number }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($card->tenant)
                                                    {{ $card->tenant->name }}
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($card->unit)
                                                    {{ $card->unit->unit_number }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $card->status_badge_class }}">
                                                    {{ ucfirst($card->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($card->last_used_at)
                                                    {{ $card->last_used_at->diffForHumans() }}
                                                @else
                                                    <span class="text-muted">Never</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('landlord.security.card-details', $card) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="mdi mdi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-card-multiple-outline h1 text-muted"></i>
                            <h5 class="text-muted mt-2">No RFID Cards</h5>
                            <p class="text-muted">Create your first RFID card to start tracking tenant access.</p>
                            <a href="{{ route('landlord.security.create-card') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Add First Card
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh the page every 30 seconds to show real-time updates
    setInterval(function() {
        // Only refresh if the page is visible
        if (!document.hidden) {
            location.reload();
        }
    }, 30000);
});
</script>
@endsection

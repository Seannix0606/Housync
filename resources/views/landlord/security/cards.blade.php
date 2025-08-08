@extends('layouts.landlord-app')

@section('title', 'RFID Cards Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('landlord.security.create-card') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus"></i> Add New Card
                    </a>
                </div>
                <h4 class="page-title">RFID Cards Management</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('landlord.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('landlord.security.dashboard') }}">Security</a></li>
                        <li class="breadcrumb-item active">Cards</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="header-title">RFID Cards</h4>
                        </div>
                        <div class="col-auto">
                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="mdi mdi-filter"></i> Filter
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="?status=all">All Cards</a></li>
                                    <li><a class="dropdown-item" href="?status=active">Active Cards</a></li>
                                    <li><a class="dropdown-item" href="?status=inactive">Inactive Cards</a></li>
                                    <li><a class="dropdown-item" href="?status=suspended">Suspended Cards</a></li>
                                    <li><a class="dropdown-item" href="?status=lost">Lost Cards</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($rfidCards->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Card Details</th>
                                        <th>Tenant</th>
                                        <th>Unit/Apartment</th>
                                        <th>Status</th>
                                        <th>Access Permissions</th>
                                        <th>Last Used</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rfidCards as $card)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $card->card_uid }}</strong>
                                                    @if($card->card_number)
                                                        <br><small class="text-muted">Card #{{ $card->card_number }}</small>
                                                    @endif
                                                    <br><small class="text-muted">Created: {{ $card->created_at->format('M d, Y') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($card->tenant)
                                                    <div>
                                                        <strong>{{ $card->tenant->name }}</strong>
                                                        <br><small class="text-muted">{{ $card->tenant->email }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    @if($card->unit)
                                                        <strong>Unit {{ $card->unit->unit_number }}</strong><br>
                                                    @endif
                                                    @if($card->apartment)
                                                        <small class="text-muted">{{ $card->apartment->name }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $card->status_badge_class }} fs-6">
                                                    {{ ucfirst($card->status) }}
                                                </span>
                                                @if($card->assigned_at)
                                                    <br><small class="text-muted">Since: {{ $card->assigned_at->format('M d') }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    @if($card->access_building)
                                                        <span class="badge bg-success-subtle text-success mb-1">Building</span>
                                                    @endif
                                                    @if($card->access_common_areas)
                                                        <span class="badge bg-info-subtle text-info mb-1">Common Areas</span>
                                                    @endif
                                                    @if($card->access_parking)
                                                        <span class="badge bg-warning-subtle text-warning mb-1">Parking</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($card->last_used_at)
                                                    <div>
                                                        {{ $card->last_used_at->diffForHumans() }}
                                                        <br><small class="text-muted">{{ $card->last_used_at->format('M d, H:i') }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Never used</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" 
                                                            data-bs-toggle="dropdown">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('landlord.security.card-details', $card) }}">
                                                                <i class="mdi mdi-eye"></i> View Details
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        @if($card->isActive())
                                                            <li>
                                                                <button class="dropdown-item text-warning" 
                                                                        onclick="updateCardStatus({{ $card->id }}, 'suspended')">
                                                                    <i class="mdi mdi-pause"></i> Suspend Card
                                                                </button>
                                                            </li>
                                                            <li>
                                                                <button class="dropdown-item text-secondary" 
                                                                        onclick="updateCardStatus({{ $card->id }}, 'inactive')">
                                                                    <i class="mdi mdi-stop"></i> Deactivate Card
                                                                </button>
                                                            </li>
                                                        @else
                                                            <li>
                                                                <button class="dropdown-item text-success" 
                                                                        onclick="updateCardStatus({{ $card->id }}, 'active')">
                                                                    <i class="mdi mdi-play"></i> Activate Card
                                                                </button>
                                                            </li>
                                                        @endif
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <button class="dropdown-item text-danger" 
                                                                    onclick="updateCardStatus({{ $card->id }}, 'lost')">
                                                                <i class="mdi mdi-alert"></i> Mark as Lost
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="mdi mdi-card-multiple-outline display-4 text-muted"></i>
                            <h4 class="mt-3 text-muted">No RFID Cards</h4>
                            <p class="text-muted">You haven't created any RFID cards yet. Create your first card to start managing tenant access.</p>
                            <a href="{{ route('landlord.security.create-card') }}" class="btn btn-primary mt-3">
                                <i class="mdi mdi-plus"></i> Create First Card
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Card Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusUpdateForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>Are you sure you want to <span id="statusAction"></span> this RFID card?</p>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Add any notes about this status change..."></textarea>
                    </div>
                    <input type="hidden" id="statusInput" name="status">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function updateCardStatus(cardId, status) {
    const modal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
    const form = document.getElementById('statusUpdateForm');
    const statusInput = document.getElementById('statusInput');
    const statusAction = document.getElementById('statusAction');
    
    // Set form action
    form.action = `/landlord/security/cards/${cardId}/status`;
    
    // Set status
    statusInput.value = status;
    
    // Set action text
    const actions = {
        'active': 'activate',
        'inactive': 'deactivate', 
        'suspended': 'suspend',
        'lost': 'mark as lost'
    };
    statusAction.textContent = actions[status] || status;
    
    // Show modal
    modal.show();
}

// Auto-refresh every 60 seconds
setInterval(function() {
    if (!document.hidden) {
        location.reload();
    }
}, 60000);
</script>
@endsection

@extends('layouts.landlord-app')

@section('title', 'Add New RFID Card')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Add New RFID Card</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('landlord.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('landlord.security.dashboard') }}">Security</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('landlord.security.cards') }}">Cards</a></li>
                        <li class="breadcrumb-item active">Add New</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">RFID Card Information</h4>
                    <p class="text-muted mb-0">Create a new RFID card for tenant access control</p>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('landlord.security.store-card') }}" method="POST">
                        @csrf
                        
                        <!-- Card Information Section -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3">Card Information</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="card_uid" class="form-label">Card UID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="card_uid" name="card_uid" 
                                           value="{{ old('card_uid') }}" required 
                                           placeholder="Scan card or enter UID manually">
                                    <div class="form-text">
                                        This is the unique identifier from the RFID card. 
                                        <button type="button" class="btn btn-link p-0" onclick="startScanning()">
                                            Click here to scan card
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="card_number" class="form-label">Card Number (Optional)</label>
                                    <input type="text" class="form-control" id="card_number" name="card_number" 
                                           value="{{ old('card_number') }}" 
                                           placeholder="Human-readable card number">
                                    <div class="form-text">Optional: A readable number for easy identification</div>
                                </div>
                            </div>
                        </div>

                        <!-- Tenant Assignment Section -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 mt-4">Tenant Assignment</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tenant_id" class="form-label">Tenant <span class="text-danger">*</span></label>
                                    <select class="form-select" id="tenant_id" name="tenant_id" required>
                                        <option value="">Select a tenant</option>
                                        @foreach($availableTenants as $tenant)
                                            <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                                {{ $tenant->name }} ({{ $tenant->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Only tenants with active assignments are shown</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="apartment_id" class="form-label">Apartment <span class="text-danger">*</span></label>
                                    <select class="form-select" id="apartment_id" name="apartment_id" required>
                                        <option value="">Select an apartment</option>
                                        @foreach($apartments as $apartment)
                                            <option value="{{ $apartment->id }}" {{ old('apartment_id') == $apartment->id ? 'selected' : '' }}>
                                                {{ $apartment->name }} - {{ $apartment->address }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="unit_id" class="form-label">Unit (Optional)</label>
                                    <select class="form-select" id="unit_id" name="unit_id">
                                        <option value="">Select a unit</option>
                                        <!-- Units will be loaded via JavaScript based on apartment selection -->
                                    </select>
                                    <div class="form-text">Select a specific unit or leave blank for apartment-wide access</div>
                                </div>
                            </div>
                        </div>

                        <!-- Access Permissions Section -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 mt-4">Access Permissions</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="access_building" 
                                               name="access_building" value="1" 
                                               {{ old('access_building', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="access_building">
                                            <strong>Building Access</strong>
                                            <br><small class="text-muted">Main entrance and building areas</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="access_common_areas" 
                                               name="access_common_areas" value="1" 
                                               {{ old('access_common_areas', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="access_common_areas">
                                            <strong>Common Areas</strong>
                                            <br><small class="text-muted">Lobby, gym, laundry, etc.</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="access_parking" 
                                               name="access_parking" value="1" 
                                               {{ old('access_parking') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="access_parking">
                                            <strong>Parking Access</strong>
                                            <br><small class="text-muted">Garage and parking areas</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes (Optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" 
                                              placeholder="Any additional notes about this card...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('landlord.security.cards') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left"></i> Back to Cards
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save"></i> Create RFID Card
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Card Scanning Modal -->
<div class="modal fade" id="scanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan RFID Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="scanStatus">
                    <i class="mdi mdi-card-search-outline display-1 text-primary"></i>
                    <h5 class="mt-3">Place card near scanner</h5>
                    <p class="text-muted">Make sure your ESP32 RFID scanner is connected and active.</p>
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Scanning...</span>
                    </div>
                </div>
                <div id="scanResult" style="display: none;">
                    <i class="mdi mdi-check-circle display-1 text-success"></i>
                    <h5 class="mt-3 text-success">Card Detected!</h5>
                    <p class="text-muted">Card UID: <span id="detectedUID"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="useDetectedCard" style="display: none;">Use This Card</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Load units when apartment is selected
document.getElementById('apartment_id').addEventListener('change', function() {
    const apartmentId = this.value;
    const unitSelect = document.getElementById('unit_id');
    
    // Clear existing options
    unitSelect.innerHTML = '<option value="">Select a unit</option>';
    
    if (apartmentId) {
        fetch(`/landlord/apartments/${apartmentId}/units`)
            .then(response => response.json())
            .then(data => {
                data.units.forEach(unit => {
                    const option = document.createElement('option');
                    option.value = unit.id;
                    option.textContent = `Unit ${unit.unit_number} - ${unit.unit_type}`;
                    unitSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading units:', error));
    }
});

// RFID Card Scanning
let scanningInterval;

function startScanning() {
    const modal = new bootstrap.Modal(document.getElementById('scanModal'));
    modal.show();
    
    // Reset modal state
    document.getElementById('scanStatus').style.display = 'block';
    document.getElementById('scanResult').style.display = 'none';
    document.getElementById('useDetectedCard').style.display = 'none';
    
    // Start polling for RFID scans
    scanningInterval = setInterval(pollForCard, 1000);
}

function pollForCard() {
    fetch('/api/esp32/device-status?device_id=scanner_01')
        .then(response => response.json())
        .then(data => {
            // This is a simplified example. In reality, you'd need to implement
            // a proper WebSocket or Server-Sent Events connection to get real-time data
            // For now, this just checks device status
            console.log('Scanner status:', data);
        })
        .catch(error => {
            console.error('Scanner communication error:', error);
        });
}

function cardDetected(cardUID) {
    clearInterval(scanningInterval);
    
    document.getElementById('scanStatus').style.display = 'none';
    document.getElementById('scanResult').style.display = 'block';
    document.getElementById('detectedUID').textContent = cardUID;
    document.getElementById('useDetectedCard').style.display = 'inline-block';
    
    document.getElementById('useDetectedCard').onclick = function() {
        document.getElementById('card_uid').value = cardUID;
        bootstrap.Modal.getInstance(document.getElementById('scanModal')).hide();
    };
}

// Close modal cleanup
document.getElementById('scanModal').addEventListener('hidden.bs.modal', function() {
    if (scanningInterval) {
        clearInterval(scanningInterval);
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const cardUID = document.getElementById('card_uid').value.trim();
    const tenantId = document.getElementById('tenant_id').value;
    const apartmentId = document.getElementById('apartment_id').value;
    
    if (!cardUID) {
        e.preventDefault();
        alert('Please enter a card UID or scan a card.');
        return;
    }
    
    if (!tenantId) {
        e.preventDefault();
        alert('Please select a tenant.');
        return;
    }
    
    if (!apartmentId) {
        e.preventDefault();
        alert('Please select an apartment.');
        return;
    }
});
</script>
@endsection

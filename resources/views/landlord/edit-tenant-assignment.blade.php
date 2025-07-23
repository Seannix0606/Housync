@extends('layouts.app')

@section('title', 'Edit Tenant Assignment')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">Edit Tenant Assignment</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('landlord.edit-tenant-assignment', $assignment->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Tenant Name</label>
                            <input type="text" class="form-control" value="{{ $assignment->tenant->name }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unit</label>
                            <input type="text" class="form-control" value="{{ $assignment->unit->unit_number }} - {{ $assignment->unit->apartment->name }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lease Start Date</label>
                            <input type="date" name="lease_start_date" class="form-control" value="{{ $assignment->lease_start_date->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lease End Date</label>
                            <input type="date" name="lease_end_date" class="form-control" value="{{ $assignment->lease_end_date->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Monthly Rent</label>
                            <input type="number" name="rent_amount" class="form-control" value="{{ $assignment->rent_amount }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control">{{ $assignment->notes }}</textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('landlord.tenants') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
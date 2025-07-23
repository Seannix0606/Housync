@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow" style="max-width: 400px; width: 100%; border-radius: 1rem;">
        <div class="card-body">
            <h3 class="card-title mb-3 text-center">Change Your Password</h3>
            <p class="text-muted text-center mb-4">For your security, please set a new password before using the portal.</p>
            <form method="POST" action="{{ route('tenant.update-password') }}">
                @csrf
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required minlength="8">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required minlength="8">
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-100">Change Password</button>
            </form>
        </div>
    </div>
</div>
@endsection 
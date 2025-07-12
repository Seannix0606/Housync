<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;

Route::get('/', function () {
    return view('index');
})->name('home');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/tenant-dashboard', function () {
    return view('tenant-dashboard');
})->name('tenant.dashboard');

Route::get('/tenant-payments', function () {
    return view('tenant-payments');
})->name('tenant.payments');

Route::get('/tenant-maintenance', function () {
    return view('tenant-maintenance');
})->name('tenant.maintenance');

Route::get('/tenant-messages', function () {
    return view('tenant-messages');
})->name('tenant.messages');

Route::get('/tenant-lease', function () {
    return view('tenant-lease');
})->name('tenant.lease');

Route::get('/tenant-profile', function () {
    return view('tenant-profile');
})->name('tenant.profile');

// Units routes
Route::get('/units', [UnitController::class, 'index'])->name('units');
Route::post('/units', [UnitController::class, 'store'])->name('units.store');
Route::get('/units/filter', [UnitController::class, 'filter'])->name('units.filter');
Route::get('/units/stats', [UnitController::class, 'getStats'])->name('units.stats');
Route::get('/units/types', [UnitController::class, 'getUnitTypes'])->name('units.types');

Route::get('/tenants', function () {
    return view('tenants');
})->name('tenants');

Route::get('/billing', function () {
    return view('billing');
})->name('billing');

Route::get('/messages', function () {
    return view('messages');
})->name('messages');

Route::get('/security', function () {
    return view('security');
})->name('security');

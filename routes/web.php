<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/units', function () {
    return view('units');
})->name('units');

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

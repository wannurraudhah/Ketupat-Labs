<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Single-page route for the React dashboard — the SPA will mount and show the dashboard when authenticated
Route::get('/dashboard', function () {
    return view('welcome');
})->name('dashboard');


<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (agent.md Section 8)
|--------------------------------------------------------------------------
*/

Route::livewire('/', 'pages::home')->name('home');
Route::livewire('/trips', 'pages::trips.index')->name('trips');
Route::livewire('/trips/{trip}', 'pages::trips.show')->name('trip.show');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function (): void {
    Route::livewire('/login', 'pages::auth.login')->name('login');
    Route::livewire('/register', 'pages::auth.register')->name('register');
});

Route::middleware('auth')->group(function (): void {
    Route::livewire('/verify-email', 'pages::auth.verify-email')->name('verify-email');
    // Fortify/Laravel email-verification confirmation endpoint
    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('user.dashboard');
    })->middleware('signed')->name('verification.verify');
});

Route::post('/logout', function () {
    auth()->guard('web')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| User Routes (/user/*, requires auth + verified)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function (): void {
    Route::livewire('/dashboard', 'pages::user.dashboard')->name('dashboard');
    Route::livewire('/profile', 'pages::user.profile')->name('profile');
    Route::livewire('/trips', 'pages::user.trips')->name('trips');
    Route::livewire('/trips/{trip}/book', 'pages::user.trip-booking')->name('trips.book');
    Route::livewire('/trips/{trip}', 'pages::user.trip-details')->name('trip.details');
});

/*
|--------------------------------------------------------------------------
| Specialist Routes (/specialist/*, requires auth + verified + role:specialist)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:specialist'])->prefix('specialist')->name('specialist.')->group(function (): void {
    Route::livewire('/dashboard', 'pages::specialist.dashboard')->name('dashboard');
    Route::livewire('/trips/{trip}', 'pages::specialist.trip-roster')->name('trip.roster');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (/admin/*, requires auth + verified + role:admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {

    Route::livewire('/', 'pages::admin.dashboard')->name('dashboard');

    // Users
    Route::livewire('/users', 'pages::admin.users')->name('users');
    Route::livewire('/users/{user}', 'pages::admin.user-show')->name('user.show');

    // Specialists
    Route::livewire('/specialists', 'pages::admin.specialists')->name('specialists');
    Route::livewire('/specialists/{specialist}', 'pages::admin.specialist-show')->name('specialist.show');

    // Challenges (dynamic, admin-managed)
    Route::livewire('/challenges', 'pages::admin.challenges')->name('challenges');

    // Trip series
    Route::livewire('/trip-series', 'pages::admin.trip-series')->name('trip-series');

    // Trips — order matters: create before {trip} wildcard
    Route::livewire('/trips', 'pages::admin.trips')->name('trips');
    Route::livewire('/trips/create', 'pages::admin.trip-editor')->name('trips.create');
    Route::livewire('/trips/{trip}/edit', 'pages::admin.trip-editor')->name('trips.edit');
    Route::livewire('/trips/{trip}/specialists', 'pages::admin.trip-specialists')->name('trip.specialists');
    Route::livewire('/trips/{trip}/refunds', 'pages::admin.refunds')->name('trip.refunds');
    Route::livewire('/trips/{trip}', 'pages::admin.trip-show')->name('trip.show');

    // Analytics
    Route::livewire('/analytics', 'pages::admin.analytics')->name('analytics');
});

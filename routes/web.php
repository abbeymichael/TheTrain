<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (agent.md Section 8)
|--------------------------------------------------------------------------
|
| Unauthenticated, Livewire 4 single-file page components registered via
| Route::livewire(uri, 'namespace::view'). Views live under
| resources/views/pages/ (the "pages::" namespace) as ⚡-prefixed .blade.php
| files — no separate PHP class file needed.
|
*/

Route::livewire('/', 'pages::home')->name('home');
Route::livewire('/trips', 'pages::trips.index')->name('trips');
Route::livewire('/trips/{trip}', 'pages::trips.show')->name('trip.show');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| Fortify scaffolding is not installed yet (agent.md Section 1). These
| placeholders exist so route names referenced in the layouts resolve;
| once Fortify is installed, swap these for its registered routes/views
| (or your own pages::auth.login / pages::auth.register / pages::auth.verify
| Livewire 4 components per agent.md Section 8).
|
*/
Route::get('/login', fn () => redirect('/'))->name('login')->middleware('guest');
Route::get('/register', fn () => redirect('/'))->name('register')->middleware('guest');
Route::post('/logout', function () {
    auth()->guard('web')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| User Routes (agent.md Section 8 — /user/*, requires auth + verified)
|--------------------------------------------------------------------------
|
| Booking gate itself (registered + verified email + status=approved +
| Stripe payment) is enforced in pages::user.trips.book, not here — these
| middlewares only gate access to the authenticated area (agent.md
| Section 2). Not yet built (agent.md Section 10) — placeholder views keep
| route names resolvable for nav links.
|
*/
Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function (): void {
    Route::get('/dashboard', fn () => view('welcome'))->name('dashboard');
    Route::get('/profile', fn () => view('welcome'))->name('profile');
    Route::get('/trips', fn () => view('welcome'))->name('trips');
    Route::get('/trips/{trip}/book', fn () => view('welcome'))->name('trips.book');
    Route::get('/trips/{trip}', fn () => view('welcome'))->name('trip.details');
});

/*
|--------------------------------------------------------------------------
| Specialist Routes (agent.md Section 8 — /specialist/*)
|--------------------------------------------------------------------------
|
| Hard requirement (agent.md Section 11): role:specialist middleware on
| every route in this group. A specialist may only ever see rosters for
| trips they are explicitly assigned to via trip_specialists — that scoping
| belongs inside pages::specialist.trip-roster itself, not the router.
|
*/
Route::middleware(['auth', 'verified', 'role:specialist'])->prefix('specialist')->name('specialist.')->group(function (): void {
    Route::get('/dashboard', fn () => view('welcome'))->name('dashboard');
    Route::get('/trips/{trip}', fn () => view('welcome'))->name('trip.roster');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (agent.md Section 8 & 11 — /admin/*)
|--------------------------------------------------------------------------
|
| Hard requirement (agent.md Section 11): role:admin middleware on every
| route in this group, no exceptions. Built components are wired directly
| via Route::livewire(); components not yet built (agent.md Section 10
| "To Build") fall back to a placeholder view so route names still resolve
| for the admin layout's nav links.
|
*/
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', fn () => view('welcome'))->name('dashboard');

    // Users
    Route::get('/users', fn () => view('welcome'))->name('users');
    Route::get('/users/{user}', fn () => view('welcome'))->name('user.show');

    // Specialists (onboarding / credential review, agent.md Section 5 & 10)
    Route::get('/specialists', fn () => view('welcome'))->name('specialists');
    Route::get('/specialists/{specialist}', fn () => view('welcome'))->name('specialist.show');

    // Challenges (dynamic, admin-managed list — agent.md Section 4 & 10)
    Route::livewire('/challenges', 'pages::admin.challenges')->name('challenges');

    // Trip series & trips (agent.md Section 3 & 10)
    Route::get('/trip-series', fn () => view('welcome'))->name('trip-series');
    Route::get('/trips', fn () => view('welcome'))->name('trips');
    Route::get('/trips/create', fn () => view('welcome'))->name('trips.create');
    Route::get('/trips/{trip}/edit', fn () => view('welcome'))->name('trips.edit');
    Route::get('/trips/{trip}', fn () => view('welcome'))->name('trip.show');
    Route::get('/trips/{trip}/specialists', fn () => view('welcome'))->name('trip.specialists');
    Route::get('/trips/{trip}/refunds', fn () => view('welcome'))->name('trip.refunds');

    // Analytics (agent.md Section 10)
    Route::get('/analytics', fn () => view('welcome'))->name('analytics');
});

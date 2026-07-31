<?php

use App\Livewire\Admin\ChallengesManager;
use App\Livewire\Public\HomePage;
use App\Livewire\Public\TripShow;
use App\Livewire\Public\TripsList;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (agent.md Section 8)
|--------------------------------------------------------------------------
*/

Route::get('/', HomePage::class)->name('home');
Route::get('/trips', TripsList::class)->name('trips');
Route::get('/trips/{trip}', TripShow::class)->name('trip.show');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| Fortify scaffolding is not installed yet; these placeholders reference
| route names used in the public layout. Once Fortify is installed they
| can be replaced with the real auth views or removed if Fortify registers
| them automatically.
|
*/
Route::get('/login', fn () => redirect('/'))->name('login')->middleware('guest');
Route::get('/register', fn () => redirect('/'))->name('register')->middleware('guest');

/*
|--------------------------------------------------------------------------
| User Routes (requires auth + verified email)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function (): void {
    Route::get('/dashboard', fn () => view('welcome'))->name('dashboard');
    Route::get('/trips', fn () => view('welcome'))->name('trips');
    Route::get('/trips/{trip}/book', fn () => view('welcome'))->name('trips.book');
    Route::get('/trips/{trip}', fn () => view('welcome'))->name('trip.details');
});

/*
|--------------------------------------------------------------------------
| Specialist Routes (requires auth + verified email + role:specialist)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:specialist'])->prefix('specialist')->name('specialist.')->group(function (): void {
    Route::get('/dashboard', fn () => view('welcome'))->name('dashboard');
    Route::get('/trips/{trip}', fn () => view('welcome'))->name('trip.roster');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (requires auth + verified email + role:admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', fn () => view('welcome'))->name('dashboard');
    Route::get('/users', fn () => view('welcome'))->name('users');
    Route::get('/users/{user}', fn () => view('welcome'))->name('user.show');
    Route::get('/specialists', fn () => view('welcome'))->name('specialists');
    Route::get('/specialists/{specialist}', fn () => view('welcome'))->name('specialist.show');
    Route::get('/challenges', ChallengesManager::class)->name('challenges');
    Route::get('/trip-series', fn () => view('welcome'))->name('trip-series');
    Route::get('/trips', fn () => view('welcome'))->name('trips');
    Route::get('/trips/create', fn () => view('welcome'))->name('trips.create');
    Route::get('/trips/{trip}/edit', fn () => view('welcome'))->name('trips.edit');
    Route::get('/trips/{trip}', fn () => view('welcome'))->name('trip.show');
    Route::get('/trips/{trip}/specialists', fn () => view('welcome'))->name('trip.specialists');
    Route::get('/trips/{trip}/refunds', fn () => view('welcome'))->name('trip.refunds');
    Route::get('/analytics', fn () => view('welcome'))->name('analytics');
});

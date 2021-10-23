<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController;
use Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\ProfileInformationController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Jetstream\Http\Controllers\Livewire\UserProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\JobOfferController::class, 'index'])
    ->name('root');

require __DIR__ . '/auth.php';

Route::resource('job_offers', App\Http\Controllers\JobOfferController::class)
    ->only(['create', 'store', 'edit', 'update', 'destroy'])
    ->middleware('auth:companies');

Route::resource('job_offers', App\Http\Controllers\JobOfferController::class)
    ->only(['show', 'index'])
    ->middleware('auth:companies,users');

Route::patch('/job_offers/{job_offer}/entries/{entry}/approval', [App\Http\Controllers\EntryController::class, 'approval'])
    ->name('job_offers.entries.approval')
    ->middleware(['auth:companies']);

Route::patch('/job_offers/{job_offer}/entries/{entry}/reject', [App\Http\Controllers\EntryController::class, 'reject'])
    ->name('job_offers.entries.reject')
    ->middleware(['auth:companies']);

Route::resource('job_offers.entries', App\Http\Controllers\EntryController::class)
    ->only(['store', 'destroy'])
    ->middleware(['auth:users']);

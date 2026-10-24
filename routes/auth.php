<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use Illuminate\Support\Facades\Route;

// No public sign-up or self-service password reset:
// managers create users and reset passwords in Settings > Users & Roles.

Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['guest', 'throttle:10,1'])
    ->name('login.post');

// Lock screen unlock
Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
    ->middleware('auth')
    ->name('password.confirm');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

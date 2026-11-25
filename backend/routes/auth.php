<?php

use App\Http\Controllers\Auth\MeController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware(['guest:web', 'throttle::login']);
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('me', MeController::class)->middleware('auth:sanctum');

<?php

use App\Http\Controllers\PathController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PathLocksController;

Route::post('/paths', [PathController::class, 'getPaths']);

Route::post('/paths/lock-path', [PathLocksController::class, 'lockRoute']);
Route::post('/paths/unlock-path', [PathLocksController::class, 'unlockRoute']);

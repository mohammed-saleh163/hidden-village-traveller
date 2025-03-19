<?php

use Paths\Controllers\PathController;
use Illuminate\Support\Facades\Route;
use Paths\Controllers\PathLocksController;

Route::post('/paths', [PathController::class, 'getPaths']);
Route::post('/paths/reserve-path', [PathLocksController::class, 'reserveRoute']);
Route::post('/paths/lock-path', [PathLocksController::class, 'lockRoute']);
Route::post('/paths/unlock-path', [PathLocksController::class, 'unlockRoute']);

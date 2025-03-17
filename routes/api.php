<?php

use App\Http\Controllers\PathController;
use Illuminate\Support\Facades\Route;

Route::post('/paths', [PathController::class, 'getPaths']);

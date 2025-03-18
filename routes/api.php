<?php

use Paths\Controllers\PathController;
use Illuminate\Support\Facades\Route;

Route::post('/paths', [PathController::class, 'getPaths']);

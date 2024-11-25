<?php

use App\Http\Controllers\DomainCheckerController;
use Illuminate\Support\Facades\Route;

Route::post('/check-domain', [DomainCheckerController::class, 'check']);

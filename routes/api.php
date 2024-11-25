<?php

use App\Http\Controllers\DomainCheckerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/check-domain', [DomainCheckerController::class, 'check']);

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DMO\VisionController;
use App\Http\Controllers\DMO\InfrastructureAuditController;

 Route::post('verify-banner1',        [InfrastructureAuditController::class, 'verifyBanner'])
                         ->name('verify-banner');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

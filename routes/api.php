<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DMO\VisionController;

Route::post('/myvalidate-bed-photo', [VisionController::class, 'validateBedPhoto'])
                    ->name('validate.bed.photo');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

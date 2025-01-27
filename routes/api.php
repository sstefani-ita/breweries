<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\V1\BreweryController;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

Route::post('/login', [LoginController::class, 'login']);

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    Route::get('/data', [BreweryController::class, 'index']);

});

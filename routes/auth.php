<?php

use Illuminate\Support\Facades\Route;


Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);


Route::middleware(['auth:api', 'role:admin', 'mythrottle'])->group(function () {
    Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('cek_token', [App\Http\Controllers\AuthController::class, 'cekToken']);
});

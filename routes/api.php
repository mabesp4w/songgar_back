<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// route group middleware
Route::middleware(['mythrottle'])->group(function () {
    Route::get('/user/{id}', [App\Http\Controllers\API\UserAPI::class, 'show']); //->middleware('auth:sanctum');
    // api majors
    Route::prefix('majors')->group(function () {
        Route::get('/', [App\Http\Controllers\API\MajorAPI::class, 'index']);
        Route::get('/all', [App\Http\Controllers\API\MajorAPI::class, 'all']);
    });
    // api employees
    Route::prefix('employees')->group(function () {
        Route::get('/', [App\Http\Controllers\API\EmployeeAPI::class, 'index']);
        Route::get('/all', [App\Http\Controllers\API\EmployeeAPI::class, 'all']);
    });

    // api slides
    Route::prefix('slides')->group(function () {
        Route::get('/', [App\Http\Controllers\API\SlideAPI::class, 'index']);
        Route::get('/all', [App\Http\Controllers\API\SlideAPI::class, 'all']);
    });

    // api announcements
    Route::prefix('announcements')->group(function () {
        Route::get('/', [App\Http\Controllers\API\AnnouncementAPI::class, 'index']);
        Route::get('/all', [App\Http\Controllers\API\AnnouncementAPI::class, 'all']);
    });

    // api news
    Route::prefix('news')->group(function () {
        Route::get('/', [App\Http\Controllers\API\NewsAPI::class, 'index']);
        Route::get('/all', [App\Http\Controllers\API\NewsAPI::class, 'all']);
    });

    // api structural
    Route::prefix('structures')->group(function () {
        Route::get('/', [App\Http\Controllers\API\StructuralAPI::class, 'index']);
        Route::get('/all', [App\Http\Controllers\API\StructuralAPI::class, 'all']);
    });
});

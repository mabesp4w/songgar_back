<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'role:admin', 'mythrottle'])->group(function () {
    Route::resources([
        'users' => App\Http\Controllers\CRUD\UserController::class,
        'employees' => App\Http\Controllers\CRUD\EmployeeController::class,
        'slides' => App\Http\Controllers\CRUD\SlideController::class,
        'announcements' => App\Http\Controllers\CRUD\AnnouncementController::class,
        'news' => App\Http\Controllers\CRUD\NewsController::class,
        'academicCalendars' => App\Http\Controllers\CRUD\AcademicCalendarController::class,
        'facilities' => App\Http\Controllers\CRUD\FacilityController::class,
        'structurals' => App\Http\Controllers\CRUD\StructuralController::class,
    ]);
    Route::group(['prefix' => 'galleries'], function () {
        Route::resources([
            'photos' => App\Http\Controllers\CRUD\GalleryPhotoController::class,
            'videos' => App\Http\Controllers\CRUD\GalleryVideoController::class,
        ]);
    });
});

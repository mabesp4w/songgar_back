<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', function () {
    // return error json
    return response()->json(['message' => 'Unauthorized'], 401);
})->name('login');

<?php

use App\Http\Controllers\Admin\HomeController;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->middleware('admin');
});
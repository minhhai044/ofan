<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::controller(HomeController::class)->middleware(['check_login','admin'])->group(function () {
    Route::get('/', 'index')->name('home');
});


Route::controller(AuthController::class)->group(function () {
    Route::get('/form_login', 'form_login')->name('form_login');
    Route::get('/form_register', 'form_register')->name('form_register');
    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login');
    Route::post('/logout', 'logout')->name('logout');
});
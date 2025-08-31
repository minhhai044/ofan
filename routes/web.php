<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::controller(HomeController::class)->middleware(['check_login', 'admin'])->group(function () {
    Route::get('/', 'index')->name('home');
});


Route::controller(AuthController::class)
    ->group(function () {
        Route::get('/form_login', 'form_login')->name('form_login');
        Route::get('/form_register', 'form_register')->name('form_register');
        Route::post('/register', 'register')->name('register');
        Route::post('/login', 'login')->name('login');
        Route::post('/logout', 'logout')->name('logout');
    });

Route::controller(ProductCategoryController::class)
    ->prefix('product_categories')
    ->as('product_categories.')
    ->middleware(['check_login', 'admin'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/', 'update')->name('update');
    });


Route::controller(UserController::class)
    ->prefix('users')
    ->as('users.')
    ->middleware(['check_login', 'admin'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('{users}/', 'edit')->name('edit');
        Route::put('/', 'update')->name('update');
    });



Route::controller(BranchController::class)
    ->prefix('branches')
    ->as('branches.')
    ->middleware(['check_login', 'admin'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('{branches}/', 'edit')->name('edit');
        Route::put('{branches}/', 'update')->name('update');
        Route::put('{branches}/updateStatus', 'updateStatus')->name('updateStatus');
    });

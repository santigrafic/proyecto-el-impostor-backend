<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminUserController;

Route::get('/', function () {
    return view('welcome');
});

//Login
Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->prefix('admin')
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->prefix('admin')
    ->name('login');

    //Rutas web
Route::middleware('auth', 'role:admin')
    ->prefix('admin')
    ->name('admin.')
    ->group(function(){
        Route::resource('users', AdminUserController::class);
    });
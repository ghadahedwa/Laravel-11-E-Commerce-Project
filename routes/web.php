<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');

Route::middleware('auth')->group(function () {
    Route::get('/account.dashboard',[UserController::class,'index'])->name('user.index');
});

Route::middleware(['auth',\App\Http\Middleware\AuthAdmin::class])->group(function(){
    Route::get('/admin',[AdminController::class,'index'])->name('admin.index');
});


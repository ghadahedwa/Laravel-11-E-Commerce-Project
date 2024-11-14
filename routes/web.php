<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandController;
use App\Http\Middleware\AuthAdmin;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/account.dashboard',[UserController::class,'index'])->name('user.index');
});

Route::prefix('admin')->name('admin.')->group(function (){
    Route::middleware(['auth',AuthAdmin::class])->group(function(){
        Route::get('/',[AdminController::class,'index'])->name('index');
        Route::resource('category',BrandController::class);
        Route::get('/brands',[AdminController::class,'brands'])->name('brands');
        Route::get('/brands/add',[AdminController::class,'add_brand'])->name('brands.add');
        Route::post('/brands/store',[AdminController::class,'brand_store'])->name('brands.store');
        Route::get('/brands/edit/{id}',[AdminController::class,'brand_edit'])->name('brands.edit');
        Route::put('/brands/update',[AdminController::class,'brand_update'])->name('brands.update');
        Route::delete('/brands/{id}/delete',[AdminController::class,'brand_delete'])->name('brands.delete');
    });
});



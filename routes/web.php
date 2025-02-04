<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Authadmin;



Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');

Route::middleware(['auth'])->group(function(){
    Route::get('/account-dashboard',[UserController::class, 'index'])->name('user.index');
});

Route::middleware(['auth', Authadmin::class])->group(function(){
    Route::get('/admin',[AdminController::class, 'index'])->name('admin.index');
    Route::get('admin/brands',[AdminController::class, 'brands'])->name('admin.brands');
    Route::get('admin/brand/add',[AdminController::class, 'addBrand'])->name('admin.brand.add');
    Route::put('admin/brand/store',[AdminController::class, 'brand_store'])->name('admin.brand.store');
    Route::get('admin/brand/edit/{id}',[AdminController::class, 'brand_edit'])->name('admin.brand.edit');
    Route::put('admin/brand/update',[AdminController::class, 'brand_update'])->name('admin.brand.update');
    Route::delete('admin/brand/update/{id}',[AdminController::class, 'brand_delete'])->name('admin.brand.delete');

    Route::get('admin/categories',[AdminController::class, 'categories'])->name('admin.categories');
});

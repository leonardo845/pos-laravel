<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModifierGroupController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Superadmin only: roles & users
    Route::middleware('role:superadmin')->group(function () {
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('suppliers', SupplierController::class)->except(['show']);
    });

    // Superadmin & owner: outlets
    Route::middleware('role:superadmin,owner')->group(function () {
        Route::resource('outlets', OutletController::class)->except(['show']);
        Route::get('outlets/{outlet}/products', [OutletController::class, 'products'])->name('outlets.products');
        Route::post('outlets/{outlet}/products', [OutletController::class, 'syncProducts'])->name('outlets.products.sync');
    });

    // Superadmin, owner & admin: master data
    Route::middleware('role:superadmin,owner,admin')->group(function () {
        Route::resource('product-categories', ProductCategoryController::class)->except(['show']);
        Route::resource('units', UnitController::class)->except(['show']);
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('modifier-groups', ModifierGroupController::class)->except(['show']);
        Route::resource('customers', CustomerController::class)->except(['show']);
    });
});



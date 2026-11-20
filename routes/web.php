<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:10,1')->name('login.store');
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class)->except(['show']);
    Route::resource('customers', CustomerController::class)->except(['show']);
    Route::resource('staff', StaffController::class)->except(['show']);
    Route::post('/products/{product}/archive', [ProductController::class, 'archive'])->name('products.archive');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->whereNumber('sale')->name('sales.show');
    Route::get('/media/{name}', [MediaController::class, 'show'])->where('name', '[A-Za-z0-9-]+\.jpg')->name('media.show');
});

<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\VoucherController; // Added
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LandingPageController;
use Illuminate\Support\Facades\Route;

/* ==================
   Public
================== */
Route::get('/', [LandingPageController::class, 'index'])->name('landing');
Route::get('/tracking', [App\Http\Controllers\TrackingController::class, 'index'])->name('tracking');

/* ==================
   Auth
================== */
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/* ==================
   Checkout (Midtrans)
================== */
Route::get('/checkout', [CheckoutController::class, 'showForm'])->name('checkout');
Route::post('/checkout/check-voucher', [CheckoutController::class, 'checkVoucher'])->name('checkout.check-voucher'); // Added
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::post('/checkout/callback', [CheckoutController::class, 'callback'])->name('checkout.callback');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');

/* ==================
   Admin CMS
================== */
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
    Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export'); // Added
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'destroy']);
    Route::resource('vouchers', VoucherController::class); // Added
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('orders/{order}/tracking', [OrderController::class, 'updateTracking'])->name('orders.update-tracking');
    Route::patch('orders/{order}/shipping', [OrderController::class, 'updateShipping'])->name('orders.update-shipping');
});

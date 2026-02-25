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
   Standalone Order
================== */
use App\Http\Controllers\OrderPageController;
use App\Http\Controllers\CheckOrderController;
Route::get('/pesan-langsung', [OrderPageController::class, 'index'])->name('order-page.index');
Route::post('/pesan-langsung', [OrderPageController::class, 'store'])->name('order-page.store');
Route::post('/api/check-voucher', [OrderPageController::class, 'checkVoucher'])->name('api.check-voucher');
Route::get('/api/check-wa', [OrderPageController::class, 'checkWa'])->name('api.check-wa');
Route::get('/cek-pesanan', [CheckOrderController::class, 'index'])->name('cek-pesanan.index');
Route::post('/cek-pesanan', [CheckOrderController::class, 'search'])->name('cek-pesanan.search');

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
    
    // Standalone Orders
    Route::get('/standalone-orders', [App\Http\Controllers\Admin\StandaloneOrderController::class, 'index'])->name('standalone-orders.index');
    Route::get('/standalone-orders/{standaloneOrder}', [App\Http\Controllers\Admin\StandaloneOrderController::class, 'show'])->name('standalone-orders.show');
    Route::patch('/standalone-orders/{standaloneOrder}/status', [App\Http\Controllers\Admin\StandaloneOrderController::class, 'updateStatus'])->name('standalone-orders.updateStatus');
});

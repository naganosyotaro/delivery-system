<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // ダッシュボード
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 発送管理
    Route::resource('shipments', ShipmentController::class);
    Route::post('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])
        ->name('shipments.status');
    Route::get('/shipments/{shipment}/pdf', [ShipmentController::class, 'pdf'])
        ->name('shipments.pdf');

    // 顧客管理
    Route::resource('customers', CustomerController::class);

    // 請求管理
    Route::resource('invoices', InvoiceController::class);

    // ドライバー向け
    Route::prefix('driver')->name('driver.')->group(function () {
        Route::get('/scan', [DriverController::class, 'scan'])->name('scan');
        Route::get('/shipments', [DriverController::class, 'shipments'])->name('shipments');
        Route::post('/shipments/{shipment}/status', [DriverController::class, 'updateStatus'])
            ->name('shipments.status');
    });

    // プロフィール
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

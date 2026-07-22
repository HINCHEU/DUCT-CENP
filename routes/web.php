<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\ManagerOrderController;
use App\Http\Controllers\WorkshopOrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    if (Auth::check()) {
        return app(HomeController::class)->index();
    }
    return redirect()->route('login');
})->name('home');

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    
    // Shared Routes
    Route::post('orders/{order}/comments', [CommentController::class, 'store'])->name('orders.comments.store');
    Route::get('orders/{order}/report', [ReportController::class, 'downloadCutList'])->name('orders.report');
    
    // Engineer Routes
    Route::middleware(['role:engineer'])->prefix('engineer')->name('engineer.')->group(function () {
        Route::resource('orders', OrderController::class);
        Route::post('orders/{order}/submit', [OrderController::class, 'submit'])->name('orders.submit');
        Route::post('orders/{order}/revert', [OrderController::class, 'revertToDraft'])->name('orders.revert');
        
        Route::put('orders/{order}/items/{item}/quantity', [OrderItemController::class, 'updateQuantity'])->name('orders.items.updateQuantity');
        Route::patch('orders/{order}/items/{item}/remark', [OrderItemController::class, 'updateRemark'])->name('orders.items.updateRemark');
        Route::resource('orders.items', OrderItemController::class)->except(['index', 'show']);
    });
    
    // Manager Routes
    Route::middleware(['role:manager'])->prefix('manager')->name('manager.')->group(function () {
        Route::get('orders', [ManagerOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [ManagerOrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/approve', [ManagerOrderController::class, 'approve'])->name('orders.approve');
        Route::post('orders/{order}/reject', [ManagerOrderController::class, 'reject'])->name('orders.reject');
        Route::post('orders/{order}/submit', [ManagerOrderController::class, 'submit'])->name('orders.submit');
        
        // Managers can edit items of submitted or draft orders
        Route::post('orders/{order}/items', [OrderItemController::class, 'storeManager'])->name('orders.items.store');
        Route::get('orders/{order}/items/{item}/edit', [OrderItemController::class, 'editManager'])->name('orders.items.edit');
        Route::put('orders/{order}/items/{item}', [OrderItemController::class, 'updateManager'])->name('orders.items.update');
        Route::put('orders/{order}/items/{item}/quantity', [OrderItemController::class, 'updateQuantity'])->name('orders.items.updateQuantity');
        Route::patch('orders/{order}/items/{item}/remark', [OrderItemController::class, 'updateRemark'])->name('orders.items.updateRemark');
        Route::delete('orders/{order}/items/{item}', [OrderItemController::class, 'destroy'])->name('orders.items.destroy');
    });
    
    // Workshop Routes
    Route::middleware(['role:workshop'])->prefix('workshop')->name('workshop.')->group(function () {
        Route::get('orders', [WorkshopOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [WorkshopOrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/status', [WorkshopOrderController::class, 'updateStatus'])->name('orders.status');
    });
    
});

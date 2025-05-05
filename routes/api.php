<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);
// Các route cần xác thực và kiểm tra quyền admin
Route::middleware(['auth:sanctum', 'check.token.expiry'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']); // Đăng xuất (cả user và admin)

    // Các route admin cần phải có quyền admin
    Route::middleware('is_admin')->group(function () {
        Route::get('/products', [ProductController::class, 'index']); // Quản lý sản phẩm chỉ dành cho admin
        Route::get('products/{id}', [ProductController::class, 'show']);
        Route::post('products', [ProductController::class, 'store']);
        Route::put('products/{id}', [ProductController::class, 'update']);
        Route::delete('products/{id}', [ProductController::class, 'destroy']);
    });
});

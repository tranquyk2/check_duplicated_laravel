<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanRecordController;
use App\Http\Controllers\AuthController;

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Create default user (chỉ dùng 1 lần)
Route::get('/create-user', [AuthController::class, 'createDefaultUser']);

// Protected routes - Yêu cầu đăng nhập
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ScanRecordController::class, 'index'])->name('dashboard');
    Route::get('/export', [ScanRecordController::class, 'export'])->name('export');
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\ScanRecordController;

Route::post('/scans', [ScanController::class, 'receive']);

// API endpoints cho dashboard
Route::get('/records', [ScanRecordController::class, 'getRecords']);
Route::get('/statistics', [ScanRecordController::class, 'getStatistics']);
Route::delete('/records/{id}', [ScanRecordController::class, 'destroy']);
Route::delete('/records', [ScanRecordController::class, 'deleteAll']);

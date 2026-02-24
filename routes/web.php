<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RekonsiliasiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BiReportController;

Route::get('/', [DashboardController::class, 'index']);

Route::get('/rekonsiliasi', function () {
    return view('pages.rekonsiliasi');
});

Route::get('/laporan-bi', function () {
    return view('pages.laporan-bi');
});

Route::get('/pengaturan', function () {
    return view('pages.pengaturan');
});

// Reconciliation API endpoints
Route::post('/api/rekonsiliasi', [RekonsiliasiController::class, 'process']);
Route::get('/api/rekonsiliasi/results', [RekonsiliasiController::class, 'getResults']);
Route::get('/api/bi-report', [BiReportController::class, 'index']);
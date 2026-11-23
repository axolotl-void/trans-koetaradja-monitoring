<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\OdometerController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfilController;

// ---- login / logout ----
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// semua halaman di bawah ini wajib login
Route::middleware('auth')->group(function () {

    Route::get('/', fn () => redirect('/dashboard'));

    Route::get('/dashboard', [DashboardController::class, 'index']);

    // ---- Bus ----
    Route::get('/buses', [BusController::class, 'index']);
    Route::get('/buses/create', [BusController::class, 'create']);
    Route::post('/buses', [BusController::class, 'store']);
    Route::get('/buses/{id}/edit', [BusController::class, 'edit']);
    Route::put('/buses/{id}', [BusController::class, 'update']);
    Route::delete('/buses/{id}', [BusController::class, 'destroy']);

    // ---- Komponen ----
    Route::get('/inventory', [InventoryController::class, 'index']);
    Route::get('/inventory/create', [InventoryController::class, 'create']);
    Route::post('/inventory', [InventoryController::class, 'store']);
    Route::get('/inventory/{id}/edit', [InventoryController::class, 'edit']);
    Route::put('/inventory/{id}', [InventoryController::class, 'update']);
    Route::delete('/inventory/{id}', [InventoryController::class, 'destroy']);
    Route::get('/inventory/{id}', [InventoryController::class, 'show']);

    // ---- Monitoring ----
    Route::get('/monitoring', [MonitoringController::class, 'index']);
    Route::get('/monitoring/create', [MonitoringController::class, 'create']);
    Route::post('/monitoring', [MonitoringController::class, 'store']);
    Route::get('/monitoring/{id}/edit', [MonitoringController::class, 'edit']);
    Route::put('/monitoring/{id}', [MonitoringController::class, 'update']);
    Route::post('/monitoring/{id}/reset', [MonitoringController::class, 'reset']);
    Route::get('/monitoring/{id}/ganti', [MonitoringController::class, 'ganti']);
    Route::post('/monitoring/{id}/proses-ganti', [MonitoringController::class, 'prosesGanti']);

    // ---- Odometer ----
    Route::get('/odometer', [OdometerController::class, 'index']);
    Route::get('/odometer/create', [OdometerController::class, 'create']);
    Route::post('/odometer', [OdometerController::class, 'store']);
    Route::get('/odometer/{id}/edit', [OdometerController::class, 'edit']);
    Route::put('/odometer/{id}', [OdometerController::class, 'update']);

    // ---- Maintenance ----
    Route::get('/maintenance', [MaintenanceController::class, 'index']);

    Route::get('/profil', [ProfilController::class, 'index']);
    Route::post('/profil/akun', [ProfilController::class, 'storeAkun']);
    Route::delete('/profil/akun/{id}', [ProfilController::class, 'destroyAkun']);
    Route::put('/profil/password', [ProfilController::class, 'updatePassword']);

    // ---- Export CSV ----
    Route::get('/export/{jenis}', [ReportController::class, 'csv'])
        ->whereIn('jenis', ['monitoring', 'maintenance', 'odometer']);
});

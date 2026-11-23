<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\StockController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$renderProtected = function (Request $request, string $view, bool $ownerOnly = false) {
    $auth = AuthController::resolveAuthFromRequest($request);
    if ($auth['token'] === null) {
        return redirect()->route('login');
    }

    if ($ownerOnly && $auth['role'] !== 'owner') {
        return redirect()->route('karyawan.tanaman');
    }

    return view($view);
};

Route::get('/', function (Request $request) {
    $auth = AuthController::resolveAuthFromRequest($request);
    if ($auth['token'] !== null) {
        return redirect(AuthController::dashboardRouteForRole($auth['role']));
    }

    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('admin')->name('admin.')->group(function () use ($renderProtected) {
    Route::get('/stok', [StockController::class, 'index'])->name('stok');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/karyawan', [EmployeeController::class, 'index'])->name('karyawan');
    Route::post('/karyawan', [EmployeeController::class, 'store'])->name('karyawan.store');
    Route::put('/karyawan/{id}', [EmployeeController::class, 'update'])->name('karyawan.update');
    Route::delete('/karyawan/{id}', [EmployeeController::class, 'destroy'])->name('karyawan.destroy');
});

Route::prefix('karyawan')->name('karyawan.')->group(function () use ($renderProtected) {
    Route::get('/tanaman', [PlantController::class, 'index'])->name('tanaman');
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');
    Route::get('/prediksi', [PredictionController::class, 'index'])->name('prediksi');
    Route::get('/diagnosis', [DiagnosisController::class, 'index'])->name('diagnosis');
});

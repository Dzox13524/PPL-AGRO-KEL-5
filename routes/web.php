<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/logout', function () {
    return redirect()->route('login');
})->name('logout');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/stok', function () {
        return view('admin.stok.index');
    })->name('stok');

    Route::get('/dashboard', function () {
        return view('admin.dashboard.index');
    })->name('dashboard');

    Route::get('/karyawan', function () {
        return view('admin.karyawan.index');
    })->name('karyawan');
});

Route::prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/tanaman', function () {
        return view('karyawan.tanaman.index');
    })->name('tanaman');

    Route::get('/monitoring', function () {
        return view('karyawan.monitoring.index');
    })->name('monitoring');

    Route::get('/prediksi', function () {
        return view('karyawan.prediksi.index');
    })->name('prediksi');

    Route::get('/diagnosis', function () {
        return view('karyawan.diagnosis.index');
    })->name('diagnosis');
});

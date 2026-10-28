<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

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
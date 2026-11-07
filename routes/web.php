<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\PublicComplaintController;
use App\Http\Controllers\PublicMapController;
use App\Http\Controllers\PublicLocationController;
use App\Http\Controllers\PublicTrackingController;
use App\Http\Controllers\StaffAuthController;
use App\Http\Controllers\StaffComplaintController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Middleware\EnsureStaff;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPageController::class)->name('landing');
Route::get('/peta', [PublicMapController::class, 'index'])->name('map.index');
Route::get('/peta/data', [PublicMapController::class, 'data'])->middleware('throttle:30,1')->name('map.data');
Route::get('/lokasi/alamat', [PublicLocationController::class, 'reverse'])->middleware('throttle:6,1')->name('location.reverse');
Route::get('/pengaduan/buat', [PublicComplaintController::class, 'create'])->name('complaints.create');
Route::post('/pengaduan', [PublicComplaintController::class, 'store'])->middleware('throttle:3,1')->name('complaints.store');
Route::get('/pengaduan/{code}/berhasil', [PublicComplaintController::class, 'success'])->name('complaints.success');
Route::get('/lacak', [PublicTrackingController::class, 'index'])->middleware('throttle:10,1')->name('tracking.index');
Route::get('/lacak/{code}', [PublicTrackingController::class, 'show'])->middleware('throttle:10,1')->name('tracking.show');

Route::prefix('petugas')->name('staff.')->group(function () {
    Route::get('/masuk', [StaffAuthController::class, 'create'])->name('login');
    Route::post('/masuk', [StaffAuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
    Route::middleware(EnsureStaff::class)->group(function () {
        Route::post('/keluar', [StaffAuthController::class, 'destroy'])->name('logout');
        Route::get('/', StaffDashboardController::class)->name('dashboard');
        Route::get('/pengaduan', [StaffComplaintController::class, 'index'])->name('complaints.index');
        Route::get('/pengaduan/{complaint}', [StaffComplaintController::class, 'show'])->name('complaints.show');
        Route::get('/pengaduan/{complaint}/{action}', [StaffComplaintController::class, 'action'])
            ->where('action', 'verify|reject|assign|progress|complete')
            ->name('complaints.action');
        Route::post('/pengaduan/{complaint}/{action}', [StaffComplaintController::class, 'storeAction'])
            ->where('action', 'verify|reject|assign|progress|complete')
            ->name('complaints.action.store');
    });
});

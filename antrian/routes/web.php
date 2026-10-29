<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\LoketController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', [DisplayController::class, 'index'])->name('display.index');
Route::get('/display/data', [DisplayController::class, 'data'])->name('display.data');

Route::get('/kiosk', [KioskController::class, 'index'])->name('kiosk.index');
Route::get('/kiosk/klasters', [KioskController::class, 'klasters'])->name('kiosk.klasters');
Route::post('/kiosk/ambil', [KioskController::class, 'store'])->name('kiosk.store');

// Bersihkan view cache lewat browser untuk hosting tanpa terminal (cPanel Shared).
// Buka: /antrian/viewclear?token=VANTA_BERSIHKAN_CACHE
// PERINGATAN: ganti token dan HAPUS rute ini setelah selesai dipakai!
Route::get('/viewclear', function (Request $request) {
    if ($request->query('token') !== 'VANTA_BERSIHKAN_CACHE') {
        abort(403);
    }

    Artisan::call('view:clear');

    return response('<pre>OK - Compiled views cleared successfully.</pre>');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/loket', [LoketController::class, 'index'])->name('loket.index');
    Route::post('/loket/pilih', [LoketController::class, 'pilih'])->name('loket.pilih');
    Route::get('/loket/data', [LoketController::class, 'data'])->name('loket.data');
    Route::post('/loket/panggil', [LoketController::class, 'panggil'])->name('loket.panggil');
    Route::post('/loket/panggil-ulang', [LoketController::class, 'panggilUlang'])->name('loket.panggil-ulang');
    Route::post('/loket/selesai', [LoketController::class, 'selesai'])->name('loket.selesai');
    Route::post('/loket/lewati', [LoketController::class, 'lewati'])->name('loket.lewati');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/polis', [AdminController::class, 'polis'])->name('polis');
        Route::post('/polis', [AdminController::class, 'polisStore'])->name('polis.store');
        Route::put('/polis/{poli}', [AdminController::class, 'polisUpdate'])->name('polis.update');
        Route::delete('/polis/{poli}', [AdminController::class, 'polisDestroy'])->name('polis.destroy');

        Route::get('/klasters', [AdminController::class, 'klasters'])->name('klaster');
        Route::post('/klasters', [AdminController::class, 'klastersStore'])->name('klaster.store');
        Route::put('/klasters/{klaster}', [AdminController::class, 'klastersUpdate'])->name('klaster.update');
        Route::delete('/klasters/{klaster}', [AdminController::class, 'klastersDestroy'])->name('klaster.destroy');

        Route::get('/lokets', [AdminController::class, 'lokets'])->name('loket');
        Route::post('/lokets', [AdminController::class, 'loketsStore'])->name('loket.store');
        Route::put('/lokets/{loket}', [AdminController::class, 'loketsUpdate'])->name('loket.update');
        Route::delete('/lokets/{loket}', [AdminController::class, 'loketsDestroy'])->name('loket.destroy');

        Route::get('/users', [AdminController::class, 'users'])->name('user');
        Route::post('/users', [AdminController::class, 'usersStore'])->name('user.store');
        Route::put('/users/{user}', [AdminController::class, 'usersUpdate'])->name('user.update');
        Route::delete('/users/{user}', [AdminController::class, 'usersDestroy'])->name('user.destroy');

        Route::get('/settings', [AdminController::class, 'settings'])->name('setting');
        Route::post('/settings', [AdminController::class, 'settingsUpdate'])->name('setting.update');

        Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
        Route::post('/reset-hari-ini', [AdminController::class, 'resetHariIni'])->name('reset-hari-ini');
    });
});
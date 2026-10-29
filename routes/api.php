<?php

use App\Http\Controllers\Api\AntrianApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| API panggilan antrian untuk perangkat eksternal (komputer kasir,
| HP petugas, dst). Setiap request wajib menyertakan API token petugas loket
| lewat header "Authorization: Bearer <token>" (lihat halaman admin > Petugas).
| Petugas dengan akses semua klaster dapat memilih loket melalui header
| "X-Loket-Id" atau parameter "loket_id".
|
| Contoh:
|   GET  /api/q/status                    -> status loket + antrian menunggu
|   POST /api/q/panggil                   -> memanggil nomor berikutnya
|   POST /api/q/panggil-ulang             -> memanggil ulang nomor aktif
|   POST /api/q/selesai                   -> menandai selesai dilayani
|   POST /api/q/lewati                    -> melewati nomor aktif
|
*/

Route::prefix('q')->middleware('auth.api')->group(function () {
    Route::get('/status', [AntrianApiController::class, 'status'])->name('api.status');
    Route::post('/panggil', [AntrianApiController::class, 'panggil'])->name('api.panggil');
    Route::post('/panggil-ulang', [AntrianApiController::class, 'panggilUlang'])->name('api.panggil-ulang');
    Route::post('/selesai', [AntrianApiController::class, 'selesai'])->name('api.selesai');
    Route::post('/lewati', [AntrianApiController::class, 'lewati'])->name('api.lewati');
});

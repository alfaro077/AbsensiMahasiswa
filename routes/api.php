<?php

use App\Http\Controllers\Api\DosenController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\MahasiswaController;
use App\Http\Controllers\Api\MataKuliahController;
use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Api\SesiKuliahController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Database: absensi
|--------------------------------------------------------------------------
|
| Semua route menggunakan prefix /api secara otomatis.
|
| Query parameters yang didukung:
|   ?search=keyword        — pencarian teks pada field yang didukung
|   ?sort_by=field         — sorting by field
|   ?sort_dir=asc|desc     — arah sorting
|   ?per_page=15           — jumlah data per halaman (1–100)
|   ?page=1                — nomor halaman
|   ?include=relation1,relation2  — eager load relasi
|   ?field=value           — filter exact match per field
|
*/

use App\Http\Controllers\Api\AuthController;

// ─── Authentication ───────────────────────────────────
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// ─── Protected Routes (Requires Login) ────────────────
Route::middleware('auth:sanctum')->group(function () {
    // Users
    Route::apiResource('users', UserController::class);

    // Jurusan
    Route::apiResource('jurusan', \App\Http\Controllers\Api\JurusanController::class);

    // Dosen
    Route::apiResource('dosen', DosenController::class);

    // Mahasiswa
    Route::apiResource('mahasiswa', MahasiswaController::class);

    // Gedung
    Route::apiResource('gedung', \App\Http\Controllers\Api\GedungController::class);

    // Mata Kuliah
    Route::apiResource('mata-kuliah', MataKuliahController::class)
        ->parameters(['mata-kuliah' => 'mata_kuliah']);

    // Enrollment
    Route::apiResource('enrollment', EnrollmentController::class);

    // Sesi Kuliah
    Route::apiResource('sesi-kuliah', SesiKuliahController::class)
        ->parameters(['sesi-kuliah' => 'sesi_kuliah']);

    // Jadwal Mata Kuliah
    Route::apiResource('jadwal-mata-kuliah', \App\Http\Controllers\Api\JadwalMataKuliahController::class)
        ->parameters(['jadwal-mata-kuliah' => 'jadwal_mata_kuliah']);

    // Presensi
    Route::get('/laporan/presensi', [PresensiController::class, 'report']);
    Route::apiResource('presensi', PresensiController::class);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
});

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\GuruAssignmentController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PengajaranController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SiswaSelfController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\WaliKelasController;
use App\Http\Controllers\WaliKelasPortalController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/register', [AuthController::class, 'registerAdmin']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('jwt.auth');

// Route::prefix('password-reset')->group(function () {
//     Route::post('/forgot', [PasswordResetController::class, 'forgot']);
//     Route::post('/verify', [PasswordResetController::class, 'verify']);
//     Route::post('/reset', [PasswordResetController::class, 'reset']);
// });

Route::middleware(['jwt.auth', 'role:admin'])->prefix('guru')->group(function () {
    Route::get('/', [GuruController::class, 'index']);
    Route::post('/', [GuruController::class, 'store']);

    Route::post('/assign-mapel', [GuruAssignmentController::class, 'store']);
    Route::get('/assign-mapel', [GuruAssignmentController::class, 'index']);
    Route::put('/assign-mapel/{id}', [GuruAssignmentController::class, 'update']);
    Route::delete('/assign-mapel/{id}', [GuruAssignmentController::class, 'destroy']);

    Route::post('/assign-walikelas', [WaliKelasController::class, 'assign']);
    Route::post('/unassign-walikelas', [WaliKelasController::class, 'unassign']);
    Route::get('/wali-kelas/daftar', [WaliKelasController::class, 'index']);

    Route::get('/{id}', [GuruController::class, 'show'])->whereNumber('id');
    Route::put('/{id}', [GuruController::class, 'update'])->whereNumber('id');
    Route::delete('/{id}', [GuruController::class, 'destroy'])->whereNumber('id');
});

Route::middleware(['jwt.auth', 'role:admin,guru'])->prefix('pengajar')->group(function () {
    Route::get('/list-kelas', [PengajaranController::class, 'index']);
});

// Route::middleware(['jwt.auth', 'role:wali_kelas'])->prefix('wali-kelas')->group(function () {
//     Route::get('/siswa', [WaliKelasPortalController::class, 'siswa']);
//     Route::get('/daftar', [WaliKelasPortalController::class, 'daftar']);
//     Route::get('/list-kelas', [WaliKelasPortalController::class, 'listKelas']);
// });

// Route::middleware(['jwt.auth', 'role:admin'])->prefix('kelas')->group(function () {
//     Route::post('/', [KelasController::class, 'store']);
//     Route::get('/', [KelasController::class, 'index']);
//     Route::get('/{id}', [KelasController::class, 'show']);
//     Route::put('/{id}', [KelasController::class, 'update']);
//     Route::delete('/{id}', [KelasController::class, 'destroy']);
// });

// Route::middleware(['jwt.auth', 'role:admin'])->prefix('mapel')->group(function () {
//     Route::post('/', [MataPelajaranController::class, 'store']);
//     Route::get('/', [MataPelajaranController::class, 'index']);
//     Route::get('/{id}', [MataPelajaranController::class, 'show']);
//     Route::put('/{id}', [MataPelajaranController::class, 'update']);
//     Route::delete('/{id}', [MataPelajaranController::class, 'destroy']);
// });

// Route::middleware(['jwt.auth', 'role:admin'])->prefix('siswa')->group(function () {
//     Route::post('/', [SiswaController::class, 'store']);
//     Route::get('/', [SiswaController::class, 'index']);
//     Route::get('/kelas/{id}', [SiswaController::class, 'getByKelas']);
//     Route::get('/{id}', [SiswaController::class, 'show']);
//     Route::put('/{id}', [SiswaController::class, 'update']);
//     Route::delete('/{id}', [SiswaController::class, 'destroy']);
//     Route::post('/assign-kelas', [SiswaController::class, 'assignKelas']);
//     Route::post('/unassign-kelas', [SiswaController::class, 'unassignKelas']);
//     Route::post('/assign-mapel', [SiswaController::class, 'assignMapel']);
//     Route::post('/unassign-mapel', [SiswaController::class, 'unassignMapel']);
// });

// Route::middleware(['jwt.auth', 'role:siswa'])->prefix('siswa')->group(function () {
//     Route::get('/profil', [SiswaSelfController::class, 'profil']);
//     Route::get('/absensi', [SiswaSelfController::class, 'absensi']);
// });

// Route::middleware(['jwt.auth', 'role:admin,guru,wali_kelas'])->prefix('absensi')->group(function () {
//     Route::post('/', [AbsensiController::class, 'store']);
//     Route::get('/list/mapel', [AbsensiController::class, 'listStudentsForMapel']);
//     Route::get('/list/kelas', [AbsensiController::class, 'listStudentsForKelas']);
//     Route::get('/rekap/mapel', [AbsensiController::class, 'recapMapel']);
//     Route::get('/rekap/mapel/export', [AbsensiController::class, 'exportRecapMapel']);
//     Route::get('/rekap/kelas', [AbsensiController::class, 'recapKelas']);
//     Route::get('/rekap/kelas/export', [AbsensiController::class, 'exportRecapKelas']);
//     Route::get('/', [AbsensiController::class, 'index']);
//     Route::get('/{id}', [AbsensiController::class, 'show']);
//     Route::put('/{id}', [AbsensiController::class, 'update']);
//     Route::delete('/{id}', [AbsensiController::class, 'destroy']);
// });

// Route::middleware(['jwt.auth', 'role:admin,guru,wali_kelas'])->prefix('todo')->group(function () {
//     Route::post('/', [TodoController::class, 'store']);
//     Route::get('/', [TodoController::class, 'index']);
//     Route::put('/{id}/status', [TodoController::class, 'updateStatus']);
//     Route::delete('/{id}', [TodoController::class, 'destroy']);
// });

// Route::middleware(['jwt.auth', 'role:admin,guru,wali_kelas,siswa'])->prefix('user')->group(function () {
//     Route::post('/device-tokens', [DeviceTokenController::class, 'register']);
// });

// Route::middleware(['jwt.auth'])->prefix('notifications')->group(function () {
//     Route::get('/', [NotificationController::class, 'index']);
//     Route::patch('/{id}/read', [NotificationController::class, 'markRead']);
//     Route::patch('/mark-read', [NotificationController::class, 'markBulk']);
//     Route::patch('/mark-all-read', [NotificationController::class, 'markAll']);
//     Route::delete('/{id}', [NotificationController::class, 'delete']);
//     Route::delete('/', [NotificationController::class, 'deleteBulk']);
//     Route::delete('/all', [NotificationController::class, 'deleteAll']);
// });

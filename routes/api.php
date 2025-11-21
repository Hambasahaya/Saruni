<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuruAssignmentController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\PengajaranController;
use App\Http\Controllers\WaliKelasController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/register', [AuthController::class, 'registerAdmin']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('jwt.auth');

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

<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DasborController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\Auth\MasukController;
use App\Http\Controllers\PendaftaranController;

// Rute "/" universal, tidak pakai middleware
Route::get('/', fn() => redirect()->route(Auth::check() ? 'dashboard.index' : 'login'));

// Rute masuk
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [MasukController::class, 'index'])->name('login');
    Route::post('/login', [MasukController::class, 'masuk'])->name('login.do');
    Route::get('/register', [PenggunaController::class, 'createPublic'])->name('register');
    Route::post('/register', [PenggunaController::class, 'storePublic'])->name('register.do');
    Route::get('/afiliasi/children/{parentId}', [PenggunaController::class, 'getChildren']);
});

// Rute dasbor dengan permission
Route::middleware(['auth'])->group(function () {
    // Dashboard routes - semua role yang login bisa akses dashboard
    Route::get('/dasbor', [DasborController::class, 'index'])->name('dashboard.index')
        ->middleware('permission:dashboard_view');

    Route::get('/dasbor_lawas', [DasborController::class, 'index_lawas'])->name('dashboard.lawas')
        ->middleware('permission:dashboard_view');

    Route::post('/keluar', [MasukController::class, 'keluar'])->name('logout');

    // Rute Profil Pribadi
    Route::prefix('profil')->group(function () {
        Route::get('/', [ProfilController::class, 'show'])->name('profil.show');
        Route::get('/edit', [ProfilController::class, 'edit'])->name('profil.edit');
        Route::put('/update', [ProfilController::class, 'update'])->name('profil.update');
        Route::put('/update-password', [ProfilController::class, 'updatePassword'])->name('profil.update-password');
        Route::delete('/avatar', [ProfilController::class, 'deleteAvatar'])->name('profil.avatar.delete');
    });

    // Manajemen Pengguna Routes - hanya untuk superadmin dan baak
    Route::prefix('pengguna')->middleware(['permission:user_view'])->group(function () {
        Route::get('/', [PenggunaController::class, 'index'])->name('pengguna.index');
        Route::get('/buat', [PenggunaController::class, 'create'])->name('pengguna.create')
            ->middleware('permission:user_create');
        Route::post('/', [PenggunaController::class, 'store'])->name('pengguna.store')
            ->middleware('permission:user_create');
        Route::get('/{pengguna}', [PenggunaController::class, 'show'])->name('pengguna.show');
        Route::get('/{pengguna}/edit', [PenggunaController::class, 'edit'])->name('pengguna.edit')
            ->middleware('permission:user_edit');
        Route::put('/{pengguna}', [PenggunaController::class, 'update'])->name('pengguna.update')
            ->middleware('permission:user_edit');
        Route::delete('/{pengguna}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy')
            ->middleware('permission:user_delete');
        Route::post('/{pengguna}/reset-password', [PenggunaController::class, 'resetPassword'])->name('pengguna.reset-password')
            ->middleware('permission:user_edit');
        Route::delete('/{pengguna}/avatar', [PenggunaController::class, 'deleteAvatar'])->name('pengguna.avatar.delete')
            ->middleware('permission:user_edit');
    });

    // Pendaftaran Routes - bisa diakses oleh multiple roles
    Route::prefix('pendaftaran')->middleware(['permission:pendaftaran_view'])->group(function () {
        Route::get('/', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::get('/data', [PendaftaranController::class, 'data'])->name('pendaftaran.data');
        Route::get('/create', [PendaftaranController::class, 'create'])->name('pendaftaran.create')
            ->middleware('permission:pendaftaran_create');
        Route::post('/', [PendaftaranController::class, 'store'])->name('pendaftaran.store')
            ->middleware('permission:pendaftaran_create');
        Route::get('/{pendaftaran}', [PendaftaranController::class, 'show'])->name('pendaftaran.show');
        Route::get('/{pendaftaran}/edit', [PendaftaranController::class, 'edit'])->name('pendaftaran.edit')
            ->middleware('permission:pendaftaran_edit');
        Route::put('/{pendaftaran}', [PendaftaranController::class, 'update'])->name('pendaftaran.update')
            ->middleware('permission:pendaftaran_edit');
        Route::delete('/{pendaftaran}', [PendaftaranController::class, 'destroy'])->name('pendaftaran.destroy')
            ->middleware('permission:pendaftaran_delete');
        Route::post('/sync', [PendaftaranController::class, 'sync'])->name('pendaftaran.sync');
        // Route::post('/sync/{pendaftaran}', [PendaftaranController::class, 'syncOne'])->name('pendaftaran.syncOne');
        Route::post('/sync/{id_calon_mahasiswa}', [PendaftaranController::class, 'syncOne'])->name('pendaftaran.syncOne');
        Route::post('/sync-new/{id_calon_mahasiswa}', [PendaftaranController::class, 'syncNew'])->name('pendaftaran.syncNew');
    });
});

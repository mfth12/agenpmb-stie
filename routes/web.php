<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DasborController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\Auth\MasukController;
use App\Http\Controllers\KonfigurasiController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\AntrianNotifWhatsappController;

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
        Route::put('/{pengguna}/approve', [PenggunaController::class, 'approve'])->name('pengguna.approve')
            ->middleware('permission:user_view'); // Gunakan permission yang sesuai, misalnya user_edit
        Route::put('/{pengguna}/reject', [PenggunaController::class, 'reject'])->name('pengguna.reject')
            ->middleware('permission:user_view'); // Gunakan permission yang sesuai, misalnya user_edit
        Route::delete('/{pengguna}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy')
            ->middleware('permission:user_delete');
        Route::post('/{pengguna}/reset-password', [PenggunaController::class, 'resetPassword'])->name('pengguna.reset-password')
            ->middleware('permission:user_edit');
        Route::delete('/{pengguna}/avatar', [PenggunaController::class, 'deleteAvatar'])->name('pengguna.avatar.delete')
            ->middleware('permission:user_edit');
    });

    // Manajemen Konfigurasi Routes - hanya untuk superadmin
    Route::prefix('konfigurasi')->middleware(['role:superadmin'])->group(function () {
        Route::get('/', [KonfigurasiController::class, 'index'])->name('konfigurasi.index');
        Route::get('/create', [KonfigurasiController::class, 'create'])->name('konfigurasi.create')
            ->middleware(['role:superadmin']);
        Route::post('/', [KonfigurasiController::class, 'store'])->name('konfigurasi.store')
            ->middleware(['role:superadmin']);
        Route::get('/{konfigurasi}/edit', [KonfigurasiController::class, 'edit'])->name('konfigurasi.edit')
            ->middleware(['role:superadmin']);
        Route::put('/{konfigurasi}', [KonfigurasiController::class, 'update'])->name('konfigurasi.update')
            ->middleware(['role:superadmin']);
        Route::delete('/{konfigurasi}', [KonfigurasiController::class, 'destroy'])->name('konfigurasi.destroy')
            ->middleware(['role:superadmin']);
    });

    // Manajemen Roles & Permissions Routes - hanya untuk superadmin
    Route::prefix('role-permission')->middleware(['role:superadmin'])->group(function () {
        Route::get('/', [RolePermissionController::class, 'indexRole'])->name('role_permission.index'); // Default ke index role

        // Role Routes
        Route::get('/role', [RolePermissionController::class, 'indexRole'])->name('role_permission.index_role');
        Route::get('/role/create', [RolePermissionController::class, 'createRole'])->name('role_permission.create_role');
        Route::post('/role', [RolePermissionController::class, 'storeRole'])->name('role_permission.store_role');
        Route::get('/role/{role}/edit', [RolePermissionController::class, 'editRole'])->name('role_permission.edit_role');
        Route::put('/role/{role}', [RolePermissionController::class, 'updateRole'])->name('role_permission.update_role');
        Route::delete('/role/{role}', [RolePermissionController::class, 'destroyRole'])->name('role_permission.destroy_role');

        // Permission Routes
        Route::get('/permission', [RolePermissionController::class, 'indexPermission'])->name('role_permission.index_permission');
        Route::get('/permission/create', [RolePermissionController::class, 'createPermission'])->name('role_permission.create_permission');
        Route::post('/permission', [RolePermissionController::class, 'storePermission'])->name('role_permission.store_permission');
        Route::get('/permission/{permission}/edit', [RolePermissionController::class, 'editPermission'])->name('role_permission.edit_permission');
        Route::put('/permission/{permission}', [RolePermissionController::class, 'updatePermission'])->name('role_permission.update_permission');
        Route::delete('/permission/{permission}', [RolePermissionController::class, 'destroyPermission'])->name('role_permission.destroy_permission');
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

    // Group untuk laporan/statistik - hanya untuk role yang diizinkan
    Route::prefix('laporan')->middleware(['permission:laporan_view'])->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/data', [LaporanController::class, 'data'])->name('laporan.data'); // Untuk DataTables
        Route::post('/generate-pdf', [LaporanController::class, 'generatePdf'])->name('laporan.generate-pdf');
    });

    // Antrian notifikasi whatsapp
    Route::prefix('antrian-notif-whatsapp')->middleware(['permission:antrian_whatsapp_view'])->group(function () {
        Route::get('/', [AntrianNotifWhatsappController::class, 'index'])->name('antrian-notif-whatsapp.index');
        Route::get('/create', [AntrianNotifWhatsappController::class, 'create'])->name('antrian-notif-whatsapp.create')
            ->middleware('permission:antrian_whatsapp_create');
        Route::post('/', [AntrianNotifWhatsappController::class, 'store'])->name('antrian-notif-whatsapp.store')
            ->middleware('permission:antrian_whatsapp_create');
        Route::get('/{antrian}', [AntrianNotifWhatsappController::class, 'show'])->name('antrian-notif-whatsapp.show');
        Route::get('/{antrian}/edit', [AntrianNotifWhatsappController::class, 'edit'])->name('antrian-notif-whatsapp.edit')
            ->middleware('permission:antrian_whatsapp_edit');
        Route::put('/{antrian}', [AntrianNotifWhatsappController::class, 'update'])->name('antrian-notif-whatsapp.update')
            ->middleware('permission:antrian_whatsapp_edit');
        Route::post('/all/hapus', [AntrianNotifWhatsappController::class, 'destroyAll'])->name('antrian-notif-whatsapp.destroy-all')
            ->middleware('permission:antrian_whatsapp_delete');
        Route::delete('/{antrian}', [AntrianNotifWhatsappController::class, 'destroy'])->name('antrian-notif-whatsapp.destroy')
            ->middleware('permission:antrian_whatsapp_delete');
        Route::post('/{antrian}/retry', [AntrianNotifWhatsappController::class, 'retry'])->name('antrian-notif-whatsapp.retry')
            ->middleware('permission:antrian_whatsapp_retry');
        Route::post('/{antrian}/force-retry', [AntrianNotifWhatsappController::class, 'forceRetry'])->name('antrian-notif-whatsapp.force-retry')
            ->middleware('permission:antrian_whatsapp_retry'); // Gunakan permission yang sama atau buat yang baru
    });
});

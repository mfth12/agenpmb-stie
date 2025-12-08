<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Superadmin Previllage
            'konfigurasi_manage',
            'akademik_manage',
            'role_manage',
            'permission_manage',

            // User Management
            'user_view',
            'user_create',
            'user_edit',
            'user_delete',

            // Student Registration (Pendaftaran)
            'pendaftaran_view',
            'pendaftaran_create',
            'pendaftaran_edit',
            'pendaftaran_delete',
            
            // Student Registration (Pendaftaran)
            'antrian_whatsapp_view',
            'antrian_whatsapp_create',
            'antrian_whatsapp_edit',
            'antrian_whatsapp_delete',

            // Financial
            'keuangan_view',
            'keuangan_manage',

            // Dashboard
            'dashboard_view',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdmin = Role::create(['name' => 'superadmin']);
        $superAdmin->givePermissionTo(Permission::all());

        $baak = Role::create(['name' => 'baak']);
        $baak->givePermissionTo([
            'dashboard_view',
            'pendaftaran_view',
            'pendaftaran_create',
            'pendaftaran_edit',
            'pendaftaran_delete',
            'user_view',
        ]);

        $mitra = Role::firstOrCreate(['name' => 'mitra']); // Menggunakan firstOrCreate untuk konsistensi dengan UserSeeder
        $mitra->givePermissionTo([
            'dashboard_view',
            'pendaftaran_view',
            'pendaftaran_create',
            'pendaftaran_edit',
            'pendaftaran_delete',
        ]);

        $keuangan = Role::create(['name' => 'keuangan']);
        $keuangan->givePermissionTo([
            'dashboard_view',
            'keuangan_view',
            'keuangan_manage',
            'pendaftaran_view',
        ]);

        // $mahasiswabaru = Role::create(['name' => 'mahasiswabaru']);
        // $mahasiswabaru->givePermissionTo([
        //     'dashboard_view',
        //     'pendaftaran_create',
        // ]);

        // $mahasiswa = Role::create(['name' => 'mahasiswa']);
        // $mahasiswa->givePermissionTo([
        //     'dashboard_view',
        //     'pendaftaran_create',
        // ]);
    }
}

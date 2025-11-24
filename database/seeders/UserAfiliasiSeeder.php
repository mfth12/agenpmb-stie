<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Import Schema

class UserAfiliasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan tabel kosong sebelum seeding untuk mencegah duplikat
        // Opsional: Hapus data lama jika diperlukan
        // DB::table('user_afiliasis')->truncate(); // Hati-hati! Ini akan menghapus semua data

        // Cek apakah tabel sudah ada dan kosong sebelum seeding
        if (!Schema::hasTable('user_afiliasis')) {
            $this->command->error('Tabel user_afiliasis tidak ditemukan. Pastikan migrasi telah dijalankan.');
            return;
        }

        $existingCount = DB::table('user_afiliasis')->count();
        if ($existingCount > 0) {
            $this->command->warn('Tabel user_afiliasis sudah berisi data. Skipping seeding.');
            return;
            // Atau uncomment baris berikut jika Anda ingin menghapus dan mengisi ulang:
            // DB::table('user_afiliasis')->truncate();
        }

        $afiliasis = [
            [
                'nama' => 'Alumni',
                'keterangan' => 'Afiliasi untuk alumni institusi',
                'parent_id' => null,
                'created_at' => Carbon::now()
            ],
            [
                'nama' => 'Civitas',
                'keterangan' => 'Afiliasi untuk civitas akademika',
                'parent_id' => null,
                'created_at' => Carbon::now()
            ],
            [
                'nama' => 'Mitra Sekolah',
                'keterangan' => 'Afiliasi untuk mitra sekolah',
                'parent_id' => null,
                'created_at' => Carbon::now()
            ],
            // Children dari Civitas
            [
                'nama' => 'Dosen',
                'keterangan' => 'Anggota dewan pengajar',
                'parent_id' => 2, // Parent: Civitas (afiliasi_id = 2)
                'created_at' => Carbon::now()
            ],
            [
                'nama' => 'Staff',
                'keterangan' => 'Staff administrasi atau non-pengajar',
                'parent_id' => 2, // Parent: Civitas (afiliasi_id = 2)
                'created_at' => Carbon::now()
            ],
            [
                'nama' => 'Lainnya',
                'keterangan' => 'Anggota civitas lainnya',
                'parent_id' => 2, // Parent: Civitas (afiliasi_id = 2)
                'created_at' => Carbon::now()
            ],
        ];

        DB::table('user_afiliasis')->insert($afiliasis);

        // $this->command->info('Seeder UserAfiliasiSeeder berhasil dijalankan.');
    }
}

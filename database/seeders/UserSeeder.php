<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan role 'agen' tersedia
        $roleSuperadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $roleBaak = Role::firstOrCreate(['name' => 'baak']);
        $roleKeuangan = Role::firstOrCreate(['name' => 'keuangan']);
        $roleAgen = Role::firstOrCreate(['name' => 'agen']);

        // Buat user spesifik untuk superadmin
        $superadminUsers = [
            [
                'username'      => 'mfth12',
                'name'          => 'Superadmin Miftah',
                'asal_sekolah'  => 'STIE Pembangunan Tanjungpinang',
                'email'         => 'mfth12@google.com',
                'nomor_hp'      => '6281331847725',
                'nomor_hp2'     => '6281331847725',
                'passsword'     => '123123',
            ],
        ];

        // Buat user spesifik untuk baak (quick kontak person PMB)
        $baakUsers = [
            [
                'username'      => 'dheska',
                'name'          => 'Dheska Mutia, SE',
                'asal_sekolah'  => 'STIE Pembangunan Tanjungpinang',
                'email'         => 'dheska@stie-pembangunan.ac.id',
                'nomor_hp'      => '6285272488108',
                'nomor_hp2'     => '6285272488108',
                'passsword'     => 'dheska',
            ],
            [
                'username'      => 'erni05',
                'name'          => 'Erni Yulianti, SE',
                'asal_sekolah'  => 'STIE Pembangunan Tanjungpinang',
                'email'         => 'erni@stie-pembangunan.ac.id',
                'nomor_hp'      => '6281378704882',
                'nomor_hp2'     => '6281378704882',
                'passsword'     => 'erni05',
            ],
            [
                'username'      => 'ekiwulan',
                'name'          => 'Surya Eki Wulandari, SE',
                'asal_sekolah'  => 'STIE Pembangunan Tanjungpinang',
                'email'         => 'eki@stie-pembangunan.ac.id',
                'nomor_hp'      => '6281266106955',
                'nomor_hp2'     => '6281266106955',
                'passsword'     => 'ekiwulan',
            ],
            [
                'username'      => 'restyayu',
                'name'          => 'Resty Ayu Fallufy, SE',
                'asal_sekolah'  => 'STIE Pembangunan Tanjungpinang',
                'email'         => 'resty@stie-pembangunan.ac.id',
                'nomor_hp'      => '6282387783200',
                'nomor_hp2'     => '6282387783200',
                'passsword'     => 'restyayu',
            ],
        ];

        // Buat user spesifik untuk superadmin
        $keuanganUsers = [
            [
                'username'      => 'yanti73',
                'name'          => 'Alfianti Nurrahmi',
                'asal_sekolah'  => 'STIE Pembangunan Tanjungpinang',
                'email'         => 'alfianti@stie-pembangunan.ac.id',
                'nomor_hp'      => '6281372060909',
                'nomor_hp2'     => '6281372060909',
                'passsword'     => 'yanti73',
            ],
        ];

        // Buat user spesifik untuk agen
        $agenUsers = [
            [
                'username'      => 'hartono91',
                'name'          => 'Hartono',
                'asal_sekolah'  => 'SMKN 2 Tanjungpinang',
                'email'         => 'hartono961@guru.smk.belajar.id',
                'nomor_hp'      => '08127757411',
                'nomor_hp2'     => '08127757411',
                // 'passsword'     => '$2y$12$cFmKPiJrgf3EME.s11wesOpUjwwVnOHTCRhA.mBty6xX58xibxpca',
                'passsword'     => 'hartono91',
            ],
            [
                'username'      => 'ucueni',
                'name'          => 'Armaini Mardalena',
                'asal_sekolah'  => 'SMA Negeri 4 Tanjungpinang',
                'email'         => 'armaini3031@hmail.com',
                'nomor_hp'      => '081350101036',
                'nomor_hp2'     => '081350101036',
                // 'passsword'     => '$2y$12$SDTjERQMmyNGPrJP2braL.J54Lnqlb5MqumSPTVPlOnvJgNrilqMC',
                'passsword'     => 'ucueni',
            ],
            [
                'username'      => 'skatratpi',
                'name'          => 'Mutia Khairunnisya, A.Md.KL',
                'asal_sekolah'  => 'SMK Maitreyawira Tanjungpinang',
                'email'         => 'khairunnisya1697@gmail.com',
                'nomor_hp'      => '082386085226',
                'nomor_hp2'     => '082386085226',
                // 'passsword'     => '$2y$12$RJ2UWCAh9juB/IEFTVJZJe6Iza.rqtHRwa80sFcgAiXnmtcDdxCm.',
                'passsword'     => 'skatratpi',
            ],
            [
                'username'      => 'ratna123456',
                'name'          => 'Ratna Wulandari',
                'asal_sekolah'  => 'SMAS Maitreyawira Tanjungpinang',
                'email'         => 'ratnawulandari432@gmail.com',
                'nomor_hp'      => '085364993104',
                'nomor_hp2'     => '085364993104',
                // 'passsword'     => '$2y$12$ayNw216k0IAnOP.SBjEUheDAWl7ZxnSSjVq3j6cx/gfddTz3Bhlee',
                'passsword'     => 'ratna123456',
            ],
        ];

        foreach ($superadminUsers as $userData) {
            $user = User::factory()->create([
                'username'      => $userData['username'],
                'name'          => $userData['name'],
                'asal_sekolah'  => $userData['asal_sekolah'],
                'email'         => $userData['email'],
                'nomor_hp'      => $userData['nomor_hp'],
                'nomor_hp2'     => $userData['nomor_hp2'] ?? $userData['nomor_hp'],
                'password'      => bcrypt($userData['passsword']),
            ]);
            $user->syncRoles([$roleSuperadmin]);
        }

        foreach ($baakUsers as $userData) {
            $user = User::factory()->create([
                'username'      => $userData['username'],
                'name'          => $userData['name'],
                'asal_sekolah'  => $userData['asal_sekolah'],
                'email'         => $userData['email'],
                'nomor_hp'      => $userData['nomor_hp'],
                'nomor_hp2'     => $userData['nomor_hp2'] ?? $userData['nomor_hp'],
                'password'      => bcrypt($userData['passsword']),
            ]);
            $user->syncRoles([$roleBaak]);
        }

        foreach ($keuanganUsers as $userData) {
            $user = User::factory()->create([
                'username'      => $userData['username'],
                'name'          => $userData['name'],
                'asal_sekolah'  => $userData['asal_sekolah'],
                'email'         => $userData['email'],
                'nomor_hp'      => $userData['nomor_hp'],
                'nomor_hp2'     => $userData['nomor_hp2'] ?? $userData['nomor_hp'],
                'password'      => bcrypt($userData['passsword']),
            ]);
            $user->syncRoles([$roleKeuangan]);
        }

        foreach ($agenUsers as $userData) {
            $user = User::factory()->create([
                'username'      => $userData['username'],
                'name'          => $userData['name'],
                'asal_sekolah'  => $userData['asal_sekolah'],
                'email'         => $userData['email'],
                'nomor_hp'      => $userData['nomor_hp'],
                'nomor_hp2'     => $userData['nomor_hp2'] ?? $userData['nomor_hp'],
                'password'      => bcrypt($userData['passsword']),
            ]);
            $user->syncRoles([$roleAgen]);
        }

        // // Buat user random menggunakan factory
        // User::factory()->count(10)->create()->each(function ($user) use ($roleAgen) {
        //     $user->syncRoles([$roleAgen]);
        // });

        // User::factory()->count(2)->withRole('keuangan')->create()->each(function ($user) use ($roleKeuangan) {
        //     $user->syncRoles([$roleKeuangan]);
        // });

        // // Buat beberapa user dengan status online
        // User::factory()->count(2)->online()->create()->each(function ($user) use ($roleAgen) {
        //     $user->syncRoles([$roleAgen]);
        // });

        // // Buat beberapa user dengan siakad_id
        // User::factory()->count(3)->withSiakad()->create()->each(function ($user) use ($roleAgen) {
        //     $user->syncRoles([$roleAgen]);
        // });

        // // Buat user dengan data lengkap
        // User::factory()->count(2)->complete()->create()->each(function ($user) use ($roleAgen) {
        //     $user->syncRoles([$roleAgen]);
        // });
    }
}

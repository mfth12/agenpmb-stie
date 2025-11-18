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
                'user_id'       => 3201,
                'username'      => 'adminbaak',
                'name'          => 'Superadmin BAAK',
                'asal_sekolah'  => 'STIE Pembangunan Tanjungpinang',
                'email'         => 'baak@stie-pembangunan.ac.id',
                'nomor_hp'      => '085264886677',
                'nomor_hp2'     => '085264886677',
                'passsword'     => '$2y$12$wakaDb5/1M21HnxooVmhx.CzkUsbpdO/N4dOHSJnZ/x5HMHFA8moK',
            ],
            [
                'user_id'       => 3225,
                // 'siakad_id'     => 3,
                'username'      => 'mfth12',
                'name'          => 'Superadmin Miftah',
                'asal_sekolah'  => 'STIE Pembangunan Tanjungpinang',
                'email'         => 'mfth12@google.com',
                'nomor_hp'      => '6281331847725',
                'nomor_hp2'     => '6281331847725',
                'passsword'     => '$2y$12$4NC3sIaMEQdL27DEgbsRdOQSThdKw1eonmJweFfX8XLhUvRdJqIP.',
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
                'user_id'       => 3227,
                'username'      => 'anto1974',
                'name'          => 'H. Sarianto, S.Ag',
                'asal_sekolah'  => 'SMK Negeri 1 Tanjungpinang',
                'email'         => 'sarianto7474@gmail.com',
                'nomor_hp'      => '08126197533',
                'nomor_hp2'     => '08126197533',
                'passsword'     => '$2y$12$FVnr/lVjd27Y6psYZn2QROk5Llu8JyzWw4P0m6xup0IRIRZgPmSsy',
                // 'passsword'     => 'anto1974',
            ],
            [
                'user_id'       => 3228,
                'username'      => 'gameover',
                'name'          => 'Mahar Anggoro',
                'asal_sekolah'  => 'SMA Pelita Nusantara Tanjungpinang',
                'email'         => 'goro1990.ag@gmail.com',
                'nomor_hp'      => '085272313800',
                'nomor_hp2'     => '085272313800',
                'passsword'     => '$2y$12$CyaJ4ayh0zNRm0OB7IqkgeZna78vqlNHxLHXRN.knhoyRlgOodtEK',
                // 'passsword'     => 'gameover',
            ],
            [
                'user_id'       => 3207,
                'username'      => 'hartono91',
                'name'          => 'Hartono',
                'asal_sekolah'  => 'SMKN 2 Tanjungpinang',
                'email'         => 'hartono961@guru.smk.belajar.id',
                'nomor_hp'      => '08127757411',
                'nomor_hp2'     => '08127757411',
                // 'passsword'     => '$2y$12$cFmKPiJrgf3EME.s11wesOpUjwwVnOHTCRhA.mBty6xX58xibxpca',
                'passsword'     => '$2y$12$8ZmmVthNHwITOFa32kRclubPoMLT7jEZKg96EwU1zgFvbJmpRbX/C',
                // 'passsword'     => 'hartono91',
            ],
            [
                'user_id'       => 3208,
                'username'      => 'ucueni',
                'name'          => 'Armaini Mardalena',
                'asal_sekolah'  => 'SMA Negeri 4 Tanjungpinang',
                'email'         => 'armaini3031@hmail.com',
                'nomor_hp'      => '081350101036',
                'nomor_hp2'     => '081350101036',
                // 'passsword'     => '$2y$12$SDTjERQMmyNGPrJP2braL.J54Lnqlb5MqumSPTVPlOnvJgNrilqMC',
                'passsword'     => '$2y$12$MWKvejhL1TtHfOF3Kh4XVuJFIvZlXD4GXyB1SDRxmp/DTSlImyKlC',
                // 'passsword'     => 'ucueni',
            ],
            [
                'user_id'       => 3209,
                'username'      => 'skatratpi',
                'name'          => 'Mutia Khairunnisya, A.Md.KL',
                'asal_sekolah'  => 'SMK Maitreyawira Tanjungpinang',
                'email'         => 'khairunnisya1697@gmail.com',
                'nomor_hp'      => '082386085226',
                'nomor_hp2'     => '082386085226',
                // 'passsword'     => '$2y$12$RJ2UWCAh9juB/IEFTVJZJe6Iza.rqtHRwa80sFcgAiXnmtcDdxCm.',
                'passsword'     => '$2y$12$uK2Uk.Inq99BUJmGGL0UoukJ2E7Q2o5TCnRPOJDL2EbIvmmUcSXee',
                // 'passsword'     => 'skatratpi',
            ],
            [
                'user_id'       => 3210,
                'username'      => 'ratna123456',
                'name'          => 'Ratna Wulandari',
                'asal_sekolah'  => 'SMAS Maitreyawira Tanjungpinang',
                'email'         => 'ratnawulandari432@gmail.com',
                'nomor_hp'      => '085364993104',
                'nomor_hp2'     => '085364993104',
                // 'passsword'     => '$2y$12$ayNw216k0IAnOP.SBjEUheDAWl7ZxnSSjVq3j6cx/gfddTz3Bhlee',
                'passsword'     => '$2y$12$NrFjp.SFU3SlBQgKQfiWQelDkfVLOCF4ll1dYMxdeCjwkCV341SRC',
                // 'passsword'     => 'ratna123456',
            ],
            [
                'user_id'       => 3211,
                'username'      => 'rahmani1',
                'name'          => 'Rahmani Nur Bayanti',
                'asal_sekolah'  => 'SMA Negeri 1 Tanjungpinang',
                'email'         => 'rahmaninurbayanti@gmail.com',
                'nomor_hp'      => '083809867801',
                'nomor_hp2'     => '083809867801',
                'passsword'     => '$2y$12$70NbBUtgtHR7BTvckA6rXeBuHR.6VD463C3BOeGPOYVlN6Vyym4.i',
                // 'passsword'     => 'rahmani1',
            ],
        ];


        foreach ($agenUsers as $userData) {
            $user = User::factory()->create([
                'user_id'       => $userData['user_id'],
                'username'      => $userData['username'],
                'name'          => $userData['name'],
                'asal_sekolah'  => $userData['asal_sekolah'],
                'email'         => $userData['email'],
                'nomor_hp'      => $userData['nomor_hp'],
                'nomor_hp2'     => $userData['nomor_hp2'] ?? $userData['nomor_hp'],
                // 'password'      => bcrypt($userData['passsword']),
                'password'      => $userData['passsword'],
            ]);
            $user->syncRoles([$roleAgen]);
        }

        foreach ($superadminUsers as $userData) {
            $user = User::factory()->create([
                'user_id'       => $userData['user_id'],
                'username'      => $userData['username'],
                'name'          => $userData['name'],
                'asal_sekolah'  => $userData['asal_sekolah'],
                'email'         => $userData['email'],
                'nomor_hp'      => $userData['nomor_hp'],
                'nomor_hp2'     => $userData['nomor_hp2'] ?? $userData['nomor_hp'],
                // 'password'      => bcrypt($userData['passsword']),
                'password'      => $userData['passsword'],
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
                // 'password'      => $userData['passsword'],
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
                // 'password'      => $userData['passsword'],
            ]);
            $user->syncRoles([$roleKeuangan]);
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

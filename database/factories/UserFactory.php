<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sekolah = [
            'SMA Negeri 1 Tanjungpinang',
            'SMA Negeri 2 Tanjungpinang',
            'SMA Negeri 3 Tanjungpinang',
            'SMA Negeri 4 Tanjungpinang',
            'SMA Swasta 1 Tanjungpinang',
            'SMA Swasta 2 Tanjungpinang',
            'MAN 1 Tanjungpinang',
            'SMK Negeri 1 Tanjungpinang',
            'SMK Negeri 2 Tanjungpinang',
            'SMA Plus Pembangunan',
        ];

        $nama = $this->faker->name();

        return [
            'siakad_id'         => null,
            'username'          => 'mitra' . $this->faker->unique()->numberBetween(100, 999),
            'password'          => bcrypt('123123'), // Default password
            'name'              => $nama,
            'asal_sekolah'      => $this->faker->randomElement($sekolah),
            'email'             => $this->faker->unique()->safeEmail(),
            'nomor_hp'          => '08' . $this->faker->numerify('##########'),
            'nomor_hp2'         => $this->faker->numerify('08##########'),
            'email_verified_at' => Carbon::now(),
            'about'             => $this->faker->sentence(),
            'default_role'      => 'mitra',
            'theme'             => 'default',
            'avatar'            => null,
            'status'            => 'active',
            'status_login'      => 'offline',
            'isdeleted'         => false,
            'last_logged_in'    => null,
            'last_synced_at'    => null,
            'remember_token'    => Str::random(10),
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ];
    }

    /**
     * State for user dengan status inactive
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * State untuk user yang sedang online
     */
    public function online(): static
    {
        return $this->state(fn(array $attributes) => [
            'status_login' => 'online',
            'last_logged_in' => Carbon::now()->subMinutes(rand(1, 60)),
        ]);
    }

    /**
     * State untuk user dengan siakad_id
     */
    public function withSiakad(): static
    {
        return $this->state(fn(array $attributes) => [
            'siakad_id' => 'SIK' . $this->faker->unique()->numberBetween(1000, 9999),
        ]);
    }

    /**
     * State untuk user dengan role tertentu
     */
    public function withRole(string $role): static
    {
        return $this->state(fn(array $attributes) => [
            'default_role' => $role,
        ]);
    }

    /**
     * State untuk user dengan password custom
     */
    public function withPassword(string $password): static
    {
        return $this->state(fn(array $attributes) => [
            'password' => bcrypt($password),
        ]);
    }

    /**
     * State untuk user yang dibuat di waktu tertentu
     */
    public function createdAt(Carbon $date): static
    {
        return $this->state(fn(array $attributes) => [
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }

    /**
     * State untuk user dengan data lengkap
     */
    public function complete(): static
    {
        return $this->state(fn(array $attributes) => [
            'about' => $this->faker->paragraph(),
            'nomor_hp2' => '08' . $this->faker->numerify('##########'),
            'last_logged_in' => Carbon::now()->subDays(rand(1, 30)),
            'last_synced_at' => Carbon::now()->subDays(rand(1, 15)),
        ]);
    }
}

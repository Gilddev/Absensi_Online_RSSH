<?php

namespace Database\Factories;
use Illuminate\Support\Facades\Hash;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Karyawan>
 */
class KaryawanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        static $nik = 1; // Mulai dari 1 hingga 100

        return [
            'nik' => $nik++, // NIK berurut 1-100
            'nama_lengkap' => $this->faker->name(),
            'jabatan' => $this->faker->randomElement(['Bidan', 'Perawat']),
            'kode_ruangan' => $this->faker->randomElement(['ANK', 'VK', 'UGD', 'NC', 'NFS', 'INT']),
            'jenis_kelamin' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
            'alamat' => $this->faker->randomElement(['Gorontalo', 'Bone Bolango']),
            'no_hp' => '081234567890',
            'password' => Hash::make('123'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

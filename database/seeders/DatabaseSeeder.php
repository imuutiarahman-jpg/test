<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Master Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'master_admin',
            'portal' => null,
        ]);

        User::factory()->create([
            'name' => 'Karyawan QMAS',
            'email' => 'qmas@example.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'portal' => 'qmas',
        ]);
    }
}

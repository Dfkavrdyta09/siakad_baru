<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'dosen@siakad.test'],
            [
                'name' => 'Dosen Contoh',
                'password' => Hash::make('password'),
                'role' => 'dosen',
            ]
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa@siakad.test'],
            [
                'name' => 'Mahasiswa Contoh',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
            ]
        );
    }
}





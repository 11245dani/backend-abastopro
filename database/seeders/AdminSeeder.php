<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Usuario::updateOrCreate(
            ['correo' => 'abastopro07@gmail.com'],
            [
                'nombre' => 'AbastoPro_Admin',
                'password' => Hash::make('abastopro07@:v'),
                'rol' => 'admin',
                'estado' => 'activo',
                'email_verified_at' => now(),
                'verification_token' => null,
            ]
        );
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Tienda;
use App\Models\Distribuidor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatosPruebaSeeder extends Seeder
{
    public function run()
    {
        // ======================= TENDEROS =========================
        
        $tenderos = [
            [
                'nombre' => 'Tendero Dani Estacio',
                'correo' => 'daniestacio96@gmail.com',
                'direccion' => 'Calle 123 #45-67',
                'telefono' => '3001112222',
            ],
            [
                'nombre' => 'Tendero Camilo Manosca',
                'correo' => 'camilo.manosca@correounivalle.edu.co',
                'direccion' => 'Carrera 89 #12-34',
                'telefono' => '3003334444',
            ],
        ];

        foreach ($tenderos as $data) {
            $usuario = Usuario::create([
                'nombre' => $data['nombre'],
                'correo' => $data['correo'],
                'password' => Hash::make('password'), // Contraseña: 'password'
                'rol' => 'tendero',
                'estado' => 'activo',
                'verification_token' => Str::random(60),
            ]);

            Tienda::create([
                'usuario_id' => $usuario->id,
                'nombre' => $data['nombre'],
                'direccion' => $data['direccion'],
                'telefono' => $data['telefono'],
            ]);
        }

        // =================== GESTORES DE DESPACHO ===================
        
        $gestores = [
            [
                'nombre' => 'Gestor Estiven Estacio',
                'correo' => 'daniestivenestaciotorres7@gmail.com',
                'direccion' => 'Avenida 1 #23-45',
                'telefono' => '3105556666',
                'nombre_empresa' => 'Distribuciones Estacio S.A.S.',
            ],
            [
                'nombre' => 'Gestor Dani Estacio UV',
                'correo' => 'dani.estacio@correounivalle.edu.co',
                'direccion' => 'Calle 67 #89-00',
                'telefono' => '3107778888',
                'nombre_empresa' => 'Distribuciones UV S.A.S.',
            ],
            [
                'nombre' => 'Gestor Mercado Compras',
                'correo' => 'mercadocompras14527@gmail.com',
                'direccion' => 'Carrera 10 #20-30',
                'telefono' => '3109990000',
                'nombre_empresa' => 'Mercado Compras LTDA',
            ],
        ];

        foreach ($gestores as $data) {
            $usuario = Usuario::create([
                'nombre' => $data['nombre'],
                'correo' => $data['correo'],
                'password' => Hash::make('password'), // Contraseña: 'password'
                'rol' => 'gestor_despacho',
                'estado' => 'activo',
                'verification_token' => Str::random(60),
            ]);

            Distribuidor::create([
                'usuario_id' => $usuario->id,
                'nombre_empresa' => $data['nombre_empresa'],
                'direccion' => $data['direccion'],
                'telefono' => $data['telefono'],
                'estado_autorizacion' => 'aprobado', // Directamente aprobado
            ]);
        }
    }
}

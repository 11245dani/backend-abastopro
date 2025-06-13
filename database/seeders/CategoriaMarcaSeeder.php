<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Marca;

class CategoriaMarcaSeeder extends Seeder
{
    public function run()
    {
        // =================== CATEGORÍAS ===================
        $categorias = [
            'Lácteos',
            'Bebidas',
            'Cereales',
            'Snacks',
            'Aseo Personal',
            'Aseo Hogar',
            'Carnes',
            'Panadería',
            'Frutas y Verduras',
            'Congelados',
            'Enlatados',
            'Pastas',
            'Salsas y Condimentos',
            'Aceites y Grasas',
            'Dulces y Chocolates',
            'Harinas y Panificados',
            'Café y Té',
            'Arroz y Legumbres',
            'Huevos',
            'Comida para Mascotas',
            'Electrodomésticos',
            'Limpieza Industrial',
            'Papel Higiénico y Desechables',
            'Bebidas Alcohólicas',
            'Ropa de Hogar',
            'Juguetería',
            'Cuidado del Bebé',
            'Cuidado de la Piel',
            'Cuidado del Cabello',
            'Productos Naturales'
        ];

        foreach ($categorias as $nombre) {
            Categoria::create(['nombre' => $nombre]);
        }

        // =================== MARCAS ===================
        $marcas = [
            'Colanta',
            'Postobón',
            'Alpina',
            'Coca-Cola',
            'Pepsi',
            'Doria',
            'Ramo',
            'Bimbo',
            'La Fazenda',
            'SuperClean',
            'Nestlé',
            'Zenu',
            'Rica',
            'La Constancia',
            'Maggi',
            'Corona',
            'Nutresa',
            'Luker',
            'Noel',
            'Jet',
            'Tosh',
            'Familia',
            'Scott',
            'Palmolive',
            'Protex',
            'Head & Shoulders',
            'Pantene',
            'Nivea',
            'Colgate',
            'Gillette'
        ];

        foreach ($marcas as $nombre) {
            Marca::create(['nombre' => $nombre]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Distribuidor;
use App\Models\Categoria;
use App\Models\Marca;

class ProductosSeeder extends Seeder
{
    public function run()
    {
        // Obtener todos los distribuidores (gestores de despacho)
        $distribuidores = Distribuidor::all();
        
        if ($distribuidores->count() < 3) {
            $this->command->error('Se necesitan al menos 3 distribuidores. Ejecuta primero el seeder de usuarios.');
            return;
        }

        // Obtener todas las categorías y marcas
        $categorias = Categoria::all();
        $marcas = Marca::all();

        if ($categorias->isEmpty() || $marcas->isEmpty()) {
            $this->command->error('Se necesitan categorías y marcas. Ejecuta primero CategoriaMarcaSeeder.');
            return;
        }

        // Array de productos base con nombres realistas
        $productosBase = [
            // Lácteos
            ['nombre' => 'Leche Entera', 'descripcion' => 'Leche entera pasteurizada 1 litro', 'precio_min' => 3500, 'precio_max' => 4500],
            ['nombre' => 'Yogurt Natural', 'descripcion' => 'Yogurt natural cremoso 200g', 'precio_min' => 2500, 'precio_max' => 3500],
            ['nombre' => 'Queso Campesino', 'descripcion' => 'Queso campesino fresco 500g', 'precio_min' => 8000, 'precio_max' => 12000],
            ['nombre' => 'Mantequilla', 'descripcion' => 'Mantequilla con sal 250g', 'precio_min' => 4500, 'precio_max' => 6500],
            ['nombre' => 'Crema de Leche', 'descripcion' => 'Crema de leche para cocinar 200ml', 'precio_min' => 3000, 'precio_max' => 4000],
            
            // Bebidas
            ['nombre' => 'Gaseosa Cola', 'descripcion' => 'Gaseosa cola 1.5 litros', 'precio_min' => 3000, 'precio_max' => 4500],
            ['nombre' => 'Agua Natural', 'descripcion' => 'Agua natural purificada 600ml', 'precio_min' => 1500, 'precio_max' => 2500],
            ['nombre' => 'Jugo de Naranja', 'descripcion' => 'Jugo de naranja natural 1 litro', 'precio_min' => 4500, 'precio_max' => 6500],
            ['nombre' => 'Cerveza Lager', 'descripcion' => 'Cerveza tipo lager 330ml', 'precio_min' => 2500, 'precio_max' => 3500],
            ['nombre' => 'Té Verde', 'descripcion' => 'Té verde en bolsitas x20', 'precio_min' => 3500, 'precio_max' => 5500],
            
            // Cereales
            ['nombre' => 'Avena en Hojuelas', 'descripcion' => 'Avena en hojuelas 500g', 'precio_min' => 3500, 'precio_max' => 5000],
            ['nombre' => 'Cereal de Maíz', 'descripcion' => 'Cereal crujiente de maíz 400g', 'precio_min' => 6000, 'precio_max' => 8500],
            ['nombre' => 'Granola Integral', 'descripcion' => 'Granola con frutos secos 350g', 'precio_min' => 8000, 'precio_max' => 12000],
            
            // Snacks
            ['nombre' => 'Papas Fritas', 'descripcion' => 'Papas fritas sabor natural 150g', 'precio_min' => 2500, 'precio_max' => 4000],
            ['nombre' => 'Maní Salado', 'descripcion' => 'Maní tostado y salado 200g', 'precio_min' => 3000, 'precio_max' => 4500],
            ['nombre' => 'Galletas Saladas', 'descripcion' => 'Galletas saladas integrales 200g', 'precio_min' => 2000, 'precio_max' => 3500],
            
            // Aseo Personal
            ['nombre' => 'Champú Anticaspa', 'descripcion' => 'Champú anticaspa 400ml', 'precio_min' => 8000, 'precio_max' => 12000],
            ['nombre' => 'Jabón Antibacterial', 'descripcion' => 'Jabón líquido antibacterial 250ml', 'precio_min' => 4000, 'precio_max' => 6500],
            ['nombre' => 'Pasta Dental', 'descripcion' => 'Pasta dental con flúor 100ml', 'precio_min' => 3500, 'precio_max' => 5500],
            ['nombre' => 'Desodorante Roll-on', 'descripcion' => 'Desodorante roll-on 50ml', 'precio_min' => 6000, 'precio_max' => 9000],
            
            // Aseo Hogar
            ['nombre' => 'Detergente Líquido', 'descripcion' => 'Detergente líquido para ropa 1L', 'precio_min' => 5000, 'precio_max' => 7500],
            ['nombre' => 'Limpiador Multiusos', 'descripcion' => 'Limpiador multiusos 500ml', 'precio_min' => 3500, 'precio_max' => 5500],
            ['nombre' => 'Papel Higiénico', 'descripcion' => 'Papel higiénico doble hoja x4', 'precio_min' => 4000, 'precio_max' => 6000],
            ['nombre' => 'Jabón en Polvo', 'descripcion' => 'Jabón en polvo para ropa 500g', 'precio_min' => 4500, 'precio_max' => 6500],
            
            // Carnes
            ['nombre' => 'Pollo Entero', 'descripcion' => 'Pollo entero fresco por kg', 'precio_min' => 7000, 'precio_max' => 9500],
            ['nombre' => 'Carne de Res', 'descripcion' => 'Carne de res para asar por kg', 'precio_min' => 15000, 'precio_max' => 22000],
            ['nombre' => 'Salchicha de Pollo', 'descripcion' => 'Salchicha de pollo 500g', 'precio_min' => 5000, 'precio_max' => 7500],
            
            // Panadería
            ['nombre' => 'Pan Integral', 'descripcion' => 'Pan integral tajado 500g', 'precio_min' => 3500, 'precio_max' => 5000],
            ['nombre' => 'Croissant', 'descripcion' => 'Croissant de mantequilla x6', 'precio_min' => 4000, 'precio_max' => 6500],
            ['nombre' => 'Galletas Dulces', 'descripcion' => 'Galletas dulces surtidas 300g', 'precio_min' => 3000, 'precio_max' => 4500],
            
            // Frutas y Verduras
            ['nombre' => 'Plátano Maduro', 'descripcion' => 'Plátano maduro por kg', 'precio_min' => 2500, 'precio_max' => 3500],
            ['nombre' => 'Tomate Chonto', 'descripcion' => 'Tomate chonto fresco por kg', 'precio_min' => 3000, 'precio_max' => 4500],
            ['nombre' => 'Cebolla Cabezona', 'descripcion' => 'Cebolla cabezona por kg', 'precio_min' => 2000, 'precio_max' => 3500],
            ['nombre' => 'Zanahoria', 'descripcion' => 'Zanahoria fresca por kg', 'precio_min' => 2500, 'precio_max' => 3800],
            
            // Congelados
            ['nombre' => 'Helado de Vainilla', 'descripcion' => 'Helado de vainilla 1 litro', 'precio_min' => 8000, 'precio_max' => 12000],
            ['nombre' => 'Verduras Mixtas', 'descripcion' => 'Verduras mixtas congeladas 500g', 'precio_min' => 4500, 'precio_max' => 6500],
            
            // Enlatados
            ['nombre' => 'Atún en Aceite', 'descripcion' => 'Atún en aceite 170g', 'precio_min' => 3500, 'precio_max' => 5000],
            ['nombre' => 'Sardinas en Salsa', 'descripcion' => 'Sardinas en salsa de tomate 425g', 'precio_min' => 4000, 'precio_max' => 6000],
            ['nombre' => 'Frijoles Rojos', 'descripcion' => 'Frijoles rojos en conserva 400g', 'precio_min' => 2500, 'precio_max' => 4000],
            
            // Pastas
            ['nombre' => 'Espagueti', 'descripcion' => 'Espagueti de trigo 500g', 'precio_min' => 2500, 'precio_max' => 4000],
            ['nombre' => 'Macarrones', 'descripcion' => 'Macarrones de sémola 500g', 'precio_min' => 2800, 'precio_max' => 4200],
            
            // Salsas y Condimentos
            ['nombre' => 'Salsa de Tomate', 'descripcion' => 'Salsa de tomate natural 200g', 'precio_min' => 2000, 'precio_max' => 3500],
            ['nombre' => 'Mayonesa', 'descripcion' => 'Mayonesa tradicional 400g', 'precio_min' => 3500, 'precio_max' => 5500],
            ['nombre' => 'Sal de Mesa', 'descripcion' => 'Sal de mesa refinada 500g', 'precio_min' => 1500, 'precio_max' => 2500],
            
            // Aceites y Grasas
            ['nombre' => 'Aceite de Girasol', 'descripcion' => 'Aceite de girasol 1 litro', 'precio_min' => 5000, 'precio_max' => 7500],
            ['nombre' => 'Aceite de Oliva', 'descripcion' => 'Aceite de oliva extra virgen 500ml', 'precio_min' => 15000, 'precio_max' => 25000],
            
            // Dulces y Chocolates
            ['nombre' => 'Chocolate en Barra', 'descripcion' => 'Chocolate con leche 100g', 'precio_min' => 2500, 'precio_max' => 4000],
            ['nombre' => 'Caramelos Surtidos', 'descripcion' => 'Caramelos surtidos 200g', 'precio_min' => 2000, 'precio_max' => 3500],
            
            // Harinas y Panificados
            ['nombre' => 'Harina de Trigo', 'descripcion' => 'Harina de trigo todo uso 1kg', 'precio_min' => 2500, 'precio_max' => 4000],
            ['nombre' => 'Levadura en Polvo', 'descripcion' => 'Levadura en polvo 100g', 'precio_min' => 2000, 'precio_max' => 3500],
            
            // Café y Té
            ['nombre' => 'Café Molido', 'descripcion' => 'Café molido tradicional 500g', 'precio_min' => 8000, 'precio_max' => 12000],
            ['nombre' => 'Té Negro', 'descripcion' => 'Té negro en bolsitas x25', 'precio_min' => 4000, 'precio_max' => 6500],
            
            // Arroz y Legumbres
            ['nombre' => 'Arroz Blanco', 'descripcion' => 'Arroz blanco de primera 1kg', 'precio_min' => 3000, 'precio_max' => 4500],
            ['nombre' => 'Lentejas', 'descripcion' => 'Lentejas secas 500g', 'precio_min' => 3500, 'precio_max' => 5500],
            
            // Huevos
            ['nombre' => 'Huevos Rojos', 'descripcion' => 'Huevos rojos tamaño A x30', 'precio_min' => 8000, 'precio_max' => 12000],
            
            // Comida para Mascotas
            ['nombre' => 'Comida para Perros', 'descripcion' => 'Alimento seco para perros 2kg', 'precio_min' => 12000, 'precio_max' => 18000],
            ['nombre' => 'Comida para Gatos', 'descripcion' => 'Alimento húmedo para gatos 400g', 'precio_min' => 4000, 'precio_max' => 6500],
        ];

        $this->command->info('Creando 200 productos...');
        
        $productosCreados = 0;
        $distribuidorIndex = 0;
        
        // Crear 200 productos distribuyéndolos equitativamente
        while ($productosCreados < 200) {
            $productoBase = $productosBase[array_rand($productosBase)];
            $categoria = $categorias->random();
            $marca = $marcas->random();
            $distribuidor = $distribuidores[$distribuidorIndex % $distribuidores->count()];
            
            // Generar variación en el nombre para evitar duplicados
            $variaciones = ['Premium', 'Especial', 'Tradicional', 'Clásico', 'Natural', 'Familiar', 'Grande', 'Económico'];
            $variacion = $variaciones[array_rand($variaciones)];
            $nombreUnico = $productoBase['nombre'] . ' ' . $variacion . ' - ' . $marca->nombre;
            
            Producto::create([
                'distribuidor_id' => $distribuidor->id,
                'nombre' => $nombreUnico,
                'descripcion' => $productoBase['descripcion'] . ' - Marca: ' . $marca->nombre,
                'imagen_url' => null, // Sin imagen por defecto
                'precio' => rand($productoBase['precio_min'], $productoBase['precio_max']),
                'stock' => rand(10, 100), // Stock aleatorio entre 10 y 100
                'categoria_id' => $categoria->id,
                'marca_id' => $marca->id,
                'estado' => 'activo',
            ]);
            
            $productosCreados++;
            $distribuidorIndex++;
            
            // Mostrar progreso cada 50 productos
            if ($productosCreados % 50 == 0) {
                $this->command->info("Creados {$productosCreados} productos...");
            }
        }
        
        // Mostrar estadísticas finales
        $this->command->info('✅ Productos creados exitosamente!');
        $this->command->info('📊 Distribución por gestor:');
        
        foreach ($distribuidores as $distribuidor) {
            $cantidadProductos = $distribuidor->productos()->count();
            $empresa = $distribuidor->nombre_empresa;
            $this->command->info("   • {$empresa}: {$cantidadProductos} productos");
        }
        
        $this->command->info("📦 Total de productos en el sistema: " . Producto::count());
    }
}
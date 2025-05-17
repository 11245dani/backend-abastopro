<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{
    public function index()
    {
        $distribuidor = Auth::user()->distribuidor;

        if (!$distribuidor) {
            return response()->json(['error' => 'Distribuidor no encontrado'], 403);
        }

        return response()->json(
        $distribuidor->productos()
        ->where('estado', 'activo')
        ->with(['categoria', 'marca'])
        ->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
            'imagen_url' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'marca_id' => 'required|exists:marcas,id',
        ]);

        $distribuidor = auth()->user()->distribuidor;

        if (!$distribuidor) {
        return response()->json(['error' => 'Distribuidor no encontrado'], 404);
     }

        if (!$distribuidor || $distribuidor->estado_autorizacion !== 'aprobado') {
            return response()->json(['error' => 'No autorizado para registrar productos'], 403);
        }

        $producto = Producto::create([
        'distribuidor_id' => $distribuidor->id,
        'nombre' => $request->nombre,
        'descripcion' => $request->descripcion,
        'imagen_url' => $request->imagen_url,
        'precio' => $request->precio,
        'stock' => $request->stock,
        'categoria_id' => $request->categoria_id,
        'marca_id' => $request->marca_id,
     ]);

        return response()->json($producto, 201);
    }

    public function update(Request $request, Producto $producto)
    {
        $distribuidor = Auth::user()->distribuidor;

        if ($producto->distribuidor_id !== $distribuidor->id) {
            return response()->json(['error' => 'No autorizado para editar este producto'], 403);
        }

        $request->validate([
            'nombre' => 'sometimes|required|string',
            'descripcion' => 'nullable|string',
            'imagen_url' => 'nullable|string',
            'precio' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'categoria_id' => 'sometimes|required|exists:categorias,id',
            'marca_id' => 'sometimes|required|exists:marcas,id',
        ]);

        $producto->update($request->only([
            'nombre', 'descripcion', 'imagen_url', 'precio', 'stock', 'categoria_id', 'marca_id'
        ]));

        return response()->json($producto);
    }

public function destroy(Producto $producto)
{
    $distribuidor = Auth::user()->distribuidor;

    if ($producto->distribuidor_id !== $distribuidor->id) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    $producto->estado = 'inactivo';
    $producto->save();

    return response()->json(['message' => 'Producto desactivado']);
}

public function listarDisponibles(Request $request)
{
    $query = Producto::where('estado', 'activo')
        ->with([
            'categoria:id,nombre',
            'marca:id,nombre',
            'distribuidor:id,nombre_empresa'
        ]);

    // Filtro por búsqueda (nombre o descripción)
    if ($request->filled('busqueda')) {
        $busqueda = $request->input('busqueda');
        $query->where(function ($q) use ($busqueda) {
            $q->where('nombre', 'like', "%$busqueda%")
              ->orWhere('descripcion', 'like', "%$busqueda%");
        });
    }

    // Filtro por categoría
    if ($request->filled('categoria_id')) {
        $query->where('categoria_id', $request->categoria_id);
    }

    // Filtro por marca
    if ($request->filled('marca_id')) {
        $query->where('marca_id', $request->marca_id);
    }

    // Filtro por distribuidor
    if ($request->filled('distribuidor_id')) {
        $query->where('distribuidor_id', $request->distribuidor_id);
    }

    // Filtro por rango de precios
    if ($request->filled('precio_min')) {
        $query->where('precio', '>=', $request->precio_min);
    }

    if ($request->filled('precio_max')) {
        $query->where('precio', '<=', $request->precio_max);
    }

    // Obtener resultados
    $productos = $query->get()->map(function ($producto) {
        return [
            'id' => $producto->id,
            'nombre' => $producto->nombre,
            'descripcion' => $producto->descripcion,
            'precio' => $producto->precio,
            'stock' => $producto->stock,
            'categoria' => $producto->categoria->nombre ?? null,
            'marca' => $producto->marca->nombre ?? null,
            'distribuidor' => $producto->distribuidor->nombre_empresa ?? null,
            'imagen_url' => $producto->imagen_url ?? null,
        ];
    });

    return response()->json($productos);
}


}

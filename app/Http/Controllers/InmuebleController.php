<?php

namespace App\Http\Controllers;

use App\Models\Inmueble;
use Illuminate\Http\Request;

class InmuebleController extends Controller
{
    // Listar todos los inmuebles (para el catálogo general)
    public function index()
    {
        return response()->json(Inmueble::all(), 200);
    }

    // Crear un nuevo inmueble
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'arrendador_id' => 'required|integer',
            'nombre'        => 'required|string|max:255',
            'tipo'          => 'required|string',
            'direccion'     => 'required|string',
            'ciudad'        => 'required|string',
            'barrio'        => 'nullable|string',
            'estrato'       => 'nullable|integer',
            'imagen_url'    => 'nullable|string',
        ]);

        $inmueble = Inmueble::create($validatedData);

        return response()->json($inmueble, 201);
    }

    // Ver un inmueble específico
    public function show(int $id)
    {
        $inmueble = Inmueble::where('id_inmueble', $id)->first();

        if (!$inmueble) {
            return response()->json(['message' => 'Inmueble no encontrado'], 404);
        }

        return response()->json($inmueble, 200);
    }

    // Actualizar inmueble
    public function update(Request $request, int $id)
    {
        $inmueble = Inmueble::where('id_inmueble', $id)->first();

        if (!$inmueble) {
            return response()->json(['message' => 'Inmueble no encontrado'], 404);
        }

        $validatedData = $request->validate([
            'arrendador_id' => 'sometimes|integer',
            'nombre'        => 'sometimes|string|max:255',
            'tipo'          => 'sometimes|string',
            'direccion'     => 'sometimes|string',
            'ciudad'        => 'sometimes|string',
            'barrio'        => 'nullable|string',
            'estrato'       => 'nullable|integer',
            'imagen_url'    => 'nullable|string',
        ]);

        $inmueble->update($validatedData);

        return response()->json($inmueble, 200);
    }

    // Eliminar inmueble
    public function destroy(int $id)
    {
        $inmueble = Inmueble::where('id_inmueble', $id)->first();

        if (!$inmueble) {
            return response()->json(['message' => 'Inmueble no encontrado'], 404);
        }

        $inmueble->delete();

        return response()->json(['message' => 'Inmueble eliminado con éxito'], 200);
    }
}
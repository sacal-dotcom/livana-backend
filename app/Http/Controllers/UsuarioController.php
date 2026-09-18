<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User; // O el modelo que uses para tus usuarios (ej. Usuario)

class UsuarioController extends Controller
{
    /**
     * READ: Obtiene la información del perfil del usuario autenticado.
     */
    public function perfil(Request $request)
    {
        $usuario = $request->user();

        return response()->json([
            'usuario' => [
                'nombres'  => $usuario->nombres ?? $usuario->name,
                'apellidos' => $usuario->apellidos ?? '',
                'email'     => $usuario->email,
                'telefono'  => $usuario->telefono ?? '',
            ]
        ], 200);
    }

    /**
     * UPDATE: Actualiza nombres, apellidos, teléfono y correo electrónico.
     */
    public function actualizar(Request $request)
    {
        $usuario = $request->user();

        // Validaciones con la regla unique ignorando el ID actual
        $datosValidados = $request->validate([
            'nombres'   => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono'  => 'nullable|string|max:20',
            'email'     => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],
        ]);

        // Mapeo y actualización de datos
        $usuario->fill([
            'nombres'   => $datosValidados['nombres'],
            'apellidos' => $datosValidados['apellidos'],
            'email'     => $datosValidados['email'],
            'telefono'  => $datosValidados['telefono'] ?? null,
        ]);

        $usuario->save();

        return response()->json([
            'message' => 'Perfil y correo electrónico actualizados con éxito.',
            'usuario' => $usuario
        ], 200);
    }

    /**
     * READ: Consulta el estado real de la cuenta y el último timestamp de uso.
     */
    public function estadoConexion(Request $request)
    {
        $usuario = $request->user();

        // Obtiene la última fecha/hora registrada (usando updated_at o el campo de tu preferencia)
        $ultimaConexion = $usuario->updated_at ?? now();

        return response()->json([
            'activo'       => $usuario->activo ?? true,
            'ultima_fecha' => $ultimaConexion->format('d/m/Y'),
            'ultima_hora'  => $ultimaConexion->format('h:i A'),
        ], 200);
    }

    /**
     * DELETE: Desactiva o elimina la cuenta del usuario.
     */
    public function eliminar(Request $request)
    {
        $usuario = $request->user();

        // Si usas borrado lógico (SoftDelete) o cambio de estado:
        if (isset($usuario->activo)) {
            $usuario->activo = false;
            $usuario->save();
        } else {
            $usuario->delete();
        }

        // Opcional: Revocar tokens de autenticación
        if (method_exists($usuario, 'tokens')) {
            $usuario->tokens()->delete();
        }

        return response()->json([
            'message' => 'La cuenta ha sido eliminada o desactivada correctamente.'
        ], 200);
    }
}
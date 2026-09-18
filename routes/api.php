<?php

use App\Http\Controllers\InmuebleController;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

/*
|--------------------------------------------------------------------------
| API Routes - Livana (Integrado con Schema SQL Real)
|--------------------------------------------------------------------------
*/

// 1. Registro de Usuario
Route::post('/registro', function (Request $request) {
    $request->validate([
        'nombres'   => 'required|string',
        'apellidos' => 'required|string',
        'email'     => 'required|email|unique:usuarios,email',
        'telefono'  => 'required|string',
        'password'  => 'required|string',
    ]);

    $usuario = Usuario::create([
        'nombres'   => $request->nombres,
        'apellidos' => $request->apellidos,
        'email'     => $request->email,
        'telefono'  => $request->telefono,
        'password'  => Hash::make($request->password),
        'estado'    => 1,
    ]);

    return response()->json([
        'message' => 'Usuario registrado con éxito',
        'usuario' => $usuario,
        'user'    => $usuario
    ], 201);
});

// 2. Login
Route::post('/login', function (Request $request) {
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    $usuario = Usuario::where('email', $request->email)->first();

    if (!$usuario || !Hash::check($request->password, $usuario->password)) {
        return response()->json([
            'message' => 'Correo o contraseña incorrectos.'
        ], 401);
    }

    return response()->json([
        'message' => 'Inicio de sesión exitoso',
        'usuario' => $usuario,
        'user'    => $usuario
    ], 200);
});

// 3. Activación de Perfil Arrendador
Route::post('/activar-arrendador', function (Request $request) {
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    $usuario = Usuario::where('email', $request->email)->first();

    if (!$usuario || !Hash::check($request->password, $usuario->password)) {
        return response()->json([
            'message' => 'Credenciales incorrectas para activar perfil.'
        ], 401);
    }

    return response()->json([
        'message' => 'Perfil de Arrendador activado con éxito.',
        'usuario' => $usuario,
        'user'    => $usuario
    ], 200);
});

// --------------------------------------------------------------------------
// 4. Módulo de Configuración / Perfil (CRUD Usuario)
// --------------------------------------------------------------------------

// 4.1 READ: Obtener Perfil del Usuario
Route::get('/usuario/perfil', function (Request $request) {
    $email = $request->query('email');

    if (!$email) {
        return response()->json(['message' => 'El correo es requerido para consultar el perfil.'], 400);
    }

    $usuario = Usuario::where('email', $email)->first();

    if (!$usuario) {
        return response()->json(['message' => 'Usuario no encontrado en la base de datos.'], 404);
    }

    $data = [
        'id'         => $usuario->id_usuario,
        'id_usuario' => $usuario->id_usuario,
        'nombres'    => $usuario->nombres,
        'apellidos'  => $usuario->apellidos,
        'email'      => $usuario->email,
        'telefono'   => $usuario->telefono,
        'estado'     => $usuario->estado,
    ];

    return response()->json([
        'usuario' => $data,
        'user'    => $data
    ], 200);
});

// 4.2 UPDATE: Actualizar Perfil y Correo
Route::put('/usuario/actualizar', function (Request $request) {
    $userId = $request->input('id') ?? $request->input('id_usuario');

    if (!$userId) {
        return response()->json(['message' => 'Identificador de usuario faltante.'], 422);
    }

    $request->validate([
        'nombres'   => 'required|string|max:100',
        'apellidos' => 'required|string|max:100',
        'telefono'  => 'nullable|string|max:20',
        'email'     => [
            'required',
            'email',
            Rule::unique('usuarios', 'email')->ignore($userId, 'id_usuario'),
        ],
    ]);

    $usuario = Usuario::find($userId);
    if (!$usuario) {
        return response()->json(['message' => 'Usuario no encontrado.'], 404);
    }

    $usuario->update([
        'nombres'   => $request->nombres,
        'apellidos' => $request->apellidos,
        'email'     => $request->email,
        'telefono'  => $request->telefono,
    ]);

    $data = [
        'id'         => $usuario->id_usuario,
        'id_usuario' => $usuario->id_usuario,
        'nombres'    => $usuario->nombres,
        'apellidos'  => $usuario->apellidos,
        'email'      => $usuario->email,
        'telefono'   => $usuario->telefono,
        'estado'     => $usuario->estado,
    ];

    return response()->json([
        'message' => 'Información actualizada con éxito',
        'usuario' => $data,
        'user'    => $data
    ], 200);
});

// 4.3 READ: Estado e Historial Real de Conexión
Route::get('/usuario/estado-conexion', function (Request $request) {
    $email = $request->query('email');

    if (!$email) {
        return response()->json(['message' => 'El correo es requerido.'], 400);
    }

    $usuario = Usuario::where('email', $email)->first();

    if (!$usuario) {
        return response()->json(['message' => 'Usuario no encontrado.'], 404);
    }

    // Convertir la fecha real a la zona horaria de Colombia
    $fechaObj = $usuario->created_at 
        ? Carbon::parse($usuario->created_at)->setTimezone('America/Bogota') 
        : now()->setTimezone('America/Bogota');

    return response()->json([
        'activo'       => (bool) ($usuario->estado == 1),
        'ultima_fecha' => $fechaObj->format('d/m/Y'),
        'ultima_hora'  => $fechaObj->format('h:i A'),
    ], 200);
});

// 4.4 DELETE: Desactivar o Eliminar Cuenta
Route::delete('/usuario/eliminar', function (Request $request) {
    $userId = $request->input('id') ?? $request->input('id_usuario');

    if (!$userId) {
        return response()->json(['message' => 'Identificador de usuario faltante.'], 422);
    }

    $usuario = Usuario::find($userId);
    if (!$usuario) {
        return response()->json(['message' => 'Usuario no encontrado.'], 404);
    }

    $usuario->delete();

    return response()->json([
        'message' => 'Cuenta eliminada correctamente.'
    ], 200);
});

// 5. Rutas para Inmuebles (CRUD Completo)
Route::apiResource('inmuebles', InmuebleController::class);
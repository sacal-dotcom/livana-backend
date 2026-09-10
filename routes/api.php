<?php

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Livana
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
        'nombres'    => $request->nombres,
        'apellidos'  => $request->apellidos,
        'email'      => $request->email,
        'telefono'   => $request->telefono,
        'password'   => $request->password,
        'rol_activo' => 'arrendatario',
        'estado'     => 1,
    ]);

    return response()->json([
        'message' => 'Usuario registrado con éxito',
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

    // Actualización de la columna real en la base de datos
    $usuario->rol_activo = 'arrendador';
    $usuario->save();

    return response()->json([
        'message' => 'Perfil de Arrendador activado con éxito.',
        'user'    => $usuario
    ], 200);
});
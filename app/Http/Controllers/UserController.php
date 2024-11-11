<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    
    public function store(UserRequest $request)
    {
        try {
            // Crear el usuario
            $user = User::create($request->all());

            // Crear token de acceso
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'message' => 'Usuario registrado exitosamente',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (QueryException $e) {
            Log::error("Error creando el usuario.: " . $e->getMessage());
            return response()->json(['error' => 'Error en el registro de usuario.'], 500);
        }
    }
}

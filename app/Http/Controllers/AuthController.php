<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{

    // Funcion para inicio de sesion con email y contraseña
    public function login(Request $request)
    {

        // Verificar las credenciales
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'errors' => [
                    'email' => ['Credenciales incorrectas.']
                ],
            ], 401);
        }
        // Crear un token de acceso
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => $user,
            'token' => $token,
        ]);
    }

    // Cerrar sesión, elimina el token de acceso del usuario (header: Bearer Token)
    public function logout(Request $request)
    {
        try {
            // Obtener el Bearer Token del encabezado
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['error' => 'Solicitud no valida. (Token no disponible)'], 401);
            }
            
            // Obtener el token de acceso del usuario de la BD
            $accessToken = PersonalAccessToken::findToken($token);

            if (!$accessToken) {
                return response()->json(['error' => 'Token no válido'], 401);
            }

            $accessToken->tokenable;

            // Eliminar el token
            $accessToken->delete();

            return response()->json(['message' => 'Cierre de sesión exitoso.'], 200);
        } catch (\Exception $e) {
            Log::error('Error al cerrar sesión: ' . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al cerrar sesión. ' . $e->getMessage()], 500);
        }
    }
}

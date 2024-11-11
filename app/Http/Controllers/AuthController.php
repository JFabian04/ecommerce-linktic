<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
   
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

   
    public function logout(Request $request)
    {
        // Verificar si el usuario está autenticado
        $user = Auth::guard('sanctum')->user();

        // Si no está autenticado, devolver un error
        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        // Revocar el token actual
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Cierre de sesión exitoso.'], 200);
    }
}

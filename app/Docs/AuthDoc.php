<?php

namespace App\Docs;

use OpenApi\Annotations as OA;


class AuthDoc
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Iniciar sesión",
     *     description="Autentica al usuario y devuelve un token de acceso",
     *     tags={"Autenticación"},
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", format="email", example="usuario@ejemplo.com", description="Correo electrónico del usuario"),
     *             @OA\Property(property="password", type="string", example="contraseña123", description="Contraseña del usuario")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Inicio de sesión exitoso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Inicio de sesión exitoso"),
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", example="1|abc123def456ghi789")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=401,
     *         description="Credenciales incorrectas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array",
     *                     @OA\Items(type="string", example="Credenciales incorrectas.")
     *                 )
     *             )
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="Content-Type",
     *         in="header",
     *         required=true,
     *         description="application/json",
     *         @OA\Schema(type="string")
     *     )
     * )
     */
    public function login() {}


    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Cerrar sesión",
     *     description="Cierra la sesión del usuario autenticado y revoca el token actual",
     *     tags={"Autenticación"},
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Cierre de sesión exitoso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Cierre de sesión exitoso.")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="No autenticado")
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         description="Token de autenticación en formato Bearer",
     *         @OA\Schema(type="string", example="Bearer 1|abc123def456ghi789")
     *     )
     * )
     */
    public function logout() {}
}

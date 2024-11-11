<?php

namespace App\Docs;

use OpenApi\Annotations as OA;


/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="Usuario",
 *     description="Modelo de usuario",
 *     required={"id", "name", "email"},
 * 
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="ID único del usuario"
 *     ),
 * 
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="Juan Pérez",
 *         description="Nombre del usuario"
 *     ),
 * 
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         example="usuario@ejemplo.com",
 *         description="Correo electrónico del usuario"
 *     ),
 * 
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2024-11-10T14:34:56Z",
 *         description="Fecha de creación del usuario"
 *     ),
 * 
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2024-11-10T15:12:34Z",
 *         description="Fecha de última actualización del usuario"
 *     )
 * )
 */
class UserDoc
{
    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Registrar nuevo usuario",
     *     description="Crea un nuevo usuario y devuelve un token de acceso",
     *     tags={"Usuarios"},
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="email", type="string", format="email", example="usuario@ejemplo.com"),
     *             @OA\Property(property="password", type="string", example="contraseña123"),
     *             @OA\Property(property="password_confirmation", type="string", example="contraseña123")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=201,
     *         description="Usuario registrado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Usuario registrado exitosamente"),
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", example="1|abc123def456ghi789")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=500,
     *         description="Error en el registro de usuario",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Error en el registro de usuario.")
     *         )
     *     )
     * )
     */
    public function store() {}
}

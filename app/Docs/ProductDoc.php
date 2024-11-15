<?php

namespace App\Docs;

use OpenApi\Annotations as OA;


/**
 * Informacioón del MODELO
 * 
 * @OA\Components(
 *     @OA\Schema(
 *         schema="Product",
 *         type="object",
 *         required={"name", "price"},
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Laptop"),
 *         @OA\Property(property="price", type="number", format="float", example=1500.50),
 *         @OA\Property(property="quantity", type="integer", example=10)
 *     )
 * )
 */

class ProductDoc
{
    /**
     * Función index()
     * 
     * @OA\Get(
     *     path="/api/products",
     *     summary="Obtener todos los productos",
     *     tags={"Productos"},
     *     description="Retorna una lista de todos los productos",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de productos",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al obtener productos"
     *     )
     * )
     */
    public function index(){}
    

    /**
     * Función store()
     * 
     * @OA\Post(
     *     path="/api/products",
     *     summary="Crear un nuevo producto",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     description="Crear un nuevo producto y retorna un estado 201",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="quantity", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Producto registrado correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la solicitud"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al crear el producto"
     *     )
     * )
     */
    public function store(){}


    /**
     * Función show()
     * 
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Obtener producto por ID",
     *     tags={"Productos"},
     *     description="Retorne el objeto del prodcuto obtenido por ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data del producto obetnido",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Productono encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro al obtener el producto"
     *     )
     * )
     */
    public function show(){}


    /**
     * Función update()
     * 
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Actualizar producto por ID",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     description="Actualizar un producto por refereciado por su ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="quantity", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto actualizado correctamente.",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encotrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al actualizar producto"
     *     )
     * )
     */
    public function update(){}


    /**
     * 
     * @OA\Patch(
     *     path="/api/products/{id}/status",
     *     summary="Actualizar estado de producto por ID",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     description="Activa/Inactiva un producto referenciado por su ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado del producto: Activo/Inactivo",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Estado del producto: Activo/Inactivo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al cambiar el estado del prodcuto"
     *     )
     * )
     */
    public function updateStatus(){}
}

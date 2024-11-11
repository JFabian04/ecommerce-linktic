<?php

namespace App\Docs;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ProductImage",
 *     type="object",
 *     required={"id", "product_id", "image_url"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID de la imagen del producto",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="product_id",
 *         type="integer",
 *         description="ID del producto al que pertenece la imagen",
 *         example=10
 *     ),
 *     @OA\Property(
 *         property="image_url",
 *         type="string",
 *         description="URL de la imagen del producto",
 *         example="https://example.com/images/product1.jpg"
 *     )
 * )
 */
class ProductImageDoc
{

    /**
     * Cargar una nueva imagen para un producto.
     *
     * @OA\Post(
     *     path="/api/products/images",
     *     summary="Cargar imagen para un producto",
     *     operationId="uploadProductImage",
     *     tags={"Productos"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Imagen para cargar",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"image", "product_id"},
     *                 @OA\Property(property="image", type="string", format="binary", description="Imagen del producto"),
     *                 @OA\Property(property="product_id", type="integer", description="ID del producto")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Imagen cargada con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Imagen cargada con éxito."),
     *             @OA\Property(property="image", type="object", ref="#/components/schemas/ProductImage")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Solicitud incorrecta")
     * )
     */
    public function store() {}


    /**
     * Eliminar una imagen de un producto.
     *
     * @OA\Delete(
     *     path="/api/products/images/{id}",
     *     summary="Eliminar imagen de un producto",
     *     operationId="deleteProductImage",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la imagen",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Imagen eliminada con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Imagen eliminada con éxito.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Imagen no encontrada")
     * )
     */

    public function destroy(){}

}

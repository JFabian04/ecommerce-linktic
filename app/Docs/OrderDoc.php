<?php

namespace App\Docs;

use OpenApi\Annotations as OA;


/**
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(
 *         property="products",
 *         type="array",
 *         @OA\Items(type="object",
 *             @OA\Property(property="product_id", type="integer", example=1),
 *             @OA\Property(property="quantity", type="integer", example=2)
 *         )
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-09T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-09T12:34:56Z")
 * )
 */
class OrderDoc
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Obtener todas las ordenes",
     *     tags={"Productos"},
     *     tags={"Ordenes"},
     *     description="Retorna la lista de ordenes",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de ordened",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Order"))
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error listando la información."
     *     )
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Crear nueva orden",
     *     tags={"Ordenes"},
     *     description="Crear una nueva orden con el ID del usuario y los productos (ID, quantity)",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="products", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="product_id", type="integer", example=1),
     *                 @OA\Property(property="quantity", type="integer", example=2)
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tu orden ha sido realizada",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Petición invalida"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error registrando la orden"
     *     )
     * )
     */
    public function store() {}


    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Obtener un order",
     *     tags={"Ordenes"},
     *     description="Retorna la ifnromación de una orde especfica por ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order details",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Orden no encontrada"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error obteniendo la orden"
     *     )
     * )
     */
    public function show() {}

    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="ELiminar order",
     *     tags={"Ordenes"},
     *     description="Elimina una orden permanentemente por su ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted successfully",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Order deleted successfully"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Orden no encotrada"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error eliminado la order"
     *     )
     * )
     */
    public function destroy() {}


    /**
     * @OA\Patch(
     *     path="/orders/{id}/status",
     *     summary="Actualizar el estado de las ordenes",
     *     tags={"Ordenes"},
     *     description="Actualiza el estado de la orden (entregado, cancelado)",
     *     operationId="updateOrderStatus",
     *     @OA\Parameter(
     *         name="orderId",
     *         in="path",
     *         required=true,
     *         description="Id de la orden a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Nuevo estado para la orden.",
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 description="Estados para la orden  'entregado', 'cancelado'.",
     *                 enum={"entregado", "cancelado"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado de la orden actualizado.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Estado de la orden actualizado"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="EL estado solicitado no es válido",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Estado no valido.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encotró la ordern",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Estado de la orden actualizado")
     *         )
     *     )
     * )
     */
    public function updateStatus() {}


    /**
     * @OA\Post(
     *     path="/api/report/orders",
     *     summary="Generar reporte de órdenes en Excel",
     *     description="Genera un archivo Excel con las órdenes en un rango de fechas especificado. Retorna la URL de descarga del archivo.",
     *     tags={"Ordenes"},
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-11-04", description="Fecha de inicio del rango de búsqueda (YYYY-MM-DD)"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2024-11-11", description="Fecha de fin del rango de búsqueda (YYYY-MM-DD)")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Archivo generado con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="El archivo ha sido generado correctamente."),
     *             @OA\Property(property="download_url", type="string", example="http://localhost/storage/order/reports/orders_2024_11_04.xlsx", description="URL para descargar el archivo")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=500,
     *         description="Error al generar el reporte",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Ocurrió un error al generar el reporte.")
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
    public function generateReport() {}
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

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
    public function index()
    {
        try {
            $orders = Order::with(['user:id,name,email', 'products:id,name,price'])->get();
            return response()->json($orders);
        } catch (QueryException $e) {
            Log::error("Error listando la información.: " . $e->getMessage());
            return response()->json(['error' => 'Error listando la información.'], 500);
        }
    }

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
    public function store(OrderRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();

            // Se utiliza el servicio para manejar la lógica de cálculo.
            $total = $this->orderService->calculateTotalOrder($request->products);

            if ($total['status']) {
                $data['total'] = $total['result'];
            } else {
                return response()->json($total['error'], 500);
            }

            // Crear la orden
            $order = Order::create($data);
            $products = [];

            foreach ($request->products as $productData) {
                $product = Product::findOrfail($productData['id']);

                $products[$productData['id']] = ['quantity' => $productData['quantity'], 'price' => $product->price];
            }

            // Almacenar los datos en la tabla intermedia
            $order->products()->attach($products);

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Tu orden ha sido realizada.'], 201);
        } catch (QueryException $e) {
            DB::rollBack();  // Revertir la transacción en caso de error
            Log::error("Error creando la orden: " . $e->getMessage());
            return response()->json(['error' => 'Error creando la orden.'], 500);
        }
    }

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
    public function show($id)
    {
        try {
            $order = Order::with(['user:id,name,email', 'products:id,name,price'])->find($id);
            if ($order) {
                return response()->json($order);
            } else {
                return response()->json(['error' => 'No se encontró la orden.'], 404);
            }
        } catch (QueryException $e) {
            Log::error("Error obteniendo la orden ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Error obteniendo la orden'], 500);
        }
    }

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
    public function destroy($id)
    {
        try {
            $order = Order::find($id);

            if ($order) {
                $order->products()->detach();
                $order->delete();
                return response()->json(['message' => 'La orden fue eliminada']);
            } else {
                return response()->json(['error' => 'No se encontró la orden.'], 404);
            }
        } catch (QueryException $e) {
            Log::error("Error eliminado la orden con ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Error eliminado la order'], 500);
        }
    }

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
    public function updateStatus(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'status' => 'required|string|in:pendiente,entregado,cancelado'
            ]);

            $order = Order::find($id);

            if ($order) {
                // Actualizar el estado de la orden
                $order->status = $data['status'];
                $order->save();

                return response()->json(['message' => 'Estado de la orden actualizado: ' . $data['status']], 200);
            } else {
                return response()->json(['error' => 'No se encontró la orden.'], 404);
            }
        } catch (QueryException $e) {
            Log::error("Error cambiando el estado de la orden: " . $e->getMessage());
            return response()->json(['error' => 'Error cambiando el estado de la orden.'], 500);
        }catch (ValidationException $e){
            return response()->json(['errors' => ['status' => ['Estado no valido.']]], 200);
        }
    }
}

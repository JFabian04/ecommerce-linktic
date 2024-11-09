<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Get all orders",
     *     tags={"Ordenes"},
     *     description="Retrieve a list of all orders",
     *     @OA\Response(
     *         response=200,
     *         description="List of orders",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Order"))
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error retrieving orders"
     *     )
     * )
     */
    public function index()
    {
        try {
            $orders = Order::with(['user', 'products'])->get();
            return response()->json($orders);
        } catch (QueryException $e) {
            Log::error("Error retrieving orders: " . $e->getMessage());
            return response()->json(['error' => 'Error retrieving orders'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Create a new order",
     *     description="Create a new order associated with a user and products",
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
     *         description="Order created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error creating order"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $order = Order::create(['user_id' => $request->user_id]);
            $products = [];

            foreach ($request->products as $productData) {
                $products[$productData['product_id']] = ['quantity' => $productData['quantity']];
            }

            $order->products()->attach($products);

            return response()->json($order->load('products'), 201);
        } catch (QueryException $e) {
            Log::error("Error creating order: " . $e->getMessage());
            return response()->json(['error' => 'Error creating order'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Get an order",
     *     description="Retrieve details of a specific order",
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
     *         description="Order not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error retrieving order"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $order = Order::with(['user', 'products'])->findOrFail($id);
            return response()->json($order);
        } catch (QueryException $e) {
            Log::error("Error retrieving order with ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Error retrieving order'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="Delete an order",
     *     description="Delete a specific order by its ID",
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
     *         description="Order not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error deleting order"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->products()->detach();
            $order->delete();

            return response()->json(['message' => 'Order deleted successfully']);
        } catch (QueryException $e) {
            Log::error("Error deleting order with ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Error deleting order'], 500);
        }
    }
}

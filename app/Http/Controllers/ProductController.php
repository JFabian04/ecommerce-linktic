<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


/**

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
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Obtener todos los productos",
     *     tags={"Productos"},
     *     description="Retorna una lista de todos los productos",
     *     @OA\Response(
     *         response=200,
     *         description="List of products",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function index()
    {
        try {
            $products = Product::all();
            return response()->json($products);
        } catch (QueryException $e) {
            Log::error("Error al obtener productos: " . $e->getMessage());
            return response()->json(['error' => 'Error al obtener productos'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     description="Creates a new product and returns the created product",
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
     *         description="Product created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function store(ProductRequest $request)
    {
        try {

            Product::create($request->all());

            return response()->json(['status' => true, 'message' => 'Producto registrado correctamente.'], 201);
        } catch (QueryException $e) {
            Log::error("Error al crear producto: " . $e->getMessage());
            return response()->json(['error' => 'Error al crear producto'], 500);
        } catch (\Exception $e) {
            Log::error("Error en la solicitud: " . $e->getMessage());
            return response()->json(['error' => 'Error en la solicitud'], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a product by ID",
     *     description="Returns a single product by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product data",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);
            return response()->json($product);
        } catch (QueryException $e) {
            Log::error("Error al obtener producto con ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Error al obtener producto'], 500);
        } catch (\Exception $e) {
            Log::error("Producto no encontrado con ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update a product by ID",
     *     description="Updates an existing product and returns the updated product",
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
     *         description="Product updated",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function update(ProductRequest $request, $id)
    {
        try {

            $product = Product::findOrFail($id);
            $product->update($request->all());

            return response()->json(['status' => true, 'message' => 'Producto actualizado correctamente.'], 201);
        } catch (QueryException $e) {
            Log::error("Error al actualizar producto con ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar producto'], 500);
        } catch (\Exception $e) {
            Log::error("Producto no encontrado con ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product by ID",
     *     description="Deletes an existing product by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Producto eliminado con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json(['status'=> true, 'message' => 'Producto eliminado con éxito']);
        } catch (QueryException $e) {
            Log::error("Error al eliminar producto con ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar producto'], 500);
        } catch (\Exception $e) {
            Log::error("Producto no encontrado con ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
    }
}

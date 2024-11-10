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
     *         description="Lista de productos",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al obtener productos"
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
     *     summary="Crear un nuevo producto",
     *     tags={"Productos"},
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
    public function store(ProductRequest $request)
    {
        try {

            Product::create($request->all());

            return response()->json(['status' => true, 'message' => 'Producto registrado correctamente.'], 201);
        } catch (QueryException $e) {
            Log::error("Error al crear el producto: " . $e->getMessage());
            return response()->json(['error' => 'Error al crear el producto'], 500);
        } catch (\Exception $e) {
            Log::error("Error en la solicitud: " . $e->getMessage());
            return response()->json(['error' => 'Error en la solicitud'], 400);
        }
    }

    /**
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
     *     summary="Actualizar producto por ID",
     *     tags={"Productos"},
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
     *     summary="Eliminar producto por ID",
     *     tags={"Productos"},
     *     description="Elimina un producto referenciado por su ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto eliminado con éxtio",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Producto eliminado con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al eliminar el prodcuto"
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

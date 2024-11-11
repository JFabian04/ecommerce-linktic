<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;



class ProductController extends Controller
{

    // Listar todos los productos
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

    // Crea un producto
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

    // Lista la info de un producto epspecifico. (Param ID del producto)
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

    // Actualiza la información de un producto especifico. (Param ID de la producto)
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

    // Elimina permanentemente un producto. (Param ID del producto)
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

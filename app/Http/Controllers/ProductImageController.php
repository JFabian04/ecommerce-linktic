<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Guid\Guid;


class ProductImageController extends Controller
{

    // Almacenar imagen del producto, Requiere la imagen en Base64 y el id del producto (formData)
    public function store(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'product_id' => 'required|exists:products,id'
            ]);

            $product = Product::find($request->product_id);

            if (!$product) {
                return response()->json([
                    'error' => 'No se encontró el producto.',
                ], 404);
            }
            // Convertir el is_main a booleanos 
            $isMain = filter_var($request->is_main, FILTER_VALIDATE_BOOLEAN);

            // Si la imagen tiene main = TRUE, pasar todas las demas imagenes a False para actualizar la imagen principal y evitar duplicados
            if ($isMain) {
                ProductImage::where('product_id', $product->id)->update(['is_main' => false]);
            }

            // Guardar la imagen
            $imageName = (string) Guid::uuid4() . '.' . $request->file('image')->getClientOriginalExtension();
            $imagePath = $request->file('image')->storeAs('image_file/' . $product->id, $imageName, 'public');
            // dd($imagePath);

            // Verificar si la imagen fue almacenada correctamente
            if (!Storage::disk('public')->exists($imagePath)) {
                return response()->json([
                    'error' => 'Error al guardar la imagen.',
                ], 500);
            }

            // Crear la imagen del producto en la base de datos
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $imagePath,
                'is_main' => $isMain
            ]);

            return response()->json([
                'message' => 'Imagen cargada con éxito.'
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error procesando la imagen: " . $e->getMessage());
            return response()->json(['error' => 'Error procesando la imagen. ' . $e->getMessage()], 500);
        }
    }

    // Eliminar una imagen de la carpeta image_file y de la BD.
    public function destroy($id)
    {
        try {
            $productImage = ProductImage::find($id);

            if (!$productImage) {
                return response()->json([
                    'error' => 'Imagen no encontrada.'
                ], 404);
            }

            // Verificar si la ruta de la imagen existe y la elimina
            if (Storage::disk('public')->exists($productImage->image_path)) {

                Storage::disk('public')->delete($productImage->image_path);
            }

            // Eliminar la entrada de la base de datos
            $productImage->delete();

            // Retornar respuesta exitosa
            return response()->json([
                'message' => 'Imagen eliminada con éxito.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al eliminar la imagen: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'error' => 'Hubo un problema al eliminar la imagen. Por favor intente nuevamente más tarde. ' . $e->getMessage()
            ], 500);
        }
    }
}

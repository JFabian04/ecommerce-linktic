<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Servicio para Calcular el total de la orden
     *
     * @param array $products
     * @return float
     */
    public function calculateTotalOrder(array $products)
    {
        try {
            $total = 0;

            foreach ($products as $productData) {
                $product = Product::find($productData['id']);

                if ($product) {
                    $productTotal = $product->price * $productData['quantity'];
                    $total += $productTotal;
                } else {
                    return ['status' => false, 'error'  => 'No se encontró el producto.', 'code' => 404];
                }
            }
            return ['status' => true, 'result'  => $total];
        } catch (\Exception $e) {
            Log::error("Error consumiendo order service: " . $e->getMessage());

            return ['status' => false, 'error'  => 'Error consumiendo el servicio de ordenes', 'code' => 500];
        }
    }

    /**
     * Actualizar el stock de los productos en la orden.
     *
     * @param array $products
     * @return bool
     */
    public function updateProductStock(array $products)
    {
        try {
            foreach ($products as $productData) {
                $product = Product::find($productData['id']);
                if (!$product) {
                    return ['status' => false, 'error'  => 'El producto no pudo ser encontrado: ' . $product->name . ' ID: ' . $product->id,  'code' => 404];
                }

                if ($product->stock < $productData['quantity']) {
                    return ['status' => false, 'error'  => 'EL Stock es insuficiente para el producto: ' . $product->name  . ' ID: ' . $product->id,  'code' => 422];
                }

                // Restar el stock del producto
                $product->stock -= $productData['quantity'];
                $product->save();
                return ['status' => true];
            }
        } catch (\Exception $e) {
            Log::error("Error actualizando el stock: " . $e->getMessage());
            return ['status' => false, 'error'  => 'Error actualizando el Stock', 'code' => 500];
        }
    }
}

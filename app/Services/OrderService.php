<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Servicio para Calcular el total de la orden.
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
                    return response()->json(['error' => 'No se encontró el producto.'], 404);
                }
            }
            return ['status' => true, 'result'  => $total];
        } catch (\Exception $e) {
            Log::error("Error consumiendo order service: " . $e->getMessage());

            return ['status' => false, 'error'  => 'Error consumiendo el servicio de ordenes'];
        }
    }
}

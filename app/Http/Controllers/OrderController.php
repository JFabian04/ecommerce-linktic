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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // Lista toda la información de las ordenes incluyendo data del usuario y del producto.
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

    // Alamcena la orden en la tabla principal y en la tabla dinamica por medio de una transacción
    public function store(OrderRequest $request)
    {
        try {
            // Transacción para evitar incosistencias entre la tabla ordenes y la tabla dinamica
            DB::beginTransaction();

            $data = $request->all();

            // Se utiliza el servicio para manejar la lógica de cálculo.
            $total = $this->orderService->calculateTotalOrder($request->products);

            if (!$total['status']) {
                return response()->json(['error' => $total['error']], 500);
            }

            $data['total'] = $total['result'];

            // Crear la orden
            $order = Order::create($data);
            // Usar el servicio para actualizar el stock de los productos
            $stockUpdate = $this->orderService->updateProductStock($request->products);


            if (!$stockUpdate['status']) {
                return response()->json(['error' => $stockUpdate['error']], $stockUpdate['code']);
            }

            // Crear array con la data para la tabla intermedia
            $products = [];
            foreach ($request->products as $productData) {
                $product = Product::find($productData['id']);

                if (!$product) {
                    return response()->json(['error' => 'Producto no encontrado'], 404);
                }

                $products[$productData['id']] = ['quantity' => $productData['quantity'], 'price' => $product->price];
            }

            // Almacenar los datos en la tabla intermedia
            $order->products()->attach($products);

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Tu orden ha sido realizada.'], 201);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error("Error creando la orden: " . $e->getMessage());
            return response()->json(['error' => 'Error creando la orden. ' . $e->getMessage()], 500);
        }
    }

    // Lista la data de laorden junto con info del usuario y del producto. (Parametro ID de la orden)
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
            return response()->json(['error' => 'Error obteniendo la orden. ' . $e->getMessage()], 500);
        }
    }

    // Elimina una orden especifica permantemente. (Parametro ID de la orden)
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
            return response()->json(['error' => 'Error eliminado la order. ' . $e->getMessage()], 500);
        }
    }

    // Actualiza el estado de orden especfica. (Param ID de la orden)
    public function updateStatus(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'status' => 'required|string|in:procesando,entregado,cancelado'
            ]);

            $order = Order::find($id);

            if (!$order) {
                return response()->json(['error' => 'No se encontró la orden.'], 404);
            }

            // Actualizar el estado de la orden
            $order->status = $data['status'];
            $order->save();

            return response()->json(['message' => 'Estado de la orden actualizado: ' . $data['status']], 200);
        } catch (QueryException $e) {
            Log::error("Error cambiando el estado de la orden: " . $e->getMessage());
            return response()->json(['error' => 'Error cambiando el estado de la orden. ' . $e->getMessage()], 500);
        } catch (ValidationException $e) {
            return response()->json(['errors' => ['status' => ['Estado no valido.']]], 200);
        }
    }

    // Generar reportes de ordenes en tipo excel. Recibe un request con la fecha inicial y fecha final. (start_date - end_date)
    public function generateReport(Request $request)
    {
        try {

            $orders = Order::with('user:id,name,email')->whereBetween('created_at', [$request->start_date, $request->end_date])
                ->get();

            // Crear un objeto Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Agregar los encabezados con formato
            $sheet->setCellValue('A1', 'ID')
                ->setCellValue('B1', 'Cliente')
                ->setCellValue('C1', 'Correo')
                ->setCellValue('D1', 'Total')
                ->setCellValue('E1', 'Fecha')
                ->setCellValue('F1', 'Estado');

            // Estilos para header
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

            $row = 2;
            foreach ($orders as $order) {
                $formattedDate = $order->created_at->format('d-m-Y');
                $sheet->setCellValue('A' . $row, $order->id)
                    ->setCellValue('B' . $row, $order->user->name)
                    ->setCellValue('C' . $row, $order->user->email)
                    ->setCellValue('D' . $row, $order->total)
                    ->setCellValue('E' . $row, $formattedDate)
                    ->setCellValue('F' . $row, $order->status);
                $row++;
            }

            // Borders para celdas
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]

            ];
            $sheet->getStyle('A1:F' . ($row - 1))->applyFromArray($styleArray);

            // Ancho automatico de columnas
            foreach (range('A', 'F') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // Alto para las filas
            $sheet->getRowDimension(1)->setRowHeight(30);
            foreach (range(2, $row - 1) as $rowIndex) {
                $sheet->getRowDimension($rowIndex)->setRowHeight(20);
            }

            // Crear el archivo Excel
            $writer = new Xlsx($spreadsheet);

            // Guardar el archivo en memoria
            $fileName = 'orders_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
            $directoryPath = storage_path('app/public/order/reports/');
            $filePath = $directoryPath . $fileName;

            // Verificar si la ruta existe, si no, crearla automáticamente
            if (!file_exists($directoryPath)) {
                mkdir($directoryPath, 0775, true);
            }

            $writer->save($filePath);
            $publicPath = asset('storage/order/reports/' . $fileName);

            return response()->json([
                'message' => 'El archivo ha sido generado correctamente.',
                'download_url' => $publicPath
            ]);
        } catch (\Exception $e) {
            Log::error("Error generando reporte: " . $e->getMessage());
            return response()->json(['error' => 'Oucrrió un error al generar el reporte.' . $e->getMessage()], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Throwable;

class VentaController extends Controller
{
    /**
     * Muestra un historial de todas las ventas con sus detalles.
     * GET /ventas
     */
    public function index()
    {
        // Con el 'with' se cargan las relaciones y evita consultas N+1
        $ventas = Venta::with(['cliente', 'detalles'])->orderBy('fecha', 'desc')->get();
        return response()->json($ventas);
    }

    /**
     * Registra una nueva venta.
     * POST /ventas
     */
    public function store(Request $request)
    {
        // URL base del microservicio de productos.
        $productosServiceUrl = 'http://localhost:8001'; // El puerto de miramar-productos

        try {
            $this->validate($request, [
                'cliente_id' => 'required|exists:clientes,id',
                'medio_pago' => 'required|string',
                'items' => 'required|array|min:1',
                'items.*.producto_id' => 'required|integer',
                'items.*.tipo' => 'required|string|in:servicio,paquete'
            ]);

            $costoTotalVenta = 0;
            $detallesParaGuardar = [];

            // Iniciar transacción para asegurar la integridad de los datos
            DB::beginTransaction();

            // 1. Validar cada producto y obtener su costo desde el microservicio de productos
            foreach ($request->items as $item) {
                $tipoPlural = $item['tipo'] . 's'; // 'servicio' -> 'servicios'
                $response = Http::get("{$productosServiceUrl}/{$tipoPlural}/{$item['producto_id']}");

                if (!$response->successful()) {
                    throw new \Exception("El producto tipo '{$item['tipo']}' con ID '{$item['producto_id']}' no es válido o no se pudo encontrar.");
                }

                $producto = $response->json();
                $costoProducto = $item['tipo'] === 'paquete' ? $producto['costo_total_paquete'] : $producto['costo'];
                
                $costoTotalVenta += $costoProducto;
                $detallesParaGuardar[] = [
                    'producto_id' => $item['producto_id'],
                    'tipo_producto' => $item['tipo'],
                    'costo_producto' => $costoProducto
                ];
            }

            // 2. Crear el registro principal de la venta
            $venta = Venta::create([
                'cliente_id' => $request->cliente_id,
                'fecha' => Carbon::now(), // Fecha actual del servidor
                'medio_pago' => $request->medio_pago,
                'costo_total' => $costoTotalVenta
            ]);

            // 3. Guardar los detalles de la venta asociándolos a la venta recién creada
            $venta->detalles()->createMany($detallesParaGuardar);

            // Si todo salió bien, confirmar los cambios en la base de datos
            DB::commit();

            // Devolver la venta completa con sus relaciones cargadas
            $venta->load(['cliente', 'detalles']);
            return response()->json($venta, 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            // Capturar cualquier otro error (ej: fallo en la API de productos)
            DB::rollBack();
            return response()->json(['message' => 'Error al procesar la venta.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Muestra una venta específica con sus detalles.
     * GET /ventas/{id}
     */
    public function show($id)
    {
        $venta = Venta::with(['cliente', 'detalles'])->find($id);

        if (!$venta) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }

        return response()->json($venta);
    }

    /**
     * Actualiza una venta.
     * PUT /ventas/{id}
     * Nota: Solo se puede actualizar el medio de pago.
     */
    public function update(Request $request, $id)
    {
        $venta = Venta::find($id);
        if (!$venta) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }
        
        try {
             $this->validate($request, [
                'medio_pago' => 'sometimes|required|string',
            ]);

            $venta->update($request->only(['medio_pago']));

            $venta->load(['cliente', 'detalles']);
            return response()->json($venta, 200);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Elimina una venta y sus detalles asociados.
     * DELETE /ventas/{id}
     */
    public function destroy($id)
    {
        $venta = Venta::find($id);

        if (!$venta) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }

        // Con onDelete('cascade') en la migración, los detalles se borran automáticamente.
        $venta->delete();

        return response()->json(null, 204); // No Content
    }
}
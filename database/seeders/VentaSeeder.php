<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class VentaSeeder extends Seeder
{
    public function run()
    {
        DB::table('venta_detalles')->delete();
        DB::table('ventas')->delete();

        $productosServiceUrl = 'http://localhost:8001';
        $cliente = Cliente::first(); // Obtenemos el cliente que creamos antes

        if (!$cliente) {
            $this->command->error('No se encontraron clientes. Ejecuta ClienteSeeder primero.');
            return;
        }

        // Definimos los productos que queremos "comprar"
        $itemsAComprar = [
            ['producto_id' => 1, 'tipo' => 'servicio'], // Vuelo a Miami
            ['producto_id' => 1, 'tipo' => 'paquete'],  // Paquete Caribeño
        ];

        try {
            DB::transaction(function () use ($cliente, $itemsAComprar, $productosServiceUrl) {
                $costoTotalVenta = 0;
                $detallesParaGuardar = [];

                foreach ($itemsAComprar as $item) {
                    $tipoPlural = $item['tipo'] . 's';
                    $response = Http::get("{$productosServiceUrl}/{$tipoPlural}/{$item['producto_id']}");
                    
                    if (!$response->successful()) {
                        throw new \Exception("Fallo al contactar la API de productos para el item ID {$item['producto_id']}");
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

                $venta = Venta::create([
                    'cliente_id' => $cliente->id,
                    'fecha' => Carbon::now(),
                    'medio_pago' => 'Transferencia Bancaria (Seeder)',
                    'costo_total' => $costoTotalVenta
                ]);

                $venta->detalles()->createMany($detallesParaGuardar);
            });

            $this->command->info('¡Venta de prueba creada exitosamente!');

        } catch (\Exception $e) {
            $this->command->error('No se pudo crear la venta de prueba. Asegúrate de que el microservicio de productos esté corriendo y tenga datos.');
            $this->command->error($e->getMessage());
        }
    }
}

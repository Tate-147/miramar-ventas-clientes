<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model {
    protected $fillable = ['venta_id', 'producto_id', 'tipo_producto', 'costo_producto'];
}
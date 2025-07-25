<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model {
    protected $fillable = ['cliente_id', 'fecha', 'medio_pago', 'costo_total'];

    public function cliente() {
        return $this->belongsTo(Cliente::class);
    }

    public function detalles() {
        return $this->hasMany(VentaDetalle::class);
    }
}
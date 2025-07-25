<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model {
    use SoftDeletes; // Activar el borrado lógico
    protected $fillable = ['nombre', 'apellido', 'dni', 'email'];
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;

class ClienteSeeder extends Seeder
{
    public function run()
    {
        DB::table('clientes')->delete(); // Limpia la tabla primero
        Cliente::create([
            'nombre' => 'Carlos',
            'apellido' => 'Sanchez',
            'dni' => '33444555',
            'email' => 'carlos.sanchez@example.com'
        ]);
    }
}

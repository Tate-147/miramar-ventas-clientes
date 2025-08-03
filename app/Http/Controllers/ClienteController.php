<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ClienteController extends Controller
{
    /**
     * Devuelve una lista de todos los clientes.
     * GET /clientes
     */
    public function index()
    {
        return response()->json(Cliente::all());
    }

    /**
     * Crea un nuevo cliente.
     * POST /clientes
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'dni' => 'required|string|unique:clientes,dni',
                'email' => 'required|email|unique:clientes,email'
            ]);

            $cliente = Cliente::create($request->all());

            return response()->json($cliente, 201); // 201 Created

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422); // Unprocessable Entity
        }
    }

    /**
     * Muestra los detalles de un cliente específico.
     * GET /clientes/{id}
     */
    public function show($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404); // Not Found
        }

        return response()->json($cliente);
    }

    /**
     * Actualiza la información de un cliente.
     * PUT /clientes/{id}
     */
    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        try {
            // Al validar, se ignora el DNI y email del propio cliente para evitar falsos positivos
            $this->validate($request, [
                'nombre' => 'sometimes|required|string|max:255',
                'apellido' => 'sometimes|required|string|max:255',
                'dni' => 'sometimes|required|string|unique:clientes,dni,' . $id,
                'email' => 'sometimes|required|email|unique:clientes,email,' . $id
            ]);

            $cliente->update($request->all());

            return response()->json($cliente, 200); // OK

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Elimina a un cliente (borrado lógico).
     * DELETE /clientes/{id}
     */
    public function destroy($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $cliente->delete(); // Esto ejecuta el soft delete

        return response()->json(null, 204); // 204 No Content
    }
}
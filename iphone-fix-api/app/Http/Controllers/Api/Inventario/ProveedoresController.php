<?php
// app/Http/Controllers/Api/Inventario/ProveedoresController.php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProveedoresController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $proveedores = Proveedor::orderBy('nombre')->paginate($perPage);

        return response()->json($proveedores);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'contacto_nombre' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'correo' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $proveedor = Proveedor::create($request->all());

        return response()->json([
            'message' => 'Proveedor creado exitosamente',
            'data' => $proveedor
        ], 201);
    }

    public function show($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        return response()->json(['data' => $proveedor]);
    }

    public function update(Request $request, $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|string|max:255',
            'contacto_nombre' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'correo' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $proveedor->update($request->all());

        return response()->json([
            'message' => 'Proveedor actualizado exitosamente',
            'data' => $proveedor
        ]);
    }

    public function destroy($id)
    {
        $proveedor = Proveedor::findOrFail($id);

        // Verificar si tiene lotes o entradas asociadas
        if ($proveedor->lotes()->count() > 0 || $proveedor->entradas()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar el proveedor porque tiene registros asociados'
            ], 400);
        }

        $proveedor->delete();

        return response()->json([
            'message' => 'Proveedor eliminado exitosamente'
        ]);
    }
}
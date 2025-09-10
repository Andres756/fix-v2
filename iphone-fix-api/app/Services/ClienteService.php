<?php

namespace App\Services;

use App\Models\Cliente;
use App\Http\Resources\ClienteResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClienteService
{
    public function all(?string $q = null)
    {
        $query = \App\Models\Cliente::query();

        if ($q) {
            $query->where('nombre', 'like', "%$q%")
                ->orWhere('documento', 'like', "%$q%");
        }

        return $query->paginate(10);
    }

    public function find($id): ClienteResource
    {
        $cliente = Cliente::findOrFail($id);
        return new ClienteResource($cliente);
    }

    public function create(array $data): ClienteResource
    {
        $cliente = Cliente::create($data);
        return new ClienteResource($cliente);
    }

    public function update($id, array $data): ClienteResource
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->update($data);
        return new ClienteResource($cliente);
    }

    public function delete($id): \Illuminate\Http\JsonResponse
    {
        Cliente::destroy($id);
        return response()->json(['message' => 'Cliente eliminado']);
    }
}

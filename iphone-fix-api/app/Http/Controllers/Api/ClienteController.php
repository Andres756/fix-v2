<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ClienteService;
use App\Http\Requests\Clientes\StoreClienteRequest;
use App\Http\Requests\Clientes\UpdateClienteRequest;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    protected ClienteService $clienteService;

    public function __construct(ClienteService $clienteService)
    {
        $this->clienteService = $clienteService;
    }

    // ✅ Listar clientes con filtro por nombre/documento
    public function index(Request $request)
    {
        $query = $request->get('q');

        return $this->clienteService->all($query);
    }

    // ✅ Crear cliente
    public function store(StoreClienteRequest $request)
    {
        return $this->clienteService->create($request->validated());
    }

    // ✅ Obtener cliente por ID
    public function show($id)
    {
        return $this->clienteService->find($id);
    }

    // ✅ Actualizar cliente
    public function update(UpdateClienteRequest $request, $id)
    {
        return $this->clienteService->update($id, $request->validated());
    }

    // ✅ Eliminar cliente
    public function destroy($id)
    {
        return $this->clienteService->delete($id);
    }
}

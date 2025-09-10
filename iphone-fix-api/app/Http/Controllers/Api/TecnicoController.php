<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TecnicoResource;
use App\Models\User;
use Illuminate\Http\Request;

class TecnicoController extends Controller
{
    /**
     * Listar técnicos
     */
    public function index(Request $request)
    {
        // Aquí podrías filtrar solo usuarios con rol "tecnico"
        $query = User::query();

        if ($request->filled('q')) {
            $query->where('name', 'like', "%{$request->q}%");
        }

        $tecnicos = $query->paginate($request->get('per_page', 15));

        return TecnicoResource::collection($tecnicos);
    }
}

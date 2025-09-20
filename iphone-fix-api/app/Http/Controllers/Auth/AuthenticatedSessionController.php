<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AuthenticatedSessionController extends Controller
{
    /**
     * Iniciar sesión y generar token Sanctum.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Crear token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
            'role'  => $user->role,
        ]);
    }

    /**
     * Cerrar sesión y revocar token.
     */
    public function destroy(Request $request): JsonResponse
    {
        // Revocar todos los tokens del usuario actual
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Sesión cerrada y token revocado.'
        ]);
    }
}

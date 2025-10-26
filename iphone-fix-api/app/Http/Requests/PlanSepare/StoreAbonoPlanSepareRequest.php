<?php

namespace App\Http\Requests\PlanSepare;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbonoPlanSepareRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'valor'         => 'required|numeric|min:100',
            'forma_pago_id' => 'required|integer|exists:formas_pago,id',
            'observaciones' => 'nullable|string|max:255',
        ];
    }
}

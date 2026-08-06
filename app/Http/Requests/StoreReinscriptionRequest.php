<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReinscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'classe_id' => ['required', 'exists:classes,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'classe_id.exists' => 'La classe selectionnee est invalide.',
        ];
    }
}

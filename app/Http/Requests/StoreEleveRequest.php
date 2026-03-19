<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEleveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'matricule'      => ['required', 'string', 'max:50', 'unique:eleves,matricule'],
            'nom'            => ['required', 'string', 'max:100'],
            'prenom'         => ['required', 'string', 'max:100'],
            'date_naissance' => ['required', 'date', 'before:today'],
            'sexe'           => ['required', 'in:M,F'],
            'classe_id'      => ['required', 'exists:classes,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'matricule.unique'      => 'Ce matricule est déjà utilisé.',
            'date_naissance.before' => 'La date de naissance doit être dans le passé.',
            'classe_id.exists'      => 'La classe sélectionnée est invalide.',
        ];
    }
}

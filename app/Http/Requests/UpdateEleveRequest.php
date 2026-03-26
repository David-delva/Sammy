<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEleveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $eleveId = $this->route('eleve')->id;

        return [
            'matricule' => ['required', 'string', 'max:50', "unique:eleves,matricule,{$eleveId}"],
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'date_naissance' => ['required', 'date', 'before:today'],
            'lieu_naissance' => ['required', 'string', 'max:100'],
            'sexe' => ['required', 'in:M,F'],
            'classe_id' => ['required', 'exists:classes,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'date_naissance.before' => 'La date de naissance doit être dans le passé.',
            'lieu_naissance.required' => 'Le lieu de naissance est obligatoire.',
            'classe_id.exists' => 'La classe sélectionnée est invalide.',
        ];
    }
}

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
            'matricule'      => ['required', 'string', 'max:50', "unique:eleves,matricule,{$eleveId}"],
            'nom'            => ['required', 'string', 'max:100'],
            'prenom'         => ['required', 'string', 'max:100'],
            'date_naissance' => ['required', 'date', 'before:today'],
            'sexe'           => ['required', 'in:M,F'],
            'classe_id'      => ['required', 'exists:classes,id'],
        ];
    }
}

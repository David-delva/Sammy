<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'eleve_id' => ['required', 'exists:eleves,id'],
            'matiere_id' => ['required', 'exists:matieres,id'],
            'note' => ['required', 'numeric', 'min:0', 'max:20'],
            'type_devoir' => ['required', 'in:devoir,composition'],
            'semestre' => ['required', 'integer', 'in:1,2'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.min' => 'La note minimale est 0.',
            'note.max' => 'La note maximale est 20.',
            'type_devoir.in' => 'Le type de devoir doit etre "devoir" ou "composition".',
            'semestre.in' => 'Le semestre doit etre 1 ou 2.',
        ];
    }
}

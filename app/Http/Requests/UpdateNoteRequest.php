<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNoteRequest extends FormRequest
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
}

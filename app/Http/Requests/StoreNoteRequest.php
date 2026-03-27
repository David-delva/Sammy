<?php

namespace App\Http\Requests;

use App\Services\AcademicIntegrityService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $annee = currentAcademicYear();

            if (! $annee) {
                return;
            }

            $message = app(AcademicIntegrityService::class)->validateStudentSubjectForYear(
                (int) $this->integer('eleve_id'),
                (int) $this->integer('matiere_id'),
                (int) $annee->id
            );

            if ($message) {
                $validator->errors()->add('matiere_id', $message);
            }
        });
    }
}

<?php

namespace App\Http\Requests;

use App\Services\AcademicIntegrityService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreMasseNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'classe_id' => ['required', 'exists:classes,id'],
            'matiere_id' => ['required', 'exists:matieres,id'],
            'type_devoir' => ['required', 'in:devoir,composition'],
            'semestre' => ['required', 'integer', 'in:1,2'],
            'notes' => ['required', 'array'],
            'notes.*' => ['nullable', 'numeric', 'min:0', 'max:20'],
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

            $integrity = app(AcademicIntegrityService::class);
            $classeId = (int) $this->integer('classe_id');
            $matiereId = (int) $this->integer('matiere_id');

            $message = $integrity->validateClassSubjectForYear($classeId, $matiereId, (int) $annee->id);

            if ($message) {
                $validator->errors()->add('matiere_id', $message);

                return;
            }

            $invalidEleves = $integrity->invalidStudentIdsForClassYear(
                array_keys($this->input('notes', [])),
                $classeId,
                (int) $annee->id
            );

            if ($invalidEleves !== []) {
                $validator->errors()->add('notes', 'La saisie contient des eleves invalides ou hors de la classe selectionnee pour cette annee academique.');
            }
        });
    }
}

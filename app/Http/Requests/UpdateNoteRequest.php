<?php

namespace App\Http\Requests;

use App\Models\Note as NoteModel;
use App\Services\AcademicIntegrityService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $note = $this->route('note');
            $currentYear = currentAcademicYear();
            $anneeId = $note instanceof NoteModel
                ? (int) $note->annee_academique_id
                : ($currentYear ? (int) $currentYear->id : 0);

            if ($anneeId <= 0) {
                return;
            }

            $message = app(AcademicIntegrityService::class)->validateStudentSubjectForYear(
                (int) $this->integer('eleve_id'),
                (int) $this->integer('matiere_id'),
                $anneeId
            );

            if ($message) {
                $validator->errors()->add('matiere_id', $message);
            }
        });
    }
}

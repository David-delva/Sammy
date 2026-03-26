@extends('layouts.app')

@section('title', 'Modifier la note')
@section('breadcrumb', 'Évaluations / Notes / Modification')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Évaluations</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Modifier la note</h2>
            <p class="mt-2 text-sm text-gray-500">Ajustez l'élève, la matière, la valeur, le semestre ou le type d'évaluation.</p>
        </div>
        <a href="{{ route('notes.index', ['date' => request()->query('date'), 'semestre' => request()->query('semestre')]) }}" class="btn-secondary self-start sm:self-auto">
            <i class="bi bi-arrow-left"></i>
            Retour a la liste
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h4>Modification</h4>
                <p class="mt-1 text-xs text-gray-400">Mettez a jour les informations de la note selectionnee.</p>
            </div>
            @if(isset($annee) && $annee)
                <span class="badge-blue">{{ $annee->libelle }}</span>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('notes.update', ['note' => $note, 'date' => request()->query('date')]) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="form-field">
                    <label for="eleve_id" class="form-label">Élève <span class="req">*</span></label>
                    <select id="eleve_id" name="eleve_id" class="form-select @error('eleve_id') error @enderror" required>
                        @foreach($eleves as $eleve)
                            <option value="{{ $eleve->id }}" data-classe="{{ $eleve->resolved_classe_id }}" {{ old('eleve_id', $note->eleve_id) == $eleve->id ? 'selected' : '' }}>
                                {{ $eleve->nom }} {{ $eleve->prenom }} ({{ $eleve->resolved_classe_nom }})
                            </option>
                        @endforeach
                    </select>
                    @error('eleve_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="matiere_id" class="form-label">Matière <span class="req">*</span></label>
                    <select id="matiere_id" name="matiere_id" class="form-select @error('matiere_id') error @enderror" required>
                        @foreach($matieres as $matiere)
                            <option value="{{ $matiere->id }}" {{ old('matiere_id', $note->matiere_id) == $matiere->id ? 'selected' : '' }}>
                                {{ $matiere->nom_matiere }}
                            </option>
                        @endforeach
                    </select>
                    @error('matiere_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 md:grid-cols-3">
                    <div class="form-field">
                        <label for="note" class="form-label">Note / 20 <span class="req">*</span></label>
                        <input type="number" step="0.25" min="0" max="20" id="note" name="note" value="{{ old('note', $note->note) }}" class="form-input @error('note') error @enderror" required>
                        @error('note')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="semestre" class="form-label">Semestre <span class="req">*</span></label>
                        <select id="semestre" name="semestre" class="form-select @error('semestre') error @enderror" required>
                            @foreach(\App\Models\Note::semestreOptions() as $value => $label)
                                <option value="{{ $value }}" {{ (string) old('semestre', $note->semestre) === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('semestre')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="type_devoir" class="form-label">Type d'évaluation <span class="req">*</span></label>
                        <select id="type_devoir" name="type_devoir" class="form-select @error('type_devoir') error @enderror" required>
                            <option value="devoir" {{ old('type_devoir', $note->type_devoir) == 'devoir' ? 'selected' : '' }}>Devoir</option>
                            <option value="composition" {{ old('type_devoir', $note->type_devoir) == 'composition' ? 'selected' : '' }}>Composition</option>
                        </select>
                        @error('type_devoir')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-between">
                    <a href="{{ route('notes.index', ['date' => request()->query('date'), 'semestre' => request()->query('semestre')]) }}" class="btn-secondary justify-center">Annuler</a>
                    <button type="submit" class="btn-primary justify-center">
                        <i class="bi bi-save"></i>
                        Mettre a jour la note
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
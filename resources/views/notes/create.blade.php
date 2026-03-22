@extends('layouts.app')

@section('title', 'Saisir une note')
@section('breadcrumb', 'Evaluations / Notes / Creation')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Evaluations</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Saisir une note</h2>
            <p class="mt-2 text-sm text-gray-500">Enregistrez une nouvelle evaluation pour un eleve dans l'annee academique active.</p>
        </div>
        <a href="{{ route('notes.index', ['date' => request()->query('date'), 'semestre' => request()->query('semestre')]) }}" class="btn-secondary self-start sm:self-auto">
            <i class="bi bi-arrow-left"></i>
            Retour a la liste
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h4>Saisie individuelle</h4>
                <p class="mt-1 text-xs text-gray-400">Selectionnez l'eleve, la matiere, le semestre et le type d'evaluation.</p>
            </div>
            @if(isset($annee) && $annee)
                <span class="badge-blue">{{ $annee->libelle }}</span>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('notes.store', ['date' => request()->query('date'), 'semestre' => request()->query('semestre')]) }}" method="POST" class="space-y-6">
                @csrf

                <div class="form-field">
                    <label for="eleve_id" class="form-label">Eleve <span class="req">*</span></label>
                    <select id="eleve_id" name="eleve_id" class="form-select @error('eleve_id') error @enderror" required>
                        <option value="">Selectionner l'eleve</option>
                        @foreach($eleves as $eleve)
                            <option value="{{ $eleve->id }}" data-classe="{{ $eleve->resolved_classe_id }}" {{ old('eleve_id') == $eleve->id ? 'selected' : '' }}>
                                {{ $eleve->nom }} {{ $eleve->prenom }} ({{ $eleve->resolved_classe_nom }})
                            </option>
                        @endforeach
                    </select>
                    @error('eleve_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="matiere_id" class="form-label">Matiere <span class="req">*</span></label>
                    <select id="matiere_id" name="matiere_id" class="form-select @error('matiere_id') error @enderror" required></select>
                    @error('matiere_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 md:grid-cols-3">
                    <div class="form-field">
                        <label for="note" class="form-label">Note / 20 <span class="req">*</span></label>
                        <input type="number" step="0.25" min="0" max="20" id="note" name="note" value="{{ old('note') }}" class="form-input @error('note') error @enderror" required>
                        @error('note')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="semestre" class="form-label">Semestre <span class="req">*</span></label>
                        <select id="semestre" name="semestre" class="form-select @error('semestre') error @enderror" required>
                            @foreach(\App\Models\Note::semestreOptions() as $value => $label)
                                <option value="{{ $value }}" {{ (string) old('semestre', \App\Models\Note::SEMESTRE_1) === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('semestre')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="type_devoir" class="form-label">Type d'evaluation <span class="req">*</span></label>
                        <select id="type_devoir" name="type_devoir" class="form-select @error('type_devoir') error @enderror" required>
                            <option value="devoir" {{ old('type_devoir') == 'devoir' ? 'selected' : '' }}>Devoir</option>
                            <option value="composition" {{ old('type_devoir') == 'composition' ? 'selected' : '' }}>Composition</option>
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
                        Enregistrer la note
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const eleveSelect = document.getElementById('eleve_id');
    const matiereSelect = document.getElementById('matiere_id');
    const toutesMatieres = @json($matieres);
    const selectedMatiereId = @json((string) old('matiere_id'));

    function populateMatieres() {
        matiereSelect.innerHTML = '<option value="">Selectionner la matiere</option>';

        toutesMatieres.forEach(matiere => {
            const option = document.createElement('option');
            option.value = matiere.id;
            option.textContent = matiere.nom_matiere;
            if (selectedMatiereId && String(matiere.id) === selectedMatiereId) {
                option.selected = true;
            }
            matiereSelect.appendChild(option);
        });
    }

    eleveSelect.addEventListener('change', populateMatieres);
    populateMatieres();
});
</script>
@endsection
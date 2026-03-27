@extends('layouts.app')

@section('title', 'Saisir une note')
@section('breadcrumb', 'Evaluations / Notes / Creation')

@section('content')
<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Evaluations</p>
                <h2 class="page-title">Enregistre une nouvelle note dans un formulaire plus guidant et plus mobile.</h2>
                <p class="page-lead">Selectionnez l'eleve, la matiere, le semestre et le type d'evaluation pour enregistrer la note dans le bon contexte.</p>

                <div class="hero-badges">
                    @if(isset($annee) && $annee)
                        <span class="hero-badge"><i class="bi bi-calendar2-week"></i>{{ $annee->libelle }}</span>
                    @endif
                    <span class="hero-badge"><i class="bi bi-people"></i>{{ $eleves->count() }} eleve(s) disponibles</span>
                    <span class="hero-badge"><i class="bi bi-book"></i>{{ $matieres->count() }} matiere(s)</span>
                </div>

                <div class="hero-actions">
                    <a href="{{ route('notes.index', ['date' => request()->query('date'), 'semestre' => request()->query('semestre')]) }}" class="btn-secondary justify-center sm:w-auto">
                        <i class="bi bi-arrow-left"></i>
                        Retour a la liste
                    </a>
                </div>
            </div>

            <aside class="hero-panel" data-tilt>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Saisie guidee</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-tight text-white">Le formulaire concentre les informations essentielles sans surcharge.</h3>
                    <p class="mt-3 text-sm leading-7 text-white/70">La selection de la matiere reste synchronisee et les champs critiques sont regroupes visuellement.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Format</p>
                        <p class="mt-2 text-2xl font-semibold text-white">/20</p>
                        <p class="mt-1 text-sm text-white/65">precision au quart de point</p>
                    </div>
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Semestre</p>
                        <p class="mt-2 text-2xl font-semibold text-white">Choix</p>
                        <p class="mt-1 text-sm text-white/65">1 ou 2 selon l'evaluation</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="card">
        <div class="card-header">
            <div>
                <h4>Saisie individuelle</h4>
                <p class="text-xs text-slate-500">Les champs obligatoires sont regroupes pour accelerer la saisie sans perdre en clarte.</p>
            </div>
            @if(isset($annee) && $annee)
                <span class="badge-blue">{{ $annee->libelle }}</span>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('notes.store', ['date' => request()->query('date'), 'semestre' => request()->query('semestre')]) }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid gap-5 xl:grid-cols-2">
                    <div class="form-field xl:col-span-2">
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

                    <div class="form-field xl:col-span-2">
                        <label for="matiere_id" class="form-label">Matiere <span class="req">*</span></label>
                        <select id="matiere_id" name="matiere_id" class="form-select @error('matiere_id') error @enderror" required disabled></select>
                        @error('matiere_id')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <p class="form-hint">Les matieres proposees dependent maintenant de la classe de l'eleve selectionne.</p>
                    </div>

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

                    <div class="form-field xl:col-span-2">
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

                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-between">
                    <a href="{{ route('notes.index', ['date' => request()->query('date'), 'semestre' => request()->query('semestre')]) }}" class="btn-secondary justify-center">Annuler</a>
                    <button type="submit" class="btn-primary justify-center">
                        <i class="bi bi-save"></i>
                        Enregistrer la note
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const eleveSelect = document.getElementById('eleve_id');
    const matiereSelect = document.getElementById('matiere_id');
    const matieresByClasse = @json($matieresByClasse);
    const selectedMatiereId = @json((string) old('matiere_id'));

    function populateMatieres() {
        const selectedOption = eleveSelect.options[eleveSelect.selectedIndex];
        const classeId = selectedOption?.dataset.classe;
        const matieres = classeId ? (matieresByClasse[classeId] ?? []) : [];

        matiereSelect.innerHTML = '<option value="">Selectionner la matiere</option>';
        matiereSelect.disabled = !classeId || matieres.length === 0;

        matieres.forEach(matiere => {
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
@endpush

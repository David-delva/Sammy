@extends('layouts.app')

@section('title', 'Modifier la note')
@section('breadcrumb', 'Evaluations / Notes / Modification')

@section('content')
<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Evaluations</p>
                <h2 class="page-title">Ajuste une note dans un formulaire plus lisible et mieux structure.</h2>
                <p class="page-lead">Modifiez l'eleve, la matiere, la valeur, le semestre ou le type d'evaluation sans quitter le contexte courant.</p>

                <div class="hero-badges">
                    @if(isset($annee) && $annee)
                        <span class="hero-badge"><i class="bi bi-calendar2-week"></i>{{ $annee->libelle }}</span>
                    @endif
                    <span class="hero-badge"><i class="bi bi-people"></i>{{ $eleves->count() }} eleve(s)</span>
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
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Edition</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-tight text-white">Les elements critiques sont regroupes pour une correction rapide.</h3>
                    <p class="mt-3 text-sm leading-7 text-white/70">Le formulaire conserve le meme ordre logique que la saisie initiale pour limiter les erreurs pendant les ajustements.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Format</p>
                        <p class="mt-2 text-2xl font-semibold text-white">/20</p>
                        <p class="mt-1 text-sm text-white/65">ajustement fin possible</p>
                    </div>
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Type</p>
                        <p class="mt-2 text-2xl font-semibold text-white">Libre</p>
                        <p class="mt-1 text-sm text-white/65">devoir ou composition</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="card">
        <div class="card-header">
            <div>
                <h4>Modification</h4>
                <p class="text-xs text-slate-500">Mettez a jour les informations de la note selectionnee puis sauvegardez.</p>
            </div>
            @if(isset($annee) && $annee)
                <span class="badge-blue">{{ $annee->libelle }}</span>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('notes.update', ['note' => $note, 'date' => request()->query('date')]) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid gap-5 xl:grid-cols-2">
                    <div class="form-field xl:col-span-2">
                        <label for="eleve_id" class="form-label">Eleve <span class="req">*</span></label>
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

                    <div class="form-field xl:col-span-2">
                        <label for="matiere_id" class="form-label">Matiere <span class="req">*</span></label>
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

                    <div class="form-field xl:col-span-2">
                        <label for="type_devoir" class="form-label">Type d'evaluation <span class="req">*</span></label>
                        <select id="type_devoir" name="type_devoir" class="form-select @error('type_devoir') error @enderror" required>
                            <option value="devoir" {{ old('type_devoir', $note->type_devoir) == 'devoir' ? 'selected' : '' }}>Devoir</option>
                            <option value="composition" {{ old('type_devoir', $note->type_devoir) == 'composition' ? 'selected' : '' }}>Composition</option>
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
                        Mettre a jour la note
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

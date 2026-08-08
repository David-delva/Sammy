@extends('layouts.app')

@section('title', 'Absences')
@section('breadcrumb', 'Evaluations / Absences')

@section('content')
<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Evaluations</p>
                <h2 class="page-title">Saisissez les heures d'absence par matière et par élève.</h2>
                <p class="page-lead">Une pénalité configurable (règle 4.9) est déduite automatiquement de la moyenne de la matière concernée.</p>
                @if(isset($annee) && $annee)
                    <div class="hero-badges">
                        <span class="hero-badge"><i class="bi bi-calendar2-week"></i>{{ $annee->libelle }}</span>
                    </div>
                @endif

                <div class="hero-actions">
                    <a href="{{ route('absences.import.form') }}" class="btn-secondary justify-center sm:w-auto">
                        <i class="bi bi-file-earmark-arrow-up"></i>
                        Importer depuis Excel
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-[260px_minmax(0,1fr)]">
        <section class="card">
            <div class="card-header">
                <h4>Classes</h4>
            </div>
            <div class="card-body divide-y divide-slate-100 p-0">
                @forelse($classes as $cl)
                    <a href="{{ route('absences.saisir', ['classe_id' => $cl->id]) }}"
                       class="flex items-center justify-between px-4 py-3 text-sm {{ isset($classe) && $classe->id === $cl->id ? 'bg-cobalt-50 font-semibold text-cobalt-700' : 'text-slate-700 hover:bg-slate-50' }}">
                        {{ $cl->nom_classe }}
                        <i class="bi bi-chevron-right text-xs text-slate-400"></i>
                    </a>
                @empty
                    <p class="p-4 text-sm text-slate-500">Aucune classe disponible.</p>
                @endforelse
            </div>
        </section>

        <div class="space-y-6">
            @if(session('success'))
                <div class="rounded-2xl border border-success-200 bg-success-50 p-4 text-sm text-success-700">{{ session('success') }}</div>
            @endif
            @error('general')
                <div class="rounded-2xl border border-danger-200 bg-danger-50 p-4 text-sm text-danger-700">{{ $message }}</div>
            @enderror

            @if(! isset($classe))
                <section class="card">
                    <div class="card-body py-16 text-center text-sm text-slate-500">
                        Sélectionnez une classe pour saisir les absences.
                    </div>
                </section>
            @elseif(! isset($annee) || ! $annee)
                <section class="card">
                    <div class="card-body py-16 text-center text-sm text-slate-500">
                        Aucune année académique active.
                    </div>
                </section>
            @else
                <section class="card">
                    <div class="card-header">
                        <div>
                            <h4>{{ $classe->nom_classe }}</h4>
                            <p class="text-xs text-slate-500">Heures d'absence cumulées par matière, pour le semestre sélectionné.</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('absences.saisir', ['classe_id' => $classe->id, 'semestre' => 1]) }}" class="{{ $semestre === 1 ? 'btn-primary' : 'btn-secondary' }}">Semestre 1</a>
                            <a href="{{ route('absences.saisir', ['classe_id' => $classe->id, 'semestre' => 2]) }}" class="{{ $semestre === 2 ? 'btn-primary' : 'btn-secondary' }}">Semestre 2</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($matieres->isEmpty() || $eleves->isEmpty())
                            <p class="text-sm text-slate-500">Aucune matière ou aucun élève inscrit pour cette classe.</p>
                        @else
                            <form action="{{ route('absences.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="classe_id" value="{{ $classe->id }}">
                                <input type="hidden" name="semestre" value="{{ $semestre }}">
                                <div class="overflow-x-auto">
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th>Elève</th>
                                                @foreach($matieres as $matiere)
                                                    <th class="text-center">{{ $matiere->nom_matiere }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($eleves as $eleve)
                                                <tr>
                                                    <td>{{ $eleve->nom }} {{ $eleve->prenom }}</td>
                                                    @foreach($matieres as $matiere)
                                                        @php $heures = $absences->get($eleve->id.':'.$matiere->id)?->heures ?? 0; @endphp
                                                        <td class="text-center">
                                                            <input type="number" min="0" max="500" step="1"
                                                                   name="heures[{{ $eleve->id }}][{{ $matiere->id }}]"
                                                                   value="{{ $heures }}"
                                                                   class="form-input w-20 text-center">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4 flex justify-end">
                                    <button type="submit" class="btn-primary">
                                        <i class="bi bi-save"></i>
                                        Enregistrer les absences
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </section>
            @endif
        </div>
    </div>
</div>
@endsection

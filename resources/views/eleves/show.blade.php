@extends('layouts.app')

@section('title', $eleve->nom . ' ' . $eleve->prenom)
@section('breadcrumb', 'Scolarite / Eleves / Detail')

@section('content')
@php
    $formatNote = static fn ($value) => $value !== null ? number_format((float) $value, 2, ',', ' ') : '--';
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Profil eleve</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">{{ $eleve->nom }} {{ $eleve->prenom }}</h2>
            <p class="mt-2 text-sm text-gray-500">Matricule {{ $eleve->matricule }} - Consultez les notes et l'etat scolaire de l'annee selectionnee.</p>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('eleves.edit', ['eleve' => $eleve, 'date' => request()->query('date')]) }}" class="btn-secondary justify-center">
                <i class="bi bi-pencil"></i>
                Modifier
            </a>
            <a href="{{ route('eleves.historique', ['eleve' => $eleve, 'date' => request()->query('date')]) }}" class="btn-secondary justify-center">
                <i class="bi bi-clock-history"></i>
                Historique
            </a>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[340px_minmax(0,1fr)]">
        <div class="card overflow-hidden">
            <div class="card-body text-center">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                    <i class="bi bi-person-circle text-5xl"></i>
                </div>
                <h3 class="mt-5 text-xl font-semibold tracking-tight text-gray-900">{{ $eleve->nom }} {{ $eleve->prenom }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ $eleve->matricule }}</p>
            </div>
            <div class="divide-y divide-gray-100 border-t border-gray-100">
                <div class="px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Classe actuelle</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900">{{ $eleve->resolved_classe ? $eleve->resolved_classe->nom_classe : 'Non inscrit' }}</p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Date de naissance</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900">{{ $eleve->date_naissance->format('d/m/Y') }}</p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Lieu de naissance</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900">{{ $eleve->lieu_naissance ?: 'Non renseigne' }}</p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Sexe</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900">{{ $eleve->sexe === 'M' ? 'Masculin' : 'Feminin' }}</p>
                </div>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="card-header">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h4>Notes de l'annee</h4>
                        @if($annee)
                            <span class="badge-blue">{{ $annee->libelle }}</span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Evaluations organisees par semestre puis par matiere pour le contexte actif.</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <a href="{{ route('bulletins.pdf', ['id' => $eleve->id, 'semestre' => 1, 'date' => request()->query('date')]) }}" class="btn-danger btn-sm">
                        <i class="bi bi-file-earmark-pdf"></i>
                        Bulletin S1
                    </a>
                    <a href="{{ route('bulletins.pdf', ['id' => $eleve->id, 'semestre' => 2, 'date' => request()->query('date')]) }}" class="btn-secondary btn-sm">
                        <i class="bi bi-file-earmark-pdf"></i>
                        Bulletin S2
                    </a>
                </div>
            </div>

            <div class="grid gap-4 border-b border-gray-100 bg-slate-50/70 px-5 py-5 sm:grid-cols-2 xl:grid-cols-5">
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Evaluations</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $notesOverview['total_notes'] }}</p>
                    <p class="mt-1 text-xs text-gray-500">Notes enregistrees</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Matieres</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $notesOverview['total_matieres'] }}</p>
                    <p class="mt-1 text-xs text-gray-500">Matieres evaluees</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Moyenne annuelle</p>
                    <p class="mt-2 text-2xl font-semibold {{ $notesOverview['moyenne_annuelle'] === null ? 'text-gray-400' : ($notesOverview['moyenne_annuelle'] >= 10 ? 'text-emerald-600' : 'text-red-600') }}">{{ $formatNote($notesOverview['moyenne_annuelle']) }}</p>
                    <p class="mt-1 text-xs text-gray-500">Sur 20</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Moyenne S1</p>
                    <p class="mt-2 text-2xl font-semibold {{ $notesOverview['moyenne_semestre_1'] === null ? 'text-gray-400' : ($notesOverview['moyenne_semestre_1'] >= 10 ? 'text-emerald-600' : 'text-red-600') }}">{{ $formatNote($notesOverview['moyenne_semestre_1']) }}</p>
                    <p class="mt-1 text-xs text-gray-500">1er semestre</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Moyenne S2</p>
                    <p class="mt-2 text-2xl font-semibold {{ $notesOverview['moyenne_semestre_2'] === null ? 'text-gray-400' : ($notesOverview['moyenne_semestre_2'] >= 10 ? 'text-emerald-600' : 'text-red-600') }}">{{ $formatNote($notesOverview['moyenne_semestre_2']) }}</p>
                    <p class="mt-1 text-xs text-gray-500">2e semestre</p>
                </div>
            </div>

            @if($notesBySemestre->isNotEmpty())
                <div class="space-y-5 p-5">
                    @foreach($notesBySemestre as $semestreGroup)
                        <section class="overflow-hidden rounded-2xl border border-gray-100 bg-slate-50/70">
                            <div class="flex flex-col gap-4 border-b border-gray-100 bg-white/80 p-5 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="{{ $semestreGroup['semestre'] === 2 ? 'badge-yellow' : 'badge-blue' }}">{{ $semestreGroup['label'] }}</span>
                                        <span class="badge-gray">{{ $semestreGroup['total_notes'] }} evaluation(s)</span>
                                        <span class="badge-purple">{{ $semestreGroup['total_matieres'] }} matiere(s)</span>
                                    </div>
                                    <p class="mt-3 text-sm text-gray-500">Les evaluations sont regroupees par matiere pour faciliter la lecture du semestre.</p>
                                </div>
                                <div class="rounded-xl border border-gray-100 bg-white px-4 py-3 text-right shadow-sm">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Moyenne du semestre</p>
                                    <p class="mt-1 text-xl font-semibold {{ $semestreGroup['moyenne_generale'] === null ? 'text-gray-400' : ($semestreGroup['moyenne_generale'] >= 10 ? 'text-emerald-600' : 'text-red-600') }}">{{ $formatNote($semestreGroup['moyenne_generale']) }}</p>
                                </div>
                            </div>

                            <div class="divide-y divide-gray-100">
                                @foreach($semestreGroup['matieres'] as $matiereGroup)
                                    <article class="p-5">
                                        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                            <div>
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h5 class="text-base font-semibold text-gray-900">{{ $matiereGroup['matiere']->nom_matiere }}</h5>
                                                    <span class="badge-gray">{{ $matiereGroup['total_notes'] }} evaluation(s)</span>
                                                </div>
                                                <p class="mt-2 text-sm text-gray-500">
                                                    Derniere saisie le {{ $matiereGroup['derniere_saisie'] ? $matiereGroup['derniere_saisie']->format('d/m/Y') : '--' }}.
                                                </p>
                                            </div>

                                            <div class="grid gap-3 sm:grid-cols-3 xl:min-w-[420px]">
                                                <div class="rounded-xl border border-gray-100 bg-white p-3 shadow-sm">
                                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Moy. devoirs</p>
                                                    <p class="mt-2 text-lg font-semibold text-gray-900">{{ $formatNote($matiereGroup['moyenne_devoirs']) }}</p>
                                                </div>
                                                <div class="rounded-xl border border-gray-100 bg-white p-3 shadow-sm">
                                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Composition</p>
                                                    <p class="mt-2 text-lg font-semibold text-gray-900">{{ $formatNote($matiereGroup['note_composition']) }}</p>
                                                </div>
                                                <div class="rounded-xl border border-gray-100 bg-white p-3 shadow-sm">
                                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Moy. matiere</p>
                                                    <p class="mt-2 text-lg font-semibold {{ $matiereGroup['moyenne_matiere'] === null ? 'text-gray-400' : ($matiereGroup['moyenne_matiere'] >= 10 ? 'text-emerald-600' : 'text-red-600') }}">{{ $formatNote($matiereGroup['moyenne_matiere']) }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100 bg-white">
                                            <table class="data-table">
                                                <thead>
                                                    <tr>
                                                        <th>Evaluation</th>
                                                        <th class="text-center">Note / 20</th>
                                                        <th class="text-right">Date de saisie</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($matiereGroup['notes'] as $note)
                                                        <tr>
                                                            <td>
                                                                <div class="flex flex-wrap items-center gap-2">
                                                                    <span class="{{ $note->type_devoir === 'composition' ? 'badge-purple' : 'badge-gray' }}">
                                                                        {{ ucfirst($note->type_devoir) }}
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="text-base font-semibold {{ $note->note >= 10 ? 'text-emerald-600' : 'text-red-600' }}">
                                                                    {{ number_format($note->note, 2, ',', ' ') }}
                                                                </span>
                                                            </td>
                                                            <td class="text-right text-sm text-gray-400">{{ $note->created_at->format('d/m/Y') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto flex max-w-sm flex-col items-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-gray-400">
                            <i class="bi bi-journal-x text-2xl"></i>
                        </div>
                        <p class="mt-4 text-sm font-medium text-gray-700">Aucune note enregistree</p>
                        <p class="mt-1 text-sm text-gray-500">Les notes de cet eleve apparaitront ici des qu'elles seront saisies.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

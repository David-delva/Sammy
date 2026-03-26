@extends('layouts.app')

@section('title', $eleve->nom . ' ' . $eleve->prenom)
@section('breadcrumb', 'Scolarite / Eleves / Detail')

@section('content')
@php
    $formatNote = static fn ($value) => $value !== null ? number_format((float) $value, 2, ',', ' ') : '--';
@endphp

<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Profil eleve</p>
                <h2 class="page-title">{{ $eleve->nom }} {{ $eleve->prenom }} conserve un suivi annuel clair et detaille.</h2>
                <p class="page-lead">Consultez la fiche civile, les moyennes, les matieres et l'historique de l'eleve dans un ecran unique beaucoup plus lisible.</p>

                <div class="hero-badges">
                    <span class="hero-badge"><i class="bi bi-upc"></i>{{ $eleve->matricule }}</span>
                    <span class="hero-badge"><i class="bi bi-building"></i>{{ $eleve->resolved_classe ? $eleve->resolved_classe->nom_classe : 'Non inscrit' }}</span>
                    @if($annee)
                        <span class="hero-badge"><i class="bi bi-calendar2-week"></i>{{ $annee->libelle }}</span>
                    @endif
                </div>

                <div class="hero-actions">
                    @if($canManageAcademicData)
                        <a href="{{ route('eleves.edit', ['eleve' => $eleve, 'date' => request()->query('date')]) }}" class="btn-secondary justify-center sm:w-auto">
                            <i class="bi bi-pencil"></i>
                            Modifier
                        </a>
                    @endif
                    <a href="{{ route('eleves.historique', ['eleve' => $eleve, 'date' => request()->query('date')]) }}" class="btn-secondary justify-center sm:w-auto">
                        <i class="bi bi-clock-history"></i>
                        Historique
                    </a>
                </div>
            </div>

            <aside class="hero-panel" data-tilt>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Synthese</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-tight text-white">Les indicateurs annuels restent visibles des l'ouverture du profil.</h3>
                    <p class="mt-3 text-sm leading-7 text-white/70">Les bulletins PDF, les moyennes par semestre et les notes par matiere sont regroupes sous une meme narration visuelle.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Evaluations</p>
                        <p class="mt-2 text-2xl font-semibold text-white">{{ $notesOverview['total_notes'] }}</p>
                        <p class="mt-1 text-sm text-white/65">note(s) enregistree(s)</p>
                    </div>
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Moyenne annuelle</p>
                        <p class="mt-2 text-2xl font-semibold text-white">{{ $formatNote($notesOverview['moyenne_annuelle']) }}</p>
                        <p class="mt-1 text-sm text-white/65">sur 20</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-[320px_minmax(0,1fr)]">
        <aside class="detail-panel" data-tilt>
            <div class="relative">
                <div class="text-center">
                    <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                        <i class="bi bi-person-circle text-5xl"></i>
                    </div>
                    <h3 class="mt-5 text-xl font-semibold tracking-tight text-slate-900">{{ $eleve->nom }} {{ $eleve->prenom }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $eleve->matricule }}</p>
                </div>

                <div class="mt-6 space-y-3">
                    <div class="detail-stat">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Classe actuelle</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $eleve->resolved_classe ? $eleve->resolved_classe->nom_classe : 'Non inscrit' }}</p>
                    </div>
                    <div class="detail-stat">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Date de naissance</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $eleve->date_naissance->format('d/m/Y') }}</p>
                    </div>
                    <div class="detail-stat">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Lieu de naissance</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $eleve->lieu_naissance ?: 'Non renseigne' }}</p>
                    </div>
                    <div class="detail-stat">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Sexe</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $eleve->sexe === 'M' ? 'Masculin' : 'Feminin' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <section class="card overflow-hidden">
            <div class="card-header">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h4>Notes de l'annee</h4>
                        @if($annee)
                            <span class="badge-blue">{{ $annee->libelle }}</span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Vue detaillee des evaluations, organisee par semestre puis par matiere.</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <a href="{{ route('bulletins.pdf', ['id' => $eleve->id, 'semestre' => 1, 'date' => request()->query('date')]) }}" class="btn-danger btn-sm justify-center">
                        <i class="bi bi-file-earmark-pdf"></i>
                        Bulletin S1
                    </a>
                    <a href="{{ route('bulletins.pdf', ['id' => $eleve->id, 'semestre' => 2, 'date' => request()->query('date')]) }}" class="btn-secondary btn-sm justify-center">
                        <i class="bi bi-file-earmark-pdf"></i>
                        Bulletin S2
                    </a>
                </div>
            </div>

            <div class="grid gap-4 border-b border-slate-100 bg-slate-50/60 px-5 py-5 sm:grid-cols-2 xl:grid-cols-5">
                <div class="detail-stat">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Evaluations</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $notesOverview['total_notes'] }}</p>
                    <p class="mt-1 text-xs text-slate-500">notes enregistrees</p>
                </div>
                <div class="detail-stat">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Matieres</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $notesOverview['total_matieres'] }}</p>
                    <p class="mt-1 text-xs text-slate-500">matieres evaluees</p>
                </div>
                <div class="detail-stat">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Moyenne annuelle</p>
                    <p class="mt-2 text-2xl font-semibold {{ $notesOverview['moyenne_annuelle'] === null ? 'text-slate-400' : ($notesOverview['moyenne_annuelle'] >= 10 ? 'text-emerald-600' : 'text-red-600') }}">{{ $formatNote($notesOverview['moyenne_annuelle']) }}</p>
                    <p class="mt-1 text-xs text-slate-500">sur 20</p>
                </div>
                <div class="detail-stat">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Moyenne S1</p>
                    <p class="mt-2 text-2xl font-semibold {{ $notesOverview['moyenne_semestre_1'] === null ? 'text-slate-400' : ($notesOverview['moyenne_semestre_1'] >= 10 ? 'text-emerald-600' : 'text-red-600') }}">{{ $formatNote($notesOverview['moyenne_semestre_1']) }}</p>
                    <p class="mt-1 text-xs text-slate-500">premier semestre</p>
                </div>
                <div class="detail-stat">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Moyenne S2</p>
                    <p class="mt-2 text-2xl font-semibold {{ $notesOverview['moyenne_semestre_2'] === null ? 'text-slate-400' : ($notesOverview['moyenne_semestre_2'] >= 10 ? 'text-emerald-600' : 'text-red-600') }}">{{ $formatNote($notesOverview['moyenne_semestre_2']) }}</p>
                    <p class="mt-1 text-xs text-slate-500">deuxieme semestre</p>
                </div>
            </div>

            @if($notesBySemestre->isNotEmpty())
                <div class="space-y-5 p-5">
                    @foreach($notesBySemestre as $semestreGroup)
                        <section class="surface-card">
                            <div class="relative flex flex-col gap-4 border-b border-slate-100 pb-5 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="{{ $semestreGroup['semestre'] === 2 ? 'badge-yellow' : 'badge-blue' }}">{{ $semestreGroup['label'] }}</span>
                                        <span class="badge-gray">{{ $semestreGroup['total_notes'] }} evaluation(s)</span>
                                        <span class="badge-purple">{{ $semestreGroup['total_matieres'] }} matiere(s)</span>
                                    </div>
                                    <p class="mt-3 text-sm text-slate-500">Les evaluations sont regroupees par matiere pour simplifier la lecture du semestre.</p>
                                </div>
                                <div class="detail-stat min-w-[220px] text-left lg:text-right">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Moyenne du semestre</p>
                                    <p class="mt-1 text-xl font-semibold {{ $semestreGroup['moyenne_generale'] === null ? 'text-slate-400' : ($semestreGroup['moyenne_generale'] >= 10 ? 'text-emerald-600' : 'text-red-600') }}">{{ $formatNote($semestreGroup['moyenne_generale']) }}</p>
                                </div>
                            </div>

                            <div class="space-y-4 pt-5">
                                @foreach($semestreGroup['matieres'] as $matiereGroup)
                                    <article class="rounded-[24px] border border-slate-100 bg-white/80 p-4 shadow-sm">
                                        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                            <div>
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h5 class="text-base font-semibold text-slate-900">{{ $matiereGroup['matiere']->nom_matiere }}</h5>
                                                    <span class="badge-gray">{{ $matiereGroup['total_notes'] }} evaluation(s)</span>
                                                </div>
                                                <p class="mt-2 text-sm text-slate-500">Derniere saisie le {{ $matiereGroup['derniere_saisie'] ? $matiereGroup['derniere_saisie']->format('d/m/Y') : '--' }}.</p>
                                            </div>

                                            <div class="grid gap-3 sm:grid-cols-3 xl:min-w-[420px]">
                                                <div class="detail-stat">
                                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Moy. devoirs</p>
                                                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $formatNote($matiereGroup['moyenne_devoirs']) }}</p>
                                                </div>
                                                <div class="detail-stat">
                                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Composition</p>
                                                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $formatNote($matiereGroup['note_composition']) }}</p>
                                                </div>
                                                <div class="detail-stat">
                                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Moy. matiere</p>
                                                    <p class="mt-2 text-lg font-semibold {{ $matiereGroup['moyenne_matiere'] === null ? 'text-slate-400' : ($matiereGroup['moyenne_matiere'] >= 10 ? 'text-emerald-600' : 'text-red-600') }}">{{ $formatNote($matiereGroup['moyenne_matiere']) }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 overflow-x-auto rounded-[22px] border border-slate-100 bg-white">
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
                                                                <span class="{{ $note->type_devoir === 'composition' ? 'badge-yellow' : 'badge-gray' }}">{{ ucfirst($note->type_devoir) }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="text-base font-semibold {{ $note->note >= 10 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($note->note, 2, ',', ' ') }}</span>
                                                            </td>
                                                            <td class="text-right text-sm text-slate-400">{{ $note->created_at->format('d/m/Y') }}</td>
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
                <div class="card-body">
                    <div class="empty-state">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                            <i class="bi bi-journal-x text-2xl"></i>
                        </div>
                        <p class="mt-5 text-sm font-semibold text-slate-900">Aucune note enregistree</p>
                        <p class="mt-2 text-sm text-slate-500">Les notes de cet eleve apparaitront ici des qu'elles seront saisies.</p>
                    </div>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection

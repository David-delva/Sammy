@extends('layouts.app')

@section('title', 'Reinscrire un eleve')
@section('breadcrumb', 'Scolarite / Eleves / Reinscription')

@section('content')
<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Scolarite</p>
                <h2 class="page-title">Reinscrire un eleve deja connu, sans recreer son profil.</h2>
                <p class="page-lead">Retrouvez les anciens profils non inscrits dans l'annee selectionnee et rattachez-les directement a une classe.</p>

                <div class="hero-badges">
                    @if($annee)
                        <span class="hero-badge"><i class="bi bi-calendar2-week"></i>{{ $annee->libelle }}</span>
                    @endif
                    @if($candidates)
                        <span class="hero-badge"><i class="bi bi-person-lines-fill"></i>{{ $candidates->total() }} profil(s) disponible(s)</span>
                    @endif
                </div>

                <div class="hero-actions">
                    <a href="{{ route('eleves.index', ['date' => request()->query('date')]) }}" class="btn-secondary justify-center sm:w-auto">
                        <i class="bi bi-arrow-left"></i>
                        Retour a la liste
                    </a>
                    @if($canManageAcademicData)
                        <a href="{{ route('eleves.create', ['date' => request()->query('date')]) }}" class="btn-primary justify-center shadow-lg shadow-cobalt-600/20 sm:w-auto">
                            <i class="bi bi-person-plus"></i>
                            Nouveau profil
                        </a>
                    @endif
                </div>
            </div>

            <aside class="hero-panel" data-tilt>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Bon reflexe</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-tight text-white">L'identite eleve reste unique, l'inscription reste annuelle.</h3>
                    <p class="mt-3 text-sm leading-7 text-white/70">Ce parcours evite les doublons de profils tout en gardant l'historique scolaire deja enregistre.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Objectif</p>
                        <p class="mt-2 text-2xl font-semibold text-white">0 doublon</p>
                        <p class="mt-1 text-sm text-white/65">un seul profil par eleve</p>
                    </div>
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Action</p>
                        <p class="mt-2 text-2xl font-semibold text-white">1 etape</p>
                        <p class="mt-1 text-sm text-white/65">choisir la classe annuelle</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="card overflow-hidden">
        <div class="card-header">
            <div>
                <h4>Rechercher un eleve existant</h4>
                <p class="text-xs text-slate-500">Filtrez par matricule, nom ou prenom pour retrouver rapidement le bon profil.</p>
            </div>
            <form method="GET" action="{{ route('eleves.reinscriptions.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                @if(request()->has('date'))
                    <input type="hidden" name="date" value="{{ request()->query('date') }}">
                @endif
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Matricule, nom ou prenom"
                    class="form-input min-w-[240px]"
                >
                <button type="submit" class="btn-primary justify-center">
                    <i class="bi bi-search"></i>
                    Rechercher
                </button>
                @if($search !== '')
                    <a href="{{ route('eleves.reinscriptions.index', ['date' => request()->query('date')]) }}" class="btn-secondary justify-center">
                        <i class="bi bi-x-lg"></i>
                        Reinitialiser
                    </a>
                @endif
            </form>
        </div>

        @if(! $annee)
            <div class="card-body">
                <div class="empty-state">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-warning-100 text-warning-600">
                        <i class="bi bi-exclamation-triangle text-2xl"></i>
                    </div>
                    <p class="mt-5 text-sm font-semibold text-slate-900">Aucune annee academique selectionnee</p>
                    <p class="mt-2 text-sm text-slate-500">Creez ou selectionnez une annee avant de reinscrire un eleve.</p>
                </div>
            </div>
        @elseif($candidates && $candidates->count() > 0)
            <div class="grid gap-4 p-5 lg:grid-cols-2">
                @foreach($candidates as $candidate)
                    <article class="surface-card space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-cobalt-100 text-cobalt-700">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h5 class="text-base font-semibold text-slate-900">{{ $candidate->nom }} {{ $candidate->prenom }}</h5>
                                    <span class="badge-gray">{{ $candidate->matricule }}</span>
                                </div>
                                <p class="mt-2 text-sm text-slate-500">Ne a {{ optional($candidate->date_naissance)->format('d/m/Y') ?: '--' }} a {{ $candidate->lieu_naissance ?: 'Non renseigne' }}.</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @if($candidate->latestInscription?->classe)
                                        <span class="badge-blue">Derniere classe : {{ $candidate->latestInscription->classe->nom_classe }}</span>
                                    @else
                                        <span class="badge-gray">Aucune inscription precedente</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('eleves.reinscriptions.store', ['eleve' => $candidate, 'date' => request()->query('date')]) }}" method="POST" class="grid gap-3 border-t border-slate-100 pt-4 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-end">
                            @csrf
                            <div class="form-field">
                                <label for="classe_id_{{ $candidate->id }}" class="form-label">Classe pour {{ $annee->libelle }}</label>
                                <select id="classe_id_{{ $candidate->id }}" name="classe_id" class="form-select @error('classe_id') error @enderror" required>
                                    <option value="">Selectionner une classe</option>
                                    @foreach($classes as $classe)
                                        <option value="{{ $classe->id }}">{{ $classe->nom_classe }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn-primary justify-center">
                                <i class="bi bi-arrow-repeat"></i>
                                Reinscrire
                            </button>
                        </form>
                    </article>
                @endforeach
            </div>

            @if($candidates->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $candidates->links() }}
                </div>
            @endif
        @else
            <div class="card-body">
                <div class="empty-state">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <i class="bi bi-search text-2xl"></i>
                    </div>
                    <p class="mt-5 text-sm font-semibold text-slate-900">Aucun profil disponible</p>
                    <p class="mt-2 text-sm text-slate-500">Tous les profils semblent deja inscrits pour {{ $annee->libelle }} ou ne correspondent pas a votre recherche.</p>
                </div>
            </div>
        @endif
    </section>
</div>
@endsection

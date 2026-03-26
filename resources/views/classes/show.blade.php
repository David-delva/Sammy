@extends('layouts.app')

@section('title', $classe->nom_classe)
@section('breadcrumb', 'Administration / Classes / Detail')

@section('content')
<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Classe</p>
                <h2 class="page-title">{{ $classe->nom_classe }} centralise eleves, matieres et acces rapides.</h2>
                <p class="page-lead">
                    Consultez la composition de la classe et les matieres rattachees dans une vue qui reste agreable a parcourir sur mobile comme sur grand ecran.
                </p>

                <div class="hero-badges">
                    <span class="hero-badge"><i class="bi bi-people"></i>{{ $classe->eleves->count() }} eleve(s)</span>
                    <span class="hero-badge"><i class="bi bi-book"></i>{{ $classe->matieres->count() }} matiere(s)</span>
                    @if(request()->query('date'))
                        <span class="hero-badge"><i class="bi bi-calendar2-week"></i>{{ \App\Models\AnneeAcademique::labelForDate(request()->query('date')) }}</span>
                    @endif
                </div>

                <div class="hero-actions">
                    <a href="{{ route('classes.index', ['date' => request()->query('date')]) }}" class="btn-secondary justify-center sm:w-auto">
                        <i class="bi bi-arrow-left"></i>
                        Retour aux classes
                    </a>
                </div>
            </div>

            <aside class="hero-panel" data-tilt>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Vue synthese</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-tight text-white">Deux panneaux pour lire l'effectif et le programme de la classe.</h3>
                    <p class="mt-3 text-sm leading-7 text-white/70">Les cartes internes conservent la meme structure visuelle que le reste du tableau de bord administratif.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Effectif</p>
                        <p class="mt-2 text-2xl font-semibold text-white">{{ $classe->eleves->count() }}</p>
                        <p class="mt-1 text-sm text-white/65">inscrit(s)</p>
                    </div>
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Programme</p>
                        <p class="mt-2 text-2xl font-semibold text-white">{{ $classe->matieres->count() }}</p>
                        <p class="mt-1 text-sm text-white/65">matiere(s)</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="card overflow-hidden">
            <div class="card-header">
                <div>
                    <h4>Eleves</h4>
                    <p class="text-xs text-slate-500">Liste des profils actuellement rattaches a cette classe.</p>
                </div>
                <span class="badge-blue">{{ $classe->eleves->count() }} inscrit(s)</span>
            </div>

            @if($classe->eleves->isEmpty())
                <div class="card-body">
                    <div class="empty-state">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                            <i class="bi bi-people text-2xl"></i>
                        </div>
                        <p class="mt-5 text-sm font-semibold text-slate-900">Aucun eleve pour cette classe</p>
                        <p class="mt-2 text-sm text-slate-500">Les futurs inscrits apparaitront ici automatiquement.</p>
                    </div>
                </div>
            @else
                <div class="mobile-list">
                    @foreach($classe->eleves as $eleve)
                        <article class="mobile-list-item sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $eleve->nom }} {{ $eleve->prenom }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $eleve->matricule ?? 'Matricule non renseigne' }}</p>
                            </div>
                            <a href="{{ route('eleves.show', $eleve) }}" class="btn-secondary btn-sm mt-3 sm:mt-0">
                                <i class="bi bi-eye"></i>
                                Profil
                            </a>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="card overflow-hidden">
            <div class="card-header">
                <div>
                    <h4>Matieres</h4>
                    <p class="text-xs text-slate-500">Programme associe a la classe avec indication du coefficient quand il existe.</p>
                </div>
                <span class="badge-purple">{{ $classe->matieres->count() }} matiere(s)</span>
            </div>

            @if($classe->matieres->isEmpty())
                <div class="card-body">
                    <div class="empty-state">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                            <i class="bi bi-book text-2xl"></i>
                        </div>
                        <p class="mt-5 text-sm font-semibold text-slate-900">Aucune matiere associee</p>
                        <p class="mt-2 text-sm text-slate-500">Utilisez le module d'assignation pour relier les matieres a cette classe.</p>
                    </div>
                </div>
            @else
                <div class="mobile-list">
                    @foreach($classe->matieres as $matiere)
                        <article class="mobile-list-item sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $matiere->nom_matiere ?? $matiere->nom }}</p>
                                <p class="mt-1 text-xs text-slate-500">Matiere du programme</p>
                            </div>
                            @if(isset($matiere->pivot->coefficient))
                                <span class="badge-gray mt-3 sm:mt-0">Coef. {{ $matiere->pivot->coefficient }}</span>
                            @else
                                <span class="badge-gray mt-3 sm:mt-0">Sans coefficient</span>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
@endsection

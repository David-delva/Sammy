@extends('layouts.app')

@section('title', 'Classes')
@section('breadcrumb', 'Administration / Classes')

@section('content')
<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Administration</p>
                <h2 class="page-title">Les classes restent consultables et actionnables sans perdre la lisibilite sur petit ecran.</h2>
                <p class="page-lead">
                    Gere les effectifs, ouvre les fiches de classe et lance les impressions utiles depuis une interface beaucoup plus nette.
                </p>

                <div class="hero-badges">
                    <span class="hero-badge"><i class="bi bi-building"></i>{{ $classes->total() }} classe(s)</span>
                    @if(request()->query('date'))
                        <span class="hero-badge"><i class="bi bi-calendar2-week"></i>{{ \App\Models\AnneeAcademique::labelForDate(request()->query('date')) }}</span>
                    @endif
                </div>

                <div class="hero-actions">
                    @if($canManageAcademicData)
                        <a href="{{ route('classes.create', ['date' => request()->query('date')]) }}" class="btn-primary justify-center shadow-lg shadow-brand-600/20 sm:w-auto">
                            <i class="bi bi-plus-lg"></i>
                            Nouvelle classe
                        </a>
                    @endif
                    <a href="{{ route('matieres.assigner') }}" class="btn-secondary justify-center sm:w-auto">
                        <i class="bi bi-diagram-3"></i>
                        Gerer les matieres
                    </a>
                </div>
            </div>

            <aside class="hero-panel" data-tilt>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Structure</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-tight text-white">Chaque classe donne acces aux eleves, aux feuilles d'appel et aux matieres associees.</h3>
                    <p class="mt-3 text-sm leading-7 text-white/70">La liste est optimisee en cartes sur mobile et en tableau detaille sur grand ecran.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Consultation</p>
                        <p class="mt-2 text-2xl font-semibold text-white">Mobile</p>
                        <p class="mt-1 text-sm text-white/65">cartes et actions directes</p>
                    </div>
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Exports</p>
                        <p class="mt-2 text-2xl font-semibold text-white">PDF</p>
                        <p class="mt-1 text-sm text-white/65">feuille d'appel en un clic</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="card overflow-hidden">
        <div class="card-header">
            <div>
                <h4>Repertoire des classes</h4>
                <p class="text-xs text-slate-500">Liste des classes pedagogiques avec acces direct aux actions les plus utiles.</p>
            </div>
            <span class="badge-blue">{{ $classes->total() }} classe(s)</span>
        </div>

        <div class="mobile-list sm:hidden">
            @forelse($classes as $classe)
                <article class="mobile-list-item">
                    <div class="flex items-start gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-brand-700">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-base font-semibold text-slate-900">{{ $classe->nom_classe }}</p>
                            <p class="mt-1 text-sm text-slate-500">Classe pedagogique</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="badge-blue">{{ $classe->eleves_count }} eleve(s)</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-2 border-t border-slate-100 pt-4">
                        <a href="{{ route('classes.show', ['classe' => $classe, 'date' => request()->query('date')]) }}" class="btn-secondary justify-center">
                            <i class="bi bi-eye"></i>
                            Voir
                        </a>
                        <a href="{{ route('classes.liste.pdf', ['classe' => $classe, 'date' => request()->query('date')]) }}" class="btn-secondary justify-center">
                            <i class="bi bi-printer"></i>
                            Feuille d'appel
                        </a>
                        @if($canManageAcademicData)
                            <a href="{{ route('classes.edit', ['classe' => $classe, 'date' => request()->query('date')]) }}" class="btn-secondary justify-center">
                                <i class="bi bi-pencil"></i>
                                Modifier
                            </a>
                            <form action="{{ route('classes.destroy', $classe) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger w-full justify-center">
                                    <i class="bi bi-trash"></i>
                                    Supprimer
                                </button>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="empty-state m-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <i class="bi bi-building text-2xl"></i>
                    </div>
                    <p class="mt-5 text-sm font-semibold text-slate-900">Aucune classe enregistree</p>
                    <p class="mt-2 text-sm text-slate-500">Creez une classe pour commencer a organiser les inscriptions et les matieres.</p>
                </div>
            @endforelse
        </div>

        <div class="hidden overflow-x-auto sm:block">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nom de la classe</th>
                        <th>Effectif</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $classe)
                        <tr>
                            <td>
                                <div class="font-semibold text-slate-900">{{ $classe->nom_classe }}</div>
                                <div class="mt-1 text-xs text-slate-400">Classe pedagogique</div>
                            </td>
                            <td>
                                <span class="badge-blue">{{ $classe->eleves_count }} eleve(s)</span>
                            </td>
                            <td>
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('classes.show', ['classe' => $classe, 'date' => request()->query('date')]) }}" class="btn-secondary btn-sm">
                                        <i class="bi bi-eye"></i>
                                        Voir
                                    </a>
                                    <a href="{{ route('classes.liste.pdf', ['classe' => $classe, 'date' => request()->query('date')]) }}" class="btn-secondary btn-sm">
                                        <i class="bi bi-printer"></i>
                                        Feuille d'appel
                                    </a>
                                    @if($canManageAcademicData)
                                        <a href="{{ route('classes.edit', ['classe' => $classe, 'date' => request()->query('date')]) }}" class="btn-secondary btn-sm">
                                            <i class="bi bi-pencil"></i>
                                            Modifier
                                        </a>
                                        <form action="{{ route('classes.destroy', $classe) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                                Supprimer
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                        <i class="bi bi-building text-2xl"></i>
                                    </div>
                                    <p class="mt-5 text-sm font-semibold text-slate-900">Aucune classe enregistree</p>
                                    <p class="mt-2 text-sm text-slate-500">Creez une classe pour commencer a organiser les inscriptions et les matieres.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($classes->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $classes->links() }}
            </div>
        @endif
    </section>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Matieres')
@section('breadcrumb', 'Administration / Matieres')

@section('content')
<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Administration</p>
                <h2 class="page-title">Un catalogue matieres plus lisible pour preparer l'assignation et les coefficients.</h2>
                <p class="page-lead">
                    Organisez les matieres du programme, visualisez leur diffusion dans les classes et passez rapidement a l'assignation annuelle.
                </p>

                <div class="hero-badges">
                    <span class="hero-badge"><i class="bi bi-book"></i>{{ $matieres->total() }} matiere(s)</span>
                    @if(isset($annee) && $annee)
                        <span class="hero-badge"><i class="bi bi-calendar2-week"></i>{{ $annee->libelle }}</span>
                    @endif
                </div>

                <div class="hero-actions">
                    @if($canManageAcademicData)
                        <a href="{{ route('matieres.assigner') }}" class="btn-secondary justify-center sm:w-auto">
                            <i class="bi bi-diagram-3"></i>
                            Assigner aux classes
                        </a>
                        <a href="{{ route('matieres.create') }}" class="btn-primary justify-center shadow-lg shadow-brand-600/20 sm:w-auto">
                            <i class="bi bi-plus-lg"></i>
                            Nouvelle matiere
                        </a>
                    @endif
                </div>
            </div>

            <aside class="hero-panel" data-tilt>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Pedagogie</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-tight text-white">Le catalogue reste connecte aux classes pour faciliter la saisie des notes.</h3>
                    <p class="mt-3 text-sm leading-7 text-white/70">Chaque entree sert ensuite a composer les programmes par classe et a alimenter les ecrans de notes.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Catalogue</p>
                        <p class="mt-2 text-2xl font-semibold text-white">Global</p>
                        <p class="mt-1 text-sm text-white/65">base unique et claire</p>
                    </div>
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Assignation</p>
                        <p class="mt-2 text-2xl font-semibold text-white">Rapide</p>
                        <p class="mt-1 text-sm text-white/65">par classe et coefficient</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="card overflow-hidden">
        <div class="card-header">
            <div>
                <h4>Matieres enregistrees</h4>
                <p class="text-xs text-slate-500">Acces simple a l'edition du catalogue et a la suppression des doublons.</p>
            </div>
            <span class="badge-blue">{{ $matieres->total() }} matiere(s)</span>
        </div>

        <div class="mobile-list sm:hidden">
            @forelse($matieres as $matiere)
                <article class="mobile-list-item">
                    <div class="flex items-start gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-brand-700">
                            <i class="bi bi-journal-bookmark-fill"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-base font-semibold text-slate-900">{{ $matiere->nom_matiere }}</p>
                            <p class="mt-1 text-sm text-slate-500">Catalogue pedagogique</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="badge-blue">{{ $matiere->classes_count }} classe(s)</span>
                            </div>
                        </div>
                    </div>

                    @if($canManageAcademicData)
                        <div class="mt-4 grid gap-2 border-t border-slate-100 pt-4">
                            <a href="{{ route('matieres.edit', $matiere) }}" class="btn-secondary justify-center">
                                <i class="bi bi-pencil"></i>
                                Modifier
                            </a>
                            <form action="{{ route('matieres.destroy', $matiere) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger w-full justify-center">
                                    <i class="bi bi-trash"></i>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    @endif
                </article>
            @empty
                <div class="empty-state m-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <i class="bi bi-journal-bookmark text-2xl"></i>
                    </div>
                    <p class="mt-5 text-sm font-semibold text-slate-900">Aucune matiere enregistree</p>
                    <p class="mt-2 text-sm text-slate-500">Commencez par creer les matieres du catalogue avant de les assigner aux classes.</p>
                </div>
            @endforelse
        </div>

        <div class="hidden overflow-x-auto sm:block">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Matiere</th>
                        <th>Classes concernees</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($matieres as $matiere)
                        <tr>
                            <td>
                                <div class="font-semibold text-slate-900">{{ $matiere->nom_matiere }}</div>
                                <div class="mt-1 text-xs text-slate-400">Catalogue pedagogique</div>
                            </td>
                            <td>
                                <span class="badge-blue">{{ $matiere->classes_count }} classe(s)</span>
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    @if($canManageAcademicData)
                                        <a href="{{ route('matieres.edit', $matiere) }}" class="btn-secondary btn-sm">
                                            <i class="bi bi-pencil"></i>
                                            Modifier
                                        </a>
                                        <form action="{{ route('matieres.destroy', $matiere) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
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
                                        <i class="bi bi-journal-bookmark text-2xl"></i>
                                    </div>
                                    <p class="mt-5 text-sm font-semibold text-slate-900">Aucune matiere enregistree</p>
                                    <p class="mt-2 text-sm text-slate-500">Commencez par creer les matieres du catalogue avant de les assigner aux classes.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($matieres->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $matieres->links() }}
            </div>
        @endif
    </section>
</div>
@endsection

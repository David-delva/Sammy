@extends('layouts.app')

@section('title', 'Eleves')
@section('breadcrumb', 'Scolarite / Eleves')

@section('content')
<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Scolarite</p>
                <h2 class="page-title">Un repertoire eleves plus lisible, plus mobile et plus rapide a parcourir.</h2>
                <p class="page-lead">
                    Gere les inscriptions, consulte les profils et filtre instantanement par classe sans perdre le contexte de l'annee en cours.
                </p>

                <div class="hero-badges">
                    <span class="hero-badge"><i class="bi bi-people-fill"></i>{{ $eleves->total() }} eleve(s)</span>
                    @if(isset($annee) && $annee)
                        <span class="hero-badge"><i class="bi bi-calendar2-week"></i>{{ $annee->libelle }}</span>
                    @endif
                    @if($classeFilter)
                        <span class="hero-badge"><i class="bi bi-funnel"></i>Classe filtree</span>
                    @endif
                </div>

                <div class="hero-actions">
                    @if($canManageAcademicData)
                        <a href="{{ route('eleves.create', ['date' => request()->query('date')]) }}" class="btn-primary justify-center shadow-lg shadow-brand-600/20 sm:w-auto">
                            <i class="bi bi-person-plus"></i>
                            Inscrire un eleve
                        </a>
                    @endif
                    <a href="{{ route('classement.index', ['date' => request()->query('date')]) }}" class="btn-secondary justify-center sm:w-auto">
                        <i class="bi bi-trophy"></i>
                        Voir le classement
                    </a>
                </div>
            </div>

            <aside class="hero-panel" data-tilt>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Controle rapide</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-tight text-white">Chaque profil reste accesible en quelques gestes.</h3>
                    <p class="mt-3 text-sm leading-7 text-white/70">Le listing combine cartes mobiles, tableau desktop, filtre par classe et acces direct aux details ou a l'edition.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Navigation</p>
                        <p class="mt-2 text-2xl font-semibold text-white">Fluides</p>
                        <p class="mt-1 text-sm text-white/65">cartes mobiles + table large</p>
                    </div>
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Acces</p>
                        <p class="mt-2 text-2xl font-semibold text-white">1 clic</p>
                        <p class="mt-1 text-sm text-white/65">profil, edition, suppression</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="card overflow-hidden">
        <div class="card-header">
            <div>
                <h4>Liste des eleves</h4>
                <p class="text-xs text-slate-500">Filtre par classe et parcours responsive pour toutes les tailles d'ecran.</p>
            </div>
            <form method="GET" action="{{ route('eleves.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                @if(request()->has('date'))
                    <input type="hidden" name="date" value="{{ request()->query('date') }}">
                @endif
                <select name="classe" onchange="this.form.submit()" class="form-select min-w-[220px]">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ $classeFilter == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom_classe }}
                        </option>
                    @endforeach
                </select>
                @if($classeFilter)
                    <a href="{{ route('eleves.index', ['date' => request()->query('date')]) }}" class="btn-secondary justify-center">
                        <i class="bi bi-x-lg"></i>
                        Reinitialiser
                    </a>
                @endif
            </form>
        </div>

        <div class="mobile-list sm:hidden">
            @forelse($eleves as $eleve)
                <article class="mobile-list-item">
                    <div class="flex items-start gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-100 text-brand-700">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ $eleve->nom }} {{ $eleve->prenom }}</p>
                                <span class="badge-gray">{{ $eleve->sexe === 'M' ? 'M' : 'F' }}</span>
                            </div>
                            <p class="mt-1 truncate text-xs text-slate-500">{{ $eleve->matricule }}</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @if($eleve->resolved_classe)
                                    <span class="badge-blue"><i class="bi bi-door-open"></i>{{ $eleve->resolved_classe->nom_classe }}</span>
                                @else
                                    <span class="badge-gray">Non inscrit</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-2 border-t border-slate-100 pt-4">
                        <a href="{{ route('eleves.show', ['eleve' => $eleve, 'date' => request()->query('date')]) }}" class="btn-secondary justify-center">
                            <i class="bi bi-eye"></i>
                            Details
                        </a>
                        @if($canManageAcademicData)
                            <a href="{{ route('eleves.edit', ['eleve' => $eleve, 'date' => request()->query('date')]) }}" class="btn-secondary justify-center">
                                <i class="bi bi-pencil"></i>
                                Modifier
                            </a>
                            <form action="{{ route('eleves.destroy', $eleve) }}" method="POST" onsubmit="return confirm('Supprimer cet eleve ?')">
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
                        <i class="bi bi-people text-2xl"></i>
                    </div>
                    <p class="mt-5 text-sm font-semibold text-slate-900">Aucun eleve trouve</p>
                    <p class="mt-2 text-sm text-slate-500">Commencez par inscrire un eleve pour alimenter les classes, les notes et les bulletins.</p>
                </div>
            @endforelse
        </div>

        <div class="hidden overflow-x-auto sm:block">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Eleve</th>
                        <th>Classe actuelle</th>
                        <th>Sexe</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eleves as $eleve)
                        <tr>
                            <td>
                                <span class="font-semibold tracking-wide text-slate-500">{{ $eleve->matricule }}</span>
                            </td>
                            <td>
                                <div class="font-semibold text-slate-900">{{ $eleve->nom }} {{ $eleve->prenom }}</div>
                                <div class="mt-1 text-xs text-slate-400">Profil eleve</div>
                            </td>
                            <td>
                                @if($eleve->resolved_classe)
                                    <span class="badge-blue">{{ $eleve->resolved_classe->nom_classe }}</span>
                                @else
                                    <span class="badge-gray">Non inscrit</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-gray">{{ $eleve->sexe === 'M' ? 'Masculin' : 'Feminin' }}</span>
                            </td>
                            <td>
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('eleves.show', ['eleve' => $eleve, 'date' => request()->query('date')]) }}" class="btn-secondary btn-sm">
                                        <i class="bi bi-eye"></i>
                                        Details
                                    </a>
                                    @if($canManageAcademicData)
                                        <a href="{{ route('eleves.edit', ['eleve' => $eleve, 'date' => request()->query('date')]) }}" class="btn-secondary btn-sm">
                                            <i class="bi bi-pencil"></i>
                                            Modifier
                                        </a>
                                        <form action="{{ route('eleves.destroy', $eleve) }}" method="POST" onsubmit="return confirm('Supprimer cet eleve ?')">
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
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                        <i class="bi bi-people text-2xl"></i>
                                    </div>
                                    <p class="mt-5 text-sm font-semibold text-slate-900">Aucun eleve trouve</p>
                                    <p class="mt-2 text-sm text-slate-500">Commencez par inscrire un eleve pour alimenter les classes, les notes et les bulletins.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($eleves->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $eleves->links() }}
            </div>
        @endif
    </section>
</div>
@endsection

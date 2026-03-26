@extends('layouts.app')

@section('title', 'Notes')
@section('breadcrumb', 'Evaluations / Notes')

@section('content')
<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Evaluations</p>
                <h2 class="page-title">Un registre de notes plus clair, filtreable et confortable sur mobile.</h2>
                <p class="page-lead">
                    Naviguez entre classe, matiere, type d'evaluation et semestre sans perdre le fil. Les cartes mobiles et le tableau detaille restent synchronises.
                </p>

                <div class="hero-badges">
                    <span class="hero-badge"><i class="bi bi-journal-check"></i>{{ $notes->total() }} note(s)</span>
                    @if(isset($currentAcademicLabel) && $currentAcademicLabel)
                        <span class="hero-badge"><i class="bi bi-calendar2-week"></i>{{ $currentAcademicLabel }}</span>
                    @endif
                    @if($selectedSemestre)
                        <span class="hero-badge"><i class="bi bi-calendar3"></i>{{ \App\Models\Note::semestreOptions()[$selectedSemestre] ?? 'Semestre' }}</span>
                    @endif
                </div>

                <div class="hero-actions">
                    @if($canManageAcademicData)
                        <a href="{{ route('notes.masse.index', ['date' => request()->query('date'), 'semestre' => $selectedSemestre]) }}" class="btn-secondary justify-center sm:w-auto">
                            <i class="bi bi-table"></i>
                            Saisie en masse
                        </a>
                        <a href="{{ route('notes.create', ['date' => request()->query('date'), 'semestre' => $selectedSemestre]) }}" class="btn-primary justify-center shadow-lg shadow-brand-600/20 sm:w-auto">
                            <i class="bi bi-plus-lg"></i>
                            Nouvelle note
                        </a>
                    @endif
                </div>
            </div>

            <aside class="hero-panel" data-tilt>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Lecture rapide</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-tight text-white">Une meme vue pour filtrer, consulter et corriger les evaluations.</h3>
                    <p class="mt-3 text-sm leading-7 text-white/70">La page met en avant les filtres actifs, les notes saisies et les actions d'edition ou de suppression.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Filtres</p>
                        <p class="mt-2 text-2xl font-semibold text-white">5 axes</p>
                        <p class="mt-1 text-sm text-white/65">recherche + classe + matiere</p>
                    </div>
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Edition</p>
                        <p class="mt-2 text-2xl font-semibold text-white">Directe</p>
                        <p class="mt-1 text-sm text-white/65">modifier ou supprimer</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="card overflow-hidden" x-data="{ filtersOpen: {{ request()->hasAny(['classe', 'matiere', 'type_devoir', 'search', 'semestre']) ? 'true' : 'false' }} }">
        <div class="card-header">
            <div>
                <h4>Filtres de recherche</h4>
                <p class="text-xs text-slate-500">Affinez les resultats avec les criteres les plus utiles pour la saisie et la verification.</p>
            </div>
            <button type="button" class="btn-secondary sm:hidden" @click="filtersOpen = !filtersOpen">
                <i class="bi" :class="filtersOpen ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                <span x-text="filtersOpen ? 'Masquer' : 'Afficher'"></span>
            </button>
        </div>

        <div x-show="filtersOpen || window.innerWidth >= 640" x-transition.opacity.duration.200ms>
            <div class="card-body">
                <form action="{{ route('notes.index') }}" method="GET" class="space-y-5">
                    @if(request()->query('date'))
                        <input type="hidden" name="date" value="{{ request()->query('date') }}">
                    @endif

                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                        <div class="xl:col-span-2">
                            <label class="form-label" for="search">Recherche</label>
                            <div class="input-group">
                                <span class="input-prefix"><i class="bi bi-search"></i></span>
                                <input type="text" id="search" name="search" value="{{ $search ?? '' }}" placeholder="Eleve, matiere, matricule...">
                            </div>
                        </div>

                        <div class="form-field">
                            <label class="form-label" for="classe">Classe</label>
                            <select id="classe" name="classe" class="form-select">
                                <option value="">Toutes les classes</option>
                                @foreach($classes as $classe)
                                    <option value="{{ $classe->id }}" {{ $selectedClasse == $classe->id ? 'selected' : '' }}>{{ $classe->nom_classe }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label class="form-label" for="matiere">Matiere</label>
                            <select id="matiere" name="matiere" class="form-select">
                                <option value="">Toutes les matieres</option>
                                @foreach($matieres as $matiere)
                                    <option value="{{ $matiere->id }}" {{ $selectedMatiere == $matiere->id ? 'selected' : '' }}>{{ $matiere->nom_matiere }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label class="form-label" for="type_devoir">Type</label>
                            <select id="type_devoir" name="type_devoir" class="form-select">
                                <option value="">Tous les types</option>
                                <option value="devoir" {{ $selectedType === 'devoir' ? 'selected' : '' }}>Devoir</option>
                                <option value="composition" {{ $selectedType === 'composition' ? 'selected' : '' }}>Composition</option>
                            </select>
                        </div>

                        <div class="form-field sm:col-span-2 xl:col-span-1">
                            <label class="form-label" for="semestre">Semestre</label>
                            <select id="semestre" name="semestre" class="form-select">
                                <option value="">Tous les semestres</option>
                                @foreach(\App\Models\Note::semestreOptions() as $value => $label)
                                    <option value="{{ $value }}" {{ (string) $selectedSemestre === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:flex-wrap sm:items-center">
                        <button type="submit" class="btn-primary justify-center">
                            <i class="bi bi-funnel"></i>
                            Filtrer
                        </button>
                        <a href="{{ route('notes.index', ['date' => request()->query('date')]) }}" class="btn-secondary justify-center">
                            <i class="bi bi-x-lg"></i>
                            Reinitialiser
                        </a>
                        <span class="sm:ml-auto inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 shadow-sm">
                            <i class="bi bi-journal-check text-brand-600"></i>
                            {{ $notes->total() }} note(s)
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="card overflow-hidden">
        <div class="card-header">
            <div>
                <h4>Registre des notes</h4>
                <p class="text-xs text-slate-500">Consultez les evaluations en cartes sur mobile ou en tableau detaille sur ecran large.</p>
            </div>
            <span class="badge-blue">{{ $notes->total() }} note(s)</span>
        </div>

        <div class="mobile-list sm:hidden">
            @forelse($notes as $note)
                <article class="mobile-list-item">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 font-bold text-sm">
                                    {{ substr($note->eleve->nom, 0, 1) }}{{ substr($note->eleve->prenom, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $note->eleve->nom }} {{ $note->eleve->prenom }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $note->eleve->matricule ?? 'Matricule indisponible' }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="badge-blue"><i class="bi bi-book-fill"></i>{{ $note->matiere->nom_matiere }}</span>
                                <span class="{{ $note->type_devoir === 'composition' ? 'badge-yellow' : 'badge-gray' }}">
                                    <i class="bi {{ $note->type_devoir === 'composition' ? 'bi-trophy-fill' : 'bi-file-text-fill' }}"></i>
                                    {{ ucfirst($note->type_devoir) }}
                                </span>
                                <span class="badge-purple"><i class="bi bi-calendar3"></i>{{ $note->semestre_label }}</span>
                            </div>
                        </div>
                        <div class="rounded-[20px] border border-slate-200 bg-white px-4 py-3 text-center shadow-sm">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Note</p>
                            <p class="mt-1 text-xl font-semibold {{ $note->note >= 10 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($note->note, 2, ',', ' ') }}</p>
                        </div>
                    </div>

                    @if($canManageAcademicData)
                        <div class="mt-4 grid gap-2 border-t border-slate-100 pt-4">
                            <a href="{{ route('notes.edit', ['note' => $note, 'date' => request()->query('date'), 'semestre' => $selectedSemestre]) }}" class="btn-secondary justify-center">
                                <i class="bi bi-pencil-square"></i>
                                Modifier
                            </a>
                            <form action="{{ route('notes.destroy', ['note' => $note, 'date' => request()->query('date')]) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger w-full justify-center">
                                    <i class="bi bi-trash-fill"></i>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    @endif
                </article>
            @empty
                <div class="empty-state m-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <i class="bi bi-journal-text text-2xl"></i>
                    </div>
                    <p class="mt-5 text-sm font-semibold text-slate-900">Aucune note enregistree</p>
                    <p class="mt-2 text-sm text-slate-500">Ajoutez une note manuellement ou utilisez la saisie en masse pour gagner du temps.</p>
                </div>
            @endforelse
        </div>

        <div class="hidden overflow-x-auto sm:block">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Eleve</th>
                        <th>Matiere</th>
                        <th class="text-center">Note</th>
                        <th>Type</th>
                        <th>Semestre</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notes as $note)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 font-bold text-sm">
                                        {{ substr($note->eleve->nom, 0, 1) }}{{ substr($note->eleve->prenom, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $note->eleve->nom }} {{ $note->eleve->prenom }}</div>
                                        <div class="mt-0.5 text-xs text-slate-500">{{ $note->eleve->matricule ?? 'Matricule indisponible' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-blue">{{ $note->matiere->nom_matiere }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-lg font-semibold {{ $note->note >= 10 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($note->note, 2, ',', ' ') }}</span>
                            </td>
                            <td>
                                <span class="{{ $note->type_devoir === 'composition' ? 'badge-yellow' : 'badge-gray' }}">{{ ucfirst($note->type_devoir) }}</span>
                            </td>
                            <td>
                                <span class="badge-purple">{{ $note->semestre_label }}</span>
                            </td>
                            <td>
                                @if($canManageAcademicData)
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('notes.edit', ['note' => $note, 'date' => request()->query('date'), 'semestre' => $selectedSemestre]) }}" class="btn-secondary btn-sm">
                                            <i class="bi bi-pencil-square"></i>
                                            Modifier
                                        </a>
                                        <form action="{{ route('notes.destroy', ['note' => $note, 'date' => request()->query('date')]) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger btn-sm">
                                                <i class="bi bi-trash-fill"></i>
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                        <i class="bi bi-journal-text text-2xl"></i>
                                    </div>
                                    <p class="mt-5 text-sm font-semibold text-slate-900">Aucune note enregistree</p>
                                    <p class="mt-2 text-sm text-slate-500">Ajoutez une note manuellement ou utilisez la saisie en masse pour gagner du temps.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notes->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $notes->links() }}
            </div>
        @endif
    </section>
</div>
@endsection

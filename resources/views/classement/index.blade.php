@extends('layouts.app')

@section('title', 'Classement')
@section('breadcrumb', 'Evaluations / Classement')

@section('content')
<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Evaluations</p>
                <h2 class="page-title">Compare les moyennes generales et explore le classement annuel classe par classe.</h2>
                <p class="page-lead">
                    Selectionne une classe pour afficher les rangs, les mentions et les acces rapides vers les profils eleves.
                </p>

                <div class="hero-badges">
                    <span class="hero-badge"><i class="bi bi-trophy"></i>{{ $classes->count() }} classe(s) disponibles</span>
                    @if(isset($annee) && $annee)
                        <span class="hero-badge"><i class="bi bi-calendar2-week"></i>{{ $annee->libelle }}</span>
                    @endif
                    @if($selectedClasseId && $classement->isNotEmpty())
                        <span class="hero-badge"><i class="bi bi-bar-chart"></i>{{ $classement->count() }} eleve(s) classes</span>
                    @endif
                </div>
            </div>

            <aside class="hero-panel" data-tilt>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Analyse</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-tight text-white">Une lecture immediate des performances annuelles.</h3>
                    <p class="mt-3 text-sm leading-7 text-white/70">Les cartes mobiles et le tableau detaille mettent en avant rang, moyenne et mention sans surcharge visuelle.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Lecture</p>
                        <p class="mt-2 text-2xl font-semibold text-white">Claire</p>
                        <p class="mt-1 text-sm text-white/65">rangs et mentions visibles</p>
                    </div>
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Sortie</p>
                        <p class="mt-2 text-2xl font-semibold text-white">PDF</p>
                        <p class="mt-1 text-sm text-white/65">export du classement</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="card">
        <div class="card-header">
            <div>
                <h4>Choisir une classe</h4>
                <p class="text-xs text-slate-500">Le classement apparait automatiquement apres selection.</p>
            </div>
            <span class="badge-gray">Filtre principal</span>
        </div>
        <div class="card-body">
            <form action="{{ route('classement.index') }}" method="GET" id="classeForm" class="grid gap-4 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
                <div class="form-field">
                    <label for="classe_id" class="form-label">Classe</label>
                    <select id="classe_id" name="classe_id" class="form-select" onchange="document.getElementById('classeForm').submit()">
                        <option value="">Choisir</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClasseId == $c->id ? 'selected' : '' }}>{{ $c->nom_classe }}</option>
                        @endforeach
                    </select>
                </div>

                @if($selectedClasseId && $classement->isNotEmpty())
                    <a href="{{ route('classement.pdf', $selectedClasseId) }}" class="btn-danger justify-center">
                        <i class="bi bi-file-earmark-pdf"></i>
                        Exporter en PDF
                    </a>
                @endif
            </form>
        </div>
    </section>

    @if($selectedClasseId)
        @if($classement->isNotEmpty())
            <section class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h4>{{ $classe->nom_classe }}</h4>
                        <p class="text-xs text-slate-500">{{ $classement->count() }} eleve(s) classes</p>
                    </div>
                    <span class="badge-blue">Classe selectionnee</span>
                </div>

                <div class="mobile-list sm:hidden">
                    @foreach($classement as $item)
                        @php
                            $mentionClass = match($item['mention']) {
                                'Excellent' => 'badge-green',
                                'Tres Bien' => 'badge-blue',
                                'Bien' => 'badge-purple',
                                'Assez Bien' => 'badge-gray',
                                default => 'badge-red',
                            };
                        @endphp
                        <article class="mobile-list-item">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        @if($item['rang'] == 1)
                                            <span class="badge-yellow"><i class="bi bi-award-fill"></i>1er</span>
                                        @else
                                            <span class="badge-gray">Rang {{ $item['rang'] }}</span>
                                        @endif
                                        <span class="{{ $mentionClass }}">{{ $item['mention'] }}</span>
                                    </div>
                                    <p class="mt-3 truncate text-base font-semibold text-slate-900">{{ $item['eleve']->nom }} {{ $item['eleve']->prenom }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $item['eleve']->matricule }}</p>
                                </div>
                                <div class="rounded-[20px] border border-slate-200 bg-white px-4 py-3 text-center shadow-sm">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Moy.</p>
                                    <p class="mt-1 text-xl font-semibold {{ $item['moyenne'] >= 10 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($item['moyenne'], 2) }}</p>
                                </div>
                            </div>

                            <div class="mt-4 border-t border-slate-100 pt-4">
                                <a href="{{ route('eleves.show', $item['eleve']->id) }}" class="btn-secondary w-full justify-center">
                                    <i class="bi bi-eye"></i>
                                    Voir le profil
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="hidden overflow-x-auto sm:block">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="text-center">Rang</th>
                                <th>Eleve</th>
                                <th class="text-center">Moyenne generale</th>
                                <th class="text-center">Mention</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classement as $item)
                                @php
                                    $mentionClass = match($item['mention']) {
                                        'Excellent' => 'badge-green',
                                        'Tres Bien' => 'badge-blue',
                                        'Bien' => 'badge-purple',
                                        'Assez Bien' => 'badge-gray',
                                        default => 'badge-red',
                                    };
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        @if($item['rang'] == 1)
                                            <span class="badge-yellow"><i class="bi bi-award-fill"></i>1</span>
                                        @else
                                            <span class="font-semibold text-slate-500">{{ $item['rang'] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-semibold text-slate-900">{{ $item['eleve']->nom }} {{ $item['eleve']->prenom }}</div>
                                        <div class="mt-1 text-xs text-slate-400">{{ $item['eleve']->matricule }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-lg font-semibold {{ $item['moyenne'] >= 10 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($item['moyenne'], 2) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="{{ $mentionClass }}">{{ $item['mention'] }}</span>
                                    </td>
                                    <td>
                                        <div class="flex justify-end">
                                            <a href="{{ route('eleves.show', $item['eleve']->id) }}" class="btn-secondary btn-sm">
                                                <i class="bi bi-eye"></i>
                                                Voir profil
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @else
            <div class="alert-info">
                <i class="bi bi-info-circle-fill"></i>
                <span>Aucune donnee de note disponible pour cette classe cette annee.</span>
            </div>
        @endif
    @endif
</div>
@endsection

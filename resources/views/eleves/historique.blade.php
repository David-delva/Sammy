@extends('layouts.app')

@section('title', 'Historique scolaire')
@section('breadcrumb', 'Scolarité / Élèves / Historique')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Historique</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Parcours scolaire</h2>
            <p class="mt-2 text-sm text-gray-500">{{ $eleve->nom }} {{ $eleve->prenom }} • {{ $eleve->matricule }}</p>
        </div>
        <a href="{{ route('eleves.show', $eleve) }}" class="btn-secondary self-start lg:self-auto">
            <i class="bi bi-arrow-left"></i>
            Retour au profil
        </a>
    </div>

    @if($historique->isEmpty())
        <div class="alert-info">
            <i class="bi bi-info-circle-fill"></i>
            <span>Aucune inscription historique trouvée pour cet élève.</span>
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-2">
            @foreach($historique as $item)
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h4>{{ $item['annee']->libelle }}</h4>
                            <p class="mt-1 text-xs text-gray-400">Parcours annuel</p>
                        </div>
                        @if($item['annee']->active)
                            <span class="badge-green">Année active</span>
                        @endif
                    </div>
                    <div class="card-body space-y-5">
                        <div class="flex items-center gap-4 rounded-xl border border-gray-100 bg-slate-50 px-4 py-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                <i class="bi bi-building text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Classe fréquentée</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $item['classe']->nom_classe }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 rounded-xl border border-gray-100 bg-slate-50 px-4 py-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $item['moyenne_generale'] >= 10 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                                <i class="bi bi-graph-up-arrow text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Moyenne générale</p>
                                <p class="mt-1 text-sm font-semibold {{ $item['moyenne_generale'] >= 10 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $item['moyenne_generale'] ? number_format($item['moyenne_generale'], 2) . ' / 20' : '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 px-5 py-4">
                        <a href="{{ route('eleves.show', $eleve) }}?date={{ explode('-', $item['annee']->libelle)[0] }}-09-01" class="btn-secondary btn-sm">
                            <i class="bi bi-arrow-right"></i>
                            Consulter cette année
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
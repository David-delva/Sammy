@extends('layouts.app')

@section('title', $classe->nom_classe)
@section('breadcrumb', 'Administration / Classes / Détail')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Classe</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">{{ $classe->nom_classe }}</h2>
            <p class="mt-2 text-sm text-gray-500">
                @if(request()->query('date'))
                    Vue de l'année <span class="font-medium text-gray-700">{{ \App\Models\AnneeAcademique::labelForDate(request()->query('date')) }}</span>
                @else
                    Vue synthétique des élèves et des matières associées.
                @endif
            </p>
        </div>

        <a href="{{ route('classes.index', ['date' => request()->query('date')]) }}" class="btn-secondary self-start lg:self-auto">
            <i class="bi bi-arrow-left"></i>
            Retour aux classes
        </a>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="card overflow-hidden">
            <div class="card-header">
                <h4>Élèves</h4>
                <span class="badge-blue">{{ $classe->eleves->count() }} inscrit(s)</span>
            </div>
            @if($classe->eleves->isEmpty())
                <div class="card-body text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-gray-400">
                        <i class="bi bi-people text-2xl"></i>
                    </div>
                    <p class="mt-4 text-sm font-medium text-gray-700">Aucun élève pour cette classe</p>
                    <p class="mt-1 text-sm text-gray-500">Les futurs inscrits apparaîtront ici automatiquement.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($classe->eleves as $eleve)
                        <div class="flex items-center justify-between gap-3 px-5 py-4">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $eleve->nom }} {{ $eleve->prenom }}</p>
                                <p class="mt-1 text-xs text-gray-400">{{ $eleve->matricule ?? 'Matricule non renseigné' }}</p>
                            </div>
                            <a href="{{ route('eleves.show', $eleve) }}" class="btn-secondary btn-sm">
                                <i class="bi bi-eye"></i>
                                Profil
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card overflow-hidden">
            <div class="card-header">
                <h4>Matières</h4>
                <span class="badge-purple">{{ $classe->matieres->count() }} matière(s)</span>
            </div>
            @if($classe->matieres->isEmpty())
                <div class="card-body text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-gray-400">
                        <i class="bi bi-book text-2xl"></i>
                    </div>
                    <p class="mt-4 text-sm font-medium text-gray-700">Aucune matière associée</p>
                    <p class="mt-1 text-sm text-gray-500">Utilisez le module d'assignation pour relier les matières à cette classe.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($classe->matieres as $matiere)
                        <div class="flex items-center justify-between gap-3 px-5 py-4">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $matiere->nom_matiere ?? $matiere->nom }}</p>
                                <p class="mt-1 text-xs text-gray-400">Matière du programme</p>
                            </div>
                            @if(isset($matiere->pivot->coefficient))
                                <span class="badge-gray">Coef. {{ $matiere->pivot->coefficient }}</span>
                            @else
                                <span class="badge-gray">Sans coefficient</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
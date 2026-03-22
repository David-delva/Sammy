@extends('layouts.app')

@section('title', 'Classement')
@section('breadcrumb', 'Évaluations / Classement')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Évaluations</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Classement annuel</h2>
            <p class="mt-2 text-sm text-gray-500">
                Comparez les moyennes générales des élèves
                @if(isset($annee) && $annee)
                    pour <span class="font-medium text-gray-700">{{ $annee->libelle }}</span>
                @endif.
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Filtrer le classement</h4>
            <span class="badge-gray">Choisir une classe</span>
        </div>
        <div class="card-body">
            <form action="{{ route('classement.index') }}" method="GET" id="classeForm" class="grid gap-5 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
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
    </div>

    @if($selectedClasseId)
        @if($classement->isNotEmpty())
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div>
                        <h4>{{ $classe->nom_classe }}</h4>
                        <p class="mt-1 text-xs text-gray-400">{{ $classement->count() }} élève(s) classé(s)</p>
                    </div>
                    <span class="badge-blue">Classe sélectionnée</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="text-center">Rang</th>
                                <th>Élève</th>
                                <th class="text-center">Moyenne générale</th>
                                <th class="text-center">Mention</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classement as $item)
                                @php
                                    $mentionClass = match($item['mention']) {
                                        'Excellent' => 'badge-green',
                                        'Très Bien' => 'badge-blue',
                                        'Bien' => 'badge-purple',
                                        'Assez Bien' => 'badge-gray',
                                        default => 'badge-red',
                                    };
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        @if($item['rang'] == 1)
                                            <span class="badge-yellow"><i class="bi bi-award-fill"></i> 1</span>
                                        @else
                                            <span class="font-semibold text-gray-500">{{ $item['rang'] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-semibold text-gray-900">{{ $item['eleve']->nom }} {{ $item['eleve']->prenom }}</div>
                                        <div class="mt-1 text-xs text-gray-400">{{ $item['eleve']->matricule }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-lg font-semibold {{ $item['moyenne'] >= 10 ? 'text-emerald-600' : 'text-red-600' }}">
                                            {{ number_format($item['moyenne'], 2) }}
                                        </span>
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
            </div>
        @else
            <div class="alert-info">
                <i class="bi bi-info-circle-fill"></i>
                <span>Aucune donnée de note disponible pour cette classe cette année.</span>
            </div>
        @endif
    @endif
</div>
@endsection
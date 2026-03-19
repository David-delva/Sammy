@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Tableau de Bord</h2>
    <div></div>
</div>

<div class="row g-4">
    @php
        $kpis = [
            ['label' => 'Classes', 'value' => $stats['total_classes'], 'icon' => 'bi-building', 'route' => route('classes.index')],
            ['label' => 'Élèves', 'value' => $stats['total_eleves'], 'icon' => 'bi-people', 'route' => route('eleves.index')],
            ['label' => 'Matières', 'value' => $stats['total_matieres'], 'icon' => 'bi-book', 'route' => route('matieres.index')],
            ['label' => 'Notes', 'value' => $stats['total_notes'], 'icon' => 'bi-clipboard-data', 'route' => route('notes.index')],
        ];
    @endphp

    @foreach($kpis as $kpi)
        @if($kpi['label'] !== 'Matières' && $kpi['label'] !== 'Notes')
            @php $allowed = true; @endphp
        @else
            @php $allowed = auth()->user()->role === 'admin'; @endphp
        @endif

        @if($allowed)
            <div class="col-md-6 col-lg-3">
                <div class="card card-cta">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:64px;height:64px;background:linear-gradient(135deg, rgba(13,110,253,0.12), rgba(13,110,253,0.06));">
                                <i class="bi {{ $kpi['icon'] }} fs-3 text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-muted">{{ $kpi['label'] }}</small>
                            <div class="d-flex align-items-baseline gap-2">
                                <h3 class="mb-0 count" data-count="{{ $kpi['value'] }}">{{ $kpi['value'] }}</h3>
                                <span class="text-muted small">tot.</span>
                            </div>
                            <div class="mt-2">
                                <a href="{{ $kpi['route'] }}" class="text-decoration-none">Voir <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>

<div class="row mt-5">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body d-flex flex-column gap-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0">Bienvenue sur le Système de Gestion Scolaire</h5>
                        <p class="mb-0 text-muted">Connecté en tant que <strong>{{ auth()->user()->name }}</strong></p>
                    </div>
                    <div>
                        <span class="badge bg-primary">{{ ucfirst(auth()->user()->role) }}</span>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-2">Fonctionnalités disponibles</h6>
                        <ul class="mb-0">
                            <li>Gestion des élèves et inscriptions</li>
                            <li>Génération des bulletins scolaires en PDF</li>
                            @if(auth()->user()->role === 'admin')
                                <li>Gestion des classes et matières</li>
                                <li>Saisie et modification des notes</li>
                                <li>Calcul automatique des moyennes et rangs</li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-2">Actions rapides</h6>
                        <div class="d-flex flex-column gap-2">
                            <a href="{{ route('eleves.create') }}" class="btn btn-outline-secondary btn-sm">Nouvel élève</a>
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('classes.create') }}" class="btn btn-outline-secondary btn-sm">Nouvelle classe</a>
                            @endif
                            <a href="{{ route('eleves.index') }}" class="btn btn-primary btn-sm">Parcourir les élèves</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Résumé rapide</h6>
                <dl class="row small mb-0">
                    <dt class="col-6 text-muted">Total classes</dt>
                    <dd class="col-6"><span class="count" data-count="{{ $stats['total_classes'] }}">{{ $stats['total_classes'] }}</span></dd>

                    <dt class="col-6 text-muted">Total élèves</dt>
                    <dd class="col-6"><span class="count" data-count="{{ $stats['total_eleves'] }}">{{ $stats['total_eleves'] }}</span></dd>

                    <dt class="col-6 text-muted">Total matières</dt>
                    <dd class="col-6"><span class="count" data-count="{{ $stats['total_matieres'] }}">{{ $stats['total_matieres'] }}</span></dd>

                    <dt class="col-6 text-muted">Total notes</dt>
                    <dd class="col-6"><span class="count" data-count="{{ $stats['total_notes'] }}">{{ $stats['total_notes'] }}</span></dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

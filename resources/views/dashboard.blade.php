@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('breadcrumb', 'Vue d\'ensemble')

@section('content')
@php
    $firstName = explode(' ', trim(auth()->user()->name))[0] ?? auth()->user()->name;
    $kpis = [
        ['label' => 'Classes actives', 'value' => $stats['total_classes'], 'icon' => 'bi-building', 'tone' => 'bg-blue-100 text-blue-700'],
        ['label' => 'Eleves inscrits', 'value' => $stats['total_eleves'], 'icon' => 'bi-people-fill', 'tone' => 'bg-brand-100 text-brand-700'],
        ['label' => 'Matieres', 'value' => $stats['total_matieres'], 'icon' => 'bi-book-fill', 'tone' => 'bg-emerald-100 text-emerald-700'],
        ['label' => 'Notes saisies', 'value' => $stats['total_notes'], 'icon' => 'bi-clipboard-data-fill', 'tone' => 'bg-amber-100 text-amber-700'],
    ];

    $actions = [
        [
            'label' => 'Inscrire un eleve',
            'href' => route('eleves.create'),
            'icon' => 'bi-person-plus-fill',
            'tone' => 'bg-brand-100 text-brand-700',
            'visible' => $canManageAcademicData,
            'description' => 'Creer rapidement une nouvelle inscription et la rattacher a une classe.',
        ],
        [
            'label' => 'Saisie en masse',
            'href' => route('notes.masse.index'),
            'icon' => 'bi-table',
            'tone' => 'bg-blue-100 text-blue-700',
            'visible' => $canManageAcademicData,
            'description' => 'Renseigner une evaluation complete par classe, matiere et semestre.',
        ],
        [
            'label' => 'Classement annuel',
            'href' => route('classement.index'),
            'icon' => 'bi-trophy-fill',
            'tone' => 'bg-amber-100 text-amber-700',
            'visible' => true,
            'description' => 'Consulter les moyennes generales et les rangs par classe.',
        ],
        [
            'label' => 'Repertoire des eleves',
            'href' => route('eleves.index'),
            'icon' => 'bi-people-fill',
            'tone' => 'bg-slate-200 text-slate-700',
            'visible' => true,
            'description' => 'Parcourir les profils, l\'historique et les bulletins disponibles.',
        ],
    ];
@endphp

<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Vue d'ensemble</p>
                <h2 class="page-title">Bonjour, {{ $firstName }}. Le pilotage de l'annee scolaire commence ici.</h2>
                <p class="page-lead">
                    L'interface rassemble les donnees essentielles de l'etablissement pour vous permettre d'agir vite,
                    sur mobile comme sur desktop.
                </p>

                <div class="hero-badges">
                    @if(isset($currentAcademicLabel))
                        <span class="hero-badge"><i class="bi bi-calendar2-week"></i>{{ $currentAcademicLabel }}</span>
                    @endif
                    <span class="hero-badge"><i class="bi bi-person-badge"></i>{{ ucfirst(auth()->user()->role) }}</span>
                    <span class="hero-badge"><i class="bi bi-clock-history"></i>{{ \Carbon\Carbon::now()->translatedFormat('l j F Y') }}</span>
                </div>

                <div class="hero-actions">
                    @if($canManageAcademicData)
                        <a href="{{ route('notes.masse.index') }}" class="btn-primary justify-center shadow-lg shadow-brand-600/20 sm:w-auto">
                            <i class="bi bi-table"></i>
                            Saisie en masse
                        </a>
                    @endif
                    <a href="{{ route('eleves.index') }}" class="btn-secondary justify-center sm:w-auto">
                        <i class="bi bi-arrow-right-circle"></i>
                        Ouvrir les eleves
                    </a>
                </div>
            </div>

            <aside class="hero-panel" data-tilt>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Resume instantane</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-tight text-white">Toutes les briques utiles pour gerer l'activite scolaire.</h3>
                    <p class="mt-3 text-sm leading-7 text-white/70">Les actions frequentes, les chiffres cles et le contexte annuel restent visibles des l'ouverture du tableau de bord.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Modules</p>
                        <p class="mt-2 text-3xl font-semibold text-white">6+</p>
                        <p class="mt-1 text-sm text-white/65">classes, eleves, notes, rangs</p>
                    </div>
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Contexte</p>
                        <p class="mt-2 text-3xl font-semibold text-white">Actif</p>
                        <p class="mt-1 text-sm text-white/65">annee academique synchronisee</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($kpis as $index => $kpi)
            <article class="stat-card" data-tilt data-reveal data-reveal-delay="{{ $index * 70 }}ms">
                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">{{ $kpi['label'] }}</p>
                        <p class="mt-4 text-4xl font-semibold tracking-tight text-slate-950" data-count="{{ $kpi['value'] }}">0</p>
                        <p class="mt-2 text-sm text-slate-500">Annee en cours</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $kpi['tone'] }}">
                        <i class="bi {{ $kpi['icon'] }} text-xl"></i>
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_360px]">
        <div class="card">
            <div class="card-header">
                <div>
                    <h4>Actions rapides</h4>
                    <p class="text-xs text-slate-500">Les raccourcis les plus utilises sont accessibles sans quitter l'ecran d'accueil.</p>
                </div>
                <span class="badge-blue">Operationnel</span>
            </div>
            <div class="card-body">
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($actions as $action)
                        @if($action['visible'])
                            <a href="{{ $action['href'] }}" class="action-card group" data-tilt>
                                <div class="relative">
                                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $action['tone'] }} transition-transform duration-200 group-hover:scale-105">
                                        <i class="bi {{ $action['icon'] }} text-lg"></i>
                                    </span>
                                    <h5 class="mt-5 text-lg font-semibold tracking-tight text-slate-900">{{ $action['label'] }}</h5>
                                    <p class="mt-3 text-sm leading-7 text-slate-500">{{ $action['description'] }}</p>
                                </div>
                                <span class="relative mt-6 inline-flex items-center gap-2 text-sm font-semibold text-brand-600">
                                    Ouvrir
                                    <i class="bi bi-arrow-right transition-transform duration-200 group-hover:translate-x-1"></i>
                                </span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <aside class="surface-card" data-tilt>
            <div class="relative space-y-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.26em] text-brand-600">Session</p>
                    <h4 class="mt-3 text-xl font-semibold tracking-tight text-slate-900">Compte et contexte</h4>
                </div>

                <div class="space-y-3">
                    <div class="surface-soft">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Utilisateur</p>
                        <p class="mt-2 text-base font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                        <p class="mt-2"><span class="badge-blue">{{ ucfirst(auth()->user()->role) }}</span></p>
                    </div>
                    <div class="surface-soft">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Annee academique</p>
                        <p class="mt-2 text-base font-semibold text-slate-900">{{ $currentAcademicLabel ?? 'Non definie' }}</p>
                        <p class="mt-2"><span class="badge-green">Session active</span></p>
                    </div>
                </div>

                <div class="rounded-[24px] border border-brand-100 bg-brand-50/70 p-4 text-sm leading-7 text-slate-600">
                    <i class="bi bi-shield-check mr-2 text-brand-600"></i>
                    Les modules de gestion, les exports PDF et les donnees de suivi restent synchronises sur le contexte choisi.
                </div>
            </div>
        </aside>
    </section>
</div>
@endsection

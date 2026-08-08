@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('breadcrumb', "Vue d'ensemble")

@section('content')
@php
    $firstName = explode(' ', trim(auth()->user()->name))[0] ?? auth()->user()->name;
    $average   = $stats['moyenne_generale'];
    $avgPct    = $average !== null ? max(8, min(100, (float) $average * 5)) : 0;
    $roleLabels = ['admin' => 'Administrateur', 'secretariat' => 'Secretariat', 'enseignant' => 'Enseignant'];
    $roleLabel  = $roleLabels[auth()->user()->role] ?? ucfirst(auth()->user()->role);

    $kpis = [
        ['label' => 'Classes',        'value' => $stats['total_classes'],  'icon' => 'bi-building',           'color' => '#009e60', 'bg' => 'rgba(0,158,96,0.12)'],
        ['label' => 'Eleves inscrits', 'value' => $stats['total_eleves'],  'icon' => 'bi-people-fill',        'color' => '#f7d117', 'bg' => 'rgba(247,209,23,0.15)'],
        ['label' => 'Matieres',        'value' => $stats['total_matieres'],'icon' => 'bi-book-fill',          'color' => '#2563eb', 'bg' => 'rgba(37,99,235,0.12)'],
        ['label' => 'Notes saisies',   'value' => $stats['total_notes'],   'icon' => 'bi-clipboard-data-fill','color' => '#009e60', 'bg' => 'rgba(0,158,96,0.10)'],
    ];

    $actions = [
        ['label' => 'Eleves',      'href' => route('eleves.index'),     'icon' => 'bi-people-fill',    'desc' => 'Profils, inscriptions et historiques'],
        ['label' => 'Facturation', 'href' => route('factures.index'),   'icon' => 'bi-receipt-cutoff', 'desc' => 'Factures, paiements et recus PDF'],
        ['label' => 'Classements', 'href' => route('classement.index'), 'icon' => 'bi-trophy-fill',    'desc' => 'Moyennes, rangs et dynamique scolaire'],
    ];
    if ($canManageAcademicData) {
        $actions[] = ['label' => 'Saisie en masse', 'href' => route('notes.masse.index'), 'icon' => 'bi-table', 'desc' => 'Evaluation complete par classe et semestre'];
    }
@endphp

<div class="space-y-5">

    {{-- HERO --}}
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Tableau de bord</p>
                <h2 class="page-title">Bonjour, {{ $firstName }}. Tout est pret pour aujourd'hui.</h2>
                <p class="page-lead">Suivez les classes, les notes et les eleves depuis un seul espace centralise.</p>

                <div class="hero-badges">
                    @if(isset($currentAcademicLabel))
                        <span class="hero-badge"><i class="bi bi-calendar2-week"></i>{{ $currentAcademicLabel }}</span>
                    @endif
                    <span class="hero-badge"><i class="bi bi-person-badge"></i>{{ $roleLabel }}</span>
                    <span class="hero-badge">
                        <span class="inline-block h-2 w-2 rounded-full bg-green-500"></span>
                        Session active
                    </span>
                </div>

                <div class="hero-actions">
                    @if($canManageAcademicData)
                        <a href="{{ route('notes.masse.index') }}" class="btn-primary">
                            <i class="bi bi-lightning-fill"></i> Saisie en masse
                        </a>
                    @endif
                    <a href="{{ route('eleves.index') }}" class="btn-secondary">
                        <i class="bi bi-people"></i> Voir les eleves
                    </a>
                </div>
            </div>

            <aside class="hero-panel" data-tilt>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Synthese annuelle</p>
                    <h3 class="mt-3 text-xl font-semibold tracking-tight text-white">Moyenne generale de l'etablissement</h3>
                    <p class="mt-2 text-sm leading-6 text-white/65">Calculee sur l'ensemble des notes saisies pour l'annee en cours.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[20px] border border-white/10 bg-black/25 px-4 py-4">
                        <p class="text-[10px] uppercase tracking-[0.22em] text-white/45">Moyenne</p>
                        <p class="mt-2 text-3xl font-bold text-white">
                            {{ $average !== null ? number_format((float) $average, 2, ',', ' ') : '--' }}
                        </p>
                        <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-white/10">
                            <div class="h-full rounded-full bg-gradient-to-r from-[#009e60] to-[#f7d117] transition-all duration-700" style="width:{{ $avgPct }}%"></div>
                        </div>
                    </div>
                    <div class="rounded-[20px] border border-white/10 bg-black/25 px-4 py-4">
                        <p class="text-[10px] uppercase tracking-[0.22em] text-white/45">Eleves</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_eleves'] }}</p>
                        <p class="mt-2 text-xs text-white/55">{{ $stats['total_classes'] }} classe(s) active(s)</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    {{-- KPIs --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($kpis as $kpi)
        <article class="card px-5 py-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ $kpi['label'] }}</p>
                    <p class="mt-3 text-4xl font-bold text-slate-900 leading-none">{{ $kpi['value'] }}</p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl" style="background:{{ $kpi['bg'] }}">
                    <i class="bi {{ $kpi['icon'] }} text-xl" style="color:{{ $kpi['color'] }}"></i>
                </div>
            </div>
        </article>
        @endforeach
    </div>

    {{-- ACTIONS + COMPTE --}}
    <div class="grid gap-5 xl:grid-cols-[1fr_320px]">

        {{-- Actions rapides --}}
        <section class="card">
            <div class="card-header">
                <div>
                    <h4>Actions rapides</h4>
                    <p class="text-xs text-slate-500">Acces direct aux modules principaux.</p>
                </div>
                <span class="badge-green"><i class="bi bi-check-circle"></i> Pret</span>
            </div>
            <div class="card-body grid gap-3 sm:grid-cols-2">
                @foreach($actions as $action)
                <a href="{{ $action['href'] }}" class="group flex items-center gap-4 rounded-[20px] border border-transparent bg-slate-50/60 px-4 py-4 transition-all duration-200 hover:border-green-200 hover:bg-green-50/40 hover:shadow-md">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-green-100 text-green-700 transition-transform duration-200 group-hover:scale-110">
                        <i class="bi {{ $action['icon'] }} text-base"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-slate-900">{{ $action['label'] }}</p>
                        <p class="mt-0.5 truncate text-xs text-slate-500">{{ $action['desc'] }}</p>
                    </div>
                    <i class="bi bi-arrow-right text-xs text-slate-300 transition-all duration-200 group-hover:translate-x-1 group-hover:text-green-600"></i>
                </a>
                @endforeach
            </div>
        </section>

        {{-- Compte --}}
        <aside class="card">
            <div class="card-header">
                <h4>Mon compte</h4>
            </div>
            <div class="card-body flex flex-col gap-3">
                <div class="flex items-center gap-3 rounded-[18px] bg-slate-50/70 px-4 py-3">
                    <div class="account-avatar shrink-0 text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-slate-900">{{ auth()->user()->name }}</p>
                        <span class="badge-green mt-1 text-[10px]">{{ $roleLabel }}</span>
                    </div>
                </div>

                <div class="rounded-[18px] border border-yellow-200/60 bg-yellow-50/40 px-4 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Annee scolaire</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $currentAcademicLabel ?? 'Non definie' }}</p>
                </div>

                <div class="rounded-[18px] bg-slate-50/70 px-4 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Moyenne generale</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">
                        {{ $average !== null ? number_format((float) $average, 2, ',', ' ') : '—' }}
                    </p>
                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full bg-gradient-to-r from-[#009e60] to-[#f7d117] transition-all duration-700" style="width:{{ $avgPct }}%"></div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection

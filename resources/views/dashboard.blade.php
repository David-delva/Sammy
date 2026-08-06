@extends('layouts.app')

@section('title', 'Tableau enseignant')
@section('breadcrumb', "Vue d'ensemble de la classe")

@section('content')
@php
    $firstName = explode(' ', trim(auth()->user()->name))[0] ?? auth()->user()->name;
    $average = $stats['moyenne_generale'];
    $averagePercent = $average !== null ? max(12, min(100, (float) $average * 10)) : 0;
    $roleLabels = [
        'admin' => 'Administrateur',
        'secretariat' => 'Secrétariat',
        'enseignant' => 'Enseignant',
    ];
    $roleLabel = $roleLabels[auth()->user()->role] ?? ucfirst(auth()->user()->role);

    $kpis = [
        ['label' => 'Classes actives', 'value' => $stats['total_classes'], 'icon' => 'bi-building', 'tone' => 'bg-blue-100 text-blue-700', 'caption' => 'emplois du temps actifs'],
        ['label' => 'Élèves connectés', 'value' => $stats['total_eleves'], 'icon' => 'bi-people-fill', 'tone' => 'bg-brand-100 text-brand-700', 'caption' => 'présences prêtes'],
        ['label' => 'Matières en ligne', 'value' => $stats['total_matieres'], 'icon' => 'bi-book-fill', 'tone' => 'bg-emerald-100 text-emerald-700', 'caption' => 'programme structuré'],
        ['label' => 'Notes saisies', 'value' => $stats['total_notes'], 'icon' => 'bi-clipboard-data-fill', 'tone' => 'bg-amber-100 text-amber-700', 'caption' => 'données synchronisées'],
    ];

    $actions = [
        [
            'label' => 'Ouvrir les élèves',
            'href' => route('eleves.index'),
            'icon' => 'bi-people-fill',
            'tone' => 'bg-brand-100 text-brand-700',
            'visible' => true,
            'description' => 'Parcourez les profils, historiques et inscriptions en cours depuis un seul annuaire élèves.',
        ],
        [
            'label' => 'Lancer la facturation',
            'href' => route('factures.index'),
            'icon' => 'bi-receipt-cutoff',
            'tone' => 'bg-blue-100 text-blue-700',
            'visible' => true,
            'description' => "Suivez l'état des factures, les paiements enregistrés et les reçus PDF imprimables.",
        ],
        [
            'label' => 'Saisie groupée des notes',
            'href' => route('notes.masse.index'),
            'icon' => 'bi-table',
            'tone' => 'bg-emerald-100 text-emerald-700',
            'visible' => $canManageAcademicData,
            'description' => 'Renseignez une évaluation complète en un passage par classe, matière et semestre.',
        ],
        [
            'label' => 'Classements',
            'href' => route('classement.index'),
            'icon' => 'bi-trophy-fill',
            'tone' => 'bg-amber-100 text-amber-700',
            'visible' => true,
            'description' => 'Consultez les moyennes annuelles, les rangs et la dynamique scolaire par classe.',
        ],
    ];

    $tools = [
        ['label' => 'Appels vidéo', 'description' => 'Lancez le support à distance et les revues de classe depuis un seul panneau.', 'icon' => 'bi-camera-video-fill', 'badge' => 'En direct'],
        ['label' => 'Discussions de groupe', 'description' => 'Gardez enseignants et administration alignés en temps réel.', 'icon' => 'bi-chat-left-dots-fill', 'badge' => '12 fils'],
        ['label' => 'Calendriers', 'description' => 'Planifiez évaluations, cours et rappels depuis une même ligne de temps.', 'icon' => 'bi-calendar2-week-fill', 'badge' => 'Cette semaine'],
        ['label' => 'Présence', 'description' => 'Enregistrez la présence des élèves avec des contrôles rapides.', 'icon' => 'bi-clipboard-check-fill', 'badge' => 'Synchronisé'],
    ];
@endphp

<div class="space-y-6">
    <section class="page-hero" data-reveal>
        <div class="page-hero-grid">
            <div>
                <p class="page-kicker">Centre de pilotage enseignant</p>
                <h2 class="page-title">Bonjour, {{ $firstName }}. Votre réseau de classes reste actif toute la journée.</h2>
                <p class="page-lead">
                    Appels, discussions, calendriers, présence et suivi académique partagent désormais le même tableau de bord.
                    L'interface reste claire pendant que la journée scolaire continue d'avancer.
                </p>

                <div class="hero-badges">
                    @if(isset($currentAcademicLabel))
                        <span class="hero-badge"><i class="bi bi-calendar2-week"></i>{{ $currentAcademicLabel }}</span>
                    @endif
                    <span class="hero-badge"><i class="bi bi-person-badge"></i>{{ $roleLabel }}</span>
                    <span class="hero-badge"><i class="bi bi-wifi"></i>Toujours connecté</span>
                    <span class="hero-badge"><i class="bi bi-clipboard-check"></i>Présence prête</span>
                </div>

                <div class="hero-actions">
                    @if($canManageAcademicData)
                        <a href="{{ route('notes.masse.index') }}" class="btn-primary justify-center shadow-lg shadow-brand-600/20 sm:w-auto">
                            Découvrir l'offre
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    @endif
                    <a href="{{ route('eleves.index') }}" class="btn-secondary justify-center sm:w-auto">
                        <i class="bi bi-people"></i>
                        Ouvrir les élèves
                    </a>
                </div>
            </div>

            <aside class="hero-panel" data-tilt>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/45">Tableau enseignant en direct</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-tight text-white">Un cockpit plus vivant pour les classes, la messagerie et les dossiers scolaires.</h3>
                    <p class="mt-3 text-sm leading-7 text-white/72">Utilisez un seul espace pour superviser la communication, la planification et le suivi académique.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Moyenne générale</p>
                        <p class="mt-2 text-3xl font-semibold text-white">{{ $average !== null ? number_format((float) $average, 2, ',', ' ') : '--' }}</p>
                        <p class="mt-1 text-sm text-white/65">dynamique annuelle</p>
                    </div>
                    <div class="rounded-[22px] border border-white/10 bg-white/8 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-white/45">Badge de notification</p>
                        <p class="mt-2 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-2 text-sm font-semibold text-white"><span class="notification-pulse"></span>Connecté</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($kpis as $index => $kpi)
            <article class="stat-card p-5" data-tilt data-reveal data-reveal-delay="{{ $index * 70 }}ms">
                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">{{ $kpi['label'] }}</p>
                        <p class="mt-4 text-4xl font-semibold tracking-tight text-slate-950" data-count="{{ $kpi['value'] }}">0</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $kpi['caption'] }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $kpi['tone'] }}">
                        <i class="bi {{ $kpi['icon'] }} text-xl"></i>
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    <section class="teacher-grid">
        <div class="workspace-card p-6 sm:p-7" data-reveal>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="page-kicker">Fonctionnalités de la plateforme</p>
                    <h3 class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">Les outils d'enseignement réunis sur un seul tableau connecté.</h3>
                    <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-500">Chaque widget est pensé pour être rapide à lire, simple à actionner et assez clair pour accompagner toute la journée scolaire.</p>
                </div>
                <span class="badge-green">Statut actif</span>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                @foreach($tools as $tool)
                    <article class="teacher-widget p-5" data-tilt>
                        <div class="relative">
                            <div class="flex items-center justify-between gap-3">
                                <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-brand-700">
                                    <i class="bi {{ $tool['icon'] }} text-xl"></i>
                                </span>
                                <span class="badge-green">{{ $tool['badge'] }}</span>
                            </div>
                            <h4 class="mt-5 text-xl font-semibold tracking-tight text-slate-900">{{ $tool['label'] }}</h4>
                            <p class="mt-3 text-sm leading-7 text-slate-500">{{ $tool['description'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="space-y-6">
            <aside class="surface-card p-6" data-tilt>
                <div class="relative space-y-5">
                    <div>
                        <p class="page-kicker">Mon compte</p>
                        <h4 class="mt-3 text-2xl font-semibold tracking-tight text-slate-900">Session et rythme scolaire</h4>
                    </div>

                    <div class="space-y-3">
                        <div class="surface-soft">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Utilisateur</p>
                            <p class="mt-2 text-base font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                            <p class="mt-2"><span class="badge-blue">{{ $roleLabel }}</span></p>
                        </div>
                        <div class="surface-soft">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Année scolaire</p>
                            <p class="mt-2 text-base font-semibold text-slate-900">{{ $currentAcademicLabel ?? 'Non définie' }}</p>
                            <p class="mt-2"><span class="badge-green">Contexte actuel</span></p>
                        </div>
                        <div class="surface-soft">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Moyenne générale</p>
                            <p class="mt-2 text-base font-semibold text-slate-900">{{ $average !== null ? number_format((float) $average, 2, ',', ' ') : 'Pas encore disponible' }}</p>
                            <div class="teacher-progress"><div class="teacher-progress-bar" style="width: {{ $averagePercent }}%"></div></div>
                        </div>
                    </div>
                </div>
            </aside>

            <aside class="surface-card p-6" data-tilt>
                <div class="relative space-y-4">
                    <div>
                        <p class="page-kicker">Actions rapides</p>
                        <h4 class="mt-3 text-2xl font-semibold tracking-tight text-slate-900">Raccourcis opérationnels</h4>
                    </div>

                    <div class="grid gap-4">
                        @foreach($actions as $action)
                            @if($action['visible'])
                                <a href="{{ $action['href'] }}" class="action-card p-5 group">
                                    <div class="relative flex items-start gap-4">
                                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl {{ $action['tone'] }} transition-transform duration-200 group-hover:scale-105">
                                            <i class="bi {{ $action['icon'] }} text-lg"></i>
                                        </span>
                                        <div>
                                            <h5 class="text-lg font-semibold tracking-tight text-slate-900">{{ $action['label'] }}</h5>
                                            <p class="mt-2 text-sm leading-7 text-slate-500">{{ $action['description'] }}</p>
                                            <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-brand-600">
                                                Ouvrir
                                                <i class="bi bi-arrow-right transition-transform duration-200 group-hover:translate-x-1"></i>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </section>
</div>
@endsection


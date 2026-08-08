@props([
    'currentAcademicLabel' => null,
    'canManageAcademicData' => false,
])

@php
    $user = auth()->user();

    $workspaceLinks = [
        ['label' => 'Tableau de bord', 'icon' => 'bi-grid-fill', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
        ['label' => '�l�ves', 'icon' => 'bi-people-fill', 'href' => route('eleves.index'), 'active' => request()->routeIs('eleves.*')],
        ['label' => 'Facturation', 'icon' => 'bi-receipt-cutoff', 'href' => route('factures.index'), 'active' => request()->routeIs('factures.*')],
        ['label' => 'Classements', 'icon' => 'bi-trophy-fill', 'href' => route('classement.index'), 'active' => request()->routeIs('classement.*')],
    ];

    $operationLinks = [];

    if ($user && in_array($user->role, ['admin', 'secretariat'], true)) {
        $operationLinks = [
            ['label' => 'Classes', 'icon' => 'bi-building', 'href' => route('classes.index'), 'active' => request()->routeIs('classes.*')],
            ['label' => 'Mati�res', 'icon' => 'bi-book-fill', 'href' => route('matieres.index'), 'active' => request()->routeIs('matieres.*')],
        ];

        if ($canManageAcademicData) {
            $operationLinks[] = ['label' => 'Affectations', 'icon' => 'bi-diagram-3-fill', 'href' => route('matieres.assigner'), 'active' => request()->routeIs('matieres.assigner*')];
            $operationLinks[] = ['label' => 'UE', 'icon' => 'bi-collection-fill', 'href' => route('ues.index'), 'active' => request()->routeIs('ues.*')];
        }

        $operationLinks[] = ['label' => 'Notes', 'icon' => 'bi-clipboard-data-fill', 'href' => route('notes.index'), 'active' => request()->routeIs('notes.*') && ! request()->routeIs('notes.masse.*')];

        if ($canManageAcademicData) {
            $operationLinks[] = ['label' => 'Saisie en masse', 'icon' => 'bi-table', 'href' => route('notes.masse.index'), 'active' => request()->routeIs('notes.masse.*')];
            $operationLinks[] = ['label' => 'Absences', 'icon' => 'bi-person-dash-fill', 'href' => route('absences.index'), 'active' => request()->routeIs('absences.*')];
        }
    }

    $adminLinks = [];

    if ($user && $user->role === 'admin') {
        $adminLinks[] = ['label' => 'Annees scolaires', 'icon' => 'bi-calendar-event-fill', 'href' => route('annees.index'), 'active' => request()->routeIs('annees.*')];
        $adminLinks[] = ['label' => 'Penalite absence', 'icon' => 'bi-sliders', 'href' => route('penalite-absence.edit'), 'active' => request()->routeIs('penalite-absence.*')];
    }
@endphp

<aside
    class="fixed inset-y-3 left-3 z-50 flex w-[292px] flex-col rounded-[34px] border border-white/12 p-4 shadow-[0_28px_80px_rgba(8,5,26,0.32)] backdrop-blur-2xl transition-transform duration-300 ease-out lg:static lg:inset-auto lg:translate-x-0"
    style="background-color: #009e60;"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-[calc(100%+1rem)] lg:translate-x-0'"
>
    <x-layout.app-brand />

    @if($currentAcademicLabel)
        <div class="mt-4 rounded-[26px] border border-white/10 bg-white/8 p-4 text-white">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-success-500/15 text-success-200">
                    <i class="bi bi-calendar2-check"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-white/45">Annee active</p>
                    <p class="truncate text-sm font-semibold text-white/90">{{ $currentAcademicLabel }}</p>
                </div>
            </div>
        </div>
    @endif

    <nav class="mt-5 flex-1 space-y-1 overflow-y-auto px-1">
        <p class="sidebar-section-label">Espace</p>

        @foreach($workspaceLinks as $link)
            <x-layout.sidebar-link :href="$link['href']" :icon="$link['icon']" :active="$link['active']">
                {{ $link['label'] }}
            </x-layout.sidebar-link>
        @endforeach

        @if($operationLinks !== [])
            <p class="sidebar-section-label">Operations</p>

            @foreach($operationLinks as $link)
                <x-layout.sidebar-link :href="$link['href']" :icon="$link['icon']" :active="$link['active']">
                    {{ $link['label'] }}
                </x-layout.sidebar-link>
            @endforeach
        @endif

        @foreach($adminLinks as $link)
            <x-layout.sidebar-link :href="$link['href']" :icon="$link['icon']" :active="$link['active']">
                {{ $link['label'] }}
            </x-layout.sidebar-link>
        @endforeach
    </nav>

</aside>


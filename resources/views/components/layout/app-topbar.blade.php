@props([
    'title' => 'Tableau de bord',
    'breadcrumb' => null,
    'academicYears' => collect(),
    'currentAcademicLabel' => null,
])

@php
    $quickLinks = [
        ['label' => "Vue d'ensemble", 'href' => route('dashboard'), 'visibility' => 'hidden lg:inline-flex'],
        ['label' => '�l�ves', 'href' => route('eleves.index'), 'visibility' => 'hidden lg:inline-flex'],
        ['label' => 'Facturation', 'href' => route('factures.index'), 'visibility' => 'hidden xl:inline-flex'],
    ];
@endphp

<header class="sticky top-3 z-30 rounded-[34px] border border-white/12 bg-white/10 px-4 py-4 shadow-[0_24px_60px_rgba(12,8,35,0.22)] backdrop-blur-2xl sm:px-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex min-w-0 items-center gap-3">
            <button @click="sidebarOpen = true" class="flex h-11 w-11 items-center justify-center rounded-2xl border border-white/16 bg-white/10 text-white shadow-sm transition hover:bg-white/16 lg:hidden">
                <i class="bi bi-list text-lg"></i>
            </button>

            <div class="min-w-0 text-white">
                <p class="truncate text-xs font-bold uppercase tracking-[0.3em] text-white/50">Tableau enseignant</p>
                <h1 class="truncate text-sm font-semibold text-white sm:text-base">{{ $title }}</h1>

                @if($breadcrumb)
                    <p class="hidden truncate text-xs text-white/60 sm:block">{{ $breadcrumb }}</p>
                @endif
            </div>
        </div>

        <div class="top-search" role="search" aria-label="Recherche">
            <i class="bi bi-search" aria-hidden="true"></i>
            <input type="search" aria-label="Rechercher" placeholder="Rechercher des élèves, classes, factures ou messages" />
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            @foreach($quickLinks as $link)
                <a href="{{ $link['href'] }}" class="nav-chip {{ $link['visibility'] }} text-white/80">{{ $link['label'] }}</a>
            @endforeach

            @if(($academicYears?->count() ?? 0) > 0)
                <x-layout.academic-year-select :academic-years="$academicYears" :current-academic-label="$currentAcademicLabel" />
            @endif

            <div class="notification-chip">
                <span class="notification-pulse"></span>
                <span class="hidden sm:inline">Connect�</span>
            </div>

            <x-layout.account-menu />
        </div>
    </div>
</header>

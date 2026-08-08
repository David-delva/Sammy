@props([
    'title' => 'Tableau de bord',
    'breadcrumb' => null,
    'academicYears' => collect(),
    'currentAcademicLabel' => null,
])

@php
    $quickLinks = [
        ['label' => "Vue d'ensemble", 'icon' => 'bi-grid-1x2-fill', 'href' => route('dashboard'), 'visibility' => 'hidden lg:inline-flex'],
        ['label' => 'Eleves',         'icon' => 'bi-people-fill',   'href' => route('eleves.index'), 'visibility' => 'hidden lg:inline-flex'],
        ['label' => 'Facturation',    'icon' => 'bi-receipt',       'href' => route('factures.index'), 'visibility' => 'hidden xl:inline-flex'],
    ];
@endphp

<header class="sticky top-0 z-30" style="background:#000000;box-shadow:0 4px 24px rgba(0,0,0,0.40);border-bottom:1px solid rgba(255,255,255,0.08);padding:10px 16px;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">

        {{-- Gauche : burger + titre --}}
        <div style="display:flex;align-items:center;gap:10px;min-width:0;">
            <button @click="sidebarOpen = true"
                style="display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:14px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.18);color:#fff;cursor:pointer;flex-shrink:0;"
                class="lg:hidden">
                <i class="bi bi-list" style="font-size:1.2rem;"></i>
            </button>

            <div style="min-width:0;">
                <p style="font-size:10px;font-weight:700;letter-spacing:0.28em;text-transform:uppercase;color:#f7d117;line-height:1;">INPTIC</p>
                <h1 style="font-size:14px;font-weight:700;color:#fff;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $title }}</h1>
                @if($breadcrumb)
                <p style="font-size:11px;color:rgba(255,255,255,0.55);margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" class="hidden sm:block">{{ $breadcrumb }}</p>
                @endif
            </div>
        </div>

        {{-- Centre : barre de recherche --}}
        <div style="flex:1;max-width:380px;min-width:160px;" class="hidden md:block">
            <div style="position:relative;">
                <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.50);font-size:13px;pointer-events:none;"></i>
                <input type="search"
                    placeholder="Rechercher eleves, classes, factures..."
                    style="width:100%;padding:8px 14px 8px 34px;border-radius:999px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.18);color:#fff;font-size:12px;outline:none;"
                    onfocus="this.style.background='rgba(255,255,255,0.18)';this.style.borderColor='rgba(247,209,23,0.50)'"
                    onblur="this.style.background='rgba(255,255,255,0.12)';this.style.borderColor='rgba(255,255,255,0.18)'"
                />
            </div>
        </div>

        {{-- Droite : liens rapides + annee + compte --}}
        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">

            {{-- Liens rapides --}}
            @foreach($quickLinks as $link)
            <a href="{{ $link['href'] }}"
                style="display:inline-flex;align-items:center;gap:5px;padding:7px 13px;border-radius:999px;background:rgba(255,255,255,0.10);border:1px solid rgba(255,255,255,0.16);color:rgba(255,255,255,0.85);font-size:12px;font-weight:600;text-decoration:none;transition:background 0.15s;"
                class="{{ $link['visibility'] }}"
                onmouseover="this.style.background='rgba(247,209,23,0.20)';this.style.color='#f7d117'"
                onmouseout="this.style.background='rgba(255,255,255,0.10)';this.style.color='rgba(255,255,255,0.85)'">
                <i class="bi {{ $link['icon'] }}" style="font-size:11px;"></i>
                {{ $link['label'] }}
            </a>
            @endforeach

            {{-- Selecteur annee academique --}}
            @if(($academicYears?->count() ?? 0) > 0)
            <div class="hidden md:block">
                <select id="academicYearSelect"
                    style="height:36px;padding:0 32px 0 12px;border-radius:999px;background:rgba(247,209,23,0.18);border:1px solid rgba(247,209,23,0.35);color:#f7d117;font-size:12px;font-weight:700;cursor:pointer;appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%23f7d117' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 10px center;outline:none;">
                    <option value="" class="text-slate-900 bg-white">Aujourd'hui</option>
                    @foreach($academicYears as $academicYear)
                    @php
                        $parts = explode('-', $academicYear->libelle);
                        $valueDate = ($parts[0] ?? '') . '-09-01';
                    @endphp
                    <option value="{{ $valueDate }}" class="text-slate-900 bg-white" {{ $currentAcademicLabel === $academicYear->libelle ? 'selected' : '' }}>
                        {{ $academicYear->libelle }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Indicateur connexion --}}
            <div style="display:inline-flex;align-items:center;gap:6px;padding:7px 13px;border-radius:999px;background:rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.12);color:rgba(255,255,255,0.80);font-size:12px;font-weight:600;" class="hidden sm:inline-flex">
                <span style="width:7px;height:7px;border-radius:50%;background:#f7d117;box-shadow:0 0 6px rgba(247,209,23,0.70);display:inline-block;animation:pulse 2s infinite;"></span>
                Connecte
            </div>

            {{-- Menu compte --}}
            <x-layout.account-menu />
        </div>
    </div>
</header>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}
</style>

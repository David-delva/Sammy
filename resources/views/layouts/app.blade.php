<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestion Scolaire') - E.T.P.</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen text-slate-900" x-data="{ sidebarOpen: false }">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -left-20 top-10 h-64 w-64 rounded-full bg-brand-300/25 blur-3xl animate-float-slow"></div>
        <div class="absolute right-0 top-32 h-72 w-72 rounded-full bg-emerald-200/20 blur-3xl animate-float-delayed"></div>
        <div class="absolute bottom-0 left-1/3 h-80 w-80 rounded-full bg-navy-900/10 blur-3xl animate-float-slow"></div>
    </div>

    <div class="flex min-h-screen">
        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-[280px] flex-col border-r border-white/10 bg-slate-950/92 backdrop-blur-xl transition-transform duration-300 ease-out lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex h-20 flex-shrink-0 items-center gap-3 border-b border-white/10 px-5">
                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-900/20">
                    <i class="bi bi-mortarboard-fill text-lg"></i>
                </div>
                <div class="min-w-0">
                    <p class="truncate text-[12px] font-bold uppercase tracking-[0.28em] text-white/55">E.T.P.</p>
                    <p class="truncate text-sm font-semibold text-white">Gestion scolaire</p>
                </div>
            </div>

            @isset($currentAcademicLabel)
                <div class="flex-shrink-0 border-b border-white/10 px-4 py-4">
                    <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/8 px-3 py-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-300">
                            <i class="bi bi-calendar2-check"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-white/45">Contexte actif</p>
                            <p class="truncate text-xs font-semibold text-white/80">{{ $currentAcademicLabel }}</p>
                        </div>
                    </div>
                </div>
            @endisset

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                <p class="sidebar-section-label">Navigation</p>

                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-fill w-5 text-center text-base"></i>
                    <span>Tableau de bord</span>
                </a>

                <a href="{{ route('eleves.index') }}" class="sidebar-link {{ request()->routeIs('eleves.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill w-5 text-center text-base"></i>
                    <span>Eleves</span>
                </a>

                <a href="{{ route('classement.index') }}" class="sidebar-link {{ request()->routeIs('classement.*') ? 'active' : '' }}">
                    <i class="bi bi-trophy-fill w-5 text-center text-base"></i>
                    <span>Classement</span>
                </a>

                @auth
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'secretariat')
                        <p class="sidebar-section-label">Administration</p>

                        <a href="{{ route('classes.index') }}" class="sidebar-link {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                            <i class="bi bi-building w-5 text-center text-base"></i>
                            <span>Classes</span>
                        </a>

                        <a href="{{ route('matieres.index') }}" class="sidebar-link {{ request()->routeIs('matieres.*') ? 'active' : '' }}">
                            <i class="bi bi-book-fill w-5 text-center text-base"></i>
                            <span>Matieres</span>
                        </a>

                        @if($canManageAcademicData)
                            <a href="{{ route('matieres.assigner') }}" class="sidebar-link {{ request()->routeIs('matieres.assigner*') ? 'active' : '' }}">
                                <i class="bi bi-diagram-3-fill w-5 text-center text-base"></i>
                                <span>Assignation</span>
                            </a>
                        @endif

                        <a href="{{ route('notes.index') }}" class="sidebar-link {{ request()->routeIs('notes.*') && !request()->routeIs('notes.masse.*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard-data-fill w-5 text-center text-base"></i>
                            <span>Notes</span>
                        </a>

                        @if($canManageAcademicData)
                            <a href="{{ route('notes.masse.index') }}" class="sidebar-link {{ request()->routeIs('notes.masse.*') ? 'active' : '' }}">
                                <i class="bi bi-table w-5 text-center text-base"></i>
                                <span>Saisie en masse</span>
                            </a>
                        @endif
                    @endif

                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('annees.index') }}" class="sidebar-link {{ request()->routeIs('annees.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar-event-fill w-5 text-center text-base"></i>
                            <span>Annees</span>
                        </a>
                    @endif
                @endauth
            </nav>

            @auth
                <div class="flex-shrink-0 border-t border-white/10 p-3">
                    <div class="flex items-center gap-3 rounded-[22px] border border-white/10 bg-white/6 px-3 py-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-2xl bg-brand-500/20 text-white">
                            <span class="text-sm font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                            <p class="truncate text-[11px] uppercase tracking-[0.18em] text-white/45">{{ ucfirst(auth()->user()->role) }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex h-10 w-10 items-center justify-center rounded-2xl text-white/60 transition hover:bg-white/10 hover:text-white" title="Deconnexion">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </aside>

        <div
            x-cloak
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-slate-950/55 backdrop-blur-[2px] lg:hidden"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        ></div>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 border-b border-white/60 bg-white/72 backdrop-blur-xl">
                <div class="mx-auto flex h-16 w-full max-w-[1600px] items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button
                            @click="sidebarOpen = true"
                            class="flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200/80 bg-white/90 text-slate-600 shadow-sm transition hover:bg-brand-50 hover:text-brand-700 lg:hidden"
                        >
                            <i class="bi bi-list text-lg"></i>
                        </button>

                        <div class="min-w-0">
                            <p class="truncate text-xs font-bold uppercase tracking-[0.3em] text-slate-400">Navigation</p>
                            <h1 class="truncate text-sm font-semibold text-slate-900 sm:text-base">@yield('title', 'Tableau de bord')</h1>
                            @hasSection('breadcrumb')
                                <p class="hidden truncate text-xs text-slate-500 sm:block">@yield('breadcrumb')</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3">
                        @isset($academicYears)
                            @if($academicYears->count() > 0)
                                <div class="hidden md:block">
                                    <select
                                        id="academicYearSelect"
                                        class="h-11 cursor-pointer appearance-none rounded-full border border-slate-200/90 bg-white/90 pl-4 pr-10 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-brand-50/50 focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                                        style="background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%236B7280' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 14px center"
                                    >
                                        <option value="">Aujourd'hui</option>
                                        @foreach($academicYears as $y)
                                            @php
                                                $parts = explode('-', $y->libelle);
                                                $valueDate = ($parts[0] ?? '') . '-09-01';
                                            @endphp
                                            <option value="{{ $valueDate }}" {{ (isset($currentAcademicLabel) && $currentAcademicLabel == $y->libelle) ? 'selected' : '' }}>
                                                {{ $y->libelle }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        @endisset

                        @auth
                            <div class="relative" x-data="{ open: false }">
                                <button
                                    @click="open = !open"
                                    class="flex h-11 items-center gap-2 rounded-full border border-slate-200/80 bg-white/90 pl-2 pr-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-brand-50/50"
                                >
                                    <div class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                                        <span class="text-[11px] font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                    </div>
                                    <span class="hidden sm:inline">{{ \Illuminate\Support\Str::words(auth()->user()->name, 1, '') }}</span>
                                    <i class="bi bi-chevron-down text-[10px] text-slate-400"></i>
                                </button>

                                <div
                                    x-cloak
                                    x-show="open"
                                    @click.outside="open = false"
                                    x-transition:enter="transition ease-out duration-120"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-90"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute right-0 top-14 z-50 w-56 rounded-[22px] border border-white/80 bg-white/96 py-2 shadow-[0_24px_70px_rgba(15,23,42,0.16)]"
                                >
                                    <div class="mb-1 border-b border-slate-100 px-4 py-3">
                                        <p class="truncate text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                                        <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
                                    </div>

                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50">
                                        <i class="bi bi-person w-4 text-slate-400"></i>Mon profil
                                    </a>

                                    <div class="mt-1 border-t border-slate-100 pt-1">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-sm text-red-600 transition hover:bg-red-50">
                                                <i class="bi bi-box-arrow-right w-4"></i>Deconnexion
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto">
                <div class="page-shell">
                    @if(isset($isCurrentAcademicYear) && !$isCurrentAcademicYear && isset($currentAcademicYear))
                        <div class="alert-warning animate-fadein" role="alert">
                            <i class="bi bi-exclamation-diamond-fill mt-0.5 flex-shrink-0"></i>
                            <div class="flex-1">
                                <strong>Mode consultation :</strong> vous visualisez l'annee {{ $currentAcademicYear->libelle }}.
                                @if($canManageAcademicData)
                                    En tant qu'administrateur, les modifications restent autorisees.
                                @else
                                    La modification est bloquee pour preserver l'historique.
                                @endif
                            </div>
                            <button onclick="window.location.href='{{ route('academic-year.reset') }}'" class="btn-secondary btn-sm self-center whitespace-nowrap" style="cursor: pointer;">
                                Retour au present
                            </button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert-success animate-fadein" role="alert">
                            <i class="bi bi-check-circle-fill flex-shrink-0 text-emerald-600"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert-warning animate-fadein" role="alert">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                            <span>{{ session('warning') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert-error animate-fadein" role="alert">
                            <i class="bi bi-x-circle-fill flex-shrink-0"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if($errors->any() && !request()->routeIs('*.create') && !request()->routeIs('*.edit'))
                        <div class="alert-error animate-fadein" role="alert">
                            <i class="bi bi-x-circle-fill mt-0.5 flex-shrink-0"></i>
                            <ul class="list-none space-y-0.5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        document.getElementById('academicYearSelect')?.addEventListener('change', function () {
            const url = new URL(window.location.href);

            if (this.value) {
                url.searchParams.set('date', this.value);
            } else {
                url.searchParams.delete('date');
            }

            window.location.href = url.toString();
        });
    </script>
    @stack('scripts')
</body>
</html>

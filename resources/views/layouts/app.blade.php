<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Gestion Scolaire') - É.T.P.</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="flex h-full bg-slate-50" x-data="{ sidebarOpen: false }">
    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-navy-900 transition-transform duration-200 ease-in-out lg:static lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex h-16 flex-shrink-0 items-center gap-3 border-b border-white/10 px-5">
            <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-brand-600">
                <i class="bi bi-mortarboard-fill text-sm text-white"></i>
            </div>
            <div class="min-w-0">
                <p class="truncate text-[13px] font-bold leading-tight text-white">École Technique</p>
                <p class="text-[10px] leading-tight text-navy-400">& Professionnelle</p>
            </div>
        </div>

        @isset($currentAcademicLabel)
            <div class="flex-shrink-0 border-b border-white/10 px-4 py-3">
                <div class="flex items-center gap-2 rounded-lg bg-white/10 px-3 py-2">
                    <span class="h-1.5 w-1.5 flex-shrink-0 animate-pulse rounded-full bg-emerald-400"></span>
                    <span class="truncate text-[11px] font-semibold text-white/80">{{ $currentAcademicLabel }}</span>
                </div>
            </div>
        @endisset

        <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 py-3">
            <p class="sidebar-section-label">Navigation</p>

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill w-5 text-center text-base"></i>
                <span>Tableau de bord</span>
            </a>

            <a href="{{ route('eleves.index') }}"
               class="sidebar-link {{ request()->routeIs('eleves.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill w-5 text-center text-base"></i>
                <span>Élèves</span>
            </a>

            <a href="{{ route('classement.index') }}"
               class="sidebar-link {{ request()->routeIs('classement.*') ? 'active' : '' }}">
                <i class="bi bi-trophy-fill w-5 text-center text-base"></i>
                <span>Classement</span>
            </a>

            @auth
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'secretariat')
                    <p class="sidebar-section-label">Administration</p>

                    <a href="{{ route('classes.index') }}"
                       class="sidebar-link {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                        <i class="bi bi-building w-5 text-center text-base"></i>
                        <span>Classes</span>
                    </a>

                    <a href="{{ route('matieres.index') }}"
                       class="sidebar-link {{ request()->routeIs('matieres.*') ? 'active' : '' }}">
                        <i class="bi bi-book-fill w-5 text-center text-base"></i>
                        <span>Matières</span>
                    </a>

                    @if($canManageAcademicData)
                    <a href="{{ route('matieres.assigner') }}"
                       class="sidebar-link {{ request()->routeIs('matieres.assigner*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3-fill w-5 text-center text-base"></i>
                        <span>Assignation</span>
                    </a>
                    @endif

                    <a href="{{ route('notes.index') }}"
                       class="sidebar-link {{ request()->routeIs('notes.*') && !request()->routeIs('notes.masse.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-data-fill w-5 text-center text-base"></i>
                        <span>Notes</span>
                    </a>

                    @if($canManageAcademicData)
                    <a href="{{ route('notes.masse.index') }}"
                       class="sidebar-link {{ request()->routeIs('notes.masse.*') ? 'active' : '' }}">
                        <i class="bi bi-table w-5 text-center text-base"></i>
                        <span>Saisie en masse</span>
                    </a>
                    @endif
                @endif

                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('annees.index') }}"
                       class="sidebar-link {{ request()->routeIs('annees.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event-fill w-5 text-center text-base"></i>
                        <span>Années</span>
                    </a>
                @endif
            @endauth
        </nav>

        @auth
            <div class="flex-shrink-0 border-t border-white/10 p-3">
                <div class="group flex items-center gap-3 rounded-lg px-2 py-2 transition-colors hover:bg-white/10">
                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-brand-600">
                        <span class="text-xs font-bold text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[12.5px] font-semibold text-white">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="truncate text-[10.5px] text-navy-400">
                            {{ ucfirst(auth()->user()->role) }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="rounded-md p-1.5 text-navy-400 opacity-0 transition-colors group-hover:opacity-100 hover:bg-white/10 hover:text-white"
                                title="Déconnexion">
                            <i class="bi bi-box-arrow-right text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </aside>

    <div x-cloak
         x-show="sidebarOpen"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/50 lg:hidden"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
        <header class="z-30 flex h-16 flex-shrink-0 items-center justify-between border-b border-gray-100 bg-white px-4 lg:px-6">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true"
                        class="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 lg:hidden">
                    <i class="bi bi-list text-lg"></i>
                </button>

                <div class="hidden sm:block">
                    <h1 class="text-sm font-semibold text-gray-800">@yield('title', 'Tableau de bord')</h1>
                    @hasSection('breadcrumb')
                        <div class="mt-0.5 text-xs text-gray-400">@yield('breadcrumb')</div>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2">
                @isset($academicYears)
                    @if($academicYears->count() > 0)
                        <div class="hidden md:block">
                            <select id="academicYearSelect"
                                    class="h-8 cursor-pointer appearance-none rounded-lg border border-gray-200 bg-gray-50 pl-3 pr-8 text-[12.5px] font-medium text-gray-700 transition-colors hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-brand-500/30"
                                    style="background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%236B7280' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 8px center">
                                <option value="">Aujourd'hui</option>
                                @foreach($academicYears as $y)
                                    @php
                                        $parts = explode('-', $y->libelle);
                                        $valueDate = ($parts[0] ?? '') . '-09-01';
                                    @endphp
                                    <option value="{{ $valueDate }}"
                                            {{ (isset($currentAcademicLabel) && $currentAcademicLabel == $y->libelle) ? 'selected' : '' }}>
                                        {{ $y->libelle }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                @endisset

                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex h-8 items-center gap-2 rounded-lg pl-2 pr-3 text-[12.5px] font-medium text-gray-600 transition-colors hover:bg-gray-100">
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-brand-100">
                                <span class="text-[10px] font-bold text-brand-700">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            </div>
                            <span class="hidden sm:inline">{{ \Illuminate\Support\Str::words(auth()->user()->name, 1, '') }}</span>
                            <i class="bi bi-chevron-down text-[10px] text-gray-400"></i>
                        </button>

                        <div x-cloak
                             x-show="open"
                             @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 top-10 z-50 w-48 rounded-xl border border-gray-100 bg-white py-1 shadow-card-md">
                            <div class="mb-1 border-b border-gray-100 px-3 py-2">
                                <p class="truncate text-xs font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                                <p class="truncate text-[11px] text-gray-400">{{ auth()->user()->email }}</p>
                            </div>

                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-2.5 px-3 py-2 text-[13px] text-gray-700 transition-colors hover:bg-gray-50">
                                <i class="bi bi-person w-4 text-gray-400"></i>Mon profil
                            </a>

                            <div class="mt-1 border-t border-gray-100 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="flex w-full items-center gap-2.5 px-3 py-2 text-left text-[13px] text-red-600 transition-colors hover:bg-red-50">
                                        <i class="bi bi-box-arrow-right w-4"></i>Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>
        </header>

        <main class="flex-1 overflow-y-auto">
            @if(isset($isCurrentAcademicYear) && !$isCurrentAcademicYear && isset($currentAcademicYear))
                <div class="mx-6 mt-4 alert-warning animate-fadein" role="alert">
                    <i class="bi bi-exclamation-diamond-fill mt-0.5 flex-shrink-0"></i>
                    <div class="flex-1">
                        <strong>Mode consultation :</strong> vous visualisez l'année {{ $currentAcademicYear->libelle }}.
                        @if($canManageAcademicData)
                            En tant qu'administrateur, les modifications restent autorisées.
                        @else
                            La modification est bloquée pour préserver l'historique.
                        @endif
                    </div>
                    <button onclick="window.location.href='{{ route('academic-year.reset') }}'" class="btn-secondary btn-sm self-center whitespace-nowrap" style="cursor: pointer;">
                        Retour au présent
                    </button>
                </div>
            @endif

            @if(session('success'))
                <div class="mx-6 mt-4 alert-success animate-fadein" role="alert">
                    <i class="bi bi-check-circle-fill flex-shrink-0 text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="mx-6 mt-4 alert-warning animate-fadein" role="alert">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mx-6 mt-4 alert-error animate-fadein" role="alert">
                    <i class="bi bi-x-circle-fill flex-shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any() && !request()->routeIs('*.create') && !request()->routeIs('*.edit'))
                <div class="mx-6 mt-4 alert-error animate-fadein" role="alert">
                    <i class="bi bi-x-circle-fill mt-0.5 flex-shrink-0"></i>
                    <ul class="list-none space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="p-6">
                @yield('content')
            </div>
        </main>
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


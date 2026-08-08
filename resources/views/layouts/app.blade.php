<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestion scolaire') - INPTIC</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
@php
    $pageTitle = trim($__env->yieldContent('title', 'Tableau de bord'));
    $pageTitle = $pageTitle !== '' ? $pageTitle : 'Tableau de bord';

    $pageBreadcrumb = trim($__env->yieldContent('breadcrumb'));
    $pageBreadcrumb = $pageBreadcrumb !== '' ? $pageBreadcrumb : null;
@endphp
<body class="min-h-screen text-slate-900 bg-gray-200" x-data="{ sidebarOpen: false }">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -left-20 top-10 h-64 w-64 rounded-full bg-cobalt-300/25 blur-3xl animate-float-slow"></div>
        <div class="absolute right-0 top-32 h-72 w-72 rounded-full bg-cobalt-100 blur-3xl animate-float-delayed opacity-70"></div>
        <div class="absolute bottom-0 left-1/3 h-80 w-80 rounded-full bg-success-200/20 blur-3xl animate-float-slow"></div>
    </div>

    <div class="mx-auto min-h-screen max-w-[1780px] px-3 py-3 sm:px-4">
        <header class="mb-3">
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm rounded">
                <div class="container-fluid px-3">
                    <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                        <i class="bi bi-mortarboard-fill text-primary fs-4"></i>
                        <span class="fw-semibold">INPTIC</span>
                    </a>
                    <div class="d-flex align-items-center ms-auto">
                        <button class="btn btn-outline-primary d-lg-none" @click="sidebarOpen = true">
                            <i class="bi bi-list"></i>
                        </button>
                    </div>
                </div>
            </nav>
        </header>
        <div class="flex min-h-[calc(100vh-1.5rem)] gap-3">
            <x-layout.app-sidebar
                :current-academic-label="$currentAcademicLabel ?? null"
                :can-manage-academic-data="$canManageAcademicData ?? false"
            />

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
                <x-layout.app-topbar
                    :title="$pageTitle"
                    :breadcrumb="$pageBreadcrumb"
                    :academic-years="$academicYears ?? collect()"
                    :current-academic-label="$currentAcademicLabel ?? null"
                />

                <main class="flex-1 overflow-y-auto pt-3">
                    <div class="page-shell">
                        <x-layout.flash-stack
                            :is-current-academic-year="$isCurrentAcademicYear ?? true"
                            :current-academic-year="$currentAcademicYear ?? null"
                            :can-manage-academic-data="$canManageAcademicData ?? false"
                        />

                        @yield('content')
                    </div>
                </main>
            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gestion scolaire') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-slate-900 antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -left-16 top-12 h-64 w-64 rounded-full bg-brand-300/25 blur-3xl animate-float-slow"></div>
        <div class="absolute right-0 top-24 h-80 w-80 rounded-full bg-blue-100 blur-3xl animate-float-delayed opacity-70"></div>
        <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-emerald-200/20 blur-3xl animate-float-slow"></div>
    </div>

    <div class="relative isolate min-h-screen overflow-hidden px-4 py-6 sm:px-6 lg:px-8">
        <div class="institution-public-backdrop"></div>

        <div class="relative mx-auto flex min-h-[calc(100vh-3rem)] w-full max-w-7xl items-center">
            <div class="institution-public-frame grid w-full overflow-hidden xl:grid-cols-[1.08fr_0.92fr]">
                <x-layout.guest-brand-panel />

                <main class="flex items-center justify-center p-5 sm:p-8 lg:p-10 xl:p-12">
                    <div class="w-full max-w-lg space-y-6">
                        <x-layout.guest-mobile-brand />

                        <div class="auth-panel">
                            <div class="relative">
                                {{ $slot }}
                            </div>
                        </div>

                        <div class="rounded-full border border-white/30 bg-white/14 px-5 py-3 text-center text-sm text-white/82 shadow-sm backdrop-blur">
                            <a href="{{ url('/') }}" class="font-medium text-white transition hover:text-emerald-200">
                                <i class="bi bi-arrow-left mr-1"></i>Retour au site
                            </a>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>
</html>

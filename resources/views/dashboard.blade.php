@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('breadcrumb', 'Vue d’ensemble')

@section('content')
@php
    $firstName = explode(' ', trim(auth()->user()->name))[0] ?? auth()->user()->name;
    $kpis = [
        ['label' => 'Classes actives', 'value' => $stats['total_classes'], 'icon' => 'bi-building', 'tone' => 'bg-blue-50 text-blue-700'],
        ['label' => 'Élèves inscrits', 'value' => $stats['total_eleves'], 'icon' => 'bi-people-fill', 'tone' => 'bg-brand-50 text-brand-700'],
        ['label' => 'Matières', 'value' => $stats['total_matieres'], 'icon' => 'bi-book-fill', 'tone' => 'bg-emerald-50 text-emerald-700'],
        ['label' => 'Notes saisies', 'value' => $stats['total_notes'], 'icon' => 'bi-clipboard-data-fill', 'tone' => 'bg-amber-50 text-amber-700'],
    ];
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Vue d'ensemble</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">Bonjour, {{ $firstName }}</h2>
            <p class="mt-2 text-sm text-gray-500">
                @if(isset($currentAcademicLabel))
                    Année scolaire <span class="font-medium text-gray-700">{{ $currentAcademicLabel }}</span> •
                @endif
                {{ \Carbon\Carbon::now()->translatedFormat('l j F Y') }}
            </p>
        </div>
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'secretariat')
            <a href="{{ route('notes.masse.index') }}" class="btn-primary self-start lg:self-auto">
                <i class="bi bi-table"></i>
                Saisie en masse
            </a>
        @endif
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($kpis as $kpi)
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">{{ $kpi['label'] }}</p>
                            <p class="mt-4 text-3xl font-semibold tracking-tight text-gray-900" data-count="{{ $kpi['value'] }}">0</p>
                            <p class="mt-2 text-sm text-gray-500">Année en cours</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $kpi['tone'] }}">
                            <i class="bi {{ $kpi['icon'] }} text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(320px,1fr)]">
        <div class="card">
            <div class="card-header">
                <h4>Actions rapides</h4>
                <span class="badge-gray">Raccourcis</span>
            </div>
            <div class="card-body space-y-3">
                <a href="{{ route('eleves.create') }}" class="flex items-center gap-3 rounded-xl border border-gray-200 bg-slate-50 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-brand-200 hover:bg-brand-50/40 hover:text-brand-700">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-700"><i class="bi bi-person-plus-fill"></i></span>
                    <span>Inscrire un élève</span>
                </a>

                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'secretariat')
                    <a href="{{ route('notes.masse.index') }}" class="flex items-center gap-3 rounded-xl border border-gray-200 bg-slate-50 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700"><i class="bi bi-table"></i></span>
                        <span>Saisie en masse</span>
                    </a>
                    <a href="{{ route('classement.index') }}" class="flex items-center gap-3 rounded-xl border border-gray-200 bg-slate-50 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-amber-200 hover:bg-amber-50 hover:text-amber-700">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700"><i class="bi bi-trophy-fill"></i></span>
                        <span>Voir le classement</span>
                    </a>
                    <a href="{{ route('classes.index') }}" class="flex items-center gap-3 rounded-xl border border-gray-200 bg-slate-50 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"><i class="bi bi-printer-fill"></i></span>
                        <span>Feuilles d'appel</span>
                    </a>
                @endif

                <a href="{{ route('eleves.index') }}" class="flex items-center gap-3 rounded-xl border border-gray-200 bg-slate-50 px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-white hover:text-gray-900">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-700"><i class="bi bi-people-fill"></i></span>
                    <span>Liste des élèves</span>
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="card">
            <div class="card-header">
                <h4>Résumé du compte</h4>
                <span class="badge-gray">Session</span>
            </div>
            <div class="card-body">
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-2xl border border-gray-100 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Utilisateur</p>
                        <p class="mt-3 text-base font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="mt-2"><span class="badge-blue">{{ ucfirst(auth()->user()->role) }}</span></p>
                    </div>
                    <div class="rounded-2xl border border-gray-100 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Contexte</p>
                        <p class="mt-3 text-base font-semibold text-gray-900">{{ $currentAcademicLabel ?? '—' }}</p>
                        <p class="mt-2"><span class="badge-green">Session active</span></p>
                    </div>
                    <div class="rounded-2xl border border-brand-100 bg-brand-50/40 p-4 md:col-span-2">
                        <p class="text-sm text-gray-600">
                            <i class="bi bi-shield-check mr-2 text-brand-600"></i>
                            Accès rapide aux modules de gestion, aux exports PDF et aux écrans de suivi pour piloter l'année en cours.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = 'Inter, ui-sans-serif, system-ui, sans-serif';
    Chart.defaults.color = '#94a3b8';

    document.querySelectorAll('[data-count]').forEach(el => {
        const target = Number(el.dataset.count || 0);
        let current = 0;
        const step = Math.max(1, Math.ceil(target / 30));
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                el.textContent = target;
                clearInterval(timer);
            } else {
                el.textContent = current;
            }
        }, 18);
    });
});
</script>
@endpush
@php
    $user = auth()->user();
@endphp

@if($user)
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" class="account-chip">
            <span class="account-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            <span class="hidden sm:inline">Mon compte</span>
            <i class="bi bi-chevron-down text-[10px] text-white/70"></i>
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
            class="absolute right-0 top-14 z-50 w-64 rounded-[26px] border border-white/60 bg-white/96 py-2 shadow-[0_24px_70px_rgba(15,23,42,0.18)]"
        >
            <div class="mb-1 border-b border-slate-100 px-4 py-3">
                <p class="truncate text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                <p class="truncate text-xs text-slate-400">{{ $user->email }}</p>
            </div>

            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50">
                <i class="bi bi-person w-4 text-slate-400"></i>Mon profil
            </a>

            <div class="mt-1 border-t border-slate-100 pt-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-sm text-red-600 transition hover:bg-red-50">
                        <i class="bi bi-box-arrow-right w-4"></i>Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif

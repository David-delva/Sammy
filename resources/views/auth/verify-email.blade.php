<x-guest-layout>
    <div class="mb-8">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">V&eacute;rification</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">V&eacute;rifiez votre adresse e-mail</h1>
        <p class="mt-2 text-sm text-gray-500">Avant de commencer, cliquez sur le lien envoy&eacute; &agrave; votre adresse e-mail. Si besoin, nous pouvons vous en renvoyer un.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert-success mb-6">
            <i class="bi bi-check-circle-fill text-emerald-600"></i>
            <span>Un nouveau lien de v&eacute;rification a &eacute;t&eacute; envoy&eacute; &agrave; l'adresse e-mail que vous avez fournie.</span>
        </div>
    @endif

    <div class="space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary w-full justify-center">
                <i class="bi bi-envelope-arrow-up"></i>
                Renvoyer l'e-mail de v&eacute;rification
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-secondary w-full justify-center">
                <i class="bi bi-box-arrow-right"></i>
                Se d&eacute;connecter
            </button>
        </form>
    </div>
</x-guest-layout>
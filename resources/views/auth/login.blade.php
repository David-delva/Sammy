<x-guest-layout>
    <div class="mb-8">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">Connexion</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">Acc&eacute;der &agrave; votre espace</h1>
        <p class="mt-2 text-sm text-gray-500">Connectez-vous pour g&eacute;rer les &eacute;l&egrave;ves, les notes et les bulletins de votre &eacute;tablissement.</p>
    </div>

    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div class="form-field">
            <label for="email" class="form-label">Adresse e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nom@exemple.com" class="form-input @error('email') error @enderror">
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="password" class="form-label">Mot de passe</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Votre mot de passe" class="form-input @error('password') error @enderror">
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between gap-4 text-sm">
            <label for="remember_me" class="flex items-center gap-3 text-gray-600">
                <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                <span>Se souvenir de moi</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="font-medium text-brand-600 transition hover:text-brand-700 hover:underline">Mot de passe oubli&eacute; ?</a>
            @endif
        </div>

        <button type="submit" class="btn-primary w-full justify-center">
            <i class="bi bi-box-arrow-in-right"></i>
            Se connecter
        </button>
    </form>

    @if (Route::has('register'))
        <div class="border-t border-gray-100 pt-5 text-center text-sm text-gray-500">
            Pas encore de compte ?
            <a href="{{ route('register') }}" class="font-medium text-brand-600 transition hover:text-brand-700 hover:underline">S'inscrire</a>
        </div>
    @endif
</x-guest-layout>
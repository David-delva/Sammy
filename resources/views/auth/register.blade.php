<x-guest-layout>
    <div class="mb-8">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cobalt-600">Inscription</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">Creer votre compte</h1>
        <p class="mt-2 text-sm text-gray-500">Ouvrez un acces pour administrer l'annee scolaire, les classes et les resultats.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div class="form-field">
            <label for="name" class="form-label">Nom complet</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Votre nom complet" class="form-input @error('name') error @enderror">
            @error('name')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="email" class="form-label">Adresse e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="nom@exemple.com" class="form-input @error('email') error @enderror">
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="password" class="form-label">Mot de passe</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimum 8 caracteres" class="form-input @error('password') error @enderror">
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirmez votre mot de passe" class="form-input @error('password_confirmation') error @enderror">
            @error('password_confirmation')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary w-full justify-center">
            <i class="bi bi-person-plus"></i>
            Creer mon compte
        </button>
    </form>

    <div class="border-t border-gray-100 pt-5 text-center text-sm text-gray-500">
        Deja un compte ?
        <a href="{{ route('login') }}" class="font-medium text-cobalt-600 transition hover:text-cobalt-700 hover:underline">Se connecter</a>
    </div>
</x-guest-layout>

<x-guest-layout>
    <div class="mb-8">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600">R&eacute;initialisation</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">Choisir un nouveau mot de passe</h1>
        <p class="mt-2 text-sm text-gray-500">D&eacute;finissez un mot de passe robuste pour s&eacute;curiser &agrave; nouveau votre compte.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-field">
            <label for="email" class="form-label">Adresse e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" class="form-input @error('email') error @enderror">
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="password" class="form-label">Nouveau mot de passe</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="form-input @error('password') error @enderror">
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="form-input @error('password_confirmation') error @enderror">
            @error('password_confirmation')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary w-full justify-center">
            <i class="bi bi-check-circle"></i>
            R&eacute;initialiser le mot de passe
        </button>
    </form>
</x-guest-layout>
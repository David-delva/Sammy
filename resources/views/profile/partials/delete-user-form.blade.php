<section
    x-data="{ open: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }"
    x-init="$watch('open', value => { if (value) { $nextTick(() => { if ($refs.password) { $refs.password.focus() } }) } })"
    @keydown.escape.window="open = false"
    class="space-y-6"
>
    <header class="space-y-2">
        <div class="flex items-center gap-2 text-red-600">
            <i class="bi bi-trash text-base"></i>
            <p class="text-xs font-semibold uppercase tracking-[0.24em]">Zone sensible</p>
        </div>
        <div>
            <h2 class="text-xl font-semibold tracking-tight text-gray-900">Supprimer le compte</h2>
            <p class="mt-1 text-sm text-gray-500">Cette action est irr&eacute;versible. Elle supprime votre compte et l'ensemble des donn&eacute;es qui lui sont directement rattach&eacute;es.</p>
        </div>
    </header>

    <div class="alert-error">
        <i class="bi bi-exclamation-octagon-fill text-red-600"></i>
        <span>Avant de continuer, assurez-vous de ne plus avoir besoin des donn&eacute;es li&eacute;es &agrave; ce compte.</span>
    </div>

    <button type="button" class="btn-danger" @click="open = true">
        <i class="bi bi-trash3"></i>
        Supprimer le compte
    </button>

    <div
        x-cloak
        x-show="open"
        class="fixed inset-0 z-[70] flex items-center justify-center p-4 sm:p-6"
        aria-labelledby="delete-account-title"
        role="dialog"
        aria-modal="true"
    >
        <div class="absolute inset-0 bg-slate-950/55" @click="open = false"></div>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-3 scale-95"
            class="relative w-full max-w-md rounded-3xl border border-gray-100 bg-white p-6 shadow-[0_30px_80px_rgba(15,23,42,0.22)]"
        >
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-red-600">Confirmation</p>
                    <h3 id="delete-account-title" class="mt-2 text-xl font-semibold tracking-tight text-gray-900">Confirmer la suppression</h3>
                </div>

                <button type="button" class="btn-icon btn-secondary" @click="open = false" aria-label="Fermer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <p class="mt-4 text-sm leading-7 text-gray-500">Saisissez votre mot de passe pour confirmer la suppression d&eacute;finitive du compte.</p>

            <form method="POST" action="{{ route('profile.destroy') }}" class="mt-6 space-y-5">
                @csrf
                @method('delete')

                <div class="form-field">
                    <label for="delete_user_password" class="form-label">Mot de passe</label>
                    <input
                        id="delete_user_password"
                        x-ref="password"
                        name="password"
                        type="password"
                        placeholder="Votre mot de passe"
                        class="form-input @error('password', 'userDeletion') error @enderror"
                    >
                    @error('password', 'userDeletion')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" class="btn-secondary justify-center" @click="open = false">Annuler</button>
                    <button type="submit" class="btn-danger justify-center">
                        <i class="bi bi-trash3"></i>
                        Supprimer d&eacute;finitivement
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
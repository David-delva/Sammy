<section>
    <header class="mb-2">
        <h5 class="mb-0">Supprimer le compte</h5>
        <p class="text-muted small">La suppression du compte est irréversible. Téléchargez vos données avant de continuer.</p>
    </header>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
        Supprimer le compte
    </button>

    <!-- Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmer la suppression</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')
            <div class="modal-body">
                <p>Cette action supprimera définitivement votre compte et toutes les données associées. Tapez votre mot de passe pour confirmer :</p>
                <div class="mb-3">
                    <input id="password" name="password" type="password" class="form-control" placeholder="Mot de passe">
                    @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
              <button type="submit" class="btn btn-danger">Supprimer le compte</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</section>

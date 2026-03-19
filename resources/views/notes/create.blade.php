@extends('layouts.app')

@section('title', 'Saisir une Note')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary"><i class="bi bi-pencil-square me-2"></i>Saisie de Note</h5>
                <span class="badge bg-primary">{{ $annee->libelle }}</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('notes.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="eleve_id" class="form-label fw-bold">Élève <span class="text-danger">*</span></label>
                        <select class="form-select @error('eleve_id') is-invalid @enderror" 
                                id="eleve_id" name="eleve_id" required>
                            <option value="">-- Sélectionner l'élève --</option>
                            @foreach($eleves as $eleve)
                                <option value="{{ $eleve->id }}" 
                                        data-classe="{{ $eleve->resolved_classe_id }}"
                                        {{ old('eleve_id') == $eleve->id ? 'selected' : '' }}>
                                    {{ $eleve->nom }} {{ $eleve->prenom }} ({{ $eleve->resolved_classe_nom }})
                                </option>
                            @endforeach
                        </select>
                        @error('eleve_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="matiere_id" class="form-label fw-bold">Matière <span class="text-danger">*</span></label>
                        <select class="form-select @error('matiere_id') is-invalid @enderror" 
                                id="matiere_id" name="matiere_id" required>
                            <option value="">-- Choisissez d'abord un élève --</option>
                            @foreach($matieres as $matiere)
                                <option value="{{ $matiere->id }}" 
                                        data-classe="{{ $matiere->classe_id }}"
                                        class="matiere-option d-none">
                                    {{ $matiere->nom_matiere }}
                                </option>
                            @endforeach
                        </select>
                        @error('matiere_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="note" class="form-label fw-bold">Note / 20 <span class="text-danger">*</span></label>
                            <input type="number" step="0.25" min="0" max="20" 
                                   class="form-control @error('note') is-invalid @enderror" 
                                   id="note" name="note" value="{{ old('note') }}" required>
                            @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="type_devoir" class="form-label fw-bold">Type d'Évaluation <span class="text-danger">*</span></label>
                            <select class="form-select @error('type_devoir') is-invalid @enderror" 
                                    id="type_devoir" name="type_devoir" required>
                                <option value="devoir" {{ old('type_devoir') == 'devoir' ? 'selected' : '' }}>Devoir</option>
                                <option value="composition" {{ old('type_devoir') == 'composition' ? 'selected' : '' }}>Composition</option>
                            </select>
                            @error('type_devoir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('notes.index') }}" class="btn btn-light border">Annuler</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i>Enregistrer la Note
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const eleveSelect = document.getElementById('eleve_id');
    const matiereSelect = document.getElementById('matiere_id');
    const matiereOptions = Array.from(matiereSelect.options);

    function filterMatieres() {
        const selectedEleve = eleveSelect.options[eleveSelect.selectedIndex];
        const classeId = selectedEleve.dataset.classe;

        matiereSelect.innerHTML = '<option value="">-- Sélectionner la matière --</option>';
        
        if (classeId) {
            matiereOptions.forEach(opt => {
                if (opt.dataset.classe === classeId) {
                    matiereSelect.appendChild(opt.cloneNode(true));
                }
            });
        } else {
            matiereSelect.innerHTML = '<option value="">-- Choisissez d'abord un élève --</option>';
        }
    }

    eleveSelect.addEventListener('change', filterMatieres);

    if (eleveSelect.value) {
        filterMatieres();
    }
});
</script>
@endsection

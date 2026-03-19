@extends('layouts.app')

@section('title', 'Classe : ' . $classe->nom_classe)

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Classe : {{ $classe->nom_classe }} @if(request()->query('date')) — {{ \App\Models\AnneeAcademique::labelForDate(request()->query('date')) }} @endif</h5>
        <a href="{{ route('classes.index', ['date' => request()->query('date')]) }}" class="btn btn-sm btn-outline-secondary">Retour à la liste</a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <h6>Élèves ({{ $classe->eleves->count() }})</h6>
                @if($classe->eleves->isEmpty())
                    <p class="text-muted">Aucun élève pour cette classe.</p>
                @else
                    <ul class="list-unstyled">
                        @foreach($classe->eleves as $eleve)
                            <li class="py-1">{{ $eleve->nom }} {{ $eleve->prenom }} @if(isset($eleve->matricule)) — <small class="text-muted">{{ $eleve->matricule }}</small> @endif</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="col-md-6 mb-3">
                <h6>Matières ({{ $classe->matieres->count() }})</h6>
                @if($classe->matieres->isEmpty())
                    <p class="text-muted">Aucune matière pour cette classe.</p>
                @else
                    <ul class="list-unstyled">
                        @foreach($classe->matieres as $matiere)
                            <li class="py-1">{{ $matiere->nom_matiere ?? $matiere->nom }} @if(isset($matiere->pivot->coefficient)) — <small class="text-muted">Coef : {{ $matiere->pivot->coefficient }}</small> @endif</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

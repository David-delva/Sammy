<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Eleve extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'date_naissance',
        'lieu_naissance',
        'sexe',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    // --- Relations ---

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function latestInscription(): HasOne
    {
        return $this->hasOne(Inscription::class)->latestOfMany();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    // --- MÃ©thodes mÃ©tier ---

    /**
     * Retourne l'inscription correspondant Ã  l'annÃ©e acadÃ©mique d'une date donnÃ©e.
     */
    public function inscriptionForDate(?string $date = null): ?Inscription
    {
        $label = AnneeAcademique::labelForDate($date);

        return $this->inscriptions()
            ->whereHas('anneeAcademique', fn ($q) => $q->where('libelle', $label))
            ->latest('created_at')
            ->first();
    }

    /**
     * Retourne la classe de l'Ã©lÃ¨ve pour une date donnÃ©e (via inscription).
     */
    public function classeForDate(?string $date = null): ?Classe
    {
        return $this->inscriptionForDate($date)?->classe;
    }
}

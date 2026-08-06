<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Eleve extends Model
{
    use HasFactory;

    protected $fillable = ['matricule', 'nom', 'prenom', 'date_naissance', 'lieu_naissance', 'sexe'];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    public static function booted(): void
    {
        static::deleting(function (self $eleve): bool {
            if ($eleve->inscriptions()->exists()) {
                return false;
            }

            if ($eleve->notes()->exists()) {
                return false;
            }

            return true;
        });
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function latestInscription(): HasOne
    {
        return $this->hasOne(Inscription::class)->latestOfMany('created_at');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Retourne l'inscription la plus recente de l'eleve.
     */
    public function inscriptionActuelle(): ?Inscription
    {
        /** @var Inscription|null $inscription */
        $inscription = $this->inscriptions()->latest('created_at')->first();

        return $inscription;
    }

    /**
     * Retourne l'inscription de l'eleve correspondant a une date donnee.
     */
    public function inscriptionForDate(?string $date = null): ?Inscription
    {
        $label = AnneeAcademique::labelForDate($date);

        /** @var Inscription|null $inscription */
        $inscription = $this->inscriptions()
            ->whereHas('anneeAcademique', fn ($q) => $q->where('libelle', $label))
            ->latest('created_at')
            ->first();

        return $inscription;
    }

    public function inscriptionForAcademicYear(?AnneeAcademique $annee): ?Inscription
    {
        if (! $annee) {
            return null;
        }

        /** @var Inscription|null $inscription */
        $inscription = $this->inscriptions()
            ->where('annee_academique_id', $annee->id)
            ->latest('created_at')
            ->first();

        return $inscription;
    }

    /**
     * Retourne la classe de l'eleve pour une date donnee (via inscription).
     */
    public function classeForDate(?string $date = null): ?Classe
    {
        return $this->inscriptionForDate($date)?->classe;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_classe',
    ];

    /**
     * Élèves de la classe (via inscriptions)
     */
    public function eleves(): BelongsToMany
    {
        return $this->belongsToMany(Eleve::class, 'inscriptions', 'classe_id', 'eleve_id')
            ->withTimestamps();
    }

    /**
     * Inscriptions de la classe
     */
    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    /**
     * Matières de la classe pour TOUTES les années
     */
    public function matieres(): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'classe_matiere')
            ->withPivot('coefficient', 'credits', 'annee_academique_id')
            ->withTimestamps();
    }

    /**
     * Matières pour une année académique spécifique
     */
    public function matieresForAnnee(int $anneeId): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'classe_matiere')
            ->withPivot('coefficient', 'credits', 'annee_academique_id')
            ->wherePivot('annee_academique_id', $anneeId)
            ->withTimestamps();
    }

    /**
     * Semestres de la classe (un par année académique et par numéro 1/2)
     */
    public function semestres(): HasMany
    {
        return $this->hasMany(Semestre::class);
    }

    /**
     * Semestres pour une année académique spécifique
     */
    public function semestresForAnnee(int $anneeId): HasMany
    {
        return $this->semestres()->where('annee_academique_id', $anneeId);
    }

    /**
     * UE de la classe pour une année académique spécifique (via ses semestres)
     */
    public function uesForAnnee(int $anneeId): Builder
    {
        return Ue::query()->whereHas('semestre', function ($query) use ($anneeId) {
            $query->where('classe_id', $this->id)->where('annee_academique_id', $anneeId);
        });
    }
}

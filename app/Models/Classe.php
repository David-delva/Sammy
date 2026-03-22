<?php

namespace App\Models;

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
            ->withPivot('coefficient', 'annee_academique_id')
            ->withTimestamps();
    }

    /**
     * Matières pour une année académique spécifique
     */
    public function matieresForAnnee(int $anneeId): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'classe_matiere')
            ->withPivot('coefficient', 'annee_academique_id')
            ->wherePivot('annee_academique_id', $anneeId)
            ->withTimestamps();
    }
}

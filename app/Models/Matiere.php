<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Relations\Pivot&object{coefficient:int, credits:int, annee_academique_id:int} $pivot
 */
class Matiere extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_matiere',
        'ue_id',
        'coefficient',
        'credits',
    ];

    protected $casts = [
        'coefficient' => 'integer',
        'credits' => 'integer',
    ];

    /**
     * Classes qui enseignent cette matière
     * (avec coefficient et crédits selon la classe et l'année — pour les programmes hors UE)
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classe::class, 'classe_matiere')
            ->withPivot('coefficient', 'credits', 'annee_academique_id')
            ->withTimestamps();
    }

    /**
     * Notes associées à cette matière
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Unité d'enseignement à laquelle cette matière est rattachée (programmes LP ASUR / UE)
     */
    public function ue(): BelongsTo
    {
        return $this->belongsTo(Ue::class);
    }
}

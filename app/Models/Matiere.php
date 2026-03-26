<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Relations\Pivot&object{coefficient:int, annee_academique_id:int} $pivot
 */
class Matiere extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_matiere',
    ];

    /**
     * Classes qui enseignent cette matière
     * (avec coefficient selon la classe et l'année)
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classe::class, 'classe_matiere')
            ->withPivot('coefficient', 'annee_academique_id')
            ->withTimestamps();
    }

    /**
     * Notes associées à cette matière
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Classe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_classe',
    ];

    public function eleves(): BelongsToMany
    {
        // Les élèves sont liés via la table des inscriptions
        return $this->belongsToMany(Eleve::class, 'inscriptions', 'classe_id', 'eleve_id')
            ->withTimestamps();
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function matieres(): HasMany
    {
        return $this->hasMany(Matiere::class);
    }
}

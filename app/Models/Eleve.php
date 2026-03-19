<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Eleve extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'date_naissance',
        'sexe',
        'classe_id',   // ← doit correspondre à ta migration
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'date_naissance' => 'date',
    ];

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function latestInscription(): HasOne
    {
        return $this->hasOne(Inscription::class)->latestOfMany();
    }

    /**
     * Return the inscription that corresponds to the academic year for the given date.
     */
    public function inscriptionForDate($date = null)
    {
        $label = \App\Models\AnneeAcademique::labelForDate($date);
        return $this->inscriptions()->whereHas('anneeAcademique', function($q) use ($label) {
            $q->where('libelle', $label);
        })->latest('created_at')->first();
    }

    public function classe(): HasOneThrough
    {
        // On récupère la classe via la dernière inscription
        return $this->hasOneThrough(
            Classe::class,
            Inscription::class,
            'eleve_id', // Clé étrangère sur la table inscriptions
            'id',       // Clé étrangère sur la table classes
            'id',       // Clé locale sur la table eleves
            'classe_id' // Clé locale sur la table inscriptions
        )->latest('inscriptions.created_at');
    }

    /**
     * Get the classe for a specific date (based on the inscription that matches the academic year label).
     */
    public function classeForDate($date = null)
    {
        $inscription = $this->inscriptionForDate($date);
        return $inscription ? $inscription->classe : null;
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}

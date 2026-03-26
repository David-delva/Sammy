<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicSubjectResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'eleve_id',
        'classe_id',
        'annee_academique_id',
        'matiere_id',
        'period',
        'coefficient',
        'total_notes',
        'moyenne_devoirs',
        'note_composition',
        'moyenne_matiere',
        'moy_x_coef',
        'last_recorded_at',
    ];

    protected $casts = [
        'period' => 'integer',
        'coefficient' => 'integer',
        'total_notes' => 'integer',
        'moyenne_devoirs' => 'float',
        'note_composition' => 'float',
        'moyenne_matiere' => 'float',
        'moy_x_coef' => 'float',
        'last_recorded_at' => 'datetime',
    ];

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class);
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }
}

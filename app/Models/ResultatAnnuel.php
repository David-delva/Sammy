<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultatAnnuel extends Model
{
    use HasFactory;

    public const DECISION_DIPLOME = 'diplome';

    public const DECISION_REPRISE_SOUTENANCE = 'reprise_soutenance';

    public const DECISION_REDOUBLE = 'redouble';

    protected $fillable = [
        'eleve_id',
        'annee_academique_id',
        'classe_id',
        'total_notes',
        'evaluated_subjects',
        'total_points',
        'total_coefficients',
        'moyenne_annuelle',
        'credits_acquis',
        'credits_total',
        'decision_jury',
        'mention',
    ];

    protected $casts = [
        'total_notes' => 'integer',
        'evaluated_subjects' => 'integer',
        'total_points' => 'float',
        'total_coefficients' => 'integer',
        'moyenne_annuelle' => 'float',
        'credits_acquis' => 'integer',
        'credits_total' => 'integer',
    ];

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }
}

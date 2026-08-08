<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultatSemestre extends Model
{
    use HasFactory;

    public const DECISION_VALIDE = 'valide';

    public const DECISION_NON_VALIDE = 'non_valide';

    public const DECISION_ADMISSIBLE_S6 = 'admissible_s6';

    protected $fillable = [
        'eleve_id',
        'semestre_id',
        'classe_id',
        'total_notes',
        'evaluated_subjects',
        'total_points',
        'total_coefficients',
        'moyenne_semestre',
        'credits_acquis',
        'credits_total',
        'decision',
    ];

    protected $casts = [
        'total_notes' => 'integer',
        'evaluated_subjects' => 'integer',
        'total_points' => 'float',
        'total_coefficients' => 'integer',
        'moyenne_semestre' => 'float',
        'credits_acquis' => 'integer',
        'credits_total' => 'integer',
    ];

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    public function semestre(): BelongsTo
    {
        return $this->belongsTo(Semestre::class);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicResult extends Model
{
    use HasFactory;

    public const PERIOD_ANNUAL = 0;

    public const PERIOD_SEMESTRE_1 = 1;

    public const PERIOD_SEMESTRE_2 = 2;

    protected $fillable = [
        'eleve_id',
        'classe_id',
        'annee_academique_id',
        'period',
        'total_notes',
        'evaluated_subjects',
        'total_points',
        'total_coefficients',
        'moyenne_generale',
    ];

    protected $casts = [
        'period' => 'integer',
        'total_notes' => 'integer',
        'evaluated_subjects' => 'integer',
        'total_points' => 'float',
        'total_coefficients' => 'integer',
        'moyenne_generale' => 'float',
    ];

    public static function periods(): array
    {
        return [
            self::PERIOD_ANNUAL,
            self::PERIOD_SEMESTRE_1,
            self::PERIOD_SEMESTRE_2,
        ];
    }

    public static function periodFromSemestre(?int $semestre): int
    {
        return $semestre ?? self::PERIOD_ANNUAL;
    }

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
}

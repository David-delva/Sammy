<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenaliteAbsence extends Model
{
    use HasFactory;

    public const DEFAULT_PENALITE_PAR_HEURE = 0.01;

    protected $fillable = [
        'classe_id',
        'annee_academique_id',
        'penalite_par_heure',
    ];

    protected $casts = [
        'penalite_par_heure' => 'float',
    ];

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class);
    }

    public static function tauxPour(?int $classeId, ?int $anneeId): float
    {
        if ($classeId !== null && $anneeId !== null) {
            $specifique = static::query()
                ->where('classe_id', $classeId)
                ->where('annee_academique_id', $anneeId)
                ->value('penalite_par_heure');

            if ($specifique !== null) {
                return (float) $specifique;
            }
        }

        $global = static::query()
            ->whereNull('classe_id')
            ->whereNull('annee_academique_id')
            ->value('penalite_par_heure');

        return $global !== null ? (float) $global : self::DEFAULT_PENALITE_PAR_HEURE;
    }
}

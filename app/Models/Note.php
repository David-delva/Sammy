<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    use HasFactory;

    public const SEMESTRE_1 = 1;

    public const SEMESTRE_2 = 2;

    public const TYPE_DEVOIR = 'devoir';

    public const TYPE_COMPOSITION = 'composition';

    public const TYPE_RATTRAPAGE = 'rattrapage';

    protected $fillable = [
        'eleve_id',
        'matiere_id',
        'note',
        'type_devoir',
        'annee_academique_id',
        'semestre',
    ];

    protected $casts = [
        'note' => 'float',
        'semestre' => 'integer',
    ];

    public static function semestreOptions(): array
    {
        return [
            self::SEMESTRE_1 => '1er semestre',
            self::SEMESTRE_2 => '2e semestre',
        ];
    }

    public static function typeDevoirOptions(): array
    {
        return [
            self::TYPE_DEVOIR => 'Devoir (CC)',
            self::TYPE_COMPOSITION => 'Composition (Examen)',
            self::TYPE_RATTRAPAGE => 'Rattrapage',
        ];
    }

    public function getSemestreLabelAttribute(): string
    {
        return self::semestreOptions()[$this->semestre] ?? 'Semestre '.$this->semestre;
    }

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class);
    }
}

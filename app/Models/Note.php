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

    public function getSemestreLabelAttribute(): string
    {
        return self::semestreOptions()[$this->semestre] ?? 'Semestre ' . $this->semestre;
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
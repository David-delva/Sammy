<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Semestre extends Model
{
    use HasFactory;

    public const NUMERO_1 = 1;

    public const NUMERO_2 = 2;

    protected $fillable = [
        'libelle',
        'numero',
        'classe_id',
        'annee_academique_id',
    ];

    protected $casts = [
        'numero' => 'integer',
    ];

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class);
    }

    public function ues(): HasMany
    {
        return $this->hasMany(Ue::class);
    }

    public function resultat(): HasOne
    {
        return $this->hasOne(ResultatSemestre::class);
    }

    public static function pour(int $classeId, int $anneeId, int $numero): self
    {
        return static::query()->firstOrCreate(
            ['classe_id' => $classeId, 'annee_academique_id' => $anneeId, 'numero' => $numero],
            ['libelle' => 'Semestre '.$numero]
        );
    }
}

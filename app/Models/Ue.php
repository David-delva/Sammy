<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ue extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'libelle',
        'credits',
        'semestre_id',
    ];

    protected $casts = [
        'credits' => 'integer',
    ];

    public function semestre(): BelongsTo
    {
        return $this->belongsTo(Semestre::class);
    }

    public function matieres(): HasMany
    {
        return $this->hasMany(Matiere::class);
    }

    public function academicUeResults(): HasMany
    {
        return $this->hasMany(AcademicUeResult::class);
    }
}

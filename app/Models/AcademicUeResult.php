<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicUeResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'eleve_id',
        'ue_id',
        'total_matieres',
        'moyenne_ue',
        'valide',
        'compense',
        'credits_acquis',
    ];

    protected $casts = [
        'total_matieres' => 'integer',
        'moyenne_ue' => 'float',
        'valide' => 'boolean',
        'compense' => 'boolean',
        'credits_acquis' => 'integer',
    ];

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    public function ue(): BelongsTo
    {
        return $this->belongsTo(Ue::class);
    }
}

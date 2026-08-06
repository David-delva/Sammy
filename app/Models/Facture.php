<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Billing\PaiementFacture;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facture extends Model
{
    use HasFactory;

    public const STATUT_EMISE = 'emise';

    public const STATUT_PARTIELLEMENT_PAYEE = 'partiellement_payee';

    public const STATUT_PAYEE = 'payee';

    public const STATUT_ANNULEE = 'annulee';

    protected $fillable = [
        'inscription_id',
        'created_by',
        'numero',
        'libelle',
        'description',
        'montant',
        'statut',
        'date_emission',
        'date_echeance',
        'date_paiement',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_emission' => 'date',
        'date_echeance' => 'date',
        'date_paiement' => 'date',
    ];

    public static function statutOptions(): array
    {
        return [
            self::STATUT_EMISE => 'Emise',
            self::STATUT_PARTIELLEMENT_PAYEE => 'Partiellement payee',
            self::STATUT_PAYEE => 'Payee',
            self::STATUT_ANNULEE => 'Annulee',
        ];
    }

    public static function manualStatutOptions(): array
    {
        return [
            self::STATUT_EMISE => 'Emise',
            self::STATUT_PAYEE => 'Payee',
            self::STATUT_ANNULEE => 'Annulee',
        ];
    }

    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(PaiementFacture::class);
    }

    public function getMontantPayeAttribute(): float
    {
        if (array_key_exists('paiements_sum_montant', $this->attributes)) {
            return round((float) $this->attributes['paiements_sum_montant'], 2);
        }

        if ($this->relationLoaded('paiements')) {
            return round((float) $this->paiements->sum('montant'), 2);
        }

        return round((float) $this->paiements()->sum('montant'), 2);
    }

    public function getSoldeRestantAttribute(): float
    {
        return max(0.0, round((float) $this->montant - $this->montant_paye, 2));
    }

    public function getHasPaiementsAttribute(): bool
    {
        if ($this->relationLoaded('paiements')) {
            return $this->paiements->isNotEmpty();
        }

        return $this->paiements()->exists();
    }
}

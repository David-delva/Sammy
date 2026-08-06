<?php

declare(strict_types=1);

namespace App\Models\Billing;

use App\Models\Facture;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaiementFacture extends Model
{
    use HasFactory;

    public const MODE_ESPECES = 'especes';

    public const MODE_VIREMENT = 'virement';

    public const MODE_MOBILE_MONEY = 'mobile_money';

    public const MODE_CHEQUE = 'cheque';

    public const MODE_REGULARISATION = 'regularisation';

    protected $table = 'paiement_factures';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'facture_id',
        'created_by',
        'montant',
        'mode_paiement',
        'reference',
        'date_paiement',
        'commentaire',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'date',
    ];

    /**
     * @return array<string, string>
     */
    public static function modeOptions(): array
    {
        return [
            self::MODE_ESPECES => 'Especes',
            self::MODE_VIREMENT => 'Virement',
            self::MODE_MOBILE_MONEY => 'Mobile money',
            self::MODE_CHEQUE => 'Cheque',
            self::MODE_REGULARISATION => 'Regularisation',
        ];
    }

    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

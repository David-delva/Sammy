<?php

declare(strict_types=1);

namespace App\Http\Requests\Billing;

use App\Models\Billing\PaiementFacture;
use App\Models\Facture;
use Illuminate\Foundation\Http\FormRequest;

class StorePaiementFactureRequest extends FormRequest
{
    public function authorize(): bool
    {
        $facture = $this->route('facture');

        return $facture instanceof Facture
            && ($this->user()?->can('storePayment', $facture) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'montant' => ['required', 'numeric', 'gt:0'],
            'mode_paiement' => ['required', 'in:'.implode(',', array_keys(PaiementFacture::modeOptions()))],
            'reference' => ['nullable', 'string', 'max:120'],
            'date_paiement' => ['required', 'date'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

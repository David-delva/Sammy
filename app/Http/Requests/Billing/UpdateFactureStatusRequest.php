<?php

declare(strict_types=1);

namespace App\Http\Requests\Billing;

use App\Models\Facture;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFactureStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $facture = $this->route('facture');

        return $facture instanceof Facture
            && ($this->user()?->can('updateStatus', $facture) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'statut' => ['required', 'in:'.implode(',', array_keys(Facture::manualStatutOptions()))],
        ];
    }
}

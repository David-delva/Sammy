<?php

declare(strict_types=1);

namespace App\Http\Requests\Billing;

use App\Models\Facture;
use Illuminate\Foundation\Http\FormRequest;

class StoreFactureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Facture::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'inscription_id' => ['required', 'exists:inscriptions,id'],
            'libelle' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'montant' => ['required', 'numeric', 'min:0'],
            'date_emission' => ['required', 'date'],
            'date_echeance' => ['nullable', 'date', 'after_or_equal:date_emission'],
        ];
    }
}

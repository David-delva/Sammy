<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $facture->numero }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #0f172a;
            font-size: 13px;
            margin: 28px;
        }
        .header,
        .section,
        .totals {
            margin-bottom: 24px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .muted {
            color: #475569;
        }
        .grid {
            width: 100%;
            border-collapse: collapse;
        }
        .grid td,
        .grid th {
            border: 1px solid #cbd5e1;
            padding: 10px 12px;
            vertical-align: top;
        }
        .grid th {
            background: #f8fafc;
            text-align: left;
        }
        .amount {
            font-size: 22px;
            font-weight: bold;
            text-align: right;
        }
        .footer {
            margin-top: 32px;
            font-size: 11px;
            color: #64748b;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            background: #e2e8f0;
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @php
        $eleve = $facture->inscription->eleve;
        $classe = $facture->inscription->classe;
        $annee = $facture->inscription->anneeAcademique;
        $statusLabels = \App\Models\Facture::statutOptions();
        $paymentModes = \App\Models\Billing\PaiementFacture::modeOptions();
    @endphp

    <div class="header">
        <div class="title">Facture d'inscription</div>
        <div class="muted">{{ config('app.name') }}</div>
        <div class="muted">Numero : {{ $facture->numero }}</div>
        <div class="muted">Date d'emission : {{ $facture->date_emission?->format('d/m/Y') }}</div>
        <div class="muted">Echeance : {{ $facture->date_echeance?->format('d/m/Y') ?? '--' }}</div>
        <div style="margin-top: 10px;"><span class="badge">{{ $statusLabels[$facture->statut] ?? ucfirst($facture->statut) }}</span></div>
    </div>

    <div class="section">
        <table class="grid">
            <tr>
                <th style="width: 50%;">Eleve</th>
                <th style="width: 50%;">Inscription</th>
            </tr>
            <tr>
                <td>
                    <strong>{{ $eleve->nom }} {{ $eleve->prenom }}</strong><br>
                    Matricule : {{ $eleve->matricule }}<br>
                    Ne(e) le : {{ $eleve->date_naissance?->format('d/m/Y') }}<br>
                    Lieu de naissance : {{ $eleve->lieu_naissance ?: '--' }}
                </td>
                <td>
                    Classe : {{ $classe->nom_classe }}<br>
                    Annee academique : {{ $annee?->libelle }}<br>
                    Creee par : {{ $facture->creator?->name ?? 'Systeme' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table class="grid">
            <tr>
                <th>Libelle</th>
                <th>Description</th>
                <th style="width: 22%;">Montant</th>
            </tr>
            <tr>
                <td>{{ $facture->libelle }}</td>
                <td>{{ $facture->description ?: 'Frais d inscription annuelle.' }}</td>
                <td class="amount">{{ number_format((float) $facture->montant, 2, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    <div class="totals">
        <table class="grid">
            <tr>
                <th style="width: 40%;">Statut de reglement</th>
                <th style="width: 20%;">Date du dernier paiement</th>
                <th style="width: 20%;">Montant paye</th>
                <th style="width: 20%;">Solde restant</th>
            </tr>
            <tr>
                <td>{{ $statusLabels[$facture->statut] ?? ucfirst($facture->statut) }}</td>
                <td>{{ $facture->date_paiement?->format('d/m/Y') ?? '--' }}</td>
                <td>{{ number_format((float) $facture->montant_paye, 2, ',', ' ') }} FCFA</td>
                <td>{{ number_format((float) $facture->solde_restant, 2, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    @if($facture->paiements->isNotEmpty())
        <div class="section">
            <table class="grid">
                <tr>
                    <th style="width: 18%;">Date</th>
                    <th style="width: 20%;">Montant</th>
                    <th style="width: 20%;">Mode</th>
                    <th style="width: 20%;">Reference</th>
                    <th style="width: 22%;">Saisi par</th>
                </tr>
                @foreach($facture->paiements->sortByDesc(fn ($paiement) => (string) $paiement->date_paiement) as $paiement)
                    <tr>
                        <td>{{ $paiement->date_paiement?->format('d/m/Y') }}</td>
                        <td>{{ number_format((float) $paiement->montant, 2, ',', ' ') }} FCFA</td>
                        <td>{{ $paymentModes[$paiement->mode_paiement] ?? ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</td>
                        <td>{{ $paiement->reference ?: '--' }}</td>
                        <td>{{ $paiement->creator?->name ?? 'Systeme' }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    <div class="footer">
        Document genere depuis l'application de gestion scolaire. Ce document peut etre imprime et archive avec le dossier d'inscription.
    </div>
</body>
</html>

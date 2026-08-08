<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin de Notes - {{ $eleve->nom ?? '' }} {{ $eleve->prenom ?? '' }}</title>
    <style>
        @page {
            margin: 10mm 9mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8.5px;
            color: #000000;
            margin: 0;
        }

        .page {
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .top td {
            vertical-align: middle;
        }

        .logo-cell {
            width: 15%;
            text-align: center;
        }

        .inptic-header {
            width: 55%;
            text-align: center;
            line-height: 1.3;
        }

        .inptic-header .main-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .inptic-header .sub-title {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .republic {
            width: 30%;
            text-align: center;
            line-height: 1.5;
        }

        .republic .head {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }

        .republic .motto {
            font-size: 8px;
        }

        .title-box {
            margin: 12px 0 4px;
            text-align: center;
        }

        .title-box .bulletin-title {
            font-size: 13px;
            font-weight: bold;
        }

        .title-box .annee-academique {
            font-size: 9px;
            margin-top: 2px;
        }

        .classe-box {
            margin: 10px 0;
            border: 1px solid #000000;
            padding: 4px 8px;
            font-size: 9px;
            font-weight: bold;
        }

        .identity {
            margin-bottom: 10px;
            font-size: 8.5px;
        }

        .identity td {
            border: 1px solid #000000;
            padding: 3px 6px;
        }

        .identity .label {
            font-weight: bold;
            width: 30%;
            background: #f2f2f2;
        }

        .grades-table th,
        .grades-table td,
        .summary-table td,
        .credits-table td,
        .credits-table th,
        .decision-table td {
            border: 1px solid #000000;
        }

        .grades-table th {
            background: #d9d9d9;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            padding: 3px 2px;
        }

        .grades-table td {
            height: 16px;
            padding: 2px 4px;
            font-size: 8px;
            text-align: center;
        }

        .grades-table .matiere-name {
            text-align: left;
            width: 40%;
        }

        .ue-header td {
            background: #f2f2f2;
            font-weight: bold;
            text-align: left;
            padding: 3px 5px;
            font-size: 8.5px;
        }

        .ue-total td {
            font-weight: bold;
            background: #fafafa;
        }

        .ue-total .note-eleve {
            background: #4a86c6;
            color: #ffffff;
        }

        .absence-row td {
            font-style: italic;
        }

        .summary-table {
            margin-top: 8px;
        }

        .summary-table td {
            padding: 4px 6px;
            font-size: 8.5px;
        }

        .summary-table .label {
            font-weight: bold;
            width: 40%;
        }

        .summary-table .value {
            text-align: center;
            width: 30%;
        }

        .summary-table .moyenne-generale {
            background: #4a86c6;
            color: #ffffff;
            font-weight: bold;
        }

        .credits-table {
            margin-top: 10px;
            font-size: 8px;
        }

        .credits-caption {
            font-weight: bold;
            font-size: 8.5px;
            margin-top: 10px;
            margin-bottom: 3px;
        }

        .credits-table th {
            background: #d9d9d9;
            padding: 3px;
            text-align: center;
            font-weight: bold;
        }

        .credits-table td {
            text-align: center;
            padding: 3px;
            height: 18px;
        }

        .decision-table {
            margin-top: 8px;
        }

        .decision-table td {
            padding: 6px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            height: 25px;
        }

        .footer-section {
            margin-top: 15px;
            width: 100%;
        }

        .footer-section .date-fait {
            text-align: right;
            font-size: 8px;
            margin-bottom: 20px;
        }

        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            font-size: 8px;
            font-weight: bold;
            padding-top: 15px;
        }

        .legal-notice {
            margin-top: 20px;
            text-align: center;
            font-size: 7px;
            font-style: italic;
            color: #333333;
        }
    </style>
</head>
<body>
    @php
        $etudiantNom = $eleve->nom ?? '';
        $etudiantPrenom = $eleve->prenom ?? '';
        $dateNaissance = $eleve->date_naissance ? \Carbon\Carbon::parse($eleve->date_naissance)->format('d/m/Y') : '';
        $lieuNaissance = $eleve->lieu_naissance ?? 'Non renseigne';
        $classeLibelle = $classe->nom_classe ?? '';
        $anneeLibelle = $annee->libelle ? str_replace('-', '/', $annee->libelle) : '';

        $moyenneGenerale = $moyenne_generale !== null ? number_format((float) $moyenne_generale, 2, ',', ' ') : '';
        $moyenneGeneraleClasseFmt = ($moyenne_generale_classe ?? null) !== null ? number_format((float) $moyenne_generale_classe, 2, ',', ' ') : '';
        $rangDisplay = $rang ? $rang.'eme / '.$total_eleves : '';
        $mentionDisplay = $mention ?? '';
        $decisionJury = $decision ?? '';
        $dateFait = $date_fait ?? date('d/m/Y');
    @endphp

    <div class="page">
        <!-- EN-TETE -->
        <table class="top">
            <tr>
                <td class="logo-cell">
                    <img src="{{ public_path('images/logo-inptic.png') }}" alt="Logo INPTIC" style="height: 55px; width: auto;" />
                </td>
                <td class="inptic-header">
                    <div class="main-title">INSTITUT NATIONAL DE LA POSTE, DES TECHNOLOGIES<br>DE L'INFORMATION ET DE LA COMMUNICATION</div>
                    <div class="sub-title">DIRECTION DES ETUDES ET DE LA PEDAGOGIE</div>
                </td>
                <td class="republic">
                    <div class="head">REPUBLIQUE GABONAISE</div>
                    <div class="motto">Union - Travail - Justice</div>
                </td>
            </tr>
        </table>

        <!-- TITRE DU BULLETIN -->
        <div class="title-box">
            <div class="bulletin-title">{{ $bulletin_titre ?? 'Bulletin de Notes' }}</div>
            <div class="annee-academique">Annee Academique : {{ $anneeLibelle }}</div>
        </div>

        <!-- CLASSE -->
        <div class="classe-box">Classe : {{ $classeLibelle }}</div>

        <!-- IDENTITE DE L'ETUDIANT -->
        <table class="identity">
            <tr>
                <td class="label">Nom et Prenom(s)</td>
                <td>{{ $etudiantNom }} {{ $etudiantPrenom }}</td>
            </tr>
            <tr>
                <td class="label">Date et lieu de naissance</td>
                <td>Ne(e) le {{ $dateNaissance }} a {{ $lieuNaissance }}</td>
            </tr>
        </table>

        <!-- TABLEAU DES NOTES -->
        <table class="grades-table">
            <thead>
                <tr>
                    <th class="matiere-name"></th>
                    <th style="width: 12%;">Credits</th>
                    <th style="width: 12%;">Coefficients</th>
                    <th style="width: 18%;">Notes de l'etudiant</th>
                    <th style="width: 18%;">Moyenne de classe</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($ues) && count($ues) > 0)
                    @foreach($ues as $ue)
                        <tr class="ue-header">
                            <td colspan="5">{{ $ue->code ? $ue->code.' ' : '' }}{{ $ue->nom ?? '' }}</td>
                        </tr>

                        @if(isset($ue->matieres))
                            @foreach($ue->matieres as $matiere)
                                <tr>
                                    <td class="matiere-name">{{ $matiere->nom ?? '' }}</td>
                                    <td>{{ $matiere->credit ?? '' }}</td>
                                    <td>{{ $matiere->coef !== null ? number_format($matiere->coef, 2, ',', ' ') : '' }}</td>
                                    <td>{{ $matiere->note !== null ? number_format($matiere->note, 2, ',', ' ') : '' }}</td>
                                    <td>{{ $matiere->moy_classe !== null ? number_format($matiere->moy_classe, 2, ',', ' ') : '' }}</td>
                                </tr>
                            @endforeach
                        @endif

                        <tr class="ue-total">
                            <td class="matiere-name">Moyenne {{ $ue->code ?? '' }}</td>
                            <td>{{ number_format($ue->credits_total ?? 0, 0) }}</td>
                            <td>{{ number_format($ue->coefficients_total ?? 0, 2, ',', ' ') }}</td>
                            <td class="note-eleve">{{ $ue->moyenne !== null ? number_format($ue->moyenne, 2, ',', ' ') : '' }}</td>
                            <td>{{ $ue->moy_classe !== null ? number_format($ue->moy_classe, 2, ',', ' ') : '' }}</td>
                        </tr>
                    @endforeach

                    <tr class="absence-row">
                        <td class="matiere-name" colspan="3">Heures d'absence</td>
                        <td>{{ ($heures_absence_total ?? 0) > 0 ? $heures_absence_total : '' }}</td>
                        <td></td>
                    </tr>
                @elseif(isset($lignes) && count($lignes) > 0)
                    @foreach($lignes as $ligne)
                        <tr>
                            <td class="matiere-name">{{ $ligne['matiere'] }}</td>
                            <td>{{ $ligne['credits'] ?: '' }}</td>
                            <td>{{ $ligne['coefficient'] }}</td>
                            <td>{{ $ligne['moyenne'] }}{{ $ligne['rattrapage'] ? ' (R)' : '' }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="height: 100px; text-align: center;">Aucune matiere enregistree pour ce semestre.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- RESUME : MOYENNE, RANG, MENTION -->
        <table class="summary-table">
            <tr>
                <td class="label">Moyenne Generale</td>
                <td class="value moyenne-generale">{{ $moyenneGenerale }}</td>
                <td class="value">{{ $moyenneGeneraleClasseFmt }}</td>
            </tr>
            <tr>
                <td class="label">Rang de l'etudiant au Semestre</td>
                <td class="value" colspan="2">{{ $rangDisplay }}</td>
            </tr>
            <tr>
                <td class="label">Mention</td>
                <td class="value" colspan="2">{{ $mentionDisplay }}</td>
            </tr>
        </table>

        @if(isset($ues) && count($ues) > 0)
        <!-- VALIDATION DES CREDITS -->
        <div class="credits-caption">Etat de la Validation des Credits au {{ $semestre_libelle ?? 'Semestre' }}</div>
        <table class="credits-table">
            <thead>
                <tr>
                    @foreach($ues as $ue)
                        <th>{{ $ue->code ?? 'UE' }}</th>
                    @endforeach
                    <th>Credits Acquis au {{ $semestre_libelle ?? 'Semestre' }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach($ues as $ue)
                        <td>{{ $ue->credits_acquis ?? 0 }} Credits / {{ $ue->credits_total ?? 0 }}</td>
                    @endforeach
                    <td>{{ $credits_acquis ?? 0 }} Credits / {{ $credits_total ?? 0 }}</td>
                </tr>
                <tr>
                    @foreach($ues as $ue)
                        <td>{{ ($ue->credits_acquis ?? 0) >= ($ue->credits_total ?? 0) ? 'UE Acquise' : 'UE Non Acquise' }}</td>
                    @endforeach
                    <td style="font-weight: bold;">{{ ($credits_acquis ?? 0) >= ($credits_total ?? 0) ? 'Semestre Acquis' : 'Semestre Non Acquis' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- DECISION DU JURY -->
        <table class="decision-table">
            <tr>
                <td>Decision du Jury : {{ $decisionJury }}</td>
            </tr>
        </table>
        @endif

        <!-- PIED DE PAGE ET SIGNATURES -->
        <div class="footer-section">
            <div class="date-fait">Fait a Libreville le {{ $dateFait }}</div>
            <table class="signatures">
                <tr>
                    <td></td>
                    <td>Le Directeur des Etudes et de la Pedagogie</td>
                </tr>
            </table>
        </div>

        <div class="legal-notice">
            Il ne sera delivre qu'un seul et unique exemplaire de bulletins de notes. L'etudiant est donc prie d'en faire plusieurs copies legalisees.
        </div>
    </div>
</body>
</html>

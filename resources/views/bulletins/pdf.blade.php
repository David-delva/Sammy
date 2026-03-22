<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin - {{ $eleve->nom }} {{ $eleve->prenom }}</title>
    <style>
        @page {
            margin: 10mm 9mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
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
            vertical-align: top;
        }

        .ministry {
            width: 72%;
            text-align: center;
            line-height: 1.28;
        }

        .ministry .line {
            text-transform: uppercase;
        }

        .ministry .line-strong {
            font-weight: bold;
            text-transform: uppercase;
        }

        .ministry .meta {
            margin-top: 3px;
        }

        .republic {
            width: 28%;
            text-align: right;
        }

        .republic-box {
            display: inline-block;
            width: 118px;
            border: 1px solid #000000;
            text-align: center;
            font-size: 8px;
            line-height: 1.2;
        }

        .republic-box .head {
            background: #1d7f3f;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            padding: 2px 4px;
        }

        .republic-box .subhead {
            background: #f4dd42;
            font-weight: bold;
            padding: 2px 4px;
        }

        .school-year {
            margin-top: 18px;
            text-align: right;
            font-size: 7px;
        }

        .title-box {
            width: 64%;
            margin: 10px auto 10px;
            border: 2px solid #4b4b4b;
            background: #e7e7e7;
            text-align: center;
            padding: 5px 6px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .identity {
            margin-bottom: 8px;
        }

        .identity p {
            margin: 4px 0;
        }

        .fill {
            display: inline-block;
            min-height: 10px;
            border-bottom: 1px solid #000000;
            vertical-align: bottom;
            padding: 0 2px;
            line-height: 1.1;
        }

        .w-name { width: 155px; }
        .w-prenom { width: 120px; }
        .w-date { width: 72px; }
        .w-place { width: 100px; }
        .w-class { width: 75px; }

        .notes th,
        .notes td,
        .summary td,
        .decision td {
            border: 1px solid #000000;
        }

        .notes th {
            background: #efefef;
            font-size: 7px;
            font-weight: bold;
            text-align: center;
            padding: 2px 3px;
        }

        .notes td {
            height: 15px;
            padding: 1px 3px;
            font-size: 7.4px;
            text-align: center;
        }

        .notes .subject {
            text-align: left;
            width: 31%;
        }

        .notes .w-dev { width: 10%; }
        .notes .w-comp { width: 10%; }
        .notes .w-moy { width: 10%; }
        .notes .w-coef { width: 8%; }
        .notes .w-points { width: 12%; }
        .notes .w-app { width: 19%; }

        .total-row td {
            font-weight: bold;
        }

        .summary {
            margin-top: 0;
        }

        .summary td {
            padding: 2px 4px;
            font-size: 7.5px;
        }

        .summary .label {
            font-weight: bold;
        }

        .summary .value {
            text-align: center;
        }

        .decision {
            margin-top: -1px;
        }

        .decision td {
            vertical-align: top;
        }

        .decision .head {
            font-weight: bold;
            padding: 3px 4px;
        }

        .decision .body {
            height: 44px;
            padding: 4px;
            line-height: 1.35;
        }

        .footer {
            margin-top: 16px;
            text-align: center;
            font-size: 5.8px;
        }
    </style>
</head>
<body>
    @php
        $rows = $lignes;
        $targetRows = 14;

        while (count($rows) < $targetRows) {
            $rows[] = [
                'matiere' => '',
                'moyenne_devoirs' => '',
                'note_composition' => '',
                'moyenne' => '',
                'coefficient' => '',
                'moy_x_coef' => '',
                'appreciation' => '',
            ];
        }

        $nom = $eleve->nom ?? '';
        $prenoms = $eleve->prenom ?? '';
        $dateNaissance = $eleve->date_naissance ? $eleve->date_naissance->format('d/m/Y') : '';
        $classeLibelle = $classe->nom_classe ?? '';
        $moyenneDisplay = $moyenne_generale !== null ? number_format($moyenne_generale, 2, ',', ' ') : '';
        $moyenneS1Display = $moyenne_semestre_1 !== null ? number_format($moyenne_semestre_1, 2, ',', ' ') : '';
        $moyenneS2Display = $moyenne_semestre_2 !== null ? number_format($moyenne_semestre_2, 2, ',', ' ') : '';
        $moyenneAnnuelleDisplay = $moyenne_annuelle !== null ? number_format($moyenne_annuelle, 2, ',', ' ') : '';
        $rangDisplay = $rang ? $rang . ' / ' . $total_eleves : '';
        $appreciationConseil = $mention ? 'Mention : ' . $mention : '';
    @endphp

    <div class="page">
        <table class="top">
            <tr>
                <td class="ministry">
                    <div class="line">MINISTERE DE LA FORMATION PROFESSIONNELLE</div>
                    <div class="line" style="height: 10px;"></div>
                    <div class="line">ECOLE TECHNIQUE ET PROFESSIONNELLE DES METIERS</div>
                    <div class="line-strong">E.T.P.M / KOULAMOUTOU</div>
                    <div class="meta">TEL 066.06.89.00 / 077.49.45.46</div>
                    <div class="meta">B.P 30 - KOULAMOUTOU</div>
                </td>
                <td class="republic">
                    <div class="republic-box">
                        <div class="head">REPUBLIQUE GABONAISE</div>
                        <div class="subhead">Union - Travail - Justice</div>
                    </div>
                    <div class="school-year">Annee scolaire : {{ $annee->libelle }}</div>
                </td>
            </tr>
        </table>

        <div class="title-box">{{ $bulletin_titre }}</div>

        <div class="identity">
            <p>
                Nom : <span class="fill w-name">{{ $nom }}</span>
                prenoms : <span class="fill w-prenom">{{ $prenoms }}</span>
            </p>
            <p>
                N&eacute;(e) le : <span class="fill w-date">{{ $dateNaissance }}</span>
                &agrave; <span class="fill w-place">{{ $eleve->lieu_naissance ?? 'Non renseigne' }}</span>
                classe : <span class="fill w-class">{{ $classeLibelle }}</span>
            </p>
        </div>

        <table class="notes">
            <thead>
                <tr>
                    <th class="subject">Matieres</th>
                    <th class="w-dev">Dev</th>
                    <th class="w-comp">comp</th>
                    <th class="w-moy">Moy/20</th>
                    <th class="w-coef">coef</th>
                    <th class="w-points">Moy*coef</th>
                    <th class="w-app">Applications</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        <td class="subject">{{ $row['matiere'] }}</td>
                        <td>{{ $row['moyenne_devoirs'] }}</td>
                        <td>{{ $row['note_composition'] }}</td>
                        <td>{{ $row['moyenne'] }}</td>
                        <td>{{ $row['coefficient'] }}</td>
                        <td>{{ $row['moy_x_coef'] }}</td>
                        <td>{{ $row['appreciation'] }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5" style="text-align: center;">Total</td>
                    <td>{{ $total_points_formatted }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <table class="summary">
            <tr>
                <td class="label" style="width: 34%;">Moyenne</td>
                <td class="value" style="width: 18%;">{{ $moyenneDisplay }}</td>
                <td class="label" style="width: 18%; text-align: center;">rang</td>
                <td class="value" style="width: 30%;">{{ $rangDisplay }}</td>
            </tr>
            <tr>
                <td class="label" colspan="3">Moyenne du 1&deg; semestre</td>
                <td class="value">{{ $moyenneS1Display }}</td>
            </tr>
            <tr>
                <td class="label" colspan="3">moyenne du 2&deg; semestre</td>
                <td class="value">{{ $moyenneS2Display }}</td>
            </tr>
            <tr>
                <td class="label" colspan="3">Moyenne annuelle</td>
                <td class="value">{{ $moyenneAnnuelleDisplay }}</td>
            </tr>
        </table>

        <table class="decision">
            <tr>
                <td style="width: 53%;">
                    <div class="head">Appreciations du conseil de classe :</div>
                    <div class="body">{{ $appreciationConseil }}</div>
                </td>
                <td>
                    <div class="head">Cachet et signature du Directeur</div>
                    <div class="body"></div>
                </td>
            </tr>
        </table>

        <div class="footer">
            Reconnu sous le N&deg;555/METRFP/SG/DGFP/SI du ministere de la Formation Professionnelle
        </div>
    </div>
</body>
</html>
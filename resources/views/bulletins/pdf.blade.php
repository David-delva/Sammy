<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin - {{ $eleve->nom }} {{ $eleve->prenom }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #007bff; text-transform: uppercase; font-size: 20px; }
        .header p { margin: 5px 0; font-weight: bold; }

        .info-section { width: 100%; margin-bottom: 20px; }
        .info-section td { vertical-align: top; width: 50%; }
        .student-info, .academic-info { border: 1px solid #ddd; padding: 10px; border-radius: 5px; background: #f9f9f9; }
        .student-info p, .academic-info p { margin: 4px 0; }
        .label { font-weight: bold; color: #555; }

        table.results { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        table.results th, table.results td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        table.results th { background-color: #007bff; color: white; text-transform: uppercase; font-size: 11px; }
        table.results tr:nth-child(even) { background-color: #f2f2f2; }
        .text-left { text-align: left !important; }

        .summary-box { width: 100%; margin-bottom: 30px; }
        .summary-box td { vertical-align: top; }
        .summary-table { width: 300px; border: 1px solid #007bff; }
        .summary-table td { padding: 8px; border-bottom: 1px solid #ddd; }
        .summary-table tr:last-child td { border-bottom: none; }
        .highlight { font-weight: bold; color: #007bff; font-size: 14px; }

        .signature { margin-top: 50px; width: 100%; }
        .signature td { width: 50%; text-align: center; font-weight: bold; }
        .signature .line { margin-top: 60px; border-top: 1px solid #333; width: 200px; display: inline-block; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Bulletin de Notes</h1>
        <p>Année Académique : {{ $annee->libelle }}</p>
    </div>

    <table class="info-section">
        <tr>
            <td>
                <div class="student-info">
                    <p><span class="label">Élève :</span> {{ $eleve->nom }} {{ $eleve->prenom }}</p>
                    <p><span class="label">Matricule :</span> {{ $eleve->matricule }}</p>
                    <p><span class="label">Sexe :</span> {{ $eleve->sexe }}</p>
                </div>
            </td>
            <td>
                <div class="academic-info" style="margin-left: 10px;">
                    <p><span class="label">Classe :</span> {{ $classe->nom_classe }}</p>
                    <p><span class="label">Date :</span> {{ date('d/m/Y') }}</p>
                </div>
            </td>
        </tr>
    </table>

    <table class="results">
        <thead>
            <tr>
                <th class="text-left">Matières</th>
                <th>Coeff.</th>
                <th>Moyenne / 20</th>
                <th>Moy. x Coeff.</th>
                <th>Appréciation</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPoints = 0; $totalCoeffs = 0; @endphp
            @foreach($results as $res)
                @php
                    $moy = $res['moyenne'];
                    $coeff = $res['matiere']->coefficient;
                    $points = $moy ? $moy * $coeff : null;
                    if ($moy !== null) {
                        $totalPoints += $points;
                        $totalCoeffs += $coeff;
                    }
                @endphp
                <tr>
                    <td class="text-left">{{ $res['matiere']->nom_matiere }}</td>
                    <td>{{ $coeff }}</td>
                    <td>{{ $moy ?? '—' }}</td>
                    <td>{{ $points ?? '—' }}</td>
                    <td>
                        @if($moy >= 16) Très Bien
                        @elseif($moy >= 14) Bien
                        @elseif($moy >= 12) Assez Bien
                        @elseif($moy >= 10) Passable
                        @else Médiocre @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-box">
        <tr>
            <td style="width: 60%;">
                <table class="summary-table">
                    <tr>
                        <td class="label">Total des points</td>
                        <td class="highlight">{{ round($totalPoints, 2) }} / {{ $totalCoeffs * 20 }}</td>
                    </tr>
                    <tr>
                        <td class="label">Moyenne Générale</td>
                        <td class="highlight" style="font-size: 18px;">{{ $moyenneGenerale ?? '—' }} / 20</td>
                    </tr>
                    <tr>
                        <td class="label">Rang de l'élève</td>
                        <td>
                            @if($rang)
                                <strong>{{ $rang }}</strong> / {{ $totalEleves }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Effectif de la classe</td>
                        <td>{{ $totalEleves }} élève(s)</td>
                    </tr>
                </table>
            </td>
            <td>
                @if($moyenneGenerale)
                <div style="border: 1px solid #ddd; padding: 15px; text-align: center; border-radius: 5px;">
                    <p style="margin: 0; font-weight: bold; color: #555;">OBSERVATIONS</p>
                    <p style="font-size: 16px; margin-top: 10px; color: #007bff;">
                        {{ $moyenneGenerale >= 10 ? 'Admis(e) en classe supérieure' : 'Résultat Insuffisant' }}
                    </p>
                </div>
                @endif
            </td>
        </tr>
    </table>

    <table class="signature">
        <tr>
            <td>
                <p>Signature des Parents</p>
                <div class="line"></div>
            </td>
            <td>
                <p>Le Chef d'Établissement</p>
                <div class="line"></div>
            </td>
        </tr>
    </table>

</body>
</html>

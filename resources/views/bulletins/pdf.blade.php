<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin - {{ $eleve->nom }} {{ $eleve->prenom }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 22px;
            line-height: 1.45;
        }

        .sheet {
            border: 1px solid #dbe4f0;
            padding: 18px;
        }

        .header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            border-bottom: 2px solid #1a56db;
            padding-bottom: 10px;
        }

        .header td {
            vertical-align: top;
        }

        .brand {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1a56db;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .title {
            font-size: 22px;
            color: #0f2d56;
            font-weight: bold;
            margin: 0 0 4px;
        }

        .subtitle {
            font-size: 11px;
            color: #64748b;
            margin: 0;
        }

        .stamp {
            text-align: right;
        }

        .stamp-box {
            display: inline-block;
            min-width: 145px;
            text-align: center;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            padding: 10px 12px;
        }

        .stamp-box .label {
            display: block;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 1px;
            margin-bottom: 5px;
            color: #1e3a8a;
        }

        .stamp-box .value {
            font-size: 16px;
            font-weight: bold;
        }

        .meta {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 18px;
        }

        .meta td {
            width: 50%;
            vertical-align: top;
            padding-right: 8px;
        }

        .panel {
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            padding: 12px 14px;
            min-height: 96px;
        }

        .panel-title {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin: 0 0 8px;
            font-weight: bold;
        }

        .panel p {
            margin: 4px 0;
        }

        .label {
            font-weight: bold;
            color: #475569;
        }

        table.results {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        table.results th {
            background: #0f2d56;
            color: #ffffff;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.7px;
            padding: 8px 7px;
            border: 1px solid #0a1f3d;
        }

        table.results td {
            border: 1px solid #dbe4f0;
            padding: 8px 7px;
            text-align: center;
            font-size: 10px;
        }

        table.results tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-left {
            text-align: left !important;
        }

        .muted {
            color: #94a3b8;
        }

        .summary {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 22px;
        }

        .summary td {
            vertical-align: top;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #bfdbfe;
            background: #eff6ff;
        }

        .summary-table td {
            padding: 9px 10px;
            border-bottom: 1px solid #dbeafe;
            font-size: 10px;
        }

        .summary-table tr:last-child td {
            border-bottom: none;
        }

        .summary-table .value {
            text-align: right;
            font-weight: bold;
            color: #0f2d56;
        }

        .decision-box {
            margin-left: 14px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            padding: 14px;
            min-height: 116px;
        }

        .decision-title {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            font-weight: bold;
            margin: 0 0 8px;
        }

        .decision-result {
            font-size: 16px;
            font-weight: bold;
            color: #1a56db;
            margin: 0 0 8px;
        }

        .signature {
            width: 100%;
            border-collapse: collapse;
            margin-top: 28px;
        }

        .signature td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature .line {
            margin: 52px auto 0;
            width: 180px;
            border-top: 1px solid #334155;
            padding-top: 6px;
            font-size: 10px;
            color: #475569;
        }
    </style>
</head>
<body>
    @php
        $totalPoints = 0;
        $totalCoeffs = 0;
        $decision = null;

        if ($moyenneGenerale !== null) {
            $decision = $moyenneGenerale >= 10
                ? 'Admis(e) en classe sup&eacute;rieure'
                : 'R&eacute;sultat insuffisant';
        }
    @endphp

    <div class="sheet">
        <table class="header">
            <tr>
                <td>
                    <div class="brand">{{ config('app.name', 'Gestion scolaire') }}</div>
                    <p class="title">Bulletin de notes</p>
                    <p class="subtitle">Ann&eacute;e acad&eacute;mique : {{ $annee->libelle }}</p>
                </td>
                <td class="stamp">
                    <div class="stamp-box">
                        <span class="label">Classe</span>
                        <span class="value">{{ $classe->nom_classe }}</span>
                    </div>
                </td>
            </tr>
        </table>

        <table class="meta">
            <tr>
                <td>
                    <div class="panel">
                        <p class="panel-title">Informations de l'&eacute;l&egrave;ve</p>
                        <p><span class="label">Nom complet :</span> {{ $eleve->nom }} {{ $eleve->prenom }}</p>
                        <p><span class="label">Matricule :</span> {{ $eleve->matricule }}</p>
                        <p><span class="label">Sexe :</span> {{ $eleve->sexe }}</p>
                    </div>
                </td>
                <td>
                    <div class="panel">
                        <p class="panel-title">Contexte acad&eacute;mique</p>
                        <p><span class="label">Classe :</span> {{ $classe->nom_classe }}</p>
                        <p><span class="label">Date d'&eacute;dition :</span> {{ date('d/m/Y') }}</p>
                        <p><span class="label">Mention g&eacute;n&eacute;rale :</span> {{ $mention ?? '-' }}</p>
                    </div>
                </td>
            </tr>
        </table>

        <table class="results">
            <thead>
                <tr>
                    <th class="text-left">Mati&egrave;re</th>
                    <th>Coeff.</th>
                    <th>Moyenne / 20</th>
                    <th>Points</th>
                    <th>Appr&eacute;ciation</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $res)
                    @php
                        $moy = $res['moyenne'];
                        $coeff = $res['matiere']->pivot->coefficient;
                        $points = $moy !== null ? $moy * $coeff : null;

                        if ($moy !== null) {
                            $totalPoints += $points;
                            $totalCoeffs += $coeff;
                        }
                    @endphp
                    <tr>
                        <td class="text-left">{{ $res['matiere']->nom_matiere }}</td>
                        <td>{{ $coeff }}</td>
                        <td>{{ $moy === null ? '-' : number_format($moy, 2, ',', ' ') }}</td>
                        <td>{{ $points === null ? '-' : number_format($points, 2, ',', ' ') }}</td>
                        <td>
                            @if($moy === null)
                                <span class="muted">-</span>
                            @elseif($moy >= 16)
                                Tr&egrave;s bien
                            @elseif($moy >= 14)
                                Bien
                            @elseif($moy >= 12)
                                Assez bien
                            @elseif($moy >= 10)
                                Passable
                            @else
                                Insuffisant
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary">
            <tr>
                <td style="width: 62%;">
                    <table class="summary-table">
                        <tr>
                            <td>Total des points</td>
                            <td class="value">{{ number_format($totalPoints, 2, ',', ' ') }}</td>
                        </tr>
                        <tr>
                            <td>Total des coefficients pris en compte</td>
                            <td class="value">{{ $totalCoeffs }}</td>
                        </tr>
                        <tr>
                            <td>Moyenne g&eacute;n&eacute;rale</td>
                            <td class="value">{{ $moyenneGenerale === null ? '-' : number_format($moyenneGenerale, 2, ',', ' ') }} / 20</td>
                        </tr>
                        <tr>
                            <td>Rang de l'&eacute;l&egrave;ve</td>
                            <td class="value">{{ $rang ? $rang . ' / ' . $totalEleves : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Effectif de la classe</td>
                            <td class="value">{{ $totalEleves }}</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <div class="decision-box">
                        <p class="decision-title">D&eacute;cision du conseil</p>
                        <p class="decision-result">{!! $decision ?? '&mdash;' !!}</p>
                        <p>
                            <span class="label">Mention :</span>
                            {{ $mention ?? '-' }}
                        </p>
                        <p>
                            <span class="label">Observation :</span>
                            {{ $moyenneGenerale !== null && $moyenneGenerale >= 10 ? 'Progression satisfaisante sur l\'ann&eacute;e.' : 'Renforcement recommand&eacute; sur les mati&egrave;res &agrave; faible moyenne.' }}
                        </p>
                    </div>
                </td>
            </tr>
        </table>

        <table class="signature">
            <tr>
                <td>
                    <div class="line">Signature des parents</div>
                </td>
                <td>
                    <div class="line">Chef d'&eacute;tablissement</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
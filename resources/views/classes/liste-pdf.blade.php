<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste - {{ $classe->nom_classe }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1f2937;
            margin: 0;
            padding: 16px 18px;
        }

        .sheet {
            border: 1px solid #dbe4f0;
            padding: 14px;
        }

        .header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border-bottom: 2px solid #1a56db;
        }

        .header td {
            vertical-align: middle;
            padding-bottom: 8px;
        }

        .eyebrow {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1a56db;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            color: #0f2d56;
            margin: 0;
        }

        .subtitle {
            margin-top: 4px;
            color: #64748b;
        }

        .class-badge {
            display: inline-block;
            padding: 6px 14px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            font-weight: bold;
            font-size: 14px;
        }

        .stats {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .stats td {
            border: 1px solid #dbeafe;
            background: #f8fbff;
            padding: 7px 8px;
            width: 25%;
            vertical-align: top;
        }

        .stats .label {
            display: block;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .stats .value {
            font-size: 11px;
            font-weight: bold;
            color: #0f2d56;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .main-table th {
            background: #0f2d56;
            color: #ffffff;
            border: 1px solid #0a1f3d;
            padding: 6px 4px;
            text-align: center;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.5px;
        }

        .main-table td {
            border: 1px solid #dbe4f0;
            padding: 5px 4px;
            font-size: 9px;
            text-align: center;
            height: 22px;
        }

        .main-table tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .main-table .num {
            width: 26px;
            color: #64748b;
        }

        .main-table .name {
            text-align: left;
            min-width: 155px;
            font-weight: bold;
        }

        .main-table .matricule {
            width: 72px;
            font-size: 8px;
            color: #475569;
        }

        .main-table .small {
            width: 26px;
        }

        .main-table .date {
            width: 68px;
            font-size: 8px;
        }

        .presence-header {
            writing-mode: vertical-rl;
            padding: 4px 2px;
            min-width: 18px;
            font-size: 7px;
        }

        .presence-cell {
            width: 18px;
            background: #ffffff;
        }

        .bottom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 8px;
        }

        .bottom td {
            vertical-align: top;
        }

        .subjects {
            width: 100%;
            border-collapse: collapse;
        }

        .subjects th {
            background: #e2e8f0;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.5px;
            text-align: left;
        }

        .subjects td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            font-size: 9px;
        }

        .subjects tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .text-right {
            text-align: right;
        }

        .signature {
            margin-left: 14px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            padding: 14px;
            min-height: 124px;
            text-align: center;
        }

        .signature .title {
            font-size: 10px;
            font-weight: bold;
            color: #475569;
            margin: 0;
        }

        .signature .line {
            margin: 56px auto 0;
            width: 160px;
            border-top: 1px solid #334155;
            padding-top: 5px;
            font-size: 9px;
            color: #475569;
        }

        .footer {
            margin-top: 10px;
            text-align: right;
            font-size: 8px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="sheet">
        <table class="header">
            <tr>
                <td>
                    <div class="eyebrow">{{ config('app.name', 'Gestion scolaire') }}</div>
                    <p class="title">Feuille d'appel - Liste des &eacute;l&egrave;ves</p>
                    <p class="subtitle">Ann&eacute;e scolaire : {{ $annee->libelle }} | &Eacute;dit&eacute;e le : {{ date('d/m/Y') }}</p>
                </td>
                <td style="text-align: right; width: 180px;">
                    <span class="class-badge">{{ $classe->nom_classe }}</span>
                </td>
            </tr>
        </table>

        <table class="stats">
            <tr>
                <td>
                    <span class="label">Effectif</span>
                    <span class="value">{{ $eleves->count() }} &eacute;l&egrave;ve(s)</span>
                </td>
                <td>
                    <span class="label">R&eacute;partition</span>
                    <span class="value">{{ $eleves->where('sexe', 'M')->count() }} gar&ccedil;on(s) / {{ $eleves->where('sexe', 'F')->count() }} fille(s)</span>
                </td>
                <td>
                    <span class="label">Mati&egrave;res</span>
                    <span class="value">{{ $matieresData->count() }}</span>
                </td>
                <td>
                    <span class="label">Coefficient total</span>
                    <span class="value">{{ $matieresData->sum('coefficient') }}</span>
                </td>
            </tr>
        </table>

        <table class="main-table">
            <thead>
                <tr>
                    <th class="num">#</th>
                    <th style="min-width: 155px; text-align: left;">Nom et pr&eacute;nom</th>
                    <th style="width: 72px;">Matricule</th>
                    <th class="small">Sexe</th>
                    <th style="width: 68px;">Naissance</th>
                    @for($s = 1; $s <= 10; $s++)
                        <th class="presence-header">S{{ $s }}</th>
                    @endfor
                    <th style="width: 42px;">Abs.</th>
                    <th style="width: 70px;">Observations</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eleves as $index => $eleve)
                    <tr>
                        <td class="num">{{ $index + 1 }}</td>
                        <td class="name">{{ $eleve->nom }} {{ $eleve->prenom }}</td>
                        <td class="matricule">{{ $eleve->matricule }}</td>
                        <td class="small">{{ $eleve->sexe }}</td>
                        <td class="date">{{ $eleve->date_naissance ? $eleve->date_naissance->format('d/m/Y') : '-' }}</td>
                        @for($s = 1; $s <= 10; $s++)
                            <td class="presence-cell"></td>
                        @endfor
                        <td></td>
                        <td></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="17" style="padding: 16px; text-align: center; color: #64748b;">Aucun &eacute;l&egrave;ve inscrit dans cette classe.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table class="bottom">
            <tr>
                <td style="width: 62%; padding-right: 14px;">
                    @if($matieresData->isNotEmpty())
                        <table class="subjects">
                            <thead>
                                <tr>
                                    <th>Mati&egrave;re</th>
                                    <th class="text-right" style="width: 90px;">Coefficient</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($matieresData as $matiere)
                                    <tr>
                                        <td>{{ $matiere->nom_matiere }}</td>
                                        <td class="text-right">{{ $matiere->coefficient }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="text-right"><strong>Total</strong></td>
                                    <td class="text-right"><strong>{{ $matieresData->sum('coefficient') }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <table class="subjects">
                            <tbody>
                                <tr>
                                    <td style="text-align: center; color: #64748b;">Aucune mati&egrave;re affect&eacute;e &agrave; cette classe pour l'ann&eacute;e s&eacute;lectionn&eacute;e.</td>
                                </tr>
                            </tbody>
                        </table>
                    @endif
                </td>
                <td>
                    <div class="signature">
                        <p class="title">Direction de l'&eacute;tablissement</p>
                        <div class="line">Signature et cachet</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer">{{ config('app.name', 'Gestion scolaire') }} - {{ $classe->nom_classe }} - {{ $annee->libelle }}</div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste — {{ $classe->nom_classe }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #1a1a2e;
            padding: 16px 20px;
        }

        /* -- En-tête -- */
        .header {
            display: table;
            width: 100%;
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .header-left  { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }

        .header h1    { font-size: 15px; color: #0d6efd; }
        .header .doc  { font-size: 12px; font-weight: bold; margin-top: 3px; }
        .header .meta { font-size: 10px; color: #555; margin-top: 2px; }

        .badge-classe {
            display: inline-block;
            background: #0d6efd;
            color: white;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        /* -- Stats -- */
        .stats {
            background: #f0f6ff;
            border: 1px solid #cce0ff;
            border-radius: 4px;
            padding: 7px 14px;
            margin-bottom: 14px;
            font-size: 10px;
        }
        .stats span { margin-right: 24px; }
        .stats strong { color: #0d6efd; }

        /* -- Table feuille d'appel -- */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .main-table th {
            background: #0d6efd;
            color: white;
            padding: 6px 6px;
            text-align: center;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #0b5ed7;
        }
        .main-table th.left { text-align: left; }
        .main-table td {
            border: 1px solid #cce0ff;
            padding: 5px 6px;
            text-align: center;
            font-size: 10px;
            height: 22px;
        }
        .main-table td.num  { color: #888; font-size: 9px; width: 28px; }
        .main-table td.name { text-align: left; font-weight: 600; min-width: 140px; }
        .main-table td.mat  { color: #999; min-width: 30px; }
        .main-table tr:nth-child(even) td { background: #f7faff; }

        .presence-header {
            writing-mode: vertical-rl;
            font-size: 8px;
            padding: 4px 2px;
            min-width: 18px;
        }
        .presence-cell {
            width: 18px;
            border: 1px solid #aac8f0;
            background: white;
        }

        /* -- Table des matières / coefficients -- */
        .matieres-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .matieres-table th {
            background: #6c757d;
            color: white;
            padding: 5px 8px;
            font-size: 9px;
            text-transform: uppercase;
        }
        .matieres-table td {
            border: 1px solid #dee2e6;
            padding: 4px 8px;
            font-size: 10px;
        }
        .matieres-table tr:nth-child(even) td { background: #f8f9fa; }

        /* -- Pied de page -- */
        .footer {
            position: fixed;
            bottom: 10px;
            right: 20px;
            font-size: 9px;
            color: #aaa;
        }

        .signature-block {
            margin-top: 16px;
            text-align: right;
            padding-right: 20px;
        }
        .signature-block p { font-size: 10px; color: #444; }
        .signature-line {
            margin-top: 35px;
            border-top: 1px solid #333;
            width: 160px;
            display: inline-block;
            text-align: center;
            font-size: 9px;
            color: #333;
            padding-top: 3px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-left">
            <h1>SYSTÈME DE GESTION SCOLAIRE</h1>
            <div class="doc">FEUILLE D'APPEL — LISTE DES ÉLÈVES</div>
            <div class="meta">
                Année scolaire : {{ $annee->libelle }}
                &nbsp;|&nbsp;
                Éditée le : {{ date('d/m/Y') }}
            </div>
        </div>
        <div class="header-right">
            <span class="badge-classe">{{ $classe->nom_classe }}</span>
        </div>
    </div>

    <div class="stats">
        <span>Effectif : <strong>{{ $eleves->count() }} élève(s)</strong></span>
        <span>
            Dont :
            <strong>{{ $eleves->where('sexe', 'M')->count() }} garçon(s)</strong>
            /
            <strong>{{ $eleves->where('sexe', 'F')->count() }} fille(s)</strong>
        </span>
        <span>Matières : <strong>{{ $matieres->count() }}</strong></span>
        <span>
            Coef. total :
            <strong>{{ $matieres->sum('coefficient') }}</strong>
        </span>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th class="num">#</th>
                <th class="left" style="min-width:140px">Nom et Prénom</th>
                <th style="width:60px">Matricule</th>
                <th style="width:25px">Sexe</th>
                <th style="width:65px">Naissance</th>
                @for($s = 1; $s <= 10; $s++)
                    <th class="presence-header">S{{ $s }}</th>
                @endfor
                <th style="width:40px; font-size:9px">Abs.</th>
                <th style="width:60px; font-size:9px">Observations</th>
            </tr>
        </thead>
        <tbody>
            @forelse($eleves as $index => $eleve)
                <tr>
                    <td class="num">{{ $index + 1 }}</td>
                    <td class="name">
                        {{ $eleve->nom }} {{ $eleve->prenom }}
                    </td>
                    <td style="font-size:8px; color:#666; text-align:center">
                        {{ $eleve->matricule }}
                    </td>
                    <td style="text-align:center">
                        {{ $eleve->sexe }}
                    </td>
                    <td style="text-align:center; font-size:9px">
                        {{ $eleve->date_naissance ? $eleve->date_naissance->format('d/m/Y') : '—' }}
                    </td>
                    @for($s = 1; $s <= 10; $s++)
                        <td class="presence-cell"></td>
                    @endfor
                    <td></td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="17" style="text-align:center; color:#999; padding:20px">
                        Aucun élève inscrit dans cette classe.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($matieres->isNotEmpty())
        <div style="display:table; width:100%">
            <div style="display:table-cell; width:60%; vertical-align:top; padding-right:20px">
                <p style="font-weight:bold; margin-bottom:6px; font-size:10px; color:#0d6efd">
                    <i>Matières et coefficients</i>
                </p>
                <table class="matieres-table">
                    <thead>
                        <tr>
                            <th class="left">Matière</th>
                            <th style="width:80px; text-align:center">Coefficient</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($matieres as $matiere)
                            <tr>
                                <td>{{ $matiere->nom_matiere }}</td>
                                <td style="text-align:center; font-weight:bold">
                                    {{ $matiere->coefficient }}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td style="font-weight:bold; text-align:right">Total</td>
                            <td style="text-align:center; font-weight:bold; color:#0d6efd">
                                {{ $matieres->sum('coefficient') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="display:table-cell; width:40%; vertical-align:bottom; text-align:right">
                <div class="signature-block">
                    <p>Le Directeur de l'Établissement</p>
                    <div class="signature-line">Signature et cachet</div>
                </div>
            </div>
        </div>
    @endif

    <div class="footer">
        {{ config('app.name') }} — {{ $classe->nom_classe }} — {{ $annee->libelle }}
    </div>

</body>
</html>

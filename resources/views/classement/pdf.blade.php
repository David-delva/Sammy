<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Classement - {{ $classe->nom_classe }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: center; }
        th { background-color: #f0f0f0; text-transform: uppercase; font-size: 10px; }
        .text-left { text-align: left; }
        
        .rang-1 { font-weight: bold; background-color: #fff9c4; }
        .mention-excellent { color: #2e7d32; font-weight: bold; }
        .mention-insuffisant { color: #c62828; }

        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #777; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Tableau d'Honneur & Classement</h1>
        <p>Classe : {{ $classe->nom_classe }} | Année : {{ $annee->libelle }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">Rang</th>
                <th style="width: 100px;">Matricule</th>
                <th class="text-left">Nom & Prénom</th>
                <th style="width: 80px;">Moyenne</th>
                <th style="width: 100px;">Mention</th>
            </tr>
        </thead>
        <tbody>
            @foreach($classement as $item)
                <tr class="{{ $item['rang'] == 1 ? 'rang-1' : '' }}">
                    <td>{{ $item['rang'] }}</td>
                    <td>{{ $item['eleve']->matricule }}</td>
                    <td class="text-left">{{ $item['eleve']->nom }} {{ $item['eleve']->prenom }}</td>
                    <td><strong>{{ number_format($item['moyenne'], 2) }}</strong></td>
                    <td class="{{ $item['moyenne'] < 10 ? 'mention-insuffisant' : ($item['moyenne'] >= 16 ? 'mention-excellent' : '') }}">
                        {{ $item['mention'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Document généré le {{ date('d/m/Y H:i') }} — Page 1/1
    </div>

</body>
</html>

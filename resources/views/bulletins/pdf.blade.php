<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin Scolaire</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #0d6efd; padding-bottom: 8px; }
        .header h1 { margin: 5px 0; font-size: 18px; color:#0d6efd }
        .info-eleve { margin-bottom: 12px; }
        .info-eleve table { width: 100%; border-collapse: collapse; }
        .info-eleve td { padding: 6px; border: 1px solid #e6eefc; }
        .info-eleve td:first-child { font-weight: 600; width: 30%; background-color: #fbfdff; }
        .notes-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .notes-table th { background-color: #f8fafc; color: #0f172a; padding: 8px; text-align: center; }
        .notes-table td { border: 1px solid #e6eefc; padding: 8px; text-align: center; }
        .notes-table tr:nth-child(even) { background-color: #fbfdff; }
        .footer { margin-top: 12px; }
        .footer table { width: 100%; border-collapse: collapse; }
        .footer td { padding: 8px; border: 1px solid #e6eefc; font-weight: 600; }
        .mention { text-align: center; font-size: 14px; margin: 12px 0; padding: 8px; border: 1px solid #e6eefc; }
        .signature { margin-top: 20px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ÉCOLE TECHNIQUE ET PROFESSIONNELLE</h1>
        <p>BULLETIN SCOLAIRE</p>
        <p>Année Scolaire : {{ date('Y') }}-{{ date('Y') + 1 }}</p>
    </div>

    <div class="info-eleve">
        <table>
            <tr>
                <td>Matricule</td>
                <td>{{ $eleve->matricule }}</td>
            </tr>
            <tr>
                <td>Nom et Prénom</td>
                <td>{{ $eleve->nom }} {{ $eleve->prenom }}</td>
            </tr>
            <tr>
                <td>Classe</td>
                <td>{{ optional($eleve->classe)->nom_classe ?? '—' }}</td>
            </tr>
            <tr>
                <td>Date de Naissance</td>
                <td>{{ optional($eleve->date_naissance)->format('d/m/Y') ?? '' }}</td>
            </tr>
        </table>
    </div>

    <table class="notes-table">
        <thead>
            <tr>
                <th>Matière</th>
                <th>Coefficient</th>
                <th>Devoirs</th>
                <th>Composition</th>
                <th>Moyenne</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resultats as $resultat)
                <tr>
                    <td style="text-align: left;">{{ $resultat['matiere'] }}</td>
                    <td>{{ $resultat['coefficient'] }}</td>
                    <td>{{ $resultat['moyenne_devoirs'] }}</td>
                    <td>{{ $resultat['note_composition'] }}</td>
                    <td><strong>{{ $resultat['moyenne'] }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <table>
            <tr>
                <td>Moyenne Générale</td>
                <td style="text-align: center; font-size: 14px;">{{ $moyenne_generale }} / 20</td>
            </tr>
            <tr>
                <td>Rang</td>
                <td style="text-align: center;">{{ $rang }} / {{ $total_eleves }}</td>
            </tr>
        </table>
    </div>

    <div class="mention">
        <strong>Mention : {{ $mention }}</strong>
    </div>

    <div class="signature">
        <p>Le Directeur</p>
        <br><br>
        <p>_____________________</p>
    </div>
</body>
</html>

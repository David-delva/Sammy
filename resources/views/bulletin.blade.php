<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin de Notes - {{ $eleve->nom }} {{ $eleve->prenom }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 13px; color: #222; }
        .header { text-align: center; margin-bottom: 18px; padding-bottom: 8px; }
        .school-info h1 { font-size: 20px; color: #0d6efd; margin: 0; }
        .school-info p { margin: 3px 0; color: #4b5563 }
        .student-info { margin-bottom: 18px; padding: 10px; border: 1px solid #e6eefc; background-color: #fbfdff; border-radius: 6px; }
        .student-info table { width: 100%; }
        .student-info td { padding: 6px; }
        .grades-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .grades-table th, .grades-table td { border: 1px solid #e6eefc; padding: 8px; text-align: center; }
        .grades-table th { background-color: #f8fafc; color: #0f172a; }
        .grades-table .matiere-cell { text-align: left; font-weight: 600; }
        .summary { margin-top: 12px; display: flex; justify-content: flex-end; }
        .summary table { width: 45%; border-collapse: collapse; }
        .summary th, .summary td { padding: 8px; border: 1px solid #e6eefc; text-align: right; }
        .summary th { background-color: #f8fafc; width: 60%; }
        .footer { margin-top: 24px; clear: both; padding-top: 10px; }
        .signature { float: right; width: 200px; text-align: center; border-top: 1px solid #333; padding-top: 8px; margin-top: 18px; }
    </style>
</head>
<body>

    <div class="header">
        <div class="school-info">
            <h1>École Technique et Professionnelle</h1>
            <p><strong>Année Scolaire :</strong> {{ $annee_scolaire }}</p>
            <p><strong>Date d'édition :</strong> {{ $date }}</p>
        </div>
    </div>

    <div class="student-info">
        <table>
            <tr>
                <td><strong>Matricule :</strong> {{ $eleve->matricule }}</td>
                <td><strong>Classe :</strong> {{ optional($eleve->classe)->nom_classe ?? '—' }}</td>
            </tr>
            <tr>
                <td><strong>Nom :</strong> {{ $eleve->nom }}</td>
                <td><strong>Prénom :</strong> {{ $eleve->prenom }}</td>
            </tr>
            <tr>
                <td><strong>Date de Naissance :</strong> {{ optional($eleve->date_naissance)->format('d/m/Y') ?? '' }}</td>
                <td><strong>Sexe :</strong> {{ $eleve->sexe == 'M' ? 'Masculin' : 'Féminin' }}</td>
            </tr>
        </table>
    </div>

    <table class="grades-table">
        <thead>
            <tr>
                <th>Matière</th>
                <th>Coef.</th>
                <th>Moy. Devoirs</th>
                <th>Composition</th>
                <th>Moyenne</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lignesBulletin as $ligne)
            <tr>
                <td class="matiere-cell">{{ $ligne['nom_matiere'] }}</td>
                <td>{{ $ligne['coefficient'] }}</td>
                <td>{{ $ligne['moyenne_devoirs'] }}</td>
                <td>{{ $ligne['note_composition'] }}</td>
                <td><strong>{{ $ligne['moyenne_finale'] }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <th>Moyenne Générale :</th>
                <td><strong>{{ $moyenneGenerale }} / 20</strong></td>
            </tr>
            <tr>
                <th>Rang :</th>
                <td><strong>{{ $rang }}</strong></td>
            </tr>
            <tr>
                <th>Appréciation / Mention :</th>
                <td><strong>{{ $mention }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <div class="signature">
            Signature de la Direction
        </div>
    </div>

</body>
</html>

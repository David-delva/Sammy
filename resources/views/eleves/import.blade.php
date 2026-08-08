@extends('layouts.app')

@section('title', 'Importer des eleves')
@section('breadcrumb', 'Scolarite / Eleves / Importation')

@section('content')
<div class="space-y-6">

    {{-- HERO --}}
    <section style="background:linear-gradient(135deg,#000000 0%,#009e60 60%,#006400 100%);border-radius:32px;padding:2rem;position:relative;overflow:hidden;box-shadow:0 28px 80px rgba(0,0,0,0.28);">
        <div style="position:absolute;top:-60px;right:-60px;width:220px;height:220px;border-radius:50%;background:rgba(247,209,23,0.10);pointer-events:none;"></div>
        <div>
            <p style="font-size:11px;font-weight:700;letter-spacing:0.3em;text-transform:uppercase;color:#f7d117;">Importation</p>
            <h2 style="margin-top:10px;font-size:1.7rem;font-weight:700;color:#fff;line-height:1.2;">Importer des eleves depuis un fichier Excel ou CSV</h2>
            <p style="margin-top:8px;font-size:14px;color:rgba(255,255,255,0.72);">Chargez un fichier .xlsx, .xls ou .csv. Les eleves seront inscrits dans la classe choisie pour l'annee academique en cours.</p>
        </div>
    </section>

    {{-- FORMAT ATTENDU --}}
    <section style="background:#fff;border-radius:24px;border:1px solid rgba(0,158,96,0.18);padding:1.5rem;box-shadow:0 8px 24px rgba(0,0,0,0.06);">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
            <div style="width:36px;height:36px;border-radius:12px;background:rgba(247,209,23,0.18);display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-table" style="color:#92400e;font-size:1rem;"></i>
            </div>
            <div>
                <p style="font-size:14px;font-weight:700;color:#0f172a;">Format du fichier</p>
                <p style="font-size:12px;color:#64748b;">La premiere ligne doit etre l'en-tete (elle sera ignoree).</p>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:12px;">
                <thead>
                    <tr style="background:rgba(0,158,96,0.07);">
                        <th style="padding:8px 12px;text-align:left;border:1px solid rgba(0,158,96,0.15);color:#009e60;font-weight:700;">Colonne A</th>
                        <th style="padding:8px 12px;text-align:left;border:1px solid rgba(0,158,96,0.15);color:#009e60;font-weight:700;">Colonne B</th>
                        <th style="padding:8px 12px;text-align:left;border:1px solid rgba(0,158,96,0.15);color:#009e60;font-weight:700;">Colonne C</th>
                        <th style="padding:8px 12px;text-align:left;border:1px solid rgba(0,158,96,0.15);color:#009e60;font-weight:700;">Colonne D</th>
                        <th style="padding:8px 12px;text-align:left;border:1px solid rgba(0,158,96,0.15);color:#009e60;font-weight:700;">Colonne E</th>
                        <th style="padding:8px 12px;text-align:left;border:1px solid rgba(0,158,96,0.15);color:#009e60;font-weight:700;">Colonne F</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background:#f8fafc;">
                        <td style="padding:8px 12px;border:1px solid rgba(0,158,96,0.10);color:#64748b;font-style:italic;">Matricule</td>
                        <td style="padding:8px 12px;border:1px solid rgba(0,158,96,0.10);color:#64748b;font-style:italic;">Nom</td>
                        <td style="padding:8px 12px;border:1px solid rgba(0,158,96,0.10);color:#64748b;font-style:italic;">Prenom</td>
                        <td style="padding:8px 12px;border:1px solid rgba(0,158,96,0.10);color:#64748b;font-style:italic;">Sexe (M/F)</td>
                        <td style="padding:8px 12px;border:1px solid rgba(0,158,96,0.10);color:#64748b;font-style:italic;">Date naissance</td>
                        <td style="padding:8px 12px;border:1px solid rgba(0,158,96,0.10);color:#64748b;font-style:italic;">Lieu naissance</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 12px;border:1px solid rgba(0,158,96,0.10);color:#0f172a;">ETP001</td>
                        <td style="padding:8px 12px;border:1px solid rgba(0,158,96,0.10);color:#0f172a;">MOUSSAVOU</td>
                        <td style="padding:8px 12px;border:1px solid rgba(0,158,96,0.10);color:#0f172a;">Jean</td>
                        <td style="padding:8px 12px;border:1px solid rgba(0,158,96,0.10);color:#0f172a;">M</td>
                        <td style="padding:8px 12px;border:1px solid rgba(0,158,96,0.10);color:#0f172a;">2005-03-15</td>
                        <td style="padding:8px 12px;border:1px solid rgba(0,158,96,0.10);color:#0f172a;">Libreville</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p style="margin-top:10px;font-size:12px;color:#64748b;"><i class="bi bi-info-circle" style="color:#009e60;"></i> Le matricule est optionnel — il sera genere automatiquement si absent. Les eleves deja inscrits pour l'annee en cours seront ignores.</p>
    </section>

    {{-- FORMULAIRE --}}
    <section style="background:#fff;border-radius:24px;border:1px solid rgba(0,158,96,0.18);padding:1.5rem;box-shadow:0 8px 24px rgba(0,0,0,0.06);">

        @if($errors->any())
        <div style="margin-bottom:16px;padding:12px 16px;border-radius:14px;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.20);">
            <p style="font-size:13px;font-weight:600;color:#ef4444;margin-bottom:6px;"><i class="bi bi-exclamation-triangle"></i> Erreurs de validation</p>
            @foreach($errors->all() as $error)
            <p style="font-size:12px;color:#ef4444;">• {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form action="{{ route('eleves.import.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="display:grid;gap:20px;">

                {{-- Classe --}}
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#0f172a;margin-bottom:6px;">
                        Classe de destination <span style="color:#ef4444;">*</span>
                    </label>
                    <select name="classe_id" required style="width:100%;padding:10px 14px;border-radius:12px;border:1px solid rgba(0,158,96,0.25);background:#fff;font-size:13px;color:#0f172a;">
                        <option value="">-- Choisir une classe --</option>
                        @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>{{ $classe->nom_classe }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Fichier --}}
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#0f172a;margin-bottom:6px;">
                        Fichier Excel ou CSV <span style="color:#ef4444;">*</span>
                    </label>
                    <div id="drop-zone" style="border:2px dashed rgba(0,158,96,0.35);border-radius:16px;padding:2rem;text-align:center;cursor:pointer;transition:background 0.2s;" onclick="document.getElementById('fichier-input').click()">
                        <i class="bi bi-cloud-upload" style="font-size:2rem;color:#009e60;"></i>
                        <p style="margin-top:10px;font-size:14px;font-weight:600;color:#0f172a;">Cliquez ou glissez votre fichier ici</p>
                        <p style="font-size:12px;color:#64748b;margin-top:4px;">Formats acceptes : .xlsx, .xls, .csv — max 5 Mo</p>
                        <p id="file-name" style="margin-top:8px;font-size:13px;font-weight:600;color:#009e60;display:none;"></p>
                    </div>
                    <input type="file" id="fichier-input" name="fichier" accept=".xlsx,.xls,.csv,.txt" required style="display:none;" onchange="showFileName(this)">
                </div>

                {{-- Annee info --}}
                @if($annee)
                <div style="padding:10px 14px;border-radius:12px;background:rgba(0,158,96,0.06);border:1px solid rgba(0,158,96,0.15);">
                    <p style="font-size:12px;color:#009e60;"><i class="bi bi-calendar2-check"></i> Les eleves seront inscrits pour l'annee <strong>{{ $annee->libelle }}</strong>.</p>
                </div>
                @endif

                {{-- Actions --}}
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <button type="submit" style="display:inline-flex;align-items:center;gap:8px;padding:10px 22px;border-radius:999px;background:#009e60;color:#fff;font-size:13px;font-weight:700;border:none;cursor:pointer;">
                        <i class="bi bi-upload"></i> Lancer l'importation
                    </button>
                    <a href="{{ route('eleves.index') }}" style="display:inline-flex;align-items:center;gap:8px;padding:10px 22px;border-radius:999px;background:rgba(100,116,139,0.10);color:#475569;font-size:13px;font-weight:600;text-decoration:none;border:1px solid rgba(100,116,139,0.20);">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </form>
    </section>
</div>

<script>
function showFileName(input) {
    const label = document.getElementById('file-name');
    if (input.files && input.files[0]) {
        label.textContent = input.files[0].name;
        label.style.display = 'block';
        document.getElementById('drop-zone').style.background = 'rgba(0,158,96,0.05)';
    }
}

const dropZone = document.getElementById('drop-zone');
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.style.background = 'rgba(0,158,96,0.08)'; });
dropZone.addEventListener('dragleave', () => { dropZone.style.background = ''; });
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.style.background = 'rgba(0,158,96,0.05)';
    const input = document.getElementById('fichier-input');
    input.files = e.dataTransfer.files;
    showFileName(input);
});
</script>
@endsection

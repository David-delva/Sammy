@extends('layouts.app')

@section('title', $eleve->nom . ' ' . $eleve->prenom)
@section('breadcrumb', 'Scolarite / Eleves / Detail')

@section('content')
@php
    $formatNote = static fn ($value) => $value !== null ? number_format((float) $value, 2, ',', ' ') : '--';
@endphp

<div class="space-y-6">

    {{-- HERO --}}
    <section style="background: linear-gradient(135deg, #000000 0%, #009e60 60%, #006400 100%); border-radius: 32px; padding: 2rem; position: relative; overflow: hidden; box-shadow: 0 28px 80px rgba(0,0,0,0.28);">
        {{-- Cercles decoratifs --}}
        <div style="position:absolute;top:-60px;right:-60px;width:220px;height:220px;border-radius:50%;background:rgba(247,209,23,0.10);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-40px;left:-40px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.06);pointer-events:none;"></div>

        <div class="grid gap-8 xl:grid-cols-2 relative">
            {{-- Gauche --}}
            <div>
                <p style="font-size:11px;font-weight:700;letter-spacing:0.3em;text-transform:uppercase;color:#f7d117;">Profil eleve</p>
                <h2 style="margin-top:12px;font-size:2rem;font-weight:700;color:#fff;line-height:1.1;">{{ $eleve->nom }} {{ $eleve->prenom }}<br><span style="font-size:1.1rem;font-weight:400;color:rgba(255,255,255,0.75);">conserve un suivi annuel clair et detaille.</span></h2>
                <p style="margin-top:12px;font-size:14px;color:rgba(255,255,255,0.70);line-height:1.7;">Consultez la fiche civile, les moyennes, les matieres et l'historique de l'eleve dans un ecran unique beaucoup plus lisible.</p>

                <div style="margin-top:20px;display:flex;flex-wrap:wrap;gap:10px;">
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:#fff;font-size:12px;font-weight:600;">
                        <i class="bi bi-upc"></i>{{ $eleve->matricule }}
                    </span>
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;background:rgba(247,209,23,0.20);border:1px solid rgba(247,209,23,0.35);color:#f7d117;font-size:12px;font-weight:600;">
                        <i class="bi bi-building"></i>{{ $eleve->resolved_classe ? $eleve->resolved_classe->nom_classe : 'Non inscrit' }}
                    </span>
                    @if($annee)
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.20);color:rgba(255,255,255,0.85);font-size:12px;font-weight:600;">
                        <i class="bi bi-calendar2-week"></i>{{ $annee->libelle }}
                    </span>
                    @endif
                </div>

                <div style="margin-top:20px;display:flex;flex-wrap:wrap;gap:10px;">
                    @if($canManageAcademicData && $eleve->resolved_classe)
                        <a href="{{ route('eleves.edit', ['eleve' => $eleve, 'date' => request()->query('date')]) }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:999px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.30);color:#fff;font-size:13px;font-weight:600;text-decoration:none;">
                            <i class="bi bi-pencil"></i> Modifier
                        </a>
                        @if($currentInscription)
                        <a href="{{ route('factures.create', ['date' => request()->query('date'), 'inscription' => $currentInscription->id]) }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:999px;background:#f7d117;color:#000;font-size:13px;font-weight:700;text-decoration:none;">
                            <i class="bi bi-receipt"></i> Nouvelle facture
                        </a>
                        @endif
                    @elseif($canManageAcademicData)
                        <a href="{{ route('eleves.reinscriptions.index', ['date' => request()->query('date'), 'search' => $eleve->matricule]) }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:999px;background:#f7d117;color:#000;font-size:13px;font-weight:700;text-decoration:none;">
                            <i class="bi bi-arrow-repeat"></i> Reinscrire
                        </a>
                    @endif
                    @if($currentInscription)
                    <a href="{{ route('factures.index', ['date' => request()->query('date'), 'eleve' => $eleve->id]) }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:999px;background:rgba(255,255,255,0.14);border:1px solid rgba(255,255,255,0.25);color:#fff;font-size:13px;font-weight:600;text-decoration:none;">
                        <i class="bi bi-printer"></i> Factures
                    </a>
                    @endif
                    <a href="{{ route('eleves.historique', ['eleve' => $eleve, 'date' => request()->query('date')]) }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:999px;background:rgba(255,255,255,0.14);border:1px solid rgba(255,255,255,0.25);color:#fff;font-size:13px;font-weight:600;text-decoration:none;">
                        <i class="bi bi-clock-history"></i> Historique
                    </a>
                </div>
            </div>

            {{-- Droite : Synthese --}}
            <div style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.22);border-radius:24px;padding:1.5rem;backdrop-filter:blur(12px);display:flex;flex-direction:column;gap:1.2rem;">
                <div>
                    <p style="font-size:10px;font-weight:700;letter-spacing:0.28em;text-transform:uppercase;color:#f7d117;">Synthese</p>
                    <h3 style="margin-top:10px;font-size:1.1rem;font-weight:600;color:#fff;line-height:1.4;">Les indicateurs annuels restent visibles des l'ouverture du profil.</h3>
                    <p style="margin-top:8px;font-size:13px;color:rgba(255,255,255,0.80);line-height:1.7;">Les bulletins PDF, les moyennes par semestre et les notes par matiere sont regroupes sous une meme narration visuelle.</p>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="background:rgba(0,0,0,0.25);border:1px solid rgba(247,209,23,0.25);border-radius:18px;padding:14px;">
                        <p style="font-size:10px;text-transform:uppercase;letter-spacing:0.2em;color:#f7d117;">Evaluations</p>
                        <p style="font-size:1.8rem;font-weight:700;color:#fff;margin-top:6px;">{{ $notesOverview['total_notes'] }}</p>
                        <p style="font-size:12px;color:rgba(255,255,255,0.70);margin-top:4px;">note(s) enregistree(s)</p>
                    </div>
                    <div style="background:rgba(0,0,0,0.25);border:1px solid rgba(247,209,23,0.25);border-radius:18px;padding:14px;">
                        <p style="font-size:10px;text-transform:uppercase;letter-spacing:0.2em;color:#f7d117;">Moyenne annuelle</p>
                        <p style="font-size:1.8rem;font-weight:700;color:#fff;margin-top:6px;">{{ $formatNote($notesOverview['moyenne_annuelle']) }}</p>
                        <p style="font-size:12px;color:rgba(255,255,255,0.70);margin-top:4px;">sur 20</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CORPS --}}
    <div class="grid gap-6 xl:grid-cols-[300px_minmax(0,1fr)]">

        {{-- Fiche eleve --}}
        <aside style="background:linear-gradient(160deg,#009e60 0%,#004d2a 100%);border-radius:28px;padding:1.5rem;border:1px solid rgba(255,255,255,0.15);box-shadow:0 20px 50px rgba(0,0,0,0.20);">
            <div class="text-center">
                <div style="margin:0 auto;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.18);border:3px solid #f7d117;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-person-fill" style="font-size:2.4rem;color:#fff;"></i>
                </div>
                <h3 style="margin-top:14px;font-size:1.1rem;font-weight:700;color:#fff;">{{ $eleve->nom }} {{ $eleve->prenom }}</h3>
                <p style="font-size:12px;color:#f7d117;margin-top:4px;">{{ $eleve->matricule }}</p>
            </div>

            <div style="margin-top:20px;display:flex;flex-direction:column;gap:10px;">
                @foreach([
                    ['label' => 'Classe actuelle', 'value' => $eleve->resolved_classe ? $eleve->resolved_classe->nom_classe : 'Non inscrit', 'icon' => 'bi-building'],
                    ['label' => 'Date de naissance', 'value' => $eleve->date_naissance->format('d/m/Y'), 'icon' => 'bi-calendar3'],
                    ['label' => 'Lieu de naissance', 'value' => $eleve->lieu_naissance ?: 'Non renseigne', 'icon' => 'bi-geo-alt'],
                    ['label' => 'Sexe', 'value' => $eleve->sexe === 'M' ? 'Masculin' : 'Feminin', 'icon' => 'bi-person'],
                    ['label' => 'Baccalauréat', 'value' => $eleve->bac ?: 'Non renseigne', 'icon' => 'bi-mortarboard'],
                    ['label' => 'Provenance', 'value' => $eleve->provenance ?: 'Non renseigne', 'icon' => 'bi-signpost'],
                ] as $item)
                <div style="background:rgba(0,0,0,0.20);border:1px solid rgba(255,255,255,0.15);border-radius:16px;padding:12px 14px;display:flex;align-items:center;gap:12px;">
                    <span style="width:34px;height:34px;border-radius:10px;background:rgba(247,209,23,0.20);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi {{ $item['icon'] }}" style="color:#f7d117;font-size:14px;"></i>
                    </span>
                    <div>
                        <p style="font-size:10px;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.55);font-weight:600;">{{ $item['label'] }}</p>
                        <p style="font-size:13px;font-weight:600;color:#fff;margin-top:2px;">{{ $item['value'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </aside>

        {{-- Notes --}}
        <section style="background:linear-gradient(180deg,rgba(255,255,255,0.98),rgba(240,248,244,0.90));border-radius:28px;border:1px solid rgba(0,158,96,0.18);box-shadow:0 20px 50px rgba(0,0,0,0.10);overflow:hidden;">

            {{-- Header notes --}}
            <div style="padding:1.2rem 1.5rem;border-bottom:1px solid rgba(0,158,96,0.15);background:linear-gradient(90deg,rgba(0,158,96,0.06),rgba(247,209,23,0.04));display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;">
                <div>
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <h4 style="font-size:15px;font-weight:700;color:#0f172a;">Notes de l'annee</h4>
                        @if($annee)
                        <span style="padding:3px 10px;border-radius:999px;background:rgba(0,158,96,0.12);color:#009e60;font-size:11px;font-weight:700;">{{ $annee->libelle }}</span>
                        @endif
                    </div>
                    <p style="font-size:12px;color:#64748b;margin-top:4px;">Vue detaillee des evaluations, organisee par semestre puis par matiere.</p>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="{{ route('bulletins.pdf', ['id' => $eleve->id, 'semestre' => 1, 'date' => request()->query('date')]) }}" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:999px;background:linear-gradient(135deg,#009e60,#006400);color:#fff;font-size:12px;font-weight:600;text-decoration:none;">
                        <i class="bi bi-file-earmark-pdf"></i> Bulletin S1
                    </a>
                    <a href="{{ route('bulletins.pdf', ['id' => $eleve->id, 'semestre' => 2, 'date' => request()->query('date')]) }}" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:999px;background:#000;color:#f7d117;font-size:12px;font-weight:600;text-decoration:none;">
                        <i class="bi bi-file-earmark-pdf"></i> Bulletin S2
                    </a>
                </div>
            </div>

            {{-- Stats --}}
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:0;border-bottom:1px solid rgba(0,158,96,0.12);">
                @foreach([
                    ['label'=>'Evaluations','value'=>$notesOverview['total_notes'],'sub'=>'notes enregistrees','color'=>'#009e60'],
                    ['label'=>'Matieres','value'=>$notesOverview['total_matieres'],'sub'=>'matieres evaluees','color'=>'#009e60'],
                    ['label'=>'Moyenne annuelle','value'=>$formatNote($notesOverview['moyenne_annuelle']),'sub'=>'sur 20','color'=>$notesOverview['moyenne_annuelle']===null?'#64748b':($notesOverview['moyenne_annuelle']>=10?'#009e60':'#ef4444')],
                    ['label'=>'Moyenne S1','value'=>$formatNote($notesOverview['moyenne_semestre_1']),'sub'=>'premier semestre','color'=>$notesOverview['moyenne_semestre_1']===null?'#64748b':($notesOverview['moyenne_semestre_1']>=10?'#009e60':'#ef4444')],
                    ['label'=>'Moyenne S2','value'=>$formatNote($notesOverview['moyenne_semestre_2']),'sub'=>'deuxieme semestre','color'=>$notesOverview['moyenne_semestre_2']===null?'#64748b':($notesOverview['moyenne_semestre_2']>=10?'#009e60':'#ef4444')],
                ] as $stat)
                <div style="padding:16px 18px;border-right:1px solid rgba(0,158,96,0.10);">
                    <p style="font-size:10px;text-transform:uppercase;letter-spacing:0.2em;color:#64748b;font-weight:600;">{{ $stat['label'] }}</p>
                    <p style="font-size:1.6rem;font-weight:700;color:{{ $stat['color'] }};margin-top:6px;">{{ $stat['value'] }}</p>
                    <p style="font-size:11px;color:#94a3b8;margin-top:3px;">{{ $stat['sub'] }}</p>
                </div>
                @endforeach
            </div>

            {{-- Semestres --}}
            @if($notesBySemestre->isNotEmpty())
            <div style="padding:1.2rem 1.5rem;display:flex;flex-direction:column;gap:1.2rem;">
                @foreach($notesBySemestre as $semestreGroup)
                <div style="border:1px solid rgba(0,158,96,0.15);border-radius:22px;overflow:hidden;">

                    {{-- Header semestre --}}
                    <div style="padding:14px 18px;background:linear-gradient(90deg,{{ $semestreGroup['semestre']===2?'rgba(247,209,23,0.10)':'rgba(0,158,96,0.08)' }},transparent);border-bottom:1px solid rgba(0,158,96,0.10);display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px;">
                        <div>
                            <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;">
                                <span style="padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;background:{{ $semestreGroup['semestre']===2?'rgba(247,209,23,0.20)':'rgba(0,158,96,0.15)' }};color:{{ $semestreGroup['semestre']===2?'#b45309':'#009e60' }};">{{ $semestreGroup['label'] }}</span>
                                <span style="padding:4px 10px;border-radius:999px;font-size:11px;font-weight:600;background:rgba(100,116,139,0.10);color:#64748b;">{{ $semestreGroup['total_notes'] }} evaluation(s)</span>
                                <span style="padding:4px 10px;border-radius:999px;font-size:11px;font-weight:600;background:rgba(100,116,139,0.10);color:#64748b;">{{ $semestreGroup['total_matieres'] }} matiere(s)</span>
                            </div>
                            <p style="font-size:12px;color:#94a3b8;margin-top:6px;">Les evaluations sont regroupees par matiere pour simplifier la lecture du semestre.</p>
                        </div>
                        <div style="text-align:right;">
                            <p style="font-size:10px;text-transform:uppercase;letter-spacing:0.2em;color:#94a3b8;font-weight:600;">Moyenne du semestre</p>
                            <p style="font-size:1.4rem;font-weight:700;color:{{ $semestreGroup['moyenne_generale']===null?'#94a3b8':($semestreGroup['moyenne_generale']>=10?'#009e60':'#ef4444') }};margin-top:4px;">{{ $formatNote($semestreGroup['moyenne_generale']) }}</p>
                        </div>
                    </div>

                    {{-- Matieres --}}
                    <div style="padding:14px 18px;display:flex;flex-direction:column;gap:12px;">
                        @foreach($semestreGroup['matieres'] as $matiereGroup)
                        <div style="background:#fff;border:1px solid rgba(0,158,96,0.12);border-radius:18px;padding:14px;box-shadow:0 4px 12px rgba(0,0,0,0.04);">
                            <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:12px;">
                                <div>
                                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                        <h5 style="font-size:14px;font-weight:700;color:#0f172a;">{{ $matiereGroup['matiere']->nom_matiere }}</h5>
                                        <span style="padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;background:rgba(100,116,139,0.10);color:#64748b;">{{ $matiereGroup['total_notes'] }} evaluation(s)</span>
                                    </div>
                                    <p style="font-size:12px;color:#94a3b8;margin-top:4px;">Derniere saisie le {{ $matiereGroup['derniere_saisie'] ? $matiereGroup['derniere_saisie']->format('d/m/Y') : '--' }}.</p>
                                </div>
                                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;min-width:380px;">
                                    @foreach([
                                        ['label'=>'Moy. devoirs','value'=>$formatNote($matiereGroup['moyenne_devoirs'])],
                                        ['label'=>'Composition','value'=>$formatNote($matiereGroup['note_composition'])],
                                        ['label'=>'Rattrapage','value'=>$formatNote($matiereGroup['note_rattrapage'])],
                                        ['label'=>'Moy. matiere','value'=>$formatNote($matiereGroup['moyenne_matiere']),'highlight'=>true],
                                    ] as $m)
                                    <div style="background:{{ isset($m['highlight'])?'rgba(0,158,96,0.08)':'rgba(248,250,252,0.80)' }};border:1px solid {{ isset($m['highlight'])?'rgba(0,158,96,0.20)':'rgba(0,0,0,0.06)' }};border-radius:12px;padding:10px;text-align:center;">
                                        <p style="font-size:10px;text-transform:uppercase;letter-spacing:0.15em;color:#94a3b8;font-weight:600;">{{ $m['label'] }}</p>
                                        <p style="font-size:1.1rem;font-weight:700;color:{{ isset($m['highlight'])?'#009e60':'#0f172a' }};margin-top:4px;">{{ $m['value'] }}</p>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Tableau notes --}}
                            <div style="margin-top:12px;border-radius:14px;overflow:hidden;border:1px solid rgba(0,158,96,0.10);">
                                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                                    <thead>
                                        <tr style="background:linear-gradient(90deg,rgba(0,158,96,0.08),rgba(247,209,23,0.05));">
                                            <th style="padding:10px 14px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:#64748b;font-weight:700;">Evaluation</th>
                                            <th style="padding:10px 14px;text-align:center;font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:#64748b;font-weight:700;">Note / 20</th>
                                            <th style="padding:10px 14px;text-align:right;font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:#64748b;font-weight:700;">Date de saisie</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($matiereGroup['notes'] as $note)
                                        <tr style="border-top:1px solid rgba(0,158,96,0.08);">
                                            @php
                                                $badgeBg = match($note->type_devoir) {
                                                    'composition' => 'rgba(247,209,23,0.18)',
                                                    'rattrapage' => 'rgba(239,68,68,0.12)',
                                                    default => 'rgba(100,116,139,0.10)',
                                                };
                                                $badgeColor = match($note->type_devoir) {
                                                    'composition' => '#92400e',
                                                    'rattrapage' => '#b91c1c',
                                                    default => '#475569',
                                                };
                                            @endphp
                                            <td style="padding:10px 14px;">
                                                <span style="padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;background:{{ $badgeBg }};color:{{ $badgeColor }};">{{ ucfirst($note->type_devoir) }}</span>
                                            </td>
                                            <td style="padding:10px 14px;text-align:center;">
                                                <span style="font-size:15px;font-weight:700;color:{{ $note->note>=10?'#009e60':'#ef4444' }};">{{ number_format($note->note, 2, ',', ' ') }}</span>
                                            </td>
                                            <td style="padding:10px 14px;text-align:right;font-size:12px;color:#94a3b8;">{{ $note->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div style="padding:3rem;text-align:center;">
                <div style="width:64px;height:64px;border-radius:50%;background:rgba(0,158,96,0.10);display:flex;align-items:center;justify-content:center;margin:0 auto;">
                    <i class="bi bi-journal-x" style="font-size:1.6rem;color:#009e60;"></i>
                </div>
                <p style="margin-top:16px;font-size:14px;font-weight:600;color:#0f172a;">Aucune note enregistree</p>
                <p style="margin-top:6px;font-size:13px;color:#64748b;">Les notes de cet eleve apparaitront ici des qu'elles seront saisies.</p>
            </div>
            @endif
        </section>
    </div>
</div>
@endsection

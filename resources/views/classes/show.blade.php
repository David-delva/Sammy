@extends('layouts.app')

@section('title', $classe->nom_classe)
@section('breadcrumb', 'Administration / Classes / Detail')

@section('content')
<div class="space-y-6">

    {{-- HERO --}}
    <section style="background:linear-gradient(135deg,#000000 0%,#009e60 60%,#006400 100%);border-radius:32px;padding:2rem;position:relative;overflow:hidden;box-shadow:0 28px 80px rgba(0,0,0,0.28);">
        <div style="position:absolute;top:-60px;right:-60px;width:220px;height:220px;border-radius:50%;background:rgba(247,209,23,0.10);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-40px;left:-40px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.06);pointer-events:none;"></div>

        <div class="grid gap-8 xl:grid-cols-2 relative">
            <div>
                <p style="font-size:11px;font-weight:700;letter-spacing:0.3em;text-transform:uppercase;color:#f7d117;">Classe</p>
                <h2 style="margin-top:12px;font-size:1.9rem;font-weight:700;color:#fff;line-height:1.15;">{{ $classe->nom_classe }}</h2>
                <p style="margin-top:10px;font-size:14px;color:rgba(255,255,255,0.72);line-height:1.7;">Consultez la composition de la classe et les matieres rattachees dans une vue claire et accessible.</p>

                <div style="margin-top:18px;display:flex;flex-wrap:wrap;gap:10px;">
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:#fff;font-size:12px;font-weight:600;">
                        <i class="bi bi-people-fill"></i>{{ $classe->eleves->count() }} eleve(s)
                    </span>
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;background:rgba(247,209,23,0.20);border:1px solid rgba(247,209,23,0.35);color:#f7d117;font-size:12px;font-weight:600;">
                        <i class="bi bi-book-fill"></i>{{ $classe->matieres->count() }} matiere(s)
                    </span>
                    @if(isset($annee) && $annee)
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.20);color:rgba(255,255,255,0.85);font-size:12px;font-weight:600;">
                        <i class="bi bi-calendar2-week"></i>{{ $annee->libelle }}
                    </span>
                    @endif
                </div>

                <div style="margin-top:18px;display:flex;flex-wrap:wrap;gap:10px;">
                    <a href="{{ route('classes.liste.pdf', ['classe' => $classe, 'date' => request()->query('date')]) }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:999px;background:#f7d117;color:#000;font-size:13px;font-weight:700;text-decoration:none;">
                        <i class="bi bi-printer"></i> Feuille d'appel PDF
                    </a>
                    <a href="{{ route('classes.index', ['date' => request()->query('date')]) }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:999px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.30);color:#fff;font-size:13px;font-weight:600;text-decoration:none;">
                        <i class="bi bi-arrow-left"></i> Retour aux classes
                    </a>
                </div>
            </div>

            <div style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.22);border-radius:24px;padding:1.5rem;backdrop-filter:blur(12px);display:flex;flex-direction:column;gap:1.2rem;">
                <div>
                    <p style="font-size:10px;font-weight:700;letter-spacing:0.28em;text-transform:uppercase;color:#f7d117;">Vue synthese</p>
                    <h3 style="margin-top:10px;font-size:1.1rem;font-weight:600;color:#fff;line-height:1.4;">Effectif et programme de la classe en un coup d'oeil.</h3>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="background:rgba(0,0,0,0.25);border:1px solid rgba(247,209,23,0.25);border-radius:18px;padding:14px;">
                        <p style="font-size:10px;text-transform:uppercase;letter-spacing:0.2em;color:#f7d117;">Effectif</p>
                        <p style="font-size:1.8rem;font-weight:700;color:#fff;margin-top:6px;">{{ $classe->eleves->count() }}</p>
                        <p style="font-size:12px;color:rgba(255,255,255,0.70);margin-top:4px;">eleve(s) inscrit(s)</p>
                    </div>
                    <div style="background:rgba(0,0,0,0.25);border:1px solid rgba(247,209,23,0.25);border-radius:18px;padding:14px;">
                        <p style="font-size:10px;text-transform:uppercase;letter-spacing:0.2em;color:#f7d117;">Programme</p>
                        <p style="font-size:1.8rem;font-weight:700;color:#fff;margin-top:6px;">{{ $classe->matieres->count() }}</p>
                        <p style="font-size:12px;color:rgba(255,255,255,0.70);margin-top:4px;">matiere(s)</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- PANNEAUX ELEVES + MATIERES --}}
    <div class="grid gap-6 xl:grid-cols-2">

        {{-- ELEVES --}}
        <section style="background:linear-gradient(180deg,rgba(255,255,255,0.98),rgba(240,248,244,0.90));border-radius:28px;border:1px solid rgba(0,158,96,0.18);box-shadow:0 20px 50px rgba(0,0,0,0.10);overflow:hidden;">
            <div style="padding:1.2rem 1.5rem;border-bottom:1px solid rgba(0,158,96,0.15);background:linear-gradient(90deg,rgba(0,158,96,0.06),rgba(247,209,23,0.04));display:flex;align-items:center;justify-content:space-between;gap:12px;">
                <div>
                    <h4 style="font-size:15px;font-weight:700;color:#0f172a;">Eleves</h4>
                    <p style="font-size:12px;color:#64748b;margin-top:4px;">Liste des profils rattaches a cette classe.</p>
                </div>
                <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:999px;background:rgba(0,158,96,0.10);color:#009e60;font-size:12px;font-weight:700;border:1px solid rgba(0,158,96,0.20);">
                    <i class="bi bi-people-fill"></i> {{ $classe->eleves->count() }} inscrit(s)
                </span>
            </div>

            @if($classe->eleves->isEmpty())
            <div style="padding:3rem;text-align:center;">
                <div style="width:64px;height:64px;border-radius:50%;background:rgba(0,158,96,0.10);display:flex;align-items:center;justify-content:center;margin:0 auto;">
                    <i class="bi bi-people" style="font-size:1.6rem;color:#009e60;"></i>
                </div>
                <p style="margin-top:12px;font-size:14px;font-weight:600;color:#0f172a;">Aucun eleve pour cette classe</p>
                <p style="font-size:13px;color:#64748b;margin-top:4px;">Les futurs inscrits apparaitront ici automatiquement.</p>
            </div>
            @else
            <div style="display:flex;flex-direction:column;">
                @foreach($classe->eleves as $eleve)
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 16px;border-bottom:1px solid rgba(0,158,96,0.07);transition:background 0.15s;" onmouseover="this.style.background='rgba(0,158,96,0.04)'" onmouseout="this.style.background='transparent'">
                    <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                        <div style="width:36px;height:36px;border-radius:12px;background:rgba(0,158,96,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-person-fill" style="color:#009e60;font-size:1rem;"></i>
                        </div>
                        <div style="min-width:0;">
                            <p style="font-size:13px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $eleve->nom }} {{ $eleve->prenom }}</p>
                            <p style="font-size:11px;color:#94a3b8;margin-top:2px;">{{ $eleve->matricule ?? 'Matricule non renseigne' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('eleves.show', $eleve) }}" style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:999px;background:rgba(0,158,96,0.10);color:#009e60;font-size:12px;font-weight:600;text-decoration:none;border:1px solid rgba(0,158,96,0.20);white-space:nowrap;flex-shrink:0;">
                        <i class="bi bi-eye"></i> Profil
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        </section>

        {{-- MATIERES --}}
        <section style="background:linear-gradient(180deg,rgba(255,255,255,0.98),rgba(240,248,244,0.90));border-radius:28px;border:1px solid rgba(0,158,96,0.18);box-shadow:0 20px 50px rgba(0,0,0,0.10);overflow:hidden;">
            <div style="padding:1.2rem 1.5rem;border-bottom:1px solid rgba(0,158,96,0.15);background:linear-gradient(90deg,rgba(0,158,96,0.06),rgba(247,209,23,0.04));display:flex;align-items:center;justify-content:space-between;gap:12px;">
                <div>
                    <h4 style="font-size:15px;font-weight:700;color:#0f172a;">Matieres</h4>
                    <p style="font-size:12px;color:#64748b;margin-top:4px;">Programme associe a la classe avec coefficients.</p>
                </div>
                <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:999px;background:rgba(247,209,23,0.15);color:#92400e;font-size:12px;font-weight:700;border:1px solid rgba(247,209,23,0.30);">
                    <i class="bi bi-book-fill"></i> {{ $classe->matieres->count() }} matiere(s)
                </span>
            </div>

            @if($classe->matieres->isEmpty())
            <div style="padding:3rem;text-align:center;">
                <div style="width:64px;height:64px;border-radius:50%;background:rgba(247,209,23,0.12);display:flex;align-items:center;justify-content:center;margin:0 auto;">
                    <i class="bi bi-book" style="font-size:1.6rem;color:#92400e;"></i>
                </div>
                <p style="margin-top:12px;font-size:14px;font-weight:600;color:#0f172a;">Aucune matiere associee</p>
                <p style="font-size:13px;color:#64748b;margin-top:4px;">Utilisez le module d'assignation pour relier les matieres a cette classe.</p>
            </div>
            @else
            <div style="display:flex;flex-direction:column;">
                @foreach($classe->matieres as $matiere)
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 16px;border-bottom:1px solid rgba(0,158,96,0.07);transition:background 0.15s;" onmouseover="this.style.background='rgba(0,158,96,0.04)'" onmouseout="this.style.background='transparent'">
                    <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                        <div style="width:36px;height:36px;border-radius:12px;background:rgba(247,209,23,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-book-fill" style="color:#92400e;font-size:0.9rem;"></i>
                        </div>
                        <div style="min-width:0;">
                            <p style="font-size:13px;font-weight:700;color:#0f172a;">{{ $matiere->nom_matiere ?? $matiere->nom }}</p>
                            <p style="font-size:11px;color:#94a3b8;margin-top:2px;">Matiere du programme</p>
                        </div>
                    </div>
                    @if(isset($matiere->pivot->coefficient))
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:999px;background:rgba(0,0,0,0.06);color:#475569;font-size:11px;font-weight:700;border:1px solid rgba(0,0,0,0.10);white-space:nowrap;flex-shrink:0;">
                        Coef. {{ $matiere->pivot->coefficient }}
                    </span>
                    @else
                    <span style="display:inline-flex;align-items:center;padding:4px 12px;border-radius:999px;background:rgba(100,116,139,0.08);color:#94a3b8;font-size:11px;font-weight:600;white-space:nowrap;flex-shrink:0;">
                        Sans coef.
                    </span>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </section>
    </div>
</div>
@endsection

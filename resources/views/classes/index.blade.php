@extends('layouts.app')

@section('title', 'Classes')
@section('breadcrumb', 'Administration / Classes')

@section('content')
<div class="space-y-6">

    {{-- HERO --}}
    <section style="background:linear-gradient(135deg,#000000 0%,#009e60 60%,#006400 100%);border-radius:32px;padding:2rem;position:relative;overflow:hidden;box-shadow:0 28px 80px rgba(0,0,0,0.28);">
        <div style="position:absolute;top:-60px;right:-60px;width:220px;height:220px;border-radius:50%;background:rgba(247,209,23,0.10);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-40px;left:-40px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.06);pointer-events:none;"></div>

        <div class="grid gap-8 xl:grid-cols-2 relative">
            <div>
                <p style="font-size:11px;font-weight:700;letter-spacing:0.3em;text-transform:uppercase;color:#f7d117;">Administration</p>
                <h2 style="margin-top:12px;font-size:1.9rem;font-weight:700;color:#fff;line-height:1.15;">Gestion des classes pedagogiques de l'etablissement.</h2>
                <p style="margin-top:10px;font-size:14px;color:rgba(255,255,255,0.72);line-height:1.7;">Consultez les effectifs, gerez les matieres associees et imprimez les feuilles d'appel en un clic.</p>

                <div style="margin-top:18px;display:flex;flex-wrap:wrap;gap:10px;">
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:#fff;font-size:12px;font-weight:600;">
                        <i class="bi bi-building"></i>{{ $classes->total() }} classe(s)
                    </span>
                    @if(request()->query('date'))
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;background:rgba(247,209,23,0.20);border:1px solid rgba(247,209,23,0.35);color:#f7d117;font-size:12px;font-weight:600;">
                        <i class="bi bi-calendar2-week"></i>{{ \App\Models\AnneeAcademique::labelForDate(request()->query('date')) }}
                    </span>
                    @endif
                </div>

                <div style="margin-top:18px;display:flex;flex-wrap:wrap;gap:10px;">
                    @if($canManageAcademicData)
                    <a href="{{ route('classes.create', ['date' => request()->query('date')]) }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:999px;background:#f7d117;color:#000;font-size:13px;font-weight:700;text-decoration:none;">
                        <i class="bi bi-plus-lg"></i> Nouvelle classe
                    </a>
                    @endif
                    <a href="{{ route('matieres.assigner') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:999px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.30);color:#fff;font-size:13px;font-weight:600;text-decoration:none;">
                        <i class="bi bi-diagram-3"></i> Gerer les matieres
                    </a>
                </div>
            </div>

            <div style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.22);border-radius:24px;padding:1.5rem;backdrop-filter:blur(12px);display:flex;flex-direction:column;gap:1.2rem;">
                <div>
                    <p style="font-size:10px;font-weight:700;letter-spacing:0.28em;text-transform:uppercase;color:#f7d117;">Structure</p>
                    <h3 style="margin-top:10px;font-size:1.1rem;font-weight:600;color:#fff;line-height:1.4;">Chaque classe donne acces aux eleves, aux feuilles d'appel et aux matieres associees.</h3>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="background:rgba(0,0,0,0.25);border:1px solid rgba(247,209,23,0.25);border-radius:18px;padding:14px;">
                        <p style="font-size:10px;text-transform:uppercase;letter-spacing:0.2em;color:#f7d117;">Consultation</p>
                        <p style="font-size:1.4rem;font-weight:700;color:#fff;margin-top:6px;">Mobile</p>
                        <p style="font-size:12px;color:rgba(255,255,255,0.70);margin-top:4px;">cartes et actions directes</p>
                    </div>
                    <div style="background:rgba(0,0,0,0.25);border:1px solid rgba(247,209,23,0.25);border-radius:18px;padding:14px;">
                        <p style="font-size:10px;text-transform:uppercase;letter-spacing:0.2em;color:#f7d117;">Exports</p>
                        <p style="font-size:1.4rem;font-weight:700;color:#fff;margin-top:6px;">PDF</p>
                        <p style="font-size:12px;color:rgba(255,255,255,0.70);margin-top:4px;">feuille d'appel en un clic</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- TABLE --}}
    <section style="background:linear-gradient(180deg,rgba(255,255,255,0.98),rgba(240,248,244,0.90));border-radius:28px;border:1px solid rgba(0,158,96,0.18);box-shadow:0 20px 50px rgba(0,0,0,0.10);overflow:hidden;">

        {{-- Header --}}
        <div style="padding:1.2rem 1.5rem;border-bottom:1px solid rgba(0,158,96,0.15);background:linear-gradient(90deg,rgba(0,158,96,0.06),rgba(247,209,23,0.04));display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;">
            <div>
                <h4 style="font-size:15px;font-weight:700;color:#0f172a;">Repertoire des classes</h4>
                <p style="font-size:12px;color:#64748b;margin-top:4px;">Liste des classes pedagogiques avec acces direct aux actions les plus utiles.</p>
            </div>
            <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:999px;background:rgba(0,158,96,0.10);color:#009e60;font-size:12px;font-weight:700;border:1px solid rgba(0,158,96,0.20);">
                <i class="bi bi-building"></i> {{ $classes->total() }} classe(s)
            </span>
        </div>

        {{-- Cartes mobiles --}}
        <div class="sm:hidden" style="padding:1rem;display:flex;flex-direction:column;gap:10px;">
            @forelse($classes as $classe)
            <div style="background:#fff;border:1px solid rgba(0,158,96,0.12);border-radius:20px;padding:14px;box-shadow:0 4px 12px rgba(0,0,0,0.05);">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:48px;height:48px;border-radius:16px;background:linear-gradient(135deg,#009e60,#006400);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-building" style="color:#fff;font-size:1.2rem;"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <p style="font-size:15px;font-weight:700;color:#0f172a;">{{ $classe->nom_classe }}</p>
                        <div style="margin-top:6px;">
                            <span style="padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;background:rgba(0,158,96,0.12);color:#009e60;">
                                <i class="bi bi-people-fill"></i> {{ $classe->eleves_count }} eleve(s)
                            </span>
                        </div>
                    </div>
                </div>
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(0,158,96,0.10);display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <a href="{{ route('classes.show', ['classe' => $classe, 'date' => request()->query('date')]) }}" style="display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px;border-radius:14px;background:rgba(0,158,96,0.10);color:#009e60;font-size:12px;font-weight:600;text-decoration:none;">
                        <i class="bi bi-eye"></i> Voir
                    </a>
                    <a href="{{ route('classes.liste.pdf', ['classe' => $classe, 'date' => request()->query('date')]) }}" style="display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px;border-radius:14px;background:rgba(0,0,0,0.06);color:#475569;font-size:12px;font-weight:600;text-decoration:none;">
                        <i class="bi bi-printer"></i> Appel PDF
                    </a>
                    @if($canManageAcademicData)
                    <a href="{{ route('classes.edit', ['classe' => $classe, 'date' => request()->query('date')]) }}" style="display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px;border-radius:14px;background:rgba(247,209,23,0.15);color:#92400e;font-size:12px;font-weight:600;text-decoration:none;">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                    <form action="{{ route('classes.destroy', $classe) }}" method="POST" onsubmit="return confirm('Supprimer cette classe ?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="width:100%;display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px;border-radius:14px;background:rgba(239,68,68,0.10);color:#ef4444;font-size:12px;font-weight:600;border:none;cursor:pointer;">
                            <i class="bi bi-trash"></i> Supprimer
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div style="padding:3rem;text-align:center;">
                <div style="width:64px;height:64px;border-radius:50%;background:rgba(0,158,96,0.10);display:flex;align-items:center;justify-content:center;margin:0 auto;">
                    <i class="bi bi-building" style="font-size:1.6rem;color:#009e60;"></i>
                </div>
                <p style="margin-top:12px;font-size:14px;font-weight:600;color:#0f172a;">Aucune classe enregistree</p>
                <p style="font-size:13px;color:#64748b;margin-top:4px;">Creez une classe pour commencer a organiser les inscriptions.</p>
            </div>
            @endforelse
        </div>

        {{-- Tableau desktop --}}
        <div class="hidden sm:block" style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:linear-gradient(90deg,rgba(0,158,96,0.07),rgba(247,209,23,0.04));">
                        <th style="padding:12px 16px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:#64748b;font-weight:700;border-bottom:1px solid rgba(0,158,96,0.12);">Classe</th>
                        <th style="padding:12px 16px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:#64748b;font-weight:700;border-bottom:1px solid rgba(0,158,96,0.12);">Effectif</th>
                        <th style="padding:12px 16px;text-align:right;font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:#64748b;font-weight:700;border-bottom:1px solid rgba(0,158,96,0.12);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $classe)
                    <tr style="border-bottom:1px solid rgba(0,158,96,0.08);transition:background 0.15s;" onmouseover="this.style.background='rgba(0,158,96,0.04)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:14px 16px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="width:40px;height:40px;border-radius:14px;background:linear-gradient(135deg,#009e60,#006400);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="bi bi-building" style="color:#fff;font-size:1rem;"></i>
                                </div>
                                <div>
                                    <p style="font-size:14px;font-weight:700;color:#0f172a;">{{ $classe->nom_classe }}</p>
                                    <p style="font-size:11px;color:#94a3b8;margin-top:2px;">Classe pedagogique</p>
                                </div>
                            </div>
                        </td>
                        <td style="padding:14px 16px;">
                            <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;background:rgba(0,158,96,0.12);color:#009e60;">
                                <i class="bi bi-people-fill"></i> {{ $classe->eleves_count }} eleve(s)
                            </span>
                        </td>
                        <td style="padding:14px 16px;">
                            <div style="display:flex;justify-content:flex-end;gap:6px;flex-wrap:wrap;">
                                <a href="{{ route('classes.show', ['classe' => $classe, 'date' => request()->query('date')]) }}" style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:999px;background:rgba(0,158,96,0.10);color:#009e60;font-size:12px;font-weight:600;text-decoration:none;border:1px solid rgba(0,158,96,0.20);">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                                <a href="{{ route('classes.liste.pdf', ['classe' => $classe, 'date' => request()->query('date')]) }}" style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:999px;background:rgba(0,0,0,0.06);color:#475569;font-size:12px;font-weight:600;text-decoration:none;border:1px solid rgba(0,0,0,0.10);">
                                    <i class="bi bi-printer"></i> Feuille d'appel
                                </a>
                                @if($canManageAcademicData)
                                <a href="{{ route('classes.edit', ['classe' => $classe, 'date' => request()->query('date')]) }}" style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:999px;background:rgba(247,209,23,0.15);color:#92400e;font-size:12px;font-weight:600;text-decoration:none;border:1px solid rgba(247,209,23,0.30);">
                                    <i class="bi bi-pencil"></i> Modifier
                                </a>
                                <form action="{{ route('classes.destroy', $classe) }}" method="POST" onsubmit="return confirm('Supprimer cette classe ?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:999px;background:rgba(239,68,68,0.10);color:#ef4444;font-size:12px;font-weight:600;border:1px solid rgba(239,68,68,0.20);cursor:pointer;">
                                        <i class="bi bi-trash"></i> Supprimer
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="padding:3rem;text-align:center;">
                            <div style="display:flex;flex-direction:column;align-items:center;gap:10px;">
                                <div style="width:64px;height:64px;border-radius:50%;background:rgba(0,158,96,0.10);display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-building" style="font-size:1.6rem;color:#009e60;"></i>
                                </div>
                                <p style="font-size:14px;font-weight:600;color:#0f172a;">Aucune classe enregistree</p>
                                <p style="font-size:13px;color:#64748b;">Creez une classe pour commencer a organiser les inscriptions et les matieres.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($classes->hasPages())
        <div style="padding:1rem 1.5rem;border-top:1px solid rgba(0,158,96,0.12);">
            {{ $classes->links() }}
        </div>
        @endif
    </section>
</div>
@endsection

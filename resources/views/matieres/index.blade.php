@extends('layouts.app')
@section('title', 'Matieres')
@section('breadcrumb', 'Administration / Matieres')
@section('content')
<div class="space-y-6">

    {{-- HERO --}}
    <section style="background:linear-gradient(135deg,#000000 0%,#009e60 60%,#006400 100%);border-radius:32px;padding:2rem;position:relative;overflow:hidden;box-shadow:0 28px 80px rgba(0,0,0,0.28);">
        <div style="position:absolute;top:-60px;right:-60px;width:220px;height:220px;border-radius:50%;background:rgba(247,209,23,0.10);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-40px;left:-40px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.06);pointer-events:none;"></div>

        <div class="grid gap-8 xl:grid-cols-2 relative">
            <div>
                <p style="font-size:11px;font-weight:700;letter-spacing:0.3em;text-transform:uppercase;color:#f7d117;">Administration</p>
                <h2 style="margin-top:12px;font-size:1.9rem;font-weight:700;color:#fff;line-height:1.15;">Un catalogue matieres lisible pour preparer l'assignation et les coefficients.</h2>
                <p style="margin-top:10px;font-size:14px;color:rgba(255,255,255,0.72);line-height:1.7;">Organisez les matieres du programme, visualisez leur diffusion dans les classes et passez rapidement a l'assignation annuelle.</p>

                <div style="margin-top:18px;display:flex;flex-wrap:wrap;gap:10px;">
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:#fff;font-size:12px;font-weight:600;">
                        <i class="bi bi-book"></i>{{ $matieres->total() }} matiere(s)
                    </span>
                    @if(isset($annee) && $annee)
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;background:rgba(247,209,23,0.20);border:1px solid rgba(247,209,23,0.35);color:#f7d117;font-size:12px;font-weight:600;">
                        <i class="bi bi-calendar2-week"></i>{{ $annee->libelle }}
                    </span>
                    @endif
                </div>

                <div style="margin-top:18px;display:flex;flex-wrap:wrap;gap:10px;">
                    @if($canManageAcademicData)
                    <a href="{{ route('matieres.create') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:999px;background:#f7d117;color:#000;font-size:13px;font-weight:700;text-decoration:none;">
                        <i class="bi bi-plus-lg"></i> Nouvelle matiere
                    </a>
                    <a href="{{ route('matieres.assigner') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:999px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.30);color:#fff;font-size:13px;font-weight:600;text-decoration:none;">
                        <i class="bi bi-diagram-3"></i> Assigner aux classes
                    </a>
                    @endif
                </div>
            </div>

            <div style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.22);border-radius:24px;padding:1.5rem;backdrop-filter:blur(12px);display:flex;flex-direction:column;gap:1.2rem;">
                <div>
                    <p style="font-size:10px;font-weight:700;letter-spacing:0.28em;text-transform:uppercase;color:#f7d117;">Pedagogie</p>
                    <h3 style="margin-top:10px;font-size:1.1rem;font-weight:600;color:#fff;line-height:1.4;">Le catalogue reste connecte aux classes pour faciliter la saisie des notes.</h3>
                    <p style="margin-top:8px;font-size:13px;color:rgba(255,255,255,0.80);line-height:1.7;">Chaque entree sert a composer les programmes par classe et a alimenter les ecrans de notes.</p>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="background:rgba(0,0,0,0.25);border:1px solid rgba(247,209,23,0.25);border-radius:18px;padding:14px;">
                        <p style="font-size:10px;text-transform:uppercase;letter-spacing:0.2em;color:#f7d117;">Catalogue</p>
                        <p style="font-size:1.4rem;font-weight:700;color:#fff;margin-top:6px;">Global</p>
                        <p style="font-size:12px;color:rgba(255,255,255,0.70);margin-top:4px;">base unique et claire</p>
                    </div>
                    <div style="background:rgba(0,0,0,0.25);border:1px solid rgba(247,209,23,0.25);border-radius:18px;padding:14px;">
                        <p style="font-size:10px;text-transform:uppercase;letter-spacing:0.2em;color:#f7d117;">Assignation</p>
                        <p style="font-size:1.4rem;font-weight:700;color:#fff;margin-top:6px;">Rapide</p>
                        <p style="font-size:12px;color:rgba(255,255,255,0.70);margin-top:4px;">par classe et coefficient</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- TABLE --}}
    <section style="background:linear-gradient(180deg,rgba(255,255,255,0.98),rgba(240,248,244,0.90));border-radius:28px;border:1px solid rgba(0,158,96,0.18);box-shadow:0 20px 50px rgba(0,0,0,0.10);overflow:hidden;">

        <div style="padding:1.2rem 1.5rem;border-bottom:1px solid rgba(0,158,96,0.15);background:linear-gradient(90deg,rgba(0,158,96,0.06),rgba(247,209,23,0.04));display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;">
            <div>
                <h4 style="font-size:15px;font-weight:700;color:#0f172a;">Matieres enregistrees</h4>
                <p style="font-size:12px;color:#64748b;margin-top:4px;">Acces simple a l'edition du catalogue et a la suppression des doublons.</p>
            </div>
            <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:999px;background:rgba(0,158,96,0.10);color:#009e60;font-size:12px;font-weight:700;border:1px solid rgba(0,158,96,0.20);">
                <i class="bi bi-book"></i> {{ $matieres->total() }} matiere(s)
            </span>
        </div>

        {{-- Cartes mobiles --}}
        <div class="sm:hidden" style="padding:1rem;display:flex;flex-direction:column;gap:10px;">
            @forelse($matieres as $matiere)
            <div style="background:#fff;border:1px solid rgba(0,158,96,0.12);border-radius:20px;padding:14px;box-shadow:0 4px 12px rgba(0,0,0,0.05);">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:48px;height:48px;border-radius:16px;background:rgba(0,158,96,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-journal-bookmark-fill" style="color:#009e60;font-size:1.2rem;"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <p style="font-size:15px;font-weight:700;color:#0f172a;">{{ $matiere->nom_matiere }}</p>
                        <div style="margin-top:6px;">
                            <span style="padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;background:rgba(0,158,96,0.12);color:#009e60;">
                                {{ $matiere->classes_count }} classe(s)
                            </span>
                        </div>
                    </div>
                </div>
                @if($canManageAcademicData)
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(0,158,96,0.10);display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <a href="{{ route('matieres.edit', $matiere) }}" style="display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px;border-radius:14px;background:rgba(247,209,23,0.15);color:#92400e;font-size:12px;font-weight:600;text-decoration:none;">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                    <form action="{{ route('matieres.destroy', $matiere) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="width:100%;display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px;border-radius:14px;background:rgba(239,68,68,0.10);color:#ef4444;font-size:12px;font-weight:600;border:none;cursor:pointer;">
                            <i class="bi bi-trash"></i> Supprimer
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @empty
            <div style="padding:3rem;text-align:center;">
                <div style="width:64px;height:64px;border-radius:50%;background:rgba(0,158,96,0.10);display:flex;align-items:center;justify-content:center;margin:0 auto;">
                    <i class="bi bi-journal-bookmark" style="font-size:1.6rem;color:#009e60;"></i>
                </div>
                <p style="margin-top:12px;font-size:14px;font-weight:600;color:#0f172a;">Aucune matiere enregistree</p>
                <p style="font-size:13px;color:#64748b;margin-top:4px;">Commencez par creer les matieres du catalogue avant de les assigner aux classes.</p>
            </div>
            @endforelse
        </div>

        {{-- Tableau desktop --}}
        <div class="hidden sm:block" style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:linear-gradient(90deg,rgba(0,158,96,0.07),rgba(247,209,23,0.04));">
                        <th style="padding:12px 16px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:#64748b;font-weight:700;border-bottom:1px solid rgba(0,158,96,0.12);">Matiere</th>
                        <th style="padding:12px 16px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:#64748b;font-weight:700;border-bottom:1px solid rgba(0,158,96,0.12);">Classes concernees</th>
                        <th style="padding:12px 16px;text-align:right;font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:#64748b;font-weight:700;border-bottom:1px solid rgba(0,158,96,0.12);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($matieres as $matiere)
                    <tr style="border-bottom:1px solid rgba(0,158,96,0.08);transition:background 0.15s;" onmouseover="this.style.background='rgba(0,158,96,0.04)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:14px 16px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="width:40px;height:40px;border-radius:14px;background:rgba(0,158,96,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="bi bi-journal-bookmark-fill" style="color:#009e60;font-size:1rem;"></i>
                                </div>
                                <div>
                                    <p style="font-size:14px;font-weight:700;color:#0f172a;">{{ $matiere->nom_matiere }}</p>
                                    <p style="font-size:11px;color:#94a3b8;margin-top:2px;">Catalogue pedagogique</p>
                                </div>
                            </div>
                        </td>
                        <td style="padding:14px 16px;">
                            <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;background:rgba(0,158,96,0.12);color:#009e60;">
                                {{ $matiere->classes_count }} classe(s)
                            </span>
                        </td>
                        <td style="padding:14px 16px;">
                            <div style="display:flex;justify-content:flex-end;gap:6px;flex-wrap:wrap;">
                                @if($canManageAcademicData)
                                <a href="{{ route('matieres.edit', $matiere) }}" style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:999px;background:rgba(247,209,23,0.15);color:#92400e;font-size:12px;font-weight:600;text-decoration:none;border:1px solid rgba(247,209,23,0.30);">
                                    <i class="bi bi-pencil"></i> Modifier
                                </a>
                                <form action="{{ route('matieres.destroy', $matiere) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')" style="display:inline;">
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
                                    <i class="bi bi-journal-bookmark" style="font-size:1.6rem;color:#009e60;"></i>
                                </div>
                                <p style="font-size:14px;font-weight:600;color:#0f172a;">Aucune matiere enregistree</p>
                                <p style="font-size:13px;color:#64748b;">Commencez par creer les matieres du catalogue avant de les assigner aux classes.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($matieres->hasPages())
        <div style="padding:1rem 1.5rem;border-top:1px solid rgba(0,158,96,0.12);">
            {{ $matieres->links() }}
        </div>
        @endif
    </section>
</div>
@endsection

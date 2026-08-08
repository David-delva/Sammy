@extends('layouts.app')
@section('title', 'Annees academiques')
@section('breadcrumb', 'Administration / Annees academiques')
@section('content')
<div class="space-y-6">

    {{-- HERO --}}
    <section style="background:linear-gradient(135deg,#000000 0%,#009e60 60%,#006400 100%);border-radius:32px;padding:2rem;position:relative;overflow:hidden;box-shadow:0 28px 80px rgba(0,0,0,0.28);">
        <div style="position:absolute;top:-60px;right:-60px;width:220px;height:220px;border-radius:50%;background:rgba(247,209,23,0.10);pointer-events:none;"></div>
        <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:16px;position:relative;">
            <div>
                <p style="font-size:11px;font-weight:700;letter-spacing:0.3em;text-transform:uppercase;color:#f7d117;">Administration</p>
                <h2 style="margin-top:10px;font-size:1.8rem;font-weight:700;color:#fff;line-height:1.2;">Annees academiques</h2>
                <p style="margin-top:8px;font-size:14px;color:rgba(255,255,255,0.72);max-width:520px;line-height:1.7;">Gerez les cycles scolaires, definissez l'annee active et accordez des derogations d'ecriture au secretariat.</p>
                <div style="margin-top:16px;">
                    <a href="{{ route('annees.create') }}" style="display:inline-flex;align-items:center;gap:6px;padding:9px 20px;border-radius:999px;background:#f7d117;color:#000;font-size:13px;font-weight:700;text-decoration:none;">
                        <i class="bi bi-plus-lg"></i> Nouvelle annee
                    </a>
                </div>
            </div>
            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                <div style="background:rgba(0,0,0,0.28);border:1px solid rgba(247,209,23,0.22);border-radius:18px;padding:14px 20px;min-width:120px;">
                    <p style="font-size:10px;text-transform:uppercase;letter-spacing:0.2em;color:#f7d117;">Total</p>
                    <p style="font-size:2rem;font-weight:700;color:#fff;margin-top:6px;">{{ $annees->count() }}</p>
                    <p style="font-size:12px;color:rgba(255,255,255,0.65);margin-top:4px;">annees enregistrees</p>
                </div>
                <div style="background:rgba(0,0,0,0.28);border:1px solid rgba(247,209,23,0.22);border-radius:18px;padding:14px 20px;min-width:160px;">
                    <p style="font-size:10px;text-transform:uppercase;letter-spacing:0.2em;color:#f7d117;">Active</p>
                    <p style="font-size:1rem;font-weight:700;color:#fff;margin-top:6px;">{{ optional($annees->firstWhere('active', true))->libelle ?? 'Aucune' }}</p>
                    <p style="font-size:12px;color:rgba(255,255,255,0.65);margin-top:4px;">annee en cours</p>
                </div>
            </div>
        </div>
    </section>

    {{-- DEROGATIONS --}}
    <section style="background:#fff;border-radius:28px;border:1px solid rgba(0,158,96,0.18);box-shadow:0 12px 40px rgba(0,0,0,0.07);overflow:hidden;">
        <div style="padding:1.2rem 1.5rem;border-bottom:1px solid rgba(0,158,96,0.15);background:linear-gradient(90deg,rgba(0,158,96,0.06),rgba(247,209,23,0.04));display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;">
            <div>
                <h4 style="font-size:15px;font-weight:700;color:#0f172a;">Derogations d'ecriture du secretariat</h4>
                <p style="font-size:12px;color:#64748b;margin-top:4px;">Le secretariat peut modifier l'annee en cours automatiquement. Pour toute autre annee, un admin doit accorder une autorisation.</p>
            </div>
            <span style="padding:5px 14px;border-radius:999px;background:rgba(0,158,96,0.10);color:#009e60;font-size:12px;font-weight:700;border:1px solid rgba(0,158,96,0.20);">{{ $secretariats->count() }} compte(s) secretariat</span>
        </div>

        <div>
            @forelse($annees as $annee)
            @php
                $authorizedUserIds = $authorizedUserIdsByYear[$annee->id] ?? [];
                $isCurrentCalendarYear = $annee->libelle === $currentCalendarLabel;
            @endphp
            <div style="padding:1.2rem 1.5rem;border-bottom:1px solid rgba(0,158,96,0.07);">
                <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px;">
                    <div>
                        <p style="font-size:14px;font-weight:700;color:#0f172a;">{{ $annee->libelle }}</p>
                        <p style="font-size:12px;color:#64748b;margin-top:3px;">{{ $isCurrentCalendarYear ? 'Acces automatique pour le secretariat.' : 'Derogation requise pour le secretariat.' }}</p>
                    </div>
                    <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;background:{{ $isCurrentCalendarYear ? 'rgba(0,158,96,0.12)' : 'rgba(0,0,0,0.06)' }};color:{{ $isCurrentCalendarYear ? '#009e60' : '#64748b' }};border:1px solid {{ $isCurrentCalendarYear ? 'rgba(0,158,96,0.20)' : 'rgba(0,0,0,0.10)' }};">
                        <i class="bi {{ $isCurrentCalendarYear ? 'bi-unlock-fill' : 'bi-lock-fill' }}"></i>
                        {{ $isCurrentCalendarYear ? 'Acces automatique' : 'Derogation requise' }}
                    </span>
                </div>
                <div style="display:grid;gap:10px;" class="lg:grid-cols-2">
                    @forelse($secretariats as $secretariat)
                    @php $isAuthorized = $isCurrentCalendarYear || in_array($secretariat->id, $authorizedUserIds, true); @endphp
                    <div style="border-radius:16px;border:1px solid {{ $isAuthorized ? 'rgba(0,158,96,0.20)' : 'rgba(0,0,0,0.08)' }};background:{{ $isAuthorized ? 'rgba(0,158,96,0.04)' : '#fafafa' }};padding:12px 14px;">
                        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:8px;">
                            <div>
                                <p style="font-size:13px;font-weight:700;color:#0f172a;">{{ $secretariat->name }}</p>
                                <p style="font-size:11px;color:#64748b;">{{ $secretariat->email }}</p>
                            </div>
                            <span style="padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;background:{{ $isAuthorized ? 'rgba(0,158,96,0.12)' : 'rgba(0,0,0,0.06)' }};color:{{ $isAuthorized ? '#009e60' : '#64748b' }};">
                                {{ $isAuthorized ? 'Peut modifier' : 'Lecture seule' }}
                            </span>
                        </div>
                        @if($isCurrentCalendarYear)
                        <p style="margin-top:8px;font-size:11px;color:#64748b;">Aucune action supplementaire necessaire.</p>
                        @elseif($isAuthorized)
                        <form action="{{ route('annees.write-access.destroy', [$annee, $secretariat]) }}" method="POST" style="margin-top:8px;">
                            @csrf @method('DELETE')
                            <button type="submit" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:999px;background:rgba(239,68,68,0.08);color:#ef4444;font-size:11px;font-weight:600;border:1px solid rgba(239,68,68,0.20);cursor:pointer;">
                                <i class="bi bi-shield-x"></i> Retirer l'autorisation
                            </button>
                        </form>
                        @else
                        <form action="{{ route('annees.write-access.store', $annee) }}" method="POST" style="margin-top:8px;">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $secretariat->id }}">
                            <button type="submit" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:999px;background:rgba(0,158,96,0.10);color:#009e60;font-size:11px;font-weight:600;border:1px solid rgba(0,158,96,0.20);cursor:pointer;">
                                <i class="bi bi-shield-check"></i> Autoriser cette annee
                            </button>
                        </form>
                        @endif
                    </div>
                    @empty
                    <div style="padding:1rem;border-radius:14px;border:1px dashed rgba(0,158,96,0.20);background:rgba(0,158,96,0.03);font-size:13px;color:#64748b;" class="lg:col-span-2">
                        Aucun compte secretariat disponible.
                    </div>
                    @endforelse
                </div>
            </div>
            @empty
            <div style="padding:2rem;text-align:center;font-size:13px;color:#64748b;">Aucune annee academique enregistree.</div>
            @endforelse
        </div>
    </section>

    {{-- LISTE --}}
    <section style="background:#fff;border-radius:28px;border:1px solid rgba(0,158,96,0.18);box-shadow:0 12px 40px rgba(0,0,0,0.07);overflow:hidden;">
        <div style="padding:1.2rem 1.5rem;border-bottom:1px solid rgba(0,158,96,0.15);background:linear-gradient(90deg,rgba(0,158,96,0.06),rgba(247,209,23,0.04));display:flex;align-items:center;justify-content:space-between;gap:12px;">
            <h4 style="font-size:15px;font-weight:700;color:#0f172a;">Liste des annees</h4>
            <span style="padding:5px 14px;border-radius:999px;background:rgba(0,158,96,0.10);color:#009e60;font-size:12px;font-weight:700;border:1px solid rgba(0,158,96,0.20);">{{ $annees->count() }} element(s)</span>
        </div>

        {{-- Mobile --}}
        <div class="sm:hidden">
            @forelse($annees as $annee)
            <div style="padding:14px 16px;border-bottom:1px solid rgba(0,158,96,0.07);display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:14px;background:{{ $annee->active ? 'rgba(0,158,96,0.12)' : 'rgba(0,0,0,0.06)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi {{ $annee->active ? 'bi-calendar-check' : 'bi-calendar-x' }}" style="color:{{ $annee->active ? '#009e60' : '#94a3b8' }};"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:14px;font-weight:700;color:#0f172a;">{{ $annee->libelle }}</p>
                    <span style="display:inline-flex;margin-top:4px;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;background:{{ $annee->active ? 'rgba(0,158,96,0.12)' : 'rgba(0,0,0,0.06)' }};color:{{ $annee->active ? '#009e60' : '#64748b' }};">{{ $annee->active ? 'Active' : 'Inactive' }}</span>
                </div>
                <div style="display:flex;gap:6px;">
                    <a href="{{ route('annees.edit', $annee) }}" style="padding:6px 10px;border-radius:10px;background:rgba(247,209,23,0.15);color:#92400e;font-size:12px;font-weight:600;text-decoration:none;border:1px solid rgba(247,209,23,0.25);"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('annees.destroy', $annee) }}" method="POST" onsubmit="return confirm('Supprimer ?')" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" style="padding:6px 10px;border-radius:10px;background:rgba(239,68,68,0.08);color:#ef4444;font-size:12px;border:1px solid rgba(239,68,68,0.18);cursor:pointer;"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <div style="padding:3rem;text-align:center;font-size:13px;color:#64748b;">Aucune annee enregistree.</div>
            @endforelse
        </div>

        {{-- Desktop --}}
        <div class="hidden sm:block" style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:linear-gradient(90deg,rgba(0,158,96,0.07),rgba(247,209,23,0.04));">
                        <th style="padding:12px 16px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:#64748b;font-weight:700;border-bottom:1px solid rgba(0,158,96,0.12);">Libelle</th>
                        <th style="padding:12px 16px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:#64748b;font-weight:700;border-bottom:1px solid rgba(0,158,96,0.12);">Statut</th>
                        <th style="padding:12px 16px;text-align:right;font-size:10px;text-transform:uppercase;letter-spacing:0.18em;color:#64748b;font-weight:700;border-bottom:1px solid rgba(0,158,96,0.12);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($annees as $annee)
                    <tr style="border-bottom:1px solid rgba(0,158,96,0.07);" onmouseover="this.style.background='rgba(0,158,96,0.03)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:14px 16px;">
                            <p style="font-size:14px;font-weight:700;color:#0f172a;">{{ $annee->libelle }}</p>
                            <p style="font-size:11px;color:#94a3b8;margin-top:2px;">Cycle scolaire</p>
                        </td>
                        <td style="padding:14px 16px;">
                            <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;background:{{ $annee->active ? 'rgba(0,158,96,0.12)' : 'rgba(0,0,0,0.06)' }};color:{{ $annee->active ? '#009e60' : '#64748b' }};border:1px solid {{ $annee->active ? 'rgba(0,158,96,0.20)' : 'rgba(0,0,0,0.10)' }};">
                                <i class="bi {{ $annee->active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                {{ $annee->active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="padding:14px 16px;">
                            <div style="display:flex;justify-content:flex-end;gap:6px;">
                                <a href="{{ route('annees.edit', $annee) }}" style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:999px;background:rgba(247,209,23,0.15);color:#92400e;font-size:12px;font-weight:600;text-decoration:none;border:1px solid rgba(247,209,23,0.30);">
                                    <i class="bi bi-pencil"></i> Modifier
                                </a>
                                <form action="{{ route('annees.destroy', $annee) }}" method="POST" onsubmit="return confirm('Supprimer cette annee ?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:999px;background:rgba(239,68,68,0.08);color:#ef4444;font-size:12px;font-weight:600;border:1px solid rgba(239,68,68,0.18);cursor:pointer;">
                                        <i class="bi bi-trash"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="padding:3rem;text-align:center;font-size:13px;color:#64748b;">Aucune annee enregistree.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

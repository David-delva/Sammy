<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gestion Scolaire')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* Palette & theme */
        :root{
            --brand-50: #e9f2ff;
            --brand-100: #cfe3ff;
            --brand-500: #0d6efd; /* primary */
            --brand-600: #0b5ed7;
            --accent: #6f42c1;
            --muted: #6c757d;
            --surface: #f8fafc;
            --card-bg: #ffffff;
            --glass: rgba(255,255,255,0.6);
            --shadow: 0 8px 24px rgba(13,110,253,0.06);
        }

        /* Page background subtle gradient */
        body{background:linear-gradient(180deg, var(--brand-50) 0%, var(--surface) 100%);}

        /* Sidebar */
        .app-sidebar{width:240px;background:linear-gradient(180deg,var(--brand-500),var(--brand-600));min-height:100vh;color:#fff;padding-top:1.25rem}
        .app-sidebar .nav-link{color:rgba(255,255,255,0.95);transition:all .18s ease}
        .app-sidebar .nav-link:hover{transform:translateX(6px);opacity:0.98}
        .app-sidebar .nav-link.active{background:rgba(255,255,255,0.06);box-shadow:inset 4px 0 0 rgba(255,255,255,0.06)}

        /* Topbar */
        .app-topbar{background:#fff;border-bottom:1px solid rgba(15,23,42,0.04)}

        /* Cards and surfaces */
        .card{border:0;border-radius:0.6rem;background:var(--card-bg);box-shadow:var(--shadow);transition:transform .22s ease, box-shadow .22s ease}
        .card:hover{transform:translateY(-6px);box-shadow:0 20px 40px rgba(2,6,23,0.08)}

        /* Tables */
        .table thead th{border-bottom:2px solid rgba(2,6,23,0.04);color:var(--muted)}
        tbody tr{transition:background .18s ease, transform .18s ease}
        tbody tr:hover{background:rgba(13,110,253,0.03);transform:translateY(-2px)}

        /* Buttons */
        .btn-primary{
            background:linear-gradient(180deg,var(--brand-500),var(--brand-600));
            border:none;color:#fff;box-shadow:0 6px 18px rgba(13,110,253,0.12);transition:transform .14s ease,box-shadow .14s ease}
        .btn-primary:hover{transform:translateY(-2px);box-shadow:0 24px 40px rgba(13,110,253,0.12)}
        .btn-outline-secondary{border-color:rgba(2,6,23,0.06);color:var(--muted)}

        /* Small UI helpers */
        .brand-logo{font-weight:700;letter-spacing:0.2px}
        .page-title{font-size:1.125rem;font-weight:600}

        /* Animations */
        @keyframes fadeUp { from {opacity:0; transform:translateY(10px)} to {opacity:1; transform:translateY(0)} }
        @keyframes pulse { 0% {transform:scale(1)} 50% {transform:scale(1.02)} 100% {transform:scale(1)} }

        .main-content{opacity:0;transform:translateY(8px)}
        .main-content.animate-in{animation:fadeUp .55s cubic-bezier(.2,.9,.2,1) forwards}

        .reveal { opacity:0; transform: translateY(8px); transition: all .6s cubic-bezier(.2,.9,.2,1) }
        .reveal.visible { opacity:1; transform: translateY(0) }

        .count { font-variant-numeric: tabular-nums; font-weight:700 }

        .card-cta{transition:transform .18s ease}
        .card-cta:hover{transform:translateY(-4px)}

        /* Focus accessibility */
        a:focus, button:focus, input:focus{outline:3px solid rgba(13,110,253,0.12);outline-offset:2px}

        /* Responsive sidebar */
        @media (max-width: 991px){ .app-sidebar{position:fixed;left:-260px;z-index:1040;transition:all .25s} .app-sidebar.show{left:0;} }
    </style>
</head>
<body>
    <div class="d-flex">
        {{-- Sidebar --}}
        <aside class="app-sidebar p-3 d-none d-lg-block">
            <div class="d-flex align-items-center mb-4">
                <i class="bi bi-mortarboard-fill me-2 fs-4"></i>
                <span class="brand-logo">École Technique</span>
            </div>
            <nav class="nav flex-column">
                @auth
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Tableau de bord</a>
                    <a class="nav-link {{ request()->routeIs('eleves.*') ? 'active' : '' }}" href="{{ route('eleves.index') }}"><i class="bi bi-people-fill me-2"></i> Élèves</a>
                    @if(auth()->user()->role === 'admin')
                        <a class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}" href="{{ route('classes.index') }}"><i class="bi bi-building me-2"></i> Classes</a>
                        <a class="nav-link {{ request()->routeIs('matieres.*') ? 'active' : '' }}" href="{{ route('matieres.index') }}"><i class="bi bi-book-fill me-2"></i> Matières</a>

                            <a class="nav-link {{ request()->routeIs('annees.*') ? 'active' : '' }}" href="{{ route('annees.index') }}"><i class="bi bi-calendar-event-fill me-2"></i> Années</a>

                        <a class="nav-link {{ request()->routeIs('notes.*') ? 'active' : '' }}" href="{{ route('notes.index') }}"><i class="bi bi-clipboard-data me-2"></i> Notes</a>
                    @endif
                @endauth
            </nav>
            <div class="mt-auto pt-4 text-white-50 small">© {{ date('Y') }} École Technique</div>
        </aside>

        <div class="flex-grow-1">
            <header class="app-topbar d-flex align-items-center justify-content-between px-3 py-2">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-sm btn-outline-secondary d-lg-none" id="toggleSidebar"><i class="bi bi-list"></i></button>
                    <h1 class="h5 mb-0 page-title">@yield('title', 'Gestion Scolaire')</h1>
                </div>
                <div class="d-flex align-items-center">
                    @auth
                        <div class="dropdown">
                            <a class="d-flex align-items-center text-decoration-none" href="#" id="userMenu" data-bs-toggle="dropdown">
                                <div class="me-2 text-muted">{{ auth()->user()->name }}</div>
                                <i class="bi bi-person-circle fs-4"></i>
                            </a>
                        {{-- Academic year selector --}}
                        <div class="ms-3 d-none d-md-inline-block">
                            <select id="academicYearSelect" class="form-select form-select-sm">
                                <option value="">Contexte: Aujourd'hui</option>
                                @if(!empty($academicYears))
                                    @foreach($academicYears as $y)
                                        @php
                                            $parts = explode('-', $y->libelle);
                                            $startYear = $parts[0] ?? null;
                                            $valueDate = $startYear ? $startYear . '-09-01' : '';
                                        @endphp
                                        <option value="{{ $valueDate }}" {{ (isset($currentAcademicLabel) && $currentAcademicLabel == $y->libelle) || (request()->query('date') && \App\Models\AnneeAcademique::labelForDate(request()->query('date')) == $y->libelle) ? 'selected' : '' }}>
                                            {{ $y->libelle }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Déconnexion</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endauth
                </div>
            </header>

            <main class="p-4 main-content">
                @if(isset($isCurrentAcademicYear) && !$isCurrentAcademicYear)
                    <div class="alert alert-info border-0 rounded-0 mb-4 py-2 text-center shadow-sm">
                        <i class="bi bi-info-circle me-2"></i>
                        Vous consultez les données de l'année <strong>{{ $currentAcademicYear->libelle ?? 'Inconnue' }}</strong>.
                        <span class="d-none d-md-inline">Le mode modification est désactivé.</span>
                        <a href="{{ url()->current() }}?date={{ date('Y-m-d') }}" class="alert-link ms-2">Retourner à aujourd'hui</a>
                    </div>
                @endif
                <div class="container-fluid">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle for small screens
        document.getElementById('toggleSidebar')?.addEventListener('click', function(){
            document.querySelector('.app-sidebar')?.classList.toggle('show');
        });

        // Utilities
        function animateCount(el, to, duration = 900) {
            const start = 0;
            const range = to - start;
            const startTime = performance.now();
            function step(now) {
                const progress = Math.min((now - startTime) / duration, 1);
                const value = Math.floor(start + range * progress);
                el.textContent = value;
                if (progress < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        }

        document.addEventListener('DOMContentLoaded', function(){
            const main = document.querySelector('.main-content');
            if(main){
                setTimeout(()=> main.classList.add('animate-in'), 80);
            }

            // enable bootstrap tooltips
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(t){
                new bootstrap.Tooltip(t);
            });

            // reveal animation on scroll
            const observer = new IntersectionObserver((entries)=>{
                entries.forEach(e => {
                    if(e.isIntersecting){
                        e.target.classList.add('visible');
                        observer.unobserve(e.target);
                    }
                });
            }, {threshold:0.08});

            document.querySelectorAll('.reveal, .card').forEach(card => {
                observer.observe(card);
            });

            // animate numbers with data-count attribute
            document.querySelectorAll('[data-count]').forEach(el => {
                const to = parseInt(el.getAttribute('data-count')) || parseInt(el.textContent) || 0;
                animateCount(el, to, 1000 + Math.min(800, to * 10));
            });

            // make alerts appear as subtle toasts
            document.querySelectorAll('.alert-dismissible').forEach(alert => {
                alert.classList.add('reveal');
            });

            // Academic year select change handling
            const sel = document.getElementById('academicYearSelect');
            if(sel){
                sel.addEventListener('change', function(){
                    const dateVal = this.value;
                    const url = new URL(window.location.href);
                    if(dateVal){
                        url.searchParams.set('date', dateVal);
                    } else {
                        url.searchParams.delete('date');
                    }
                    // navigate preserving path
                    window.location.href = url.toString();
                });
            }
        });
    </script>
</body>
</html>

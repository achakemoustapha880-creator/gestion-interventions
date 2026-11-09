<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titre', 'Interventions') – Gestion des interventions</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ===== Palette : modifiez --accent pour changer la couleur de toute l'application ===== */
        :root {
            --accent: #1f5fc6;
            --accent-dark: #184ea3;
            --page-bg: #2b69cf;
            --soft: #eef2f9;
            --border: #e3e9f4;
            --text: #1b2a4a;
            --muted: #6b7a99;

            --bs-primary: #1f5fc6;
            --bs-primary-rgb: 31, 95, 198;
            --bs-primary-bg-subtle: #e1eafa;
            --bs-primary-border-subtle: #b9cff3;
            --bs-primary-text-emphasis: #174a9c;
            --bs-link-color: #1f5fc6;
            --bs-link-hover-color: #184ea3;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif; font-size: .93rem;
            background: var(--page-bg); color: var(--text); padding: 22px;
        }
        a { text-decoration: none; }

        /* ===== Grande carte blanche arrondie ===== */
        .app-shell {
            display: flex; background: #fff; border-radius: 36px; overflow: hidden;
            height: calc(100vh - 44px); height: calc(100dvh - 44px);
            box-shadow: 0 20px 50px rgba(8, 30, 80, .25);
        }

        /* ===== Menu latéral ===== */
        .side { width: 260px; flex-shrink: 0; display: flex; flex-direction: column; background: #fff; transition: transform .25s; }
        .brand { display: flex; align-items: center; gap: .7rem; padding: 1.4rem 1.6rem; color: var(--accent); flex-shrink: 0; }
        .brand i { font-size: 1.9rem; }
        .brand .nom { font-weight: 700; font-size: 1.35rem; line-height: 1; }
        .brand small { display: block; color: var(--muted); font-size: .7rem; margin-top: 3px; font-weight: 500; }

        .side-body {
            flex: 1; min-height: 0; display: flex; flex-direction: column;
            background: var(--accent); border-top-right-radius: 64px; padding-top: 1.8rem;
        }
        .btn-new {
            display: block; margin: 0 1.5rem 1.4rem; padding: .65rem 1rem; text-align: center;
            background: #fff; color: var(--accent); font-weight: 600; border-radius: 50px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .12); transition: transform .15s;
        }
        .btn-new:hover { transform: translateY(-1px); color: var(--accent-dark); }

        .side nav { flex: 1; min-height: 0; overflow-y: auto; padding: 0 1.1rem; }
        .menu-link {
            display: flex; align-items: center; gap: .85rem; padding: .6rem 1rem; margin-bottom: .15rem;
            color: #e4edfc; border-radius: .75rem; font-size: .9rem; transition: background .15s;
        }
        .menu-link i { font-size: 1.1rem; width: 1.3rem; text-align: center; }
        .menu-link:hover { background: rgba(255, 255, 255, .12); color: #fff; }
        .menu-link.active { background: rgba(255, 255, 255, .2); color: #fff; font-weight: 600; }

        .user-box { flex-shrink: 0; padding: 1rem 1.3rem 1.2rem; border-top: 1px solid rgba(255, 255, 255, .18); }
        .avatar {
            width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;
            background: #fff; color: var(--accent); font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }
        .btn-logout { background: rgba(255, 255, 255, .14); color: #fff; border: 0; }
        .btn-logout:hover { background: rgba(255, 255, 255, .24); color: #fff; }

        /* ===== Zone principale ===== */
        .main { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .topbar { display: flex; align-items: center; gap: 1rem; padding: 1.2rem 1.8rem; flex-shrink: 0; }
        .page-title { font-size: 1.55rem; font-weight: 700; color: var(--accent); margin: 0; }
        .date-chip { background: var(--soft); border-radius: 50px; padding: .45rem 1rem; font-size: .83rem; color: var(--muted); }
        .avatar-top {
            width: 38px; height: 38px; border-radius: 50%; background: var(--accent); color: #fff; font-weight: 600;
            display: flex; align-items: center; justify-content: center;
        }
        .content { flex: 1; overflow-y: auto; padding: 0 1.8rem 1.8rem; }

        /* ===== Composants ===== */
        .card { background: #fff; border: 1px solid var(--border); border-radius: 18px; box-shadow: 0 2px 8px rgba(31, 95, 198, .06); }
        .card-header { border-bottom: 1px solid var(--border); border-radius: 18px 18px 0 0 !important; padding: 1rem 1.3rem; }
        .card-footer { border-top: 1px solid var(--border); border-radius: 0 0 18px 18px !important; }
        .stat-icon {
            width: 52px; height: 52px; border-radius: 15px; font-size: 1.5rem;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }

        .table > :not(caption) > * > * { padding: .85rem 1rem; vertical-align: middle; border-color: var(--border); }
        .table thead th {
            background: var(--soft); color: var(--muted); font-size: .72rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .05em; border-bottom: 0;
        }
        .table tbody a:not(.btn):not(.btn-icon) { color: var(--text); font-weight: 500; }

        .btn { border-radius: 50px; font-weight: 500; }
        .btn-sm { padding-left: .9rem; padding-right: .9rem; }
        .btn-primary {
            --bs-btn-bg: var(--accent); --bs-btn-border-color: var(--accent);
            --bs-btn-hover-bg: var(--accent-dark); --bs-btn-hover-border-color: var(--accent-dark);
            --bs-btn-active-bg: #123d82; --bs-btn-active-border-color: #123d82;
        }
        .btn-outline-primary {
            --bs-btn-color: var(--accent); --bs-btn-border-color: var(--accent);
            --bs-btn-hover-bg: var(--accent); --bs-btn-hover-border-color: var(--accent);
            --bs-btn-active-bg: var(--accent); --bs-btn-active-border-color: var(--accent);
        }
        .form-control, .form-select { border-radius: 12px; border-color: #d5deee; background-color: #fbfcff; padding: .55rem .85rem; }
        .form-control:focus, .form-select:focus { border-color: var(--accent); box-shadow: 0 0 0 .2rem rgba(31, 95, 198, .15); }
        .form-check-input:checked { background-color: var(--accent); border-color: var(--accent); }

        .nav-pills .nav-link { color: var(--accent); border-radius: 50px; font-weight: 500; }
        .nav-pills .nav-link.active { background: var(--accent); color: #fff; }

        .btn-icon {
            width: 34px; height: 34px; border: 0; border-radius: 10px; font-size: .9rem;
            display: inline-flex; align-items: center; justify-content: center; transition: filter .15s;
        }
        .btn-icon:hover { filter: brightness(.93); }
        .btn-icon.view   { background: #e1eafa; color: #1f5fc6; }
        .btn-icon.edit   { background: #dcfce7; color: #15803d; }
        .btn-icon.delete { background: #fee2e2; color: #dc2626; }
        .btn-icon.start  { background: #fef3c7; color: #b45309; }
        .btn-icon.finish { background: #dcfce7; color: #15803d; }

        .badge { font-weight: 600; padding: .45em .8em; }
        .alert { border: 0; border-radius: 14px; }
        #resultats { transition: opacity .15s; }

        /* Barre de défilement discrète */
        .content::-webkit-scrollbar, .side nav::-webkit-scrollbar { width: 8px; }
        .content::-webkit-scrollbar-thumb { background: #cfd9ec; border-radius: 8px; }
        .side nav::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, .3); border-radius: 8px; }

        /* ===== Mobile ===== */
        .voile { display: none; }
        @media (max-width: 991.98px) {
            body { padding: 0; }
            .app-shell { border-radius: 0; height: 100vh; height: 100dvh; box-shadow: none; }
            .side { position: fixed; inset: 0 auto 0 0; z-index: 1050; transform: translateX(-100%); box-shadow: 8px 0 30px rgba(0, 0, 0, .2); }
            .side.open { transform: none; }
            .side.open ~ .voile { display: block; position: fixed; inset: 0; background: rgba(0, 0, 0, .35); z-index: 1040; }
            .topbar, .content { padding-left: 1rem; padding-right: 1rem; }
        }
    </style>
</head>
<body>

<div class="app-shell">

    <aside class="side" id="sidebar">
        <div class="brand">
            <i class="bi bi-tools"></i>
            <div>
                <div class="nom">Interventions</div>
                <small>Gestion des interventions</small>
            </div>
        </div>

        <div class="side-body">
            <a href="{{ route('interventions.create') }}" class="btn-new">
                <i class="bi bi-plus-lg me-1"></i> Nouvelle intervention
            </a>

            <nav>
                <a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-house-door-fill"></i> Tableau de bord
                </a>
                <a class="menu-link {{ request()->routeIs('interventions.index', 'interventions.show', 'interventions.edit', 'interventions.create') ? 'active' : '' }}"
                   href="{{ route('interventions.index') }}">
                    <i class="bi bi-clipboard2-check-fill"></i> Interventions
                </a>
                <a class="menu-link {{ request()->routeIs('interventions.historique') ? 'active' : '' }}" href="{{ route('interventions.historique') }}">
                    <i class="bi bi-clock-history"></i> Historique
                </a>
                <a class="menu-link {{ request()->routeIs('equipements.*') ? 'active' : '' }}" href="{{ route('equipements.index') }}">
                    <i class="bi bi-pc-display"></i> Équipements
                </a>
                <a class="menu-link {{ request()->routeIs('techniciens.*') ? 'active' : '' }}" href="{{ route('techniciens.index') }}">
                    <i class="bi bi-person-gear"></i> Techniciens
                </a>
                <a class="menu-link {{ request()->routeIs('clients.index', 'clients.show', 'clients.edit', 'clients.create') ? 'active' : '' }}" href="{{ route('clients.index') }}">
                    <i class="bi bi-people-fill"></i> Clients
                </a>

                @can('admin')
                    @php
                        $nbCorbeille = \App\Models\Intervention::onlyTrashed()->count() + \App\Models\Client::onlyTrashed()->count();
                    @endphp

                    <a class="menu-link {{ request()->routeIs('rapports') ? 'active' : '' }}" href="{{ route('rapports') }}">
                        <i class="bi bi-bar-chart-fill"></i> Rapports
                    </a>
                    <a class="menu-link {{ request()->routeIs('utilisateurs.*') ? 'active' : '' }}" href="{{ route('utilisateurs.index') }}">
                        <i class="bi bi-gear-fill"></i> Utilisateurs
                    </a>
                    <a class="menu-link {{ request()->routeIs('interventions.corbeille', 'clients.corbeille') ? 'active' : '' }}" href="{{ route('interventions.corbeille') }}">
                        <i class="bi bi-trash3-fill"></i> Corbeille
                        @if ($nbCorbeille > 0)
                            <span class="badge rounded-pill bg-danger ms-auto">{{ $nbCorbeille }}</span>
                        @endif
                    </a>
                @endcan
            </nav>

            <div class="user-box">
                <a href="{{ route('profil.edit') }}" class="d-flex align-items-center gap-3 mb-3" title="Mon profil">
                    <div class="avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</div>
                    <div class="overflow-hidden">
                        <div class="text-white fw-semibold text-truncate">{{ auth()->user()->name }}</div>
                        <small class="text-truncate d-block" style="color:#cfe0fb">{{ auth()->user()->email }}</small>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-sm btn-logout w-100 text-start">
                        <i class="bi bi-box-arrow-left me-2"></i> Se déconnecter
                    </button>
                </form>
            </div>
        </div>
    </aside>
    <div class="voile" onclick="document.getElementById('sidebar').classList.remove('open')"></div>

    <div class="main">
        <header class="topbar">
            <button class="btn btn-light d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h1 class="page-title">@yield('titre', 'Interventions')</h1>
            <div class="ms-auto d-flex align-items-center gap-3">
                <div class="date-chip d-none d-md-block">
                    <i class="bi bi-calendar3 me-1"></i> {{ now()->locale('fr')->translatedFormat('l d F Y') }}
                </div>
                <a href="{{ route('profil.edit') }}" class="avatar-top" title="Mon profil">
                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </a>
            </div>
        </header>

        <main class="content">
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul></div>
            @endif

            @yield('contenu')
        </main>
    </div>
</div>

<script>
// Recherche en direct : tout formulaire marqué data-live met à jour la zone #resultats
(function () {
    let minuteur, requete;

    function charger(form) {
        const params = new URLSearchParams(new FormData(form));
        const url = form.getAttribute('action').split('?')[0] + '?' + params.toString();
        const zone = document.getElementById('resultats');
        if (!zone) { form.submit(); return; }

        if (requete) requete.abort();
        requete = new AbortController();
        zone.style.opacity = .5;

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: requete.signal })
            .then(r => r.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const nouveau = doc.getElementById('resultats');
                if (nouveau) {
                    zone.innerHTML = nouveau.innerHTML;
                    history.replaceState(null, '', url);
                }
                zone.style.opacity = 1;
            })
            .catch(e => { if (e.name !== 'AbortError') form.submit(); });
    }

    document.addEventListener('input', function (e) {
        const form = e.target.closest('form[data-live]');
        if (!form) return;
        clearTimeout(minuteur);
        minuteur = setTimeout(() => charger(form), 300);
    });

    document.addEventListener('submit', function (e) {
        if (e.target.matches('form[data-live]')) {
            e.preventDefault();
            charger(e.target);
        }
    });
})();
</script>

</body>
</html>
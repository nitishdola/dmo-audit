<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('page_title')@yield('page_title') · @endif PMJAY Assam · Admin</title>

    {{-- Tailwind CSS (CDN) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Google Fonts: Syne + DM Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800;900&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        /* ── Base ── */
        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            display: flex;
        }

        /* ── Sidebar ── */
        #sidebar {
            width: 260px;
            min-height: 100vh;
            background: #0f172a;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 50;
            transition: transform .3s cubic-bezier(.22,1,.36,1);
        }
        #sidebar.collapsed { transform: translateX(-260px); }

        .sidebar-logo {
            padding: 1.5rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.07);
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .sidebar-logo-mark {
            width: 2.25rem; height: 2.25rem;
            border-radius: .625rem;
            background: linear-gradient(135deg, #06b6d4, #3b82f6);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 900;
            font-size: .875rem;
            color: #fff;
            flex-shrink: 0;
        }
        .sidebar-logo-text {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: .9rem;
            color: #fff;
            line-height: 1.2;
        }
        .sidebar-logo-sub {
            font-size: .65rem;
            color: #64748b;
            font-weight: 500;
            letter-spacing: .03em;
        }

        /* Nav sections */
        .nav-section-label {
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #334155;
            padding: 1.25rem 1.25rem .5rem;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .625rem 1rem;
            margin: .125rem .75rem;
            border-radius: .75rem;
            font-size: .8125rem;
            font-weight: 500;
            color: #94a3b8;
            text-decoration: none;
            transition: background .15s, color .15s;
            cursor: pointer;
            position: relative;
        }
        .nav-item:hover { background: rgba(255,255,255,.06); color: #e2e8f0; }
        .nav-item.active {
            background: rgba(6,182,212,.12);
            color: #22d3ee;
        }
        .nav-item.active .nav-icon { color: #06b6d4; }
        .nav-icon {
            width: 1.5rem; height: 1.5rem;
            display: flex; align-items: center; justify-content: center;
            font-size: .8125rem;
            flex-shrink: 0;
            border-radius: .5rem;
        }
        .nav-item.active .nav-icon {
            background: rgba(6,182,212,.15);
        }
        .nav-badge {
            margin-left: auto;
            background: #1e293b;
            color: #64748b;
            font-size: .65rem;
            font-weight: 700;
            padding: .15rem .5rem;
            border-radius: 9999px;
        }
        .nav-item.active .nav-badge {
            background: rgba(6,182,212,.15);
            color: #22d3ee;
        }

        /* Active accent line */
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: -0.75rem;
            top: 50%; transform: translateY(-50%);
            width: 3px; height: 60%;
            border-radius: 0 3px 3px 0;
            background: #06b6d4;
        }

        /* Sidebar footer */
        .sidebar-footer {
            margin-top: auto;
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.07);
        }
        .user-chip {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .625rem .875rem;
            border-radius: .875rem;
            background: rgba(255,255,255,.04);
            cursor: pointer;
            transition: background .15s;
        }
        .user-chip:hover { background: rgba(255,255,255,.08); }
        .user-avatar {
            width: 2rem; height: 2rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, #0ea5e9, #6366f1);
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; font-weight: 800;
            color: #fff;
            flex-shrink: 0;
            font-family: 'Syne', sans-serif;
        }
        .user-name { font-size: .8rem; font-weight: 600; color: #e2e8f0; }
        .user-role { font-size: .65rem; color: #475569; font-weight: 500; }

        /* ── Main area ── */
        #main-wrap {
            margin-left: 260px;
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left .3s cubic-bezier(.22,1,.36,1);
        }
        #main-wrap.expanded { margin-left: 0; }

        /* ── Topbar ── */
        #topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 1.75rem;
            height: 3.75rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 40;
            flex-shrink: 0;
        }
        .topbar-toggle {
            width: 2rem; height: 2rem;
            border-radius: .625rem;
            border: 1.5px solid #e2e8f0;
            background: transparent;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: #64748b;
            transition: background .15s, color .15s;
            flex-shrink: 0;
        }
        .topbar-toggle:hover { background: #f8fafc; color: #1e293b; }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .8rem;
            color: #94a3b8;
        }
        .breadcrumb a { color: #94a3b8; text-decoration: none; transition: color .15s; }
        .breadcrumb a:hover { color: #0ea5e9; }
        .breadcrumb-sep { color: #cbd5e1; font-size: .65rem; }
        .breadcrumb-current { color: #334155; font-weight: 600; }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .topbar-icon-btn {
            width: 2.25rem; height: 2.25rem;
            border-radius: .75rem;
            border: 1.5px solid #e2e8f0;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: #64748b;
            font-size: .875rem;
            transition: background .15s, color .15s;
            position: relative;
            text-decoration: none;
        }
        .topbar-icon-btn:hover { background: #f8fafc; color: #1e293b; }
        .notif-dot {
            position: absolute;
            top: .35rem; right: .35rem;
            width: .45rem; height: .45rem;
            border-radius: 9999px;
            background: #f43f5e;
            border: 1.5px solid #fff;
        }

        .topbar-avatar {
            width: 2.25rem; height: 2.25rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, #0ea5e9, #6366f1);
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; font-weight: 800;
            color: #fff;
            cursor: pointer;
            font-family: 'Syne', sans-serif;
            border: 2px solid #e2e8f0;
            transition: border-color .15s;
        }
        .topbar-avatar:hover { border-color: #0ea5e9; }

        /* ── Page content ── */
        #page-content {
            flex: 1;
            padding: 1.75rem 1.75rem 3rem;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
        }

        /* ── Mobile overlay ── */
        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 49;
            backdrop-filter: blur(2px);
        }
        #sidebar-overlay.visible { display: block; }

        /* ── Mobile breakpoint ── */
        @media (max-width: 1023px) {
            #sidebar { transform: translateX(-260px); }
            #sidebar.mobile-open { transform: translateX(0); }
            #main-wrap { margin-left: 0 !important; }
        }

        /* ── Scrollbar ── */
        #sidebar-nav::-webkit-scrollbar { width: 4px; }
        #sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        #sidebar-nav::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 9999px; }

        /* ── Page-level section titles from yield ── */
        #page-title-area { margin-bottom: .25rem; }
    </style>

    {{-- Per-page CSS --}}
    @yield('pageCss')
</head>
<body>

{{-- ════════════════ SIDEBAR ════════════════ --}}
<aside id="sidebar">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="sidebar-logo-mark">PA</div>
        <div>
            <div class="sidebar-logo-text">PMJAY Assam</div>
            <div class="sidebar-logo-sub">Admin Console</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav id="sidebar-nav" style="flex:1; overflow-y:auto; padding-bottom:1rem;">

        <div class="nav-section-label">Overview</div>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-th-large"></i></div>
            Dashboard
        </a>

        <div class="nav-section-label">Audit Management</div>

        <a href="{{ route('admin.audits.telephonic.index') }}"
           class="nav-item {{ request()->routeIs('admin.audits.telephonic.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-phone-alt"></i></div>
            Telephonic Audits
        </a>

        <a href="{{ route('admin.audits.field.index') }}"
           class="nav-item {{ request()->routeIs('admin.audits.field.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-people-arrows"></i></div>
            Field Visits
        </a>

        <a href="{{ route('admin.audits.live.index') }}"
           class="nav-item {{ request()->routeIs('admin.audits.live.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-hospital-user"></i></div>
            Live Audits
        </a>

        <div class="nav-section-label">Operations</div>

        <a href="{{ route('admin.dmos.index') }}"
           class="nav-item {{ request()->routeIs('admin.dmos.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-user-tie"></i></div>
            DMO Officers
        </a>

        <a href="{{ route('admin.hospitals.index') }}"
           class="nav-item {{ request()->routeIs('admin.hospitals.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-hospital"></i></div>
            Hospitals
        </a>

        <a href="{{ route('admin.districts.index') }}"
           class="nav-item {{ request()->routeIs('admin.districts.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-map-marker-alt"></i></div>
            Districts
        </a>

        <div class="nav-section-label">System</div>

        <a href="{{ route('admin.users.index') }}"
           class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-users"></i></div>
            Users
        </a>

        

    </nav>

    {{-- User chip --}}
    <div class="sidebar-footer">
        <div class="user-chip">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}</div>
            <div style="min-width:0;">
                <div class="user-name truncate">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="user-role">Administrator</div>
            </div>
            <i class="fas fa-ellipsis-v text-slate-600 text-xs ml-auto"></i>
        </div>
    </div>
</aside>

{{-- Mobile overlay --}}
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

{{-- ════════════════ MAIN ════════════════ --}}
<div id="main-wrap">

    {{-- Topbar --}}
    <header id="topbar">

        {{-- Sidebar toggle --}}
        <button class="topbar-toggle" id="sidebar-toggle" onclick="toggleSidebar()" title="Toggle sidebar">
            <i class="fas fa-bars text-xs"></i>
        </button>

        {{-- Breadcrumb / page title --}}
        <div id="page-title-area">
            @yield('main_title')
        </div>

        {{-- Right actions --}}
        <div class="topbar-right">

            {{-- Period selector (only shown on audit dashboard) --}}
            @hasSection('period_selector')
            @yield('period_selector')
            @endif

            {{-- User dropdown trigger --}}
            <div class="topbar-avatar" onclick="toggleUserMenu()" title="{{ auth()->user()->name ?? 'Admin' }}">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
            </div>

            {{-- User dropdown --}}
            <div id="user-menu"
                 style="display:none; position:absolute; top:3.5rem; right:1.25rem; background:#fff; border:1px solid #e2e8f0; border-radius:1rem; box-shadow:0 12px 32px rgba(0,0,0,.12); min-width:200px; z-index:100; overflow:hidden;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid #f1f5f9;">
                    <p style="font-size:.875rem; font-weight:600; color:#1e293b;">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p style="font-size:.75rem; color:#94a3b8; margin-top:.125rem;">{{ auth()->user()->email ?? '' }}</p>
                </div>
                <div style="padding:.5rem;">
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button type="submit"
                                style="display:flex; align-items:center; gap:.75rem; width:100%; padding:.625rem .875rem; border-radius:.75rem; font-size:.8125rem; color:#e11d48; background:none; border:none; cursor:pointer; transition:background .15s; text-align:left;"
                                onmouseover="this.style.background='#fff1f2'" onmouseout="this.style.background='transparent'">
                            <i class="fas fa-sign-out-alt w-4 text-center"></i> Sign out
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </header>

    {{-- Page content --}}
    <main id="page-content">
        @yield('main_content')
    </main>

    {{-- Footer --}}
    <footer style="padding:.875rem 1.75rem; text-align:center; font-size:.7rem; color:#cbd5e1; border-top:1px solid #e2e8f0; background:#fff; flex-shrink:0;">
        PMJAY Assam &nbsp;·&nbsp; Admin Portal &nbsp;·&nbsp; {{ now()->year }}
        &nbsp;·&nbsp; <span style="color:#94a3b8;">Build {{ config('app.version', '1.0') }}</span>
    </footer>

</div>

{{-- ════════════════ JS ════════════════ --}}
<script>
/* ── Sidebar toggle ── */
let sidebarOpen = window.innerWidth >= 1024;

function toggleSidebar() {
    const sidebar  = document.getElementById('sidebar');
    const mainWrap = document.getElementById('main-wrap');
    const overlay  = document.getElementById('sidebar-overlay');

    if (window.innerWidth >= 1024) {
        // Desktop: collapse / expand with margin shift
        sidebarOpen = !sidebarOpen;
        sidebar.classList.toggle('collapsed', !sidebarOpen);
        mainWrap.classList.toggle('expanded', !sidebarOpen);
    } else {
        // Mobile: slide over with backdrop
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('visible');
    }
}

function closeSidebar() {
    document.getElementById('sidebar').classList.remove('mobile-open');
    document.getElementById('sidebar-overlay').classList.remove('visible');
}

/* ── User dropdown ── */
function toggleUserMenu() {
    const menu = document.getElementById('user-menu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function (e) {
    const menu    = document.getElementById('user-menu');
    const avatar  = document.querySelector('.topbar-avatar');
    if (!menu.contains(e.target) && !avatar.contains(e.target)) {
        menu.style.display = 'none';
    }
});

/* ── Active nav highlight on page load ── */
document.querySelectorAll('.nav-item').forEach(item => {
    if (item.href && item.href === window.location.href) {
        item.classList.add('active');
    }
});
</script>

{{-- Per-page JS --}}
@yield('pageJs')

</body>
</html>

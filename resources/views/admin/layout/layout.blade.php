<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('page_title')@yield('page_title') · @endif DMO Audit · Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-w: 252px;
            --topbar-h:  56px;

            /* Core palette */
            --ink:       #0a0f1e;
            --ink-2:     #1a2236;
            --ink-3:     #2d3a52;
            --slate-soft:#f0f3f8;
            --slate-line:#e3e8f0;
            --slate-mid: #8c96ab;
            --white:     #ffffff;

            /* Accent */
            --teal:      #0ea5e9;
            --teal-dim:  rgba(14,165,233,.12);
            --teal-glow: rgba(14,165,233,.25);

            /* Status */
            --green:     #10b981;
            --amber:     #f59e0b;
            --rose:      #f43f5e;
            --violet:    #8b5cf6;

            /* Typography */
            --font-body:    'Plus Jakarta Sans', sans-serif;
            --font-display: 'Instrument Serif', Georgia, serif;

            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 18px;
            --radius-xl: 24px;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }

        body {
            font-family: var(--font-body);
            background: var(--slate-soft);
            color: var(--ink);
            display: flex;
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
        }

        /* ━━━━━━━━━━━━━━━━━━ SIDEBAR ━━━━━━━━━━━━━━━━━━ */
        #sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--ink);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 50;
            transition: transform .3s cubic-bezier(.22,1,.36,1), width .3s;
            border-right: 1px solid rgba(255,255,255,.04);
        }
        #sidebar.collapsed { transform: translateX(calc(-1 * var(--sidebar-w))); }

        /* Logo area */
        .sb-logo {
            height: var(--topbar-h);
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 18px;
            border-bottom: 1px solid rgba(255,255,255,.05);
            flex-shrink: 0;
        }
        .sb-logo-mark {
            width: 32px; height: 32px;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--teal) 0%, #3b82f6 100%);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 800;
            color: #fff;
            letter-spacing: -.5px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px var(--teal-glow);
        }
        .sb-logo-title {
            font-size: 13px; font-weight: 700;
            color: #fff; line-height: 1.2;
            letter-spacing: -.01em;
        }
        .sb-logo-sub {
            font-size: 10px; color: var(--slate-mid);
            font-weight: 500; letter-spacing: .02em;
        }

        /* Nav scroll area */
        #sb-nav {
            flex: 1;
            overflow-y: auto;
            padding: 10px 0 12px;
        }
        #sb-nav::-webkit-scrollbar { width: 3px; }
        #sb-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.08); border-radius: 9999px; }

        .nav-section {
            font-size: 9.5px; font-weight: 700;
            letter-spacing: .14em; text-transform: uppercase;
            color: rgba(255,255,255,.2);
            padding: 18px 20px 6px;
        }
        .nav-section:first-child { padding-top: 10px; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            margin: 1px 10px;
            border-radius: var(--radius-md);
            font-size: 12.5px;
            font-weight: 500;
            color: rgba(255,255,255,.45);
            text-decoration: none;
            transition: background .15s, color .15s;
            white-space: nowrap;
            overflow: hidden;
            position: relative;
        }
        .nav-item:hover {
            background: rgba(255,255,255,.05);
            color: rgba(255,255,255,.85);
        }
        .nav-item.active {
            background: var(--teal-dim);
            color: #7dd3f7;
        }
        .nav-item.active .ni-icon {
            color: var(--teal);
            background: rgba(14,165,233,.15);
        }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 2.5px;
            border-radius: 0 3px 3px 0;
            background: var(--teal);
        }

        .ni-icon {
            width: 28px; height: 28px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px;
            color: rgba(255,255,255,.3);
            flex-shrink: 0;
            transition: background .15s, color .15s;
        }

        /* Sidebar footer */
        .sb-footer {
            padding: 12px 10px;
            border-top: 1px solid rgba(255,255,255,.05);
            flex-shrink: 0;
        }
        .user-chip {
            display: flex; align-items: center; gap: 9px;
            padding: 8px 10px;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: background .15s;
        }
        .user-chip:hover { background: rgba(255,255,255,.05); }
        .user-avatar {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--teal), #6366f1);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 800;
            color: #fff; flex-shrink: 0;
        }
        .user-name { font-size: 11.5px; font-weight: 600; color: rgba(255,255,255,.8); }
        .user-role { font-size: 10px; color: var(--slate-mid); }

        /* ━━━━━━━━━━━━━━━━━━ MAIN WRAP ━━━━━━━━━━━━━━━━━━ */
        #main-wrap {
            margin-left: var(--sidebar-w);
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left .3s cubic-bezier(.22,1,.36,1);
        }
        #main-wrap.expanded { margin-left: 0; }

        /* ━━━━━━━━━━━━━━━━━━ TOPBAR ━━━━━━━━━━━━━━━━━━ */
        #topbar {
            height: var(--topbar-h);
            background: var(--white);
            border-bottom: 1px solid var(--slate-line);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 22px;
            position: sticky;
            top: 0;
            z-index: 40;
            flex-shrink: 0;
        }

        .tb-toggle {
            width: 32px; height: 32px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--slate-line);
            background: transparent;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--slate-mid);
            font-size: 12px;
            transition: background .15s, color .15s, border-color .15s;
        }
        .tb-toggle:hover { background: var(--slate-soft); color: var(--ink); border-color: #c8d0de; }

        /* Breadcrumb */
        .breadcrumb {
            display: flex; align-items: center; gap: 5px;
            font-size: 12px; color: var(--slate-mid);
        }
        .breadcrumb a { color: var(--slate-mid); text-decoration: none; transition: color .15s; }
        .breadcrumb a:hover { color: var(--teal); }
        .breadcrumb-sep { color: #c8d0de; font-size: 10px; }
        .breadcrumb-current { color: var(--ink-2); font-weight: 600; }

        .tb-right {
            margin-left: auto;
            display: flex; align-items: center; gap: 8px;
        }

        .tb-icon-btn {
            width: 32px; height: 32px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--slate-line);
            background: var(--white);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--slate-mid);
            font-size: 12px;
            transition: background .15s, color .15s;
            position: relative; text-decoration: none;
        }
        .tb-icon-btn:hover { background: var(--slate-soft); color: var(--ink); }

        .notif-dot {
            position: absolute; top: 7px; right: 7px;
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--rose);
            border: 1.5px solid var(--white);
        }

        .tb-avatar {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--teal), #6366f1);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 800;
            color: #fff; cursor: pointer;
            border: 2px solid var(--slate-line);
            transition: border-color .15s;
        }
        .tb-avatar:hover { border-color: var(--teal); }

        /* ━━━━━━━━━━━━━━━━━━ PAGE CONTENT ━━━━━━━━━━━━━━━━━━ */
        #page-content {
            flex: 1;
            padding: 24px 24px 40px;
            max-width: 1440px;
            width: 100%;
            margin: 0 auto;
        }

        /* User dropdown */
        #user-menu {
            display: none;
            position: absolute;
            top: calc(var(--topbar-h) + 4px);
            right: 20px;
            background: var(--white);
            border: 1px solid var(--slate-line);
            border-radius: var(--radius-lg);
            box-shadow: 0 16px 40px rgba(10,15,30,.12);
            min-width: 210px;
            z-index: 100;
            overflow: hidden;
        }
        .um-header { padding: 14px 16px; border-bottom: 1px solid var(--slate-line); }
        .um-name  { font-size: 13px; font-weight: 700; color: var(--ink); }
        .um-email { font-size: 11px; color: var(--slate-mid); margin-top: 2px; }
        .um-body  { padding: 6px; }
        .um-item  {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 10px;
            border-radius: var(--radius-sm);
            font-size: 12.5px;
            color: var(--rose);
            background: none; border: none; width: 100%;
            cursor: pointer; text-align: left;
            transition: background .15s;
        }
        .um-item:hover { background: #fff1f2; }

        /* Mobile overlay */
        #sb-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(10,15,30,.5);
            z-index: 49;
            backdrop-filter: blur(3px);
        }
        #sb-overlay.visible { display: block; }

        /* Footer */
        #page-footer {
            padding: 12px 24px;
            text-align: center;
            font-size: 11px;
            color: var(--slate-mid);
            border-top: 1px solid var(--slate-line);
            background: var(--white);
            flex-shrink: 0;
        }

        /* Mobile */
        @media (max-width: 1023px) {
            #sidebar { transform: translateX(calc(-1 * var(--sidebar-w))); }
            #sidebar.mobile-open { transform: translateX(0); }
            #main-wrap { margin-left: 0 !important; }
            #page-content { padding: 16px 16px 32px; }
        }
    </style>

    @yield('pageCss')
</head>
<body>

{{-- ══ SIDEBAR ══ --}}
<aside id="sidebar">
    <div class="sb-logo">
        <div class="sb-logo-mark">DA</div>
        <div>
            <div class="sb-logo-title">DMO Audit Portal</div>
            <div class="sb-logo-sub">Atal Amrit Abhiyan · Assam</div>
        </div>
    </div>

    <nav id="sb-nav">
        <div class="nav-section">Overview</div>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <div class="ni-icon"><i class="fas fa-chart-pie"></i></div>
            Dashboard
        </a>

        <div class="nav-section">Audit Management</div>

        <a href="{{ route('admin.audits.telephonic.index') }}"
           class="nav-item {{ request()->routeIs('admin.audits.telephonic.*') ? 'active' : '' }}">
            <div class="ni-icon"><i class="fas fa-phone-alt"></i></div>
            Telephonic Audits
        </a>

        <a href="{{ route('admin.audits.field.index') }}"
           class="nav-item {{ request()->routeIs('admin.audits.field.*') ? 'active' : '' }}">
            <div class="ni-icon"><i class="fas fa-people-arrows"></i></div>
            Field Visits
        </a>

        <a href="{{ route('admin.audits.live.index') }}"
           class="nav-item {{ request()->routeIs('admin.audits.live.*') ? 'active' : '' }}">
            <div class="ni-icon"><i class="fas fa-hospital-user"></i></div>
            Live Audits
        </a>

        <a href="{{ route('admin.audits.infra-audit.index') }}"
           class="nav-item {{ request()->routeIs('admin.audits.infra-audit.*') ? 'active' : '' }}">
            <div class="ni-icon"><i class="fas fa-building-columns"></i></div>
            Infra Audits
        </a>

        <div class="nav-section">Operations</div>

        <a href="{{ route('admin.dmos.index') }}"
           class="nav-item {{ request()->routeIs('admin.dmos.*') ? 'active' : '' }}">
            <div class="ni-icon"><i class="fas fa-user-tie"></i></div>
            DMO Officers
        </a>

        <a href="{{ route('admin.hospitals.index') }}"
           class="nav-item {{ request()->routeIs('admin.hospitals.*') ? 'active' : '' }}">
            <div class="ni-icon"><i class="fas fa-hospital"></i></div>
            Hospitals
        </a>

        <a href="{{ route('admin.districts.index') }}"
           class="nav-item {{ request()->routeIs('admin.districts.*') ? 'active' : '' }}">
            <div class="ni-icon"><i class="fas fa-map-marker-alt"></i></div>
            Districts
        </a>

        <div class="nav-section">Data</div>

        <a href="{{ route('admin.pmjay.index') }}"
           class="nav-item {{ request()->routeIs('admin.pmjay.*') && !request()->routeIs('admin.pmjay.upload') ? 'active' : '' }}">
            <div class="ni-icon"><i class="fas fa-database"></i></div>
            PMJAY Records
        </a>

        <a href="{{ route('admin.pmjay.upload') }}"
           class="nav-item {{ request()->routeIs('admin.pmjay.upload') ? 'active' : '' }}">
            <div class="ni-icon"><i class="fas fa-file-import"></i></div>
            Import Data
        </a>

        <a href="/admin/generate-audits"
           class="nav-item {{ request()->is('admin/generate-audits') ? 'active' : '' }}">
            <div class="ni-icon"><i class="fas fa-wand-magic-sparkles"></i></div>
            Generate Audits
        </a>

        <div class="nav-section">System</div>

        <a href="{{ route('admin.users.index') }}"
           class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <div class="ni-icon"><i class="fas fa-users"></i></div>
            Users
        </a>
    </nav>

    <div class="sb-footer">
        <div class="user-chip">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}</div>
            <div style="min-width:0;">
                <div class="user-name truncate">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="user-role">Administrator</div>
            </div>
            <i class="fas fa-ellipsis-v" style="color:rgba(255,255,255,.2); font-size:11px; margin-left:auto; flex-shrink:0;"></i>
        </div>
    </div>
</aside>

<div id="sb-overlay" onclick="closeSidebar()"></div>

{{-- ══ MAIN ══ --}}
<div id="main-wrap">

    <header id="topbar">
        <button class="tb-toggle" id="sb-toggle" onclick="toggleSidebar()" title="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>

        <div id="page-title-area">
            @yield('main_title')
        </div>

        <div class="tb-right">
            @hasSection('period_selector')
            @yield('period_selector')
            @endif

            <div class="tb-avatar" onclick="toggleUserMenu()" title="{{ auth()->user()->name ?? 'Admin' }}">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
            </div>

            <div id="user-menu">
                <div class="um-header">
                    <div class="um-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="um-email">{{ auth()->user()->email ?? '' }}</div>
                </div>
                <div class="um-body">
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button type="submit" class="um-item">
                            <i class="fas fa-sign-out-alt"></i> Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main id="page-content">
        @yield('main_content')
    </main>

    <footer id="page-footer">
        DMO Audit Portal &nbsp;·&nbsp; Atal Amrit Abhiyan Society, Assam &nbsp;·&nbsp; {{ now()->year }}
        &nbsp;·&nbsp; Build {{ config('app.version', '1.0') }}
    </footer>
</div>

<script>
let sidebarOpen = window.innerWidth >= 1024;

function toggleSidebar() {
    const sb  = document.getElementById('sidebar');
    const mw  = document.getElementById('main-wrap');
    const ov  = document.getElementById('sb-overlay');
    if (window.innerWidth >= 1024) {
        sidebarOpen = !sidebarOpen;
        sb.classList.toggle('collapsed', !sidebarOpen);
        mw.classList.toggle('expanded', !sidebarOpen);
    } else {
        sb.classList.toggle('mobile-open');
        ov.classList.toggle('visible');
    }
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('mobile-open');
    document.getElementById('sb-overlay').classList.remove('visible');
}
function toggleUserMenu() {
    const m = document.getElementById('user-menu');
    m.style.display = m.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    const m = document.getElementById('user-menu');
    if (!m.contains(e.target) && !document.querySelector('.tb-avatar').contains(e.target)) {
        m.style.display = 'none';
    }
});
</script>

@yield('pageJs')
</body>
</html>

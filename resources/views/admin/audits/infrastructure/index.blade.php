@extends('admin.layout.layout')

@section('main_title')
<div class="flex flex-wrap items-center justify-between gap-4 mb-8">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <div class="h-8 w-1 rounded-full" style="background:linear-gradient(180deg,#ec4899,#f43f5e)"></div>
            <span class="text-xs font-bold tracking-[.2em] uppercase text-slate-400">PMJAY Assam · Admin Console</span>
        </div>
        <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight leading-none">
            Infrastructure Audits
        </h1>
        <p class="text-sm text-slate-500 mt-1">Annexure 2.2 · Hospital infrastructure field investigations</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition">
            <i class="fas fa-arrow-left text-xs"></i> Dashboard
        </a>
    </div>
</div>
@endsection

@section('pageCss')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap');
    * { font-family:'DM Sans',sans-serif; }
    h1,.sec-head { font-family:"Roboto",sans-serif; }

    /* ── KPI cards ── */
    .kpi-card { background:#fff; border:1px solid #e2e8f0; border-radius:1.25rem; padding:1.25rem 1.5rem; position:relative; overflow:hidden; }
    .kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
    .kpi-rose::before    { background:linear-gradient(90deg,#ec4899,#f43f5e); }
    .kpi-blue::before    { background:linear-gradient(90deg,#3b82f6,#0ea5e9); }
    .kpi-violet::before  { background:linear-gradient(90deg,#8b5cf6,#6366f1); }
    .kpi-emerald::before { background:linear-gradient(90deg,#10b981,#14b8a6); }
    .kpi-amber::before   { background:linear-gradient(90deg,#f59e0b,#f97316); }
    .kpi-num { font-family:"Roboto",sans-serif; font-size:2rem; font-weight:900; line-height:1; letter-spacing:-.03em; }

    /* ── Filters bar ── */
    .filter-input { padding:.55rem .875rem; border:2px solid #e2e8f0; border-radius:.75rem; background:#f8fafc; font-size:.8rem; color:#1e293b; outline:none; font-family:inherit; transition:border-color .15s; }
    .filter-input:focus { border-color:#f43f5e; background:#fff; }
    .btn-filter { display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.25rem; border-radius:.75rem; font-size:.8rem; font-weight:700; cursor:pointer; transition:all .18s; border:none; font-family:inherit; }
    .btn-primary { background:#f43f5e; color:#fff; }
    .btn-primary:hover { background:#e11d48; }
    .btn-ghost { background:transparent; color:#64748b; border:2px solid #e2e8f0; }
    .btn-ghost:hover { background:#f8fafc; border-color:#94a3b8; }

    /* ── Table ── */
    .audit-table { width:100%; border-collapse:collapse; }
    .audit-table th { background:#f8fafc; font-size:.68rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:#94a3b8; padding:.75rem 1rem; text-align:left; border-bottom:2px solid #e2e8f0; white-space:nowrap; }
    .audit-table td { padding:.875rem 1rem; font-size:.8rem; color:#334155; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
    .audit-table tbody tr { transition:background .12s; }
    .audit-table tbody tr:hover { background:#fdf2f8; }
    .audit-table tbody tr.has-flags td:first-child { border-left:3px solid #f43f5e; }

    /* ── Badges ── */
    .badge { display:inline-flex; align-items:center; gap:.3rem; font-size:.7rem; font-weight:700; padding:.25rem .65rem; border-radius:9999px; white-space:nowrap; }
    .badge-public   { background:#dbeafe; color:#1e40af; }
    .badge-private  { background:#fce7f3; color:#9d174d; }
    .badge-pass     { background:#d1fae5; color:#065f46; }
    .badge-fail     { background:#fee2e2; color:#991b1b; }
    .badge-warn     { background:#fef3c7; color:#92400e; }
    .badge-na       { background:#f1f5f9; color:#64748b; }
    .badge-good     { background:#d1fae5; color:#065f46; }
    .badge-average  { background:#fef3c7; color:#92400e; }
    .badge-poor     { background:#fee2e2; color:#991b1b; }
    .flag-dot { width:.5rem; height:.5rem; border-radius:9999px; background:#f43f5e; flex-shrink:0; }

    /* ── Pagination ── */
    .page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:2rem; height:2rem; border-radius:.625rem; font-size:.75rem; font-weight:600; border:1.5px solid #e2e8f0; color:#64748b; text-decoration:none; transition:all .15s; }
    .page-btn:hover { background:#fff1f4; border-color:#f43f5e; color:#f43f5e; }
    .page-btn.active { background:#f43f5e; border-color:#f43f5e; color:#fff; }
    .page-btn.disabled { opacity:.35; pointer-events:none; }

    /* ── Empty state ── */
    .empty-state { text-align:center; padding:4rem 1rem; color:#94a3b8; }

    /* ── Chart card ── */
    .chart-card { background:#fff; border:1px solid #e2e8f0; border-radius:1.25rem; padding:1.5rem; }
    .sec-head { font-family:"Roboto",sans-serif; font-size:1rem; font-weight:800; color:#0f172a; letter-spacing:-.01em; }

    /* ── Toast ── */
    #toast-container { position:fixed; top:1.25rem; right:1.25rem; z-index:9999; display:flex; flex-direction:column; gap:.625rem; pointer-events:none; max-width:min(420px,calc(100vw - 2.5rem)); }
    .toast { pointer-events:auto; display:flex; align-items:flex-start; gap:.75rem; padding:.875rem 1rem; border-radius:.875rem; border:1.5px solid transparent; box-shadow:0 8px 24px rgba(0,0,0,.14); font-size:.8rem; font-weight:500; transform:translateX(120%); opacity:0; transition:transform .32s cubic-bezier(.22,1,.36,1),opacity .28s; position:relative; overflow:hidden; }
    .toast.toast-in  { transform:translateX(0); opacity:1; }
    .toast.toast-out { transform:translateX(120%); opacity:0; transition:transform .25s ease-in; }
    .toast-success { background:rgba(240,253,244,.97); border-color:#86efac; color:#14532d; }
    .toast-error   { background:rgba(255,241,242,.97); border-color:#fda4af; color:#9f1239; }
    .toast-icon { width:1.75rem; height:1.75rem; border-radius:9999px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:.75rem; }
    .toast-success .toast-icon { background:#dcfce7; color:#16a34a; }
    .toast-error   .toast-icon { background:#fee2e2; color:#dc2626; }
    .toast-body  { flex:1; }
    .toast-title { font-weight:700; font-size:.8rem; margin-bottom:.1rem; }
    .toast-close { background:none; border:none; cursor:pointer; opacity:.4; font-size:.7rem; padding:.15rem; color:inherit; flex-shrink:0; }
    .toast-close:hover { opacity:.85; }
    .toast-progress { position:absolute; bottom:0; left:0; height:3px; border-radius:0 0 .875rem .875rem; animation:toast-drain linear forwards; }
    .toast-success .toast-progress { background:#22c55e; }
    .toast-error   .toast-progress { background:#f43f5e; }
    @keyframes toast-drain { from{width:100%} to{width:0%} }
</style>
@endsection

@section('main_content')

{{-- ══════════════════════════════════════
     KPI STRIP
     ══════════════════════════════════════ --}}
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">

    <div class="kpi-card kpi-rose">
        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-1">Total</p>
        <div class="kpi-num text-slate-900">{{ number_format($kpis['total']) }}</div>
        <p class="text-xs text-slate-400 mt-1">in period</p>
    </div>

    <div class="kpi-card kpi-blue">
        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-1">Public</p>
        <div class="kpi-num text-blue-600">{{ number_format($kpis['public_count']) }}</div>
        <p class="text-xs text-slate-400 mt-1">Private: {{ number_format($kpis['private_count']) }}</p>
    </div>

    <div class="kpi-card kpi-violet">
        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-1">Banner Passed</p>
        <div class="kpi-num text-violet-600">{{ number_format($kpis['banner_passed']) }}</div>
        <div class="flex items-center gap-1.5 mt-1">
            <div class="flex-1 h-1 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-violet-500 rounded-full" style="width:{{ $kpis['banner_rate'] }}%"></div>
            </div>
            <span class="text-xs font-bold text-violet-600">{{ $kpis['banner_rate'] }}%</span>
        </div>
    </div>

    <div class="kpi-card kpi-emerald">
        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-1">Hygiene Good</p>
        <div class="kpi-num text-emerald-600">{{ number_format($kpis['hygiene_good']) }}</div>
        <p class="text-xs text-rose-500 mt-1">Poor: {{ number_format($kpis['hygiene_poor']) }}</p>
    </div>

    <div class="kpi-card kpi-amber">
        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-1">ICU Available</p>
        <div class="kpi-num text-amber-600">{{ number_format($kpis['icu_count']) }}</div>
        <p class="text-xs text-slate-400 mt-1">OT: {{ number_format($kpis['ot_count']) }}</p>
    </div>

    <div class="kpi-card kpi-rose">
        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-1">Flagged</p>
        <div class="kpi-num text-rose-500">{{ number_format($kpis['flag_count']) }}</div>
        <p class="text-xs text-slate-400 mt-1">need review</p>
    </div>
</div>

{{-- ══════════════════════════════════════
     TREND SPARKLINE + QUICK STATS ROW
     ══════════════════════════════════════ --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-6">

    {{-- Sparkline --}}
    <div class="chart-card xl:col-span-2">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="sec-head">Submission Trend</p>
                <p class="text-xs text-slate-400 mt-0.5">Daily infra audits — last 30 days</p>
            </div>
            <span class="badge badge-na text-[10px]">{{ $from->format('d M') }} – {{ $to->format('d M Y') }}</span>
        </div>
        <canvas id="sparkChart" height="90"></canvas>
    </div>

    {{-- Hygiene / Banner quick split --}}
    <div class="chart-card flex flex-col gap-4">
        <p class="sec-head">Period Snapshot</p>

        <div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Banner AI Check</p>
            <div class="space-y-2">
                @php
                    $bTotal = $kpis['banner_passed'] + $kpis['banner_failed'] + $kpis['banner_unchecked'];
                    $bTotal = max($bTotal, 1);
                @endphp
                @foreach([['Passed', $kpis['banner_passed'], '#10b981'], ['Failed', $kpis['banner_failed'], '#f43f5e'], ['Unchecked', $kpis['banner_unchecked'], '#e2e8f0']] as [$lbl, $val, $col])
                <div class="flex items-center gap-2 text-xs">
                    <span class="w-16 text-slate-500 shrink-0">{{ $lbl }}</span>
                    <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width:{{ round($val/$bTotal*100) }}%;background:{{ $col }};"></div>
                    </div>
                    <strong class="w-8 text-right text-slate-700">{{ $val }}</strong>
                </div>
                @endforeach
            </div>
        </div>

        <div class="border-t border-slate-100 pt-3">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Overall Hygiene</p>
            <div class="flex h-4 rounded-full overflow-hidden gap-0.5">
                @php
                    $hTotal = max($kpis['hygiene_good'] + $kpis['hygiene_average'] + $kpis['hygiene_poor'], 1);
                @endphp
                <div style="width:{{ round($kpis['hygiene_good']/$hTotal*100) }}%;background:#10b981;" data-tip="Good {{ $kpis['hygiene_good'] }}"></div>
                <div style="width:{{ round($kpis['hygiene_average']/$hTotal*100) }}%;background:#fbbf24;" data-tip="Average {{ $kpis['hygiene_average'] }}"></div>
                <div style="width:{{ round($kpis['hygiene_poor']/$hTotal*100) }}%;background:#f43f5e;" data-tip="Poor {{ $kpis['hygiene_poor'] }}"></div>
            </div>
            <div class="flex justify-between mt-1.5 text-[10px] text-slate-500">
                <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Good {{ $kpis['hygiene_good'] }}</span>
                <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-amber-400"></span> Avg {{ $kpis['hygiene_average'] }}</span>
                <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-rose-500"></span> Poor {{ $kpis['hygiene_poor'] }}</span>
            </div>
        </div>

        <div class="border-t border-slate-100 pt-3 grid grid-cols-2 gap-2 text-xs text-center">
            <div class="bg-slate-50 rounded-xl p-2">
                <p class="text-slate-400 uppercase tracking-wider text-[10px]">DGHS Registered</p>
                <p class="font-black text-slate-800 text-base">{{ $kpis['dghs_count'] }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-2">
                <p class="text-slate-400 uppercase tracking-wider text-[10px]">HDU Available</p>
                <p class="font-black text-slate-800 text-base">{{ $kpis['hdu_count'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     FILTERS
     ══════════════════════════════════════ --}}
<div class="chart-card mb-5">
    <form method="GET" action="{{ route('admin.audits.infra-audit.index') }}"
          class="flex flex-wrap items-end gap-3">

        <div class="flex-1 min-w-48">
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Search Hospital</label>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Name, address, or ID…"
                       class="filter-input pl-8 w-full">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Hospital Type</label>
            <select name="type" class="filter-input">
                <option value="">All types</option>
                <option value="Public"  {{ $type === 'Public'  ? 'selected' : '' }}>Public</option>
                <option value="Private" {{ $type === 'Private' ? 'selected' : '' }}>Private</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Banner AI Status</label>
            <select name="banner" class="filter-input">
                <option value="">All statuses</option>
                <option value="passed"    {{ $bannerStatus === 'passed'    ? 'selected' : '' }}>✓ Passed</option>
                <option value="failed"    {{ $bannerStatus === 'failed'    ? 'selected' : '' }}>✗ Failed</option>
                <option value="unchecked" {{ $bannerStatus === 'unchecked' ? 'selected' : '' }}>— Not checked</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Hygiene</label>
            <select name="hygiene" class="filter-input">
                <option value="">All ratings</option>
                <option value="Good"    {{ $hygiene === 'Good'    ? 'selected' : '' }}>Good</option>
                <option value="Average" {{ $hygiene === 'Average' ? 'selected' : '' }}>Average</option>
                <option value="Poor"    {{ $hygiene === 'Poor'    ? 'selected' : '' }}>Poor</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">From</label>
            <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="filter-input">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">To</label>
            <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="filter-input">
        </div>

        <div class="flex gap-2 pb-0.5">
            <button type="submit" class="btn-filter btn-primary">
                <i class="fas fa-filter"></i> Apply
            </button>
            @if($search || $type || $bannerStatus || $hygiene)
            <a href="{{ route('admin.infra-audit.index') }}" class="btn-filter btn-ghost">
                <i class="fas fa-xmark"></i> Clear
            </a>
            @endif
        </div>
    </form>
</div>

{{-- ══════════════════════════════════════
     AUDIT TABLE
     ══════════════════════════════════════ --}}
<div class="chart-card">

    {{-- Results info --}}
    <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
        <p class="text-xs text-slate-500">
            Showing <strong>{{ $audits->firstItem() ?? 0 }}–{{ $audits->lastItem() ?? 0 }}</strong>
            of <strong>{{ $audits->total() }}</strong> records
            @if($search || $type || $bannerStatus || $hygiene)
                <span class="ml-2 badge badge-warn"><i class="fas fa-filter text-[9px]"></i> Filtered</span>
            @endif
        </p>
    </div>

    @if($audits->isEmpty())
    <div class="empty-state">
        <i class="fas fa-building-columns text-4xl mb-3 block text-slate-300"></i>
        <p class="font-semibold text-slate-600 text-sm">No infrastructure audits found</p>
        <p class="text-xs mt-1 text-slate-400">
            @if($search || $type || $bannerStatus || $hygiene)
                Try adjusting your filters.
            @else
                No audits have been submitted in this period.
            @endif
        </p>
    </div>
    @else

    <div class="overflow-x-auto">
        <table class="audit-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Hospital</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Submitted by</th>
                    <th>Banner AI</th>
                    <th>Hygiene</th>
                    <th>ICU</th>
                    <th>OT</th>
                    <th>Flags</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($audits as $audit)
                @php
                    $hasFlags = $audit->overall_hygiene === 'Poor'
                             || $audit->ai_banner_pass  === false
                             || $audit->hospital_existence === 'No'
                             || $audit->onduty_doctors === 'No';
                @endphp
                <tr class="{{ $hasFlags ? 'has-flags' : '' }}">
                    <td class="text-slate-400 text-xs font-medium">
                        {{ ($audits->currentPage()-1) * $audits->perPage() + $loop->iteration }}
                    </td>

                    <td>
                        <p class="font-bold text-slate-800 text-sm">{{ $audit->hospital_name }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ Str::limit($audit->hospital_address, 40) }}</p>
                        @if($audit->hospital_id)
                        <p class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $audit->hospital_id }}</p>
                        @endif
                    </td>

                    <td class="whitespace-nowrap text-xs">
                        {{ $audit->investigation_date?->format('d M Y') ?? '—' }}
                    </td>

                    <td>
                        <span class="badge {{ $audit->hospital_type === 'Public' ? 'badge-public' : 'badge-private' }}">
                            {{ $audit->hospital_type }}
                        </span>
                    </td>

                    <td>
                        <p class="text-xs font-semibold text-slate-700">{{ $audit->submittedBy?->name ?? '—' }}</p>
                        <p class="text-[10px] text-slate-400">{{ $audit->created_at?->format('d M, h:i A') }}</p>
                    </td>

                    <td>
                        @if(is_null($audit->ai_banner_pass))
                            <span class="badge badge-na"><i class="fas fa-minus text-[9px]"></i> N/A</span>
                        @elseif($audit->ai_banner_pass)
                            <span class="badge badge-pass"><i class="fas fa-robot text-[9px]"></i> Passed</span>
                        @else
                            <span class="badge badge-fail"><i class="fas fa-exclamation text-[9px]"></i> Failed</span>
                        @endif
                    </td>

                    <td>
                        @if($audit->overall_hygiene)
                            <span class="badge badge-{{ strtolower($audit->overall_hygiene) }}">
                                {{ $audit->overall_hygiene }}
                            </span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>

                    <td class="text-center">
                        @if($audit->icu_available === 'Yes')
                            <span class="badge badge-pass">
                                <i class="fas fa-check text-[9px]"></i>
                                @if($audit->icu_beds) {{ $audit->icu_beds }} @endif
                            </span>
                        @elseif($audit->icu_available === 'No')
                            <span class="badge badge-fail">None</span>
                        @else
                            <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>

                    <td class="text-center">
                        @if($audit->ot_available === 'Yes')
                            <span class="badge badge-pass">
                                <i class="fas fa-check text-[9px]"></i>
                                @if($audit->ot_count) {{ $audit->ot_count }} @endif
                            </span>
                        @elseif($audit->ot_available === 'No')
                            <span class="badge badge-fail">None</span>
                        @else
                            <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>

                    <td>
                        @if($hasFlags)
                            <span class="badge badge-fail">
                                <span class="flag-dot"></span> Issues
                            </span>
                        @else
                            <span class="badge badge-pass">
                                <i class="fas fa-check-circle text-[9px]"></i> OK
                            </span>
                        @endif
                    </td>

                    <td class="whitespace-nowrap">
                        <a href="{{ route('admin.audits.infra-audit.show', $audit->id) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs font-semibold transition">
                            <i class="fas fa-eye text-[10px]"></i> View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($audits->hasPages())
    <div class="flex items-center justify-between mt-5 flex-wrap gap-3">
        <p class="text-xs text-slate-500">
            Page {{ $audits->currentPage() }} of {{ $audits->lastPage() }}
        </p>
        <div class="flex items-center gap-1.5">
            <a href="{{ $audits->previousPageUrl() ?? '#' }}"
               class="page-btn {{ $audits->onFirstPage() ? 'disabled' : '' }}">
                <i class="fas fa-chevron-left text-[10px]"></i>
            </a>
            @foreach($audits->getUrlRange(max(1,$audits->currentPage()-2), min($audits->lastPage(),$audits->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}" class="page-btn {{ $page === $audits->currentPage() ? 'active' : '' }}">
                {{ $page }}
            </a>
            @endforeach
            <a href="{{ $audits->nextPageUrl() ?? '#' }}"
               class="page-btn {{ !$audits->hasMorePages() ? 'disabled' : '' }}">
                <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        </div>
    </div>
    @endif

    @endif {{-- /empty check --}}
</div>

<div id="toast-container"></div>

@endsection

@section('pageJs')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = "'DM Sans', sans-serif";
Chart.defaults.color       = '#94a3b8';

/* Submission sparkline */
(function () {
    const ctx  = document.getElementById('sparkChart').getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 90);
    grad.addColorStop(0, 'rgba(244,63,94,.25)');
    grad.addColorStop(1, 'rgba(244,63,94,0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json(collect(range(29,0))->map(fn($i)=>now()->subDays($i)->format('d M'))),
            datasets: [{
                data:            @json($sparkData),
                borderColor:     '#f43f5e',
                backgroundColor: grad,
                tension:         .4,
                fill:            true,
                pointRadius:     0,
                pointHoverRadius:4,
                borderWidth:     2,
            }]
        },
        options: {
            responsive: true,
            interaction: { mode:'index', intersect:false },
            plugins: {
                legend: { display:false },
                tooltip: { backgroundColor:'#0f172a', bodyColor:'#f1f5f9', padding:8, cornerRadius:8,
                           callbacks: { label: ctx => ` ${ctx.parsed.y} audits` } }
            },
            scales: {
                x: { grid:{ display:false }, ticks:{ maxTicksLimit:8, font:{ size:10 } } },
                y: { grid:{ color:'#f1f5f9' }, border:{ dash:[4,4] }, ticks:{ font:{ size:10 }, stepSize:1 } }
            }
        }
    });
})();
</script>
@endsection

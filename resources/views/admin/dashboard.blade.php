@extends('admin.layout.layout')

@section('main_title')
<div class="flex flex-wrap items-center justify-between gap-4 mb-8">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <div class="h-8 w-1 rounded-full bg-linear-to-b from-cyan-400 to-blue-600"></div>
            <span class="text-xs font-bold tracking-[.2em] uppercase text-slate-400">PMJAY Assam · Admin Console</span>
        </div>
        <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight leading-none">
            Audit Intelligence
        </h1>
        
    </div>
    <div class="flex items-center gap-3">
        <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold px-4 py-2.5 rounded-xl">
            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Live Data
        </div>
        
    </div>
</div>
@endsection

@section('pageCss')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800;900&family=DM+Sans:wght@300;400;500;600&display=swap');

    * { font-family: 'DM Sans', sans-serif; }
    h1, h2, .font-display { font-family: "Roboto", sans-serif; }

    /* ── Stat cards ── */
    .stat-card {
        background: #fff;
        border-radius: 1.25rem;
        border: 1px solid #e2e8f0;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover { box-shadow: 0 12px 40px rgba(0,0,0,.1); transform: translateY(-2px); }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
    }
    .card-tele::before   { background: linear-gradient(90deg, #06b6d4, #0ea5e9); }
    .card-field::before  { background: linear-gradient(90deg, #f59e0b, #f97316); }
    .card-live::before   { background: linear-gradient(90deg, #8b5cf6, #6366f1); }
    .card-total::before  { background: linear-gradient(90deg, #10b981, #14b8a6); }

    /* ── Big number ── */
    .big-num { font-family: "Roboto", sans-serif; font-size: 2.75rem; font-weight: 900; line-height: 1; letter-spacing: -.03em; }

    /* ── Progress ring ── */
    .ring-wrap { position: relative; width: 64px; height: 64px; flex-shrink: 0; }
    .ring-wrap svg { transform: rotate(-90deg); }
    .ring-bg   { fill: none; stroke: #f1f5f9; stroke-width: 6; }
    .ring-val  { fill: none; stroke-width: 6; stroke-linecap: round; transition: stroke-dashoffset 1s ease; }
    .ring-label { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: .7rem; font-weight: 800; font-family: "Roboto", sans-serif; }

    /* ── Trend badge ── */
    .trend-up   { background: #d1fae5; color: #059669; }
    .trend-down { background: #fee2e2; color: #dc2626; }
    .trend-flat { background: #f1f5f9; color: #64748b; }
    .trend-badge { display: inline-flex; align-items: center; gap: .3rem; font-size: .7rem; font-weight: 700; padding: .2rem .6rem; border-radius: 9999px; }

    /* ── Chart container ── */
    .chart-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 1.25rem; padding: 1.5rem; }

    /* ── DMO table ── */
    .dmo-row { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr 100px; align-items: center; gap: .5rem; padding: .875rem 1.25rem; border-radius: .875rem; transition: background .15s; }
    .dmo-row:hover { background: #f8fafc; }
    .dmo-row + .dmo-row { border-top: 1px solid #f1f5f9; }
    .dmo-header { color: #94a3b8; font-size: .7rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; }

    /* ── Activity feed ── */
    .feed-item { display: flex; gap: .875rem; padding: .75rem 0; }
    .feed-item + .feed-item { border-top: 1px solid #f1f5f9; }
    .feed-dot { width: .625rem; height: .625rem; border-radius: 9999px; flex-shrink: 0; margin-top: .35rem; }

    /* ── AI donut legend ── */
    .legend-dot { width: .75rem; height: .75rem; border-radius: 9999px; flex-shrink: 0; }

    /* ── Section header ── */
    .sec-head { font-family: "Roboto", sans-serif; font-size: 1rem; font-weight: 800; color: #0f172a; letter-spacing: -.01em; }

    /* ── Heatmap cell ── */
    .hm-cell { border-radius: .375rem; aspect-ratio: 1; transition: transform .15s; cursor: default; }
    .hm-cell:hover { transform: scale(1.3); z-index: 10; position: relative; }

    /* ── Skeleton shimmer ── */
    @keyframes shimmer { 0%{background-position:-400px 0} 100%{background-position:400px 0} }
    .shimmer { background: linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size:800px 100%; animation:shimmer 1.5s infinite; }

    /* ── Scroll fade ── */
    @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:none} }
    .fade-up { animation: fadeUp .5s ease forwards; opacity: 0; }
    .fade-up:nth-child(1){animation-delay:.05s} .fade-up:nth-child(2){animation-delay:.1s}
    .fade-up:nth-child(3){animation-delay:.15s} .fade-up:nth-child(4){animation-delay:.2s}

    /* ── Tooltip ── */
    [data-tip] { position: relative; }
    [data-tip]:hover::after { content: attr(data-tip); position: absolute; bottom: calc(100% + 6px); left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: .7rem; padding: .3rem .6rem; border-radius: .5rem; white-space: nowrap; pointer-events: none; z-index: 50; }
</style>
@endsection

@section('main_content')

{{-- ══════════════════════════════════════
     TOP KPI CARDS
     ══════════════════════════════════════ --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

    {{-- Total Audits --}}
    <div class="stat-card card-total fade-up col-span-2 xl:col-span-1">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">Total Audits</p>
                <div class="big-num text-slate-900">{{ number_format($totals['grand_total']) }}</div>
                <div class="flex items-center gap-2 mt-2">
                    <span class="trend-badge trend-up">
                        <i class="fas fa-arrow-up text-[9px]"></i> {{ $totals['growth_pct'] }}%
                    </span>
                    <span class="text-xs text-slate-400">vs last period</span>
                </div>
            </div>
            <div class="ring-wrap">
                <svg viewBox="0 0 64 64" width="64" height="64">
                    <circle class="ring-bg" cx="32" cy="32" r="29"/>
                    <circle class="ring-val" cx="32" cy="32" r="29"
                        stroke="#10b981"
                        stroke-dasharray="{{ round(2 * M_PI * 29) }}"
                        stroke-dashoffset="{{ round(2 * M_PI * 29 * (1 - $totals['completion_rate']/100)) }}"/>
                </svg>
                <div class="ring-label text-emerald-600">{{ $totals['completion_rate'] }}%</div>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-3 gap-2 pt-4 border-t border-slate-100">
            <div class="text-center">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider">Done</p>
                <p class="text-sm font-bold text-slate-800">{{ number_format($totals['completed']) }}</p>
            </div>
            <div class="text-center border-x border-slate-100">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider">Pending</p>
                <p class="text-sm font-bold text-amber-600">{{ number_format($totals['pending']) }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider">DMOs</p>
                <p class="text-sm font-bold text-slate-800">{{ $totals['active_dmos'] }}</p>
            </div>
        </div>
    </div>

    {{-- Telephonic --}}
    <div class="stat-card card-tele fade-up">
        <div class="flex items-start justify-between gap-2 mb-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">Telephonic</p>
                <div class="big-num text-slate-900">{{ number_format($tele['total']) }}</div>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-phone-alt"></i>
            </div>
        </div>
        <div class="flex items-center gap-2 mb-3">
            <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-cyan-400 to-sky-500 rounded-full"
                     style="width:{{ $tele['completion_rate'] }}%"></div>
            </div>
            <span class="text-xs font-bold text-cyan-600 shrink-0">{{ $tele['completion_rate'] }}%</span>
        </div>
        <div class="flex justify-between text-xs text-slate-500">
            <span>Completed: <strong class="text-slate-800">{{ number_format($tele['completed']) }}</strong></span>
            <span class="text-rose-500">{{ number_format($tele['total'] - $tele['completed']) }} pending</span>
        </div>
        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs text-slate-400">Avg / DMO / day</span>
            <span class="text-sm font-bold text-slate-700">{{ $tele['avg_per_dmo_day'] }}</span>
        </div>
    </div>

    {{-- Field Visits --}}
    <div class="stat-card card-field fade-up">
        <div class="flex items-start justify-between gap-2 mb-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">Field Visits</p>
                <div class="big-num text-slate-900">{{ number_format($field['total']) }}</div>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-people-arrows"></i>
            </div>
        </div>
        <div class="flex items-center gap-2 mb-3">
            <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-amber-400 to-orange-500 rounded-full"
                     style="width:{{ $field['completion_rate'] }}%"></div>
            </div>
            <span class="text-xs font-bold text-amber-600 shrink-0">{{ $field['completion_rate'] }}%</span>
        </div>
        <div class="flex justify-between text-xs text-slate-500">
            <span>Completed: <strong class="text-slate-800">{{ number_format($field['completed']) }}</strong></span>
            <span class="text-rose-500">{{ number_format($field['total'] - $field['completed']) }} pending</span>
        </div>
        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs text-slate-400">Avg / DMO / day</span>
            <span class="text-sm font-bold text-slate-700">{{ $field['avg_per_dmo_day'] }}</span>
        </div>
    </div>

    {{-- Live Audits --}}
    <div class="stat-card card-live fade-up">
        <div class="flex items-start justify-between gap-2 mb-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">Live Audits</p>
                <div class="big-num text-slate-900">{{ number_format($live['total']) }}</div>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-hospital-user"></i>
            </div>
        </div>
        <div class="flex items-center gap-2 mb-3">
            <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-violet-500 to-indigo-500 rounded-full"
                     style="width:{{ $live['ai_pass_rate'] }}%"></div>
            </div>
            <span class="text-xs font-bold text-violet-600 shrink-0">{{ $live['ai_pass_rate'] }}%</span>
        </div>
        <div class="flex justify-between text-xs text-slate-500">
            <span>AI Passed: <strong class="text-slate-800">{{ number_format($live['ai_passed']) }}</strong></span>
            <span class="text-amber-500">{{ number_format($live['ai_skipped']) }} skipped</span>
        </div>
        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs text-slate-400">Avg / DMO / day</span>
            <span class="text-sm font-bold text-slate-700">{{ $live['avg_per_dmo_day'] }}</span>
        </div>
    </div>

</div>


{{-- ══════════════════════════════════════
     ROW 3: District Heatmap + Audit Mix
     ══════════════════════════════════════ --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-8">

    {{-- Audit type breakdown bar --}}
    <div class="chart-card">
        <div class="flex items-center justify-between mb-5">
            <div>
                <p class="sec-head">Audit Mix by District</p>
                <p class="text-xs text-slate-400 mt-0.5">Composition across top districts</p>
            </div>
        </div>
        <canvas id="districtBar" height="240"></canvas>
    </div>

    {{-- Weekly activity heatmap --}}
    <div class="chart-card">
        <div class="flex items-center justify-between mb-5">
            <div>
                <p class="sec-head">Weekly Activity Heatmap</p>
                <p class="text-xs text-slate-400 mt-0.5">Audit submissions by day &amp; hour</p>
            </div>
            <div class="flex items-center gap-2 text-[10px] text-slate-400">
                <span class="h-2.5 w-2.5 rounded bg-slate-100"></span> Low
                <span class="h-2.5 w-2.5 rounded bg-violet-300"></span>
                <span class="h-2.5 w-2.5 rounded bg-violet-600"></span> High
            </div>
        </div>
        @php
            $days  = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
            $hours = ['6am','9am','12pm','3pm','6pm','9pm'];
        @endphp
        <div class="overflow-x-auto">
            <div class="min-w-[360px]">
                {{-- Hour labels --}}
                <div class="grid gap-1 mb-1" style="grid-template-columns: 36px repeat(6, 1fr);">
                    <div></div>
                    @foreach($hours as $h)
                    <div class="text-center text-[9px] text-slate-400 font-medium">{{ $h }}</div>
                    @endforeach
                </div>
                {{-- Heatmap grid --}}
                @foreach($days as $di => $day)
                <div class="grid gap-1 mb-1" style="grid-template-columns: 36px repeat(6, 1fr);">
                    <div class="text-[10px] text-slate-400 font-medium flex items-center">{{ $day }}</div>
                    @for($hi = 0; $hi < 6; $hi++)
                    @php
                        $val     = $heatmap[$di][$hi] ?? 0;
                        $max     = $heatmapMax ?? 1;
                        $pct     = $max > 0 ? $val / $max : 0;
                        $opacity = round($pct * 100);
                        $bg      = $pct > .75 ? '#7c3aed' : ($pct > .45 ? '#a78bfa' : ($pct > .15 ? '#ddd6fe' : '#f1f5f9'));
                    @endphp
                    <div class="hm-cell h-8"
                         style="background:{{ $bg }};"
                         data-tip="{{ $val }} audits">
                    </div>
                    @endfor
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     ROW 4: DMO Performance Table
     ══════════════════════════════════════ --}}
<div class="chart-card mb-8">
    <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
        <div>
            <p class="sec-head">DMO Performance</p>
            <p class="text-xs text-slate-400 mt-0.5">Individual officer metrics · ranked by completion</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <input type="text" id="dmo-search" placeholder="Search DMO…"
                       class="pl-8 pr-4 py-2 text-xs border border-slate-200 rounded-xl bg-white focus:border-blue-400 focus:outline-none w-48 transition">
            </div>
            <select id="sort-select"
                    class="text-xs border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none cursor-pointer">
                <option value="total">Sort: Total</option>
                <option value="completion">Sort: Completion %</option>
                <option value="live">Sort: Live Audits</option>
            </select>
        </div>
    </div>

    {{-- Table header --}}
    <div class="dmo-row dmo-header bg-slate-50 rounded-xl mb-2">
        <span>Officer</span>
        <span class="text-center">Telephonic</span>
        <span class="text-center">Field Visits</span>
        <span class="text-center">Live Audits</span>
        <span class="text-center">Total</span>
        <span class="text-center">Completion</span>
    </div>

    <div id="dmo-table-body">
        @foreach($dmoStats as $dmo)
        @php
            $compPct = $dmo['total'] > 0 ? round(($dmo['completed'] / $dmo['total']) * 100) : 0;
            $rankColor = $loop->index === 0 ? '#f59e0b' : ($loop->index === 1 ? '#94a3b8' : ($loop->index === 2 ? '#b45309' : '#cbd5e1'));
        @endphp
        <div class="dmo-row" data-name="{{ strtolower($dmo['name']) }}" data-total="{{ $dmo['total'] }}"
             data-completion="{{ $compPct }}" data-live="{{ $dmo['live'] }}">

            {{-- Officer --}}
            <div class="flex items-center gap-3 min-w-0">
                <div class="h-9 w-9 rounded-xl flex items-center justify-center text-xs font-black shrink-0"
                     style="background:{{ $dmo['avatar_bg'] ?? '#f1f5f9' }}; color:{{ $dmo['avatar_color'] ?? '#475569' }};">
                    {{ strtoupper(substr($dmo['name'], 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $dmo['name'] }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ $dmo['district'] }}</p>
                </div>
                @if($loop->index < 3)
                <span class="text-xs ml-1" style="color:{{ $rankColor }}">
                    @if($loop->index === 0) 🥇 @elseif($loop->index === 1) 🥈 @else 🥉 @endif
                </span>
                @endif
            </div>

            {{-- Telephonic --}}
            <div class="text-center">
                <span class="text-sm font-bold text-cyan-600">{{ $dmo['tele_completed'] }}</span>
                <span class="text-xs text-slate-400"> / {{ $dmo['tele_total'] }}</span>
            </div>

            {{-- Field --}}
            <div class="text-center">
                <span class="text-sm font-bold text-amber-600">{{ $dmo['field_completed'] }}</span>
                <span class="text-xs text-slate-400"> / {{ $dmo['field_total'] }}</span>
            </div>

            {{-- Live --}}
            <div class="text-center">
                <span class="text-sm font-bold text-violet-600">{{ $dmo['live'] }}</span>
            </div>

            {{-- Total --}}
            <div class="text-center">
                <span class="text-sm font-bold text-slate-800">{{ $dmo['total'] }}</span>
            </div>

            {{-- Completion bar --}}
            <div class="flex flex-col items-center gap-1">
                <span class="text-xs font-bold {{ $compPct >= 80 ? 'text-emerald-600' : ($compPct >= 50 ? 'text-amber-600' : 'text-rose-500') }}">
                    {{ $compPct }}%
                </span>
                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all"
                         style="width:{{ $compPct }}%;
                                background:{{ $compPct >= 80 ? '#10b981' : ($compPct >= 50 ? '#f59e0b' : '#f43f5e') }}">
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ══════════════════════════════════════
     ROW 5: Recent Activity Feed + Top Hospitals
     ══════════════════════════════════════ --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-8">

    {{-- Activity Feed --}}
    <div class="chart-card">
        <div class="flex items-center justify-between mb-5">
            <div>
                <p class="sec-head">Recent Activity</p>
                <p class="text-xs text-slate-400 mt-0.5">Latest submissions across all types</p>
            </div>
            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
        </div>
        <div>
            @foreach($recentActivity as $act)
            <div class="feed-item">
                <div class="feed-dot mt-1.5
                    {{ $act['type'] === 'telephonic' ? 'bg-cyan-400' : ($act['type'] === 'field' ? 'bg-amber-400' : 'bg-violet-500') }}">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $act['dmo_name'] }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $act['type'] === 'telephonic' ? '📞 Telephonic' : ($act['type'] === 'field' ? '🏥 Field Visit' : '⚡ Live Audit') }}
                        @if(isset($act['patient_name']))
                            · <span class="font-medium">{{ $act['patient_name'] }}</span>
                        @endif
                        @if(isset($act['hospital_name']))
                            · {{ $act['hospital_name'] }}
                        @endif
                    </p>
                </div>
                <span class="text-[10px] text-slate-400 shrink-0 mt-0.5">
                    {{ $act['created_at']->diffForHumans() }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Top Hospitals by Audit Count --}}
    <div class="chart-card">
        <div class="flex items-center justify-between mb-5">
            <div>
                <p class="sec-head">Most Audited Hospitals</p>
                <p class="text-xs text-slate-400 mt-0.5">By total audit volume this period</p>
            </div>
        </div>
        <div class="space-y-3">
            @foreach($topHospitals as $idx => $hosp)
            @php $maxCount = $topHospitals->first()['count']; @endphp
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-xs font-black text-slate-300 w-5 shrink-0">{{ $idx + 1 }}</span>
                        <span class="text-sm font-semibold text-slate-700 truncate">{{ $hosp['name'] }}</span>
                    </div>
                    <span class="text-xs font-bold text-slate-800 shrink-0 ml-2">{{ number_format($hosp['count']) }}</span>
                </div>
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden ml-7">
                    <div class="h-full rounded-full"
                         style="width:{{ $maxCount > 0 ? round($hosp['count'] / $maxCount * 100) : 0 }}%;
                                background: linear-gradient(90deg,
                                    {{ ['#06b6d4','#f59e0b','#8b5cf6','#10b981','#f43f5e','#0ea5e9','#a16207'][$idx % 7] }},
                                    {{ ['#0ea5e9','#f97316','#6366f1','#14b8a6','#e11d48','#38bdf8','#d97706'][$idx % 7] }});">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Toast container --}}
<div id="toast-container"
     style="position:fixed;top:1.25rem;right:1.25rem;z-index:9999;display:flex;flex-direction:column;gap:.625rem;pointer-events:none;max-width:min(420px,calc(100vw - 2.5rem));">
</div>

@endsection

@section('pageJs')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
/* ── Shared chart defaults ── */
Chart.defaults.font.family = "'DM Sans', sans-serif";
Chart.defaults.color       = '#94a3b8';

/* ═══════════════════════════════════════
   1. Activity Timeline (multi-line)
   ═══════════════════════════════════════ */
(function () {
    const labels = @json($chartDates);
    const ctx    = document.getElementById('activityChart').getContext('2d');

    const tele  = @json($chartTele);
    const field = @json($chartField);
    const live  = @json($chartLive);

    const gradientCyan   = ctx.createLinearGradient(0, 0, 0, 200);
    const gradientAmber  = ctx.createLinearGradient(0, 0, 0, 200);
    const gradientViolet = ctx.createLinearGradient(0, 0, 0, 200);
    gradientCyan.addColorStop(0,   'rgba(6,182,212,.25)');
    gradientCyan.addColorStop(1,   'rgba(6,182,212,0)');
    gradientAmber.addColorStop(0,  'rgba(245,158,11,.25)');
    gradientAmber.addColorStop(1,  'rgba(245,158,11,0)');
    gradientViolet.addColorStop(0, 'rgba(139,92,246,.25)');
    gradientViolet.addColorStop(1, 'rgba(139,92,246,0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                { label:'Telephonic', data:tele,  borderColor:'#06b6d4', backgroundColor:gradientCyan,
                  tension:.4, fill:true, pointRadius:0, pointHoverRadius:5, borderWidth:2.5 },
                { label:'Field',      data:field, borderColor:'#f59e0b', backgroundColor:gradientAmber,
                  tension:.4, fill:true, pointRadius:0, pointHoverRadius:5, borderWidth:2.5 },
                { label:'Live',       data:live,  borderColor:'#8b5cf6', backgroundColor:gradientViolet,
                  tension:.4, fill:true, pointRadius:0, pointHoverRadius:5, borderWidth:2.5 },
            ]
        },
        options: {
            responsive: true,
            interaction: { mode:'index', intersect:false },
            plugins: {
                legend: { display:false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#94a3b8',
                    bodyColor: '#f1f5f9',
                    padding: 12,
                    cornerRadius: 10,
                    titleFont: { size:11, weight:'600' },
                }
            },
            scales: {
                x: { grid:{ display:false }, ticks:{ maxTicksLimit:8, font:{size:11} } },
                y: { grid:{ color:'#f1f5f9' }, border:{ dash:[4,4] }, ticks:{ font:{size:11} } }
            }
        }
    });
})();

/* ═══════════════════════════════════════
   2. AI Verification Donut
   ═══════════════════════════════════════ */
(function () {
    new Chart(document.getElementById('aiDonut'), {
        type: 'doughnut',
        data: {
            labels: ['Passed','Failed','Skipped'],
            datasets: [{
                data: [{{ $live['ai_passed'] }}, {{ $live['ai_failed'] }}, {{ $live['ai_skipped'] }}],
                backgroundColor: ['#10b981','#f43f5e','#fbbf24'],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            cutout: '74%',
            responsive: true,
            plugins: {
                legend: { display:false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    bodyColor: '#f1f5f9',
                    padding: 10,
                    cornerRadius: 8,
                }
            }
        }
    });
})();

/* ═══════════════════════════════════════
   3. District Stacked Bar
   ═══════════════════════════════════════ */
(function () {
    new Chart(document.getElementById('districtBar'), {
        type: 'bar',
        data: {
            labels: @json($districtLabels),
            datasets: [
                { label:'Telephonic', data:@json($districtTele),  backgroundColor:'#67e8f9', borderRadius:4 },
                { label:'Field',      data:@json($districtField), backgroundColor:'#fcd34d', borderRadius:4 },
                { label:'Live',       data:@json($districtLive),  backgroundColor:'#c4b5fd', borderRadius:4 },
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position:'bottom', labels:{ boxWidth:10, padding:16, font:{size:11} } },
                tooltip: { backgroundColor:'#0f172a', bodyColor:'#f1f5f9', cornerRadius:8, padding:10 }
            },
            scales: {
                x: { stacked:true, grid:{ display:false }, ticks:{ font:{size:11} } },
                y: { stacked:true, grid:{ color:'#f1f5f9' }, border:{ dash:[4,4] }, ticks:{ font:{size:11} } }
            }
        }
    });
})();

/* ═══════════════════════════════════════
   4. DMO Table — live search + sort
   ═══════════════════════════════════════ */
(function () {
    const tbody  = document.getElementById('dmo-table-body');
    const search = document.getElementById('dmo-search');
    const sortSel= document.getElementById('sort-select');

    function filterSort() {
        const q   = search.value.toLowerCase();
        const key = sortSel.value;
        const rows= [...tbody.querySelectorAll('.dmo-row')];

        rows
            .filter(r => r.dataset.name !== undefined)
            .sort((a, b) => +b.dataset[key] - +a.dataset[key])
            .forEach(row => {
                row.style.display = row.dataset.name.includes(q) ? '' : 'none';
                tbody.appendChild(row);
            });
    }
    search.addEventListener('input',  filterSort);
    sortSel.addEventListener('change', filterSort);
})();

/* ═══════════════════════════════════════
   5. Toast (for session flash)
   ═══════════════════════════════════════ */
function toast(type, title, msg, dur) {
    dur = dur ?? 4500;
    const icons = { success:'fa-check-circle', error:'fa-times-circle', info:'fa-info-circle' };
    const colors = {
        success: 'rgba(240,253,244,.97)',
        error:   'rgba(255,241,242,.97)',
        info:    'rgba(239,246,255,.97)',
    };
    const el = document.createElement('div');
    el.style.cssText = `pointer-events:auto;display:flex;align-items:flex-start;gap:.75rem;padding:.875rem 1rem;border-radius:.875rem;background:${colors[type]};box-shadow:0 8px 24px rgba(0,0,0,.14);font-size:.8125rem;transform:translateX(120%);opacity:0;transition:transform .32s cubic-bezier(.22,1,.36,1),opacity .28s;position:relative;overflow:hidden;`;
    el.innerHTML = `<i class="fas ${icons[type] ?? 'fa-info-circle'}" style="margin-top:.15rem;flex-shrink:0;"></i>
        <div><strong style="display:block;font-size:.8125rem;margin-bottom:.1rem;">${title}</strong>${msg}</div>
        <button onclick="this.closest('div').remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;opacity:.5;font-size:.75rem;"><i class="fas fa-times"></i></button>`;
    document.getElementById('toast-container').appendChild(el);
    requestAnimationFrame(() => requestAnimationFrame(() => { el.style.transform='translateX(0)'; el.style.opacity='1'; }));
    setTimeout(() => { el.style.transform='translateX(120%)'; el.style.opacity='0'; setTimeout(()=>el.remove(),300); }, dur);
}

@if(session('success')) toast('success', 'Success', '{{ session("success") }}'); @endif
@if(session('error'))   toast('error',   'Error',   '{{ session("error") }}');   @endif
</script>
@endsection

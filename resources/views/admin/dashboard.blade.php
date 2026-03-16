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

    .stat-card {
        background: #fff; border-radius: 1.25rem; border: 1px solid #e2e8f0; padding: 1.5rem;
        position: relative; overflow: hidden; transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover { box-shadow: 0 12px 40px rgba(0,0,0,.1); transform: translateY(-2px); }
    .stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
    .card-tele::before   { background: linear-gradient(90deg,#06b6d4,#0ea5e9); }
    .card-field::before  { background: linear-gradient(90deg,#f59e0b,#f97316); }
    .card-live::before   { background: linear-gradient(90deg,#8b5cf6,#6366f1); }
    .card-total::before  { background: linear-gradient(90deg,#10b981,#14b8a6); }
    .card-infra::before  { background: linear-gradient(90deg,#ec4899,#f43f5e); }

    .big-num { font-family:"Roboto",sans-serif; font-size:2.75rem; font-weight:900; line-height:1; letter-spacing:-.03em; }

    .ring-wrap { position:relative; width:64px; height:64px; flex-shrink:0; }
    .ring-wrap svg { transform:rotate(-90deg); }
    .ring-bg  { fill:none; stroke:#f1f5f9; stroke-width:6; }
    .ring-val { fill:none; stroke-width:6; stroke-linecap:round; transition:stroke-dashoffset 1s ease; }
    .ring-label { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:.7rem; font-weight:800; font-family:"Roboto",sans-serif; }

    .trend-up   { background:#d1fae5; color:#059669; }
    .trend-down { background:#fee2e2; color:#dc2626; }
    .trend-badge { display:inline-flex; align-items:center; gap:.3rem; font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:9999px; }

    .chart-card { background:#fff; border:1px solid #e2e8f0; border-radius:1.25rem; padding:1.5rem; }

    /* DMO table — 7 columns now (infra added) */
    .dmo-row { display:grid; grid-template-columns:2fr 1fr 1fr 1fr 1fr 1fr 100px; align-items:center; gap:.5rem; padding:.875rem 1.25rem; border-radius:.875rem; transition:background .15s; }
    .dmo-row:hover { background:#f8fafc; }
    .dmo-row + .dmo-row { border-top:1px solid #f1f5f9; }
    .dmo-header { color:#94a3b8; font-size:.7rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; }

    .feed-item { display:flex; gap:.875rem; padding:.75rem 0; }
    .feed-item + .feed-item { border-top:1px solid #f1f5f9; }
    .feed-dot { width:.625rem; height:.625rem; border-radius:9999px; flex-shrink:0; margin-top:.35rem; }

    .legend-dot { width:.75rem; height:.75rem; border-radius:9999px; flex-shrink:0; }
    .sec-head { font-family:"Roboto",sans-serif; font-size:1rem; font-weight:800; color:#0f172a; letter-spacing:-.01em; }

    .hm-cell { border-radius:.375rem; aspect-ratio:1; transition:transform .15s; cursor:default; }
    .hm-cell:hover { transform:scale(1.3); z-index:10; position:relative; }

    @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:none} }
    .fade-up { animation:fadeUp .5s ease forwards; opacity:0; }
    .fade-up:nth-child(1){animation-delay:.05s} .fade-up:nth-child(2){animation-delay:.1s}
    .fade-up:nth-child(3){animation-delay:.15s} .fade-up:nth-child(4){animation-delay:.2s}
    .fade-up:nth-child(5){animation-delay:.25s}

    [data-tip] { position:relative; }
    [data-tip]:hover::after { content:attr(data-tip); position:absolute; bottom:calc(100% + 6px); left:50%; transform:translateX(-50%); background:#0f172a; color:#fff; font-size:.7rem; padding:.3rem .6rem; border-radius:.5rem; white-space:nowrap; pointer-events:none; z-index:50; }
</style>
@endsection

@section('main_content')

{{-- ══════════════════════════════════════════════════════════
     ROW 1 — KPI CARDS  (5 cards; infra is the 5th)
     ══════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 xl:grid-cols-5 gap-5 mb-8">

    {{-- Total --}}
    <div class="stat-card card-total fade-up col-span-2 xl:col-span-1">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">Total Audits</p>
                <div class="big-num text-slate-900">{{ number_format($totals['grand_total']) }}</div>
                <div class="flex items-center gap-2 mt-2">
                    <span class="trend-badge {{ $totals['growth_up'] ? 'trend-up' : 'trend-down' }}">
                        <i class="fas fa-arrow-{{ $totals['growth_up'] ? 'up' : 'down' }} text-[9px]"></i>
                        {{ $totals['growth_pct'] }}%
                    </span>
                    <span class="text-xs text-slate-400">vs last period</span>
                </div>
            </div>
            <div class="ring-wrap">
                <svg viewBox="0 0 64 64" width="64" height="64">
                    <circle class="ring-bg" cx="32" cy="32" r="29"/>
                    <circle class="ring-val" cx="32" cy="32" r="29"
                        stroke="#10b981"
                        stroke-dasharray="{{ round(2*M_PI*29) }}"
                        stroke-dashoffset="{{ round(2*M_PI*29*(1-$totals['completion_rate']/100)) }}"/>
                </svg>
                <div class="ring-label text-emerald-600">{{ $totals['completion_rate'] }}%</div>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-3 gap-2 pt-4 border-t border-slate-100">
            <div class="text-center"><p class="text-[10px] text-slate-400 uppercase tracking-wider">Done</p><p class="text-sm font-bold text-slate-800">{{ number_format($totals['completed']) }}</p></div>
            <div class="text-center border-x border-slate-100"><p class="text-[10px] text-slate-400 uppercase tracking-wider">Pending</p><p class="text-sm font-bold text-amber-600">{{ number_format($totals['pending']) }}</p></div>
            <div class="text-center"><p class="text-[10px] text-slate-400 uppercase tracking-wider">DMOs</p><p class="text-sm font-bold text-slate-800">{{ $totals['active_dmos'] }}</p></div>
        </div>
    </div>

    {{-- Telephonic --}}
    <div class="stat-card card-tele fade-up">
        <div class="flex items-start justify-between gap-2 mb-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">Telephonic</p>
                <div class="big-num text-slate-900">{{ number_format($tele['total']) }}</div>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl shrink-0"><i class="fas fa-phone-alt"></i></div>
        </div>
        <div class="flex items-center gap-2 mb-3">
            <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-gradient-to-r from-cyan-400 to-sky-500 rounded-full" style="width:{{ $tele['completion_rate'] }}%"></div></div>
            <span class="text-xs font-bold text-cyan-600 shrink-0">{{ $tele['completion_rate'] }}%</span>
        </div>
        <div class="flex justify-between text-xs text-slate-500">
            <span>Completed: <strong class="text-slate-800">{{ number_format($tele['completed']) }}</strong></span>
            <span class="text-rose-500">{{ number_format($tele['total']-$tele['completed']) }} pending</span>
        </div>
        
    </div>

    {{-- Field Visits --}}
    <div class="stat-card card-field fade-up">
        <div class="flex items-start justify-between gap-2 mb-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">Field Visits</p>
                <div class="big-num text-slate-900">{{ number_format($field['total']) }}</div>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0"><i class="fas fa-people-arrows"></i></div>
        </div>
        <div class="flex items-center gap-2 mb-3">
            <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-gradient-to-r from-amber-400 to-orange-500 rounded-full" style="width:{{ $field['completion_rate'] }}%"></div></div>
            <span class="text-xs font-bold text-amber-600 shrink-0">{{ $field['completion_rate'] }}%</span>
        </div>
        <div class="flex justify-between text-xs text-slate-500">
            <span>Completed: <strong class="text-slate-800">{{ number_format($field['completed']) }}</strong></span>
            <span class="text-rose-500">{{ number_format($field['total']-$field['completed']) }} pending</span>
        </div>
       
    </div>

    {{-- Live Audits --}}
    <div class="stat-card card-live fade-up">
        <div class="flex items-start justify-between gap-2 mb-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">Live Audits</p>
                <div class="big-num text-slate-900">{{ number_format($live['total']) }}</div>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center text-xl shrink-0"><i class="fas fa-hospital-user"></i></div>
        </div>
        <div class="flex items-center gap-2 mb-3">
            <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-gradient-to-r from-violet-500 to-indigo-500 rounded-full" style="width:{{ $live['ai_pass_rate'] }}%"></div></div>
            <span class="text-xs font-bold text-violet-600 shrink-0">{{ $live['ai_pass_rate'] }}%</span>
        </div>
        <div class="flex justify-between text-xs text-slate-500">
            <span>AI Passed: <strong class="text-slate-800">{{ number_format($live['ai_passed']) }}</strong></span>
            <span class="text-amber-500">{{ number_format($live['ai_skipped']) }} skipped</span>
        </div>
       
    </div>

    {{-- ── Infrastructure Audits ── --}}
    <div class="stat-card card-infra fade-up">
        <div class="flex items-start justify-between gap-2 mb-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">Infra Audits</p>
                <div class="big-num text-slate-900">{{ number_format($infra['total']) }}</div>
                <div class="flex items-center gap-2 mt-2">
                    <span class="trend-badge {{ $infra['growth_up'] ? 'trend-up' : 'trend-down' }}">
                        <i class="fas fa-arrow-{{ $infra['growth_up'] ? 'up' : 'down' }} text-[9px]"></i>
                        {{ $infra['growth_pct'] }}%
                    </span>
                    <span class="text-xs text-slate-400">vs last period</span>
                </div>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-building-columns"></i>
            </div>
        </div>
        {{-- Banner pass-rate progress bar --}}
        <div class="flex items-center gap-2 mb-3">
            <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-pink-500 to-rose-500 rounded-full"
                     style="width:{{ $infra['banner_pass_rate'] }}%"></div>
            </div>
            <span class="text-xs font-bold text-rose-500 shrink-0">{{ $infra['banner_pass_rate'] }}%</span>
        </div>
        {{-- Banner counts --}}
        <div class="flex justify-between text-xs text-slate-500 mb-3">
            <span>Banner ✓ <strong class="text-slate-800">{{ number_format($infra['banner_passed']) }}</strong></span>
            <span class="text-rose-400">✗ {{ number_format($infra['banner_failed']) }}</span>
        </div>
        
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     ROW 2 — Timeline (4 series) + Infra breakdown panel
     ══════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-8">

    <div class="chart-card xl:col-span-2">
        <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
            <div>
                <p class="sec-head">Audit Activity Timeline</p>
                <p class="text-xs text-slate-400 mt-0.5">Daily submissions across all audit types</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold flex-wrap">
                <span class="flex items-center gap-1.5"><span class="h-2 w-4 rounded-full bg-cyan-500"></span> Telephonic</span>
                <span class="flex items-center gap-1.5"><span class="h-2 w-4 rounded-full bg-amber-400"></span> Field</span>
                <span class="flex items-center gap-1.5"><span class="h-2 w-4 rounded-full bg-violet-500"></span> Live</span>
                <span class="flex items-center gap-1.5"><span class="h-2 w-4 rounded-full bg-rose-400"></span> Infra</span>
            </div>
        </div>
        <canvas id="activityChart" height="200"></canvas>
    </div>

    {{-- Infra breakdown panel --}}
    <div class="chart-card flex flex-col gap-5">
        <div>
            <p class="sec-head mb-0.5">Infra Breakdown</p>
            <p class="text-xs text-slate-400">Banner verification &amp; hygiene</p>
        </div>

        {{-- Banner donut --}}
        <div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Banner AI Check</p>
            <div class="flex items-center gap-4">
                <div class="relative w-20 h-20 shrink-0">
                    <canvas id="bannerDonut"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center text-xs font-black text-slate-700">
                        {{ $infra['banner_pass_rate'] }}%
                    </div>
                </div>
                <div class="flex flex-col gap-1.5 text-xs">
                    <span class="flex items-center gap-2"><span class="legend-dot bg-emerald-500"></span> Passed <strong class="ml-auto pl-3">{{ $infra['banner_passed'] }}</strong></span>
                    <span class="flex items-center gap-2"><span class="legend-dot bg-rose-400"></span> Failed <strong class="ml-auto pl-3">{{ $infra['banner_failed'] }}</strong></span>
                    <span class="flex items-center gap-2"><span class="legend-dot bg-slate-200"></span> Not checked <strong class="ml-auto pl-3">{{ $infra['banner_unchecked'] }}</strong></span>
                </div>
            </div>
        </div>

        {{-- Hygiene donut --}}
        <div class="border-t border-slate-100 pt-4">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Hygiene Rating</p>
            <div class="flex items-center gap-4">
                <div class="relative w-20 h-20 shrink-0">
                    <canvas id="hygieneDonut"></canvas>
                </div>
                <div class="flex flex-col gap-1.5 text-xs">
                    @php $hygieneAvg = $infra['total'] - $infra['hygiene_good'] - $infra['hygiene_poor']; @endphp
                    <span class="flex items-center gap-2"><span class="legend-dot bg-emerald-500"></span> Good <strong class="ml-auto pl-3">{{ $infra['hygiene_good'] }}</strong></span>
                    <span class="flex items-center gap-2"><span class="legend-dot bg-amber-400"></span> Average <strong class="ml-auto pl-3">{{ $hygieneAvg }}</strong></span>
                    <span class="flex items-center gap-2"><span class="legend-dot bg-rose-400"></span> Poor <strong class="ml-auto pl-3">{{ $infra['hygiene_poor'] }}</strong></span>
                </div>
            </div>
        </div>

        {{-- Public / Private split --}}
        <div class="border-t border-slate-100 pt-4">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Hospital Type</p>
            @php
                $pubPct  = $infra['total'] > 0 ? round($infra['public_count']  / $infra['total'] * 100) : 0;
                $privPct = $infra['total'] > 0 ? round($infra['private_count'] / $infra['total'] * 100) : 0;
            @endphp
            <div class="flex h-3 rounded-full overflow-hidden gap-0.5 mb-2">
                <div class="bg-blue-500 transition-all" style="width:{{ $pubPct }}%" data-tip="Public {{ $pubPct }}%"></div>
                <div class="bg-fuchsia-400 transition-all" style="width:{{ $privPct }}%" data-tip="Private {{ $privPct }}%"></div>
            </div>
            <div class="flex justify-between text-xs text-slate-500">
                <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-blue-500"></span> Public {{ $pubPct }}%</span>
                <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-fuchsia-400"></span> Private {{ $privPct }}%</span>
            </div>
        </div>
    </div>
</div>




{{-- ══════════════════════════════════════════════════════════
     ROW 4 — DMO Performance Table (Infra column added)
     ══════════════════════════════════════════════════════════ --}}
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
            <select id="sort-select" class="text-xs border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none cursor-pointer">
                <option value="total">Sort: Total</option>
                <option value="completion">Sort: Completion %</option>
                <option value="live">Sort: Live Audits</option>
                <option value="infra">Sort: Infra Audits</option>
            </select>
        </div>
    </div>

    <div class="dmo-row dmo-header bg-slate-50 rounded-xl mb-2">
        <span>Officer</span>
        <span class="text-center">Telephonic</span>
        <span class="text-center">Field Visits</span>
        <span class="text-center">Live Audits</span>
        <span class="text-center text-rose-400">Infra Audits</span>
        <span class="text-center">Total</span>
        <span class="text-center">Completion</span>
    </div>

    <div id="dmo-table-body">
        @foreach($dmoStats as $dmo)
        @php $compPct = $dmo['total'] > 0 ? round($dmo['completed']/$dmo['total']*100) : 0; @endphp
        <div class="dmo-row"
             data-name="{{ strtolower($dmo['name']) }}"
             data-total="{{ $dmo['total'] }}"
             data-completion="{{ $compPct }}"
             data-live="{{ $dmo['live'] }}"
             data-infra="{{ $dmo['infra'] }}">
            <div class="flex items-center gap-3 min-w-0">
                <div class="h-9 w-9 rounded-xl flex items-center justify-center text-xs font-black shrink-0"
                     style="background:{{ $dmo['avatar_bg'] }};color:{{ $dmo['avatar_color'] }};">
                    {{ strtoupper(substr($dmo['name'],0,2)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $dmo['name'] }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ $dmo['district'] }}</p>
                </div>
                @if($loop->index < 3)
                <span class="text-xs ml-1">
                    @if($loop->index===0)🥇@elseif($loop->index===1)🥈@else🥉@endif
                </span>
                @endif
            </div>
            <div class="text-center"><span class="text-sm font-bold text-cyan-600">{{ $dmo['tele_completed'] }}</span><span class="text-xs text-slate-400"> / {{ $dmo['tele_total'] }}</span></div>
            <div class="text-center"><span class="text-sm font-bold text-amber-600">{{ $dmo['field_completed'] }}</span><span class="text-xs text-slate-400"> / {{ $dmo['field_total'] }}</span></div>
            <div class="text-center"><span class="text-sm font-bold text-violet-600">{{ $dmo['live'] }}</span></div>
            <div class="text-center"><span class="text-sm font-bold text-rose-500">{{ $dmo['infra'] }}</span></div>
            <div class="text-center"><span class="text-sm font-bold text-slate-800">{{ $dmo['total'] }}</span></div>
            <div class="flex flex-col items-center gap-1">
                <span class="text-xs font-bold {{ $compPct>=80 ? 'text-emerald-600' : ($compPct>=50 ? 'text-amber-600' : 'text-rose-500') }}">{{ $compPct }}%</span>
                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full" style="width:{{ $compPct }}%;background:{{ $compPct>=80 ? '#10b981' : ($compPct>=50 ? '#f59e0b' : '#f43f5e') }}"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     ROW 5 — Activity Feed + Top Hospitals
     ══════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-8">

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
                    {{ $act['type']==='telephonic' ? 'bg-cyan-400'
                     : ($act['type']==='field'      ? 'bg-amber-400'
                     : ($act['type']==='live'        ? 'bg-violet-500'
                     :                                 'bg-rose-400')) }}">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $act['dmo_name'] }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        @if($act['type']==='telephonic') 📞 Telephonic
                        @elseif($act['type']==='field')  🏥 Field Visit
                        @elseif($act['type']==='live')   ⚡ Live Audit
                        @else                            🏛 Infra Audit
                        @endif
                        @if(!empty($act['patient_name']))  · <span class="font-medium">{{ $act['patient_name'] }}</span>@endif
                        @if(!empty($act['hospital_name'])) · {{ $act['hospital_name'] }}@endif
                    </p>
                </div>
                <span class="text-[10px] text-slate-400 shrink-0 mt-0.5">{{ $act['created_at']->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
    </div>

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
                        <span class="text-xs font-black text-slate-300 w-5 shrink-0">{{ $idx+1 }}</span>
                        <span class="text-sm font-semibold text-slate-700 truncate">{{ $hosp['name'] }}</span>
                    </div>
                    <span class="text-xs font-bold text-slate-800 shrink-0 ml-2">{{ number_format($hosp['count']) }}</span>
                </div>
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden ml-7">
                    <div class="h-full rounded-full"
                         style="width:{{ $maxCount>0 ? round($hosp['count']/$maxCount*100) : 0 }}%;
                                background:linear-gradient(90deg,{{ ['#06b6d4','#f59e0b','#8b5cf6','#10b981','#f43f5e','#0ea5e9','#a16207'][$idx%7] }},{{ ['#0ea5e9','#f97316','#6366f1','#14b8a6','#e11d48','#38bdf8','#d97706'][$idx%7] }});">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div id="toast-container" style="position:fixed;top:1.25rem;right:1.25rem;z-index:9999;display:flex;flex-direction:column;gap:.625rem;pointer-events:none;max-width:min(420px,calc(100vw - 2.5rem));"></div>

@endsection

@section('pageJs')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = "'DM Sans', sans-serif";
Chart.defaults.color       = '#94a3b8';

/* 1. Activity Timeline — 4 series */
(function () {
    const ctx = document.getElementById('activityChart').getContext('2d');
    const mk  = (r,g,b) => { const gr=ctx.createLinearGradient(0,0,0,200); gr.addColorStop(0,`rgba(${r},${g},${b},.22)`); gr.addColorStop(1,`rgba(${r},${g},${b},0)`); return gr; };
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartDates),
            datasets: [
                { label:'Telephonic', data:@json($chartTele),  borderColor:'#06b6d4', backgroundColor:mk(6,182,212),   tension:.4,fill:true,pointRadius:0,pointHoverRadius:5,borderWidth:2.5 },
                { label:'Field',      data:@json($chartField), borderColor:'#f59e0b', backgroundColor:mk(245,158,11),  tension:.4,fill:true,pointRadius:0,pointHoverRadius:5,borderWidth:2.5 },
                { label:'Live',       data:@json($chartLive),  borderColor:'#8b5cf6', backgroundColor:mk(139,92,246),  tension:.4,fill:true,pointRadius:0,pointHoverRadius:5,borderWidth:2.5 },
                { label:'Infra',      data:@json($chartInfra), borderColor:'#f43f5e', backgroundColor:mk(244,63,94),   tension:.4,fill:true,pointRadius:0,pointHoverRadius:5,borderWidth:2.5 },
            ]
        },
        options: {
            responsive:true, interaction:{ mode:'index', intersect:false },
            plugins:{ legend:{ display:false }, tooltip:{ backgroundColor:'#0f172a',titleColor:'#94a3b8',bodyColor:'#f1f5f9',padding:12,cornerRadius:10 } },
            scales:{
                x:{ grid:{ display:false }, ticks:{ maxTicksLimit:8, font:{ size:11 } } },
                y:{ grid:{ color:'#f1f5f9' }, border:{ dash:[4,4] }, ticks:{ font:{ size:11 } } }
            }
        }
    });
})();

/* 2. Banner donut */
(function () {
    new Chart(document.getElementById('bannerDonut'), {
        type:'doughnut',
        data:{ labels:['Passed','Failed','Not checked'], datasets:[{ data:[{{ $infra['banner_passed'] }},{{ $infra['banner_failed'] }},{{ $infra['banner_unchecked'] }}], backgroundColor:['#10b981','#f43f5e','#e2e8f0'], borderWidth:0, hoverOffset:4 }] },
        options:{ cutout:'70%', responsive:true, plugins:{ legend:{ display:false }, tooltip:{ backgroundColor:'#0f172a', bodyColor:'#f1f5f9', padding:8, cornerRadius:8 } } }
    });
})();

/* 3. Hygiene donut */
(function () {
    const avg = {{ $infra['total'] - $infra['hygiene_good'] - $infra['hygiene_poor'] }};
    new Chart(document.getElementById('hygieneDonut'), {
        type:'doughnut',
        data:{ labels:['Good','Average','Poor'], datasets:[{ data:[{{ $infra['hygiene_good'] }},avg,{{ $infra['hygiene_poor'] }}], backgroundColor:['#10b981','#fbbf24','#f43f5e'], borderWidth:0, hoverOffset:4 }] },
        options:{ cutout:'70%', responsive:true, plugins:{ legend:{ display:false }, tooltip:{ backgroundColor:'#0f172a', bodyColor:'#f1f5f9', padding:8, cornerRadius:8 } } }
    });
})();

/* 4. District stacked bar — 4 series */
(function () {
    new Chart(document.getElementById('districtBar'), {
        type:'bar',
        data:{
            labels: @json($districtLabels),
            datasets:[
                { label:'Telephonic', data:@json($districtTele),  backgroundColor:'#67e8f9', borderRadius:4 },
                { label:'Field',      data:@json($districtField), backgroundColor:'#fcd34d', borderRadius:4 },
                { label:'Live',       data:@json($districtLive),  backgroundColor:'#c4b5fd', borderRadius:4 }
            ]
        },
        options:{
            responsive:true,
            plugins:{ legend:{ position:'bottom', labels:{ boxWidth:10, padding:16, font:{ size:11 } } }, tooltip:{ backgroundColor:'#0f172a', bodyColor:'#f1f5f9', cornerRadius:8, padding:10 } },
            scales:{ x:{ stacked:true, grid:{ display:false }, ticks:{ font:{ size:11 } } }, y:{ stacked:true, grid:{ color:'#f1f5f9' }, border:{ dash:[4,4] }, ticks:{ font:{ size:11 } } } }
        }
    });
})();

/* 5. DMO table search + sort (infra column included) */
(function () {
    const tbody  = document.getElementById('dmo-table-body');
    const search = document.getElementById('dmo-search');
    const sortSel= document.getElementById('sort-select');
    function filterSort() {
        const q  = search.value.toLowerCase();
        const key= sortSel.value;
        [...tbody.querySelectorAll('.dmo-row')]
            .filter(r => r.dataset.name!==undefined)
            .sort((a,b) => +b.dataset[key] - +a.dataset[key])
            .forEach(r => { r.style.display=r.dataset.name.includes(q)?'':'none'; tbody.appendChild(r); });
    }
    search.addEventListener('input', filterSort);
    sortSel.addEventListener('change', filterSort);
})();

/* 6. Toast */
function toast(type,title,msg,dur){
    dur=dur??4500;
    const icons={success:'fa-check-circle',error:'fa-times-circle',info:'fa-info-circle'};
    const colors={success:'rgba(240,253,244,.97)',error:'rgba(255,241,242,.97)',info:'rgba(239,246,255,.97)'};
    const el=document.createElement('div');
    el.style.cssText=`pointer-events:auto;display:flex;align-items:flex-start;gap:.75rem;padding:.875rem 1rem;border-radius:.875rem;background:${colors[type]};box-shadow:0 8px 24px rgba(0,0,0,.14);font-size:.8125rem;transform:translateX(120%);opacity:0;transition:transform .32s cubic-bezier(.22,1,.36,1),opacity .28s;position:relative;overflow:hidden;`;
    el.innerHTML=`<i class="fas ${icons[type]??'fa-info-circle'}" style="margin-top:.15rem;flex-shrink:0;"></i><div><strong style="display:block;font-size:.8125rem;margin-bottom:.1rem;">${title}</strong>${msg}</div><button onclick="this.closest('div').remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;opacity:.5;font-size:.75rem;"><i class="fas fa-times"></i></button>`;
    document.getElementById('toast-container').appendChild(el);
    requestAnimationFrame(()=>requestAnimationFrame(()=>{el.style.transform='translateX(0)';el.style.opacity='1';}));
    setTimeout(()=>{el.style.transform='translateX(120%)';el.style.opacity='0';setTimeout(()=>el.remove(),300);},dur);
}
@if(session('success')) toast('success','Success','{{ session("success") }}'); @endif
@if(session('error'))   toast('error','Error','{{ session("error") }}');       @endif
</script>
@endsection

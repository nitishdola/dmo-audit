@extends('admin.layout.layout')

@section('main_title')
<div style="display:flex; align-items:center; gap:10px;">
    <div style="display:flex; flex-direction:column; gap:2px;">
        <span style="font-size:10px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:#8c96ab;">Atal Amrit Abhiyan · Assam</span>
        <span style="font-size:15px; font-weight:700; color:#0a0f1e; letter-spacing:-.02em; line-height:1;">Audit Intelligence</span>
    </div>
    <div style="display:flex; align-items:center; gap:5px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:20px; padding:4px 10px; margin-left:4px;">
        <span style="width:6px; height:6px; background:#10b981; border-radius:50%; display:inline-block; animation:pulse 2s infinite;"></span>
        <span style="font-size:11px; font-weight:600; color:#059669;">Live</span>
    </div>
</div>
@endsection

@section('pageCss')
<style>
    :root {
        --ink:      #0a0f1e;
        --ink-2:    #1a2236;
        --slate-soft:#f0f3f8;
        --slate-line:#e3e8f0;
        --slate-mid: #8c96ab;
        --white:    #ffffff;
        --teal:     #0ea5e9;
        --green:    #10b981;
        --amber:    #f59e0b;
        --rose:     #f43f5e;
        --violet:   #8b5cf6;
        --font:     'Plus Jakarta Sans', sans-serif;
        --r-md:     12px;
        --r-lg:     16px;
        --r-xl:     20px;
    }

    * { font-family: var(--font); }

    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
    @keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:none} }
    .fu { animation: fadeUp .45s ease both; }
    .fu:nth-child(1){animation-delay:.04s} .fu:nth-child(2){animation-delay:.08s}
    .fu:nth-child(3){animation-delay:.12s} .fu:nth-child(4){animation-delay:.16s}
    .fu:nth-child(5){animation-delay:.20s}

    /* ── Cards ── */
    .card {
        background: var(--white);
        border: 1px solid var(--slate-line);
        border-radius: var(--r-xl);
        padding: 20px;
        position: relative;
        overflow: hidden;
    }
    .card:hover { box-shadow: 0 8px 32px rgba(10,15,30,.07); }

    /* Coloured top stripe */
    .card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2.5px;
        border-radius: var(--r-xl) var(--r-xl) 0 0;
    }
    .ct::before { background: linear-gradient(90deg,#06b6d4,#0ea5e9); }
    .cf::before { background: linear-gradient(90deg,#f59e0b,#f97316); }
    .cl::before { background: linear-gradient(90deg,#8b5cf6,#6366f1); }
    .cg::before { background: linear-gradient(90deg,#10b981,#14b8a6); }
    .cr::before { background: linear-gradient(90deg,#f43f5e,#fb7185); }

    /* KPI number */
    .kpi-num {
        font-size: 36px; font-weight: 800;
        letter-spacing: -.04em; line-height: 1;
        color: var(--ink);
    }

    /* Icon bubble */
    .ib {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; flex-shrink: 0;
    }
    .ib-teal   { background:#e0f7ff; color:#0284c7; }
    .ib-amber  { background:#fff8e0; color:#d97706; }
    .ib-violet { background:#f3eeff; color:#7c3aed; }
    .ib-green  { background:#e7f9f2; color:#059669; }
    .ib-rose   { background:#fff0f3; color:#e11d48; }

    /* Progress bar */
    .pbar-track { height: 5px; background:#f1f5f9; border-radius:9999px; overflow:hidden; }
    .pbar-fill  { height:100%; border-radius:9999px; transition: width 1s ease; }

    /* Trend badge */
    .badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 10.5px; font-weight: 700;
        padding: 2px 7px; border-radius: 9999px;
    }
    .badge-up   { background:#d1fae5; color:#059669; }
    .badge-down { background:#fee2e2; color:#dc2626; }

    /* Sub-row inside total card */
    .sub-grid {
        display: grid; grid-template-columns: 1fr 1fr 1fr;
        border-top: 1px solid var(--slate-line);
        margin-top: 14px; padding-top: 12px;
        gap: 1px;
    }
    .sub-cell { text-align: center; padding: 0 4px; }
    .sub-cell + .sub-cell { border-left: 1px solid var(--slate-line); }
    .sub-lbl { font-size: 9.5px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--slate-mid); }
    .sub-val  { font-size: 15px; font-weight: 800; margin-top: 2px; }

    /* Section heading */
    .sec-head {
        font-size: 13.5px; font-weight: 700;
        color: var(--ink); letter-spacing: -.01em;
    }
    .sec-sub  { font-size: 11px; color: var(--slate-mid); margin-top: 2px; }

    /* Ring SVG */
    .ring-wrap { position:relative; width:56px; height:56px; flex-shrink:0; }
    .ring-wrap svg { transform:rotate(-90deg); }
    .ring-bg  { fill:none; stroke:#f1f5f9; stroke-width:5; }
    .ring-val { fill:none; stroke-width:5; stroke-linecap:round; }
    .ring-lbl { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:800; }

    /* DMO table */
    .dmo-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 90px;
        align-items: center;
        gap: 6px;
        padding: 10px 14px;
        border-radius: var(--r-md);
        transition: background .12s;
    }
    .dmo-grid:hover { background: var(--slate-soft); }
    .dmo-grid + .dmo-grid { border-top: 1px solid #f5f7fb; }
    .dmo-hdr { font-size: 9.5px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--slate-mid); }

    /* Feed */
    .feed-row { display:flex; gap:11px; padding: 9px 0; }
    .feed-row + .feed-row { border-top: 1px solid #f5f7fb; }
    .feed-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink:0; margin-top: 5px; }

    /* Hospital bars */
    .hosp-bar { height: 7px; border-radius: 9999px; transition: width 1s ease; }

    /* Tooltip */
    [data-tip] { position:relative; }
    [data-tip]:hover::after {
        content: attr(data-tip);
        position: absolute; bottom: calc(100% + 5px); left: 50%;
        transform: translateX(-50%);
        background: var(--ink); color:#fff;
        font-size: 10px; padding: 3px 7px; border-radius: 6px;
        white-space: nowrap; pointer-events: none; z-index: 50;
    }

    /* Legend */
    .leg-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
</style>
@endsection

@section('main_content')

{{-- ══ ROW 1 — KPI CARDS ══ --}}
<div style="display:grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr; gap:14px; margin-bottom:18px;"
     class="fu-grid">

    {{-- Total --}}
    <div class="card cg fu">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px;">
            <div>
                <p style="font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--slate-mid); margin-bottom:8px;">Total Audits</p>
                <div class="kpi-num">{{ number_format($totals['grand_total']) }}</div>
                <div style="display:flex; align-items:center; gap:6px; margin-top:8px;">
                    <span class="badge {{ $totals['growth_up'] ? 'badge-up' : 'badge-down' }}">
                        <i class="fas fa-arrow-{{ $totals['growth_up'] ? 'up' : 'down' }}" style="font-size:7px;"></i>
                        {{ $totals['growth_pct'] }}%
                    </span>
                    <span style="font-size:11px; color:var(--slate-mid);">vs last period</span>
                </div>
            </div>
            <div class="ring-wrap">
                <svg viewBox="0 0 56 56" width="56" height="56">
                    <circle class="ring-bg" cx="28" cy="28" r="24"/>
                    <circle class="ring-val" cx="28" cy="28" r="24"
                        stroke="#10b981"
                        stroke-dasharray="{{ round(2*M_PI*24) }}"
                        stroke-dashoffset="{{ round(2*M_PI*24*(1-$totals['completion_rate']/100)) }}"/>
                </svg>
                <div class="ring-lbl" style="color:#059669;">{{ $totals['completion_rate'] }}%</div>
            </div>
        </div>
        <div class="sub-grid">
            <div class="sub-cell"><div class="sub-lbl">Done</div><div class="sub-val" style="color:var(--green);">{{ number_format($totals['completed']) }}</div></div>
            <div class="sub-cell"><div class="sub-lbl">Pending</div><div class="sub-val" style="color:var(--amber);">{{ number_format($totals['pending']) }}</div></div>
            <div class="sub-cell"><div class="sub-lbl">DMOs</div><div class="sub-val" style="color:var(--ink);">{{ $totals['active_dmos'] }}</div></div>
        </div>
    </div>

    {{-- Telephonic --}}
    <div class="card ct fu">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin-bottom:12px;">
            <div>
                <p style="font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--slate-mid); margin-bottom:8px;">Telephonic</p>
                <div class="kpi-num" style="font-size:28px;">{{ number_format($tele['total']) }}</div>
            </div>
            <div class="ib ib-teal"><i class="fas fa-phone-alt"></i></div>
        </div>
        <div class="pbar-track" style="margin-bottom:7px;">
            <div class="pbar-fill" style="width:{{ $tele['completion_rate'] }}%; background:linear-gradient(90deg,#06b6d4,#0ea5e9);"></div>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--slate-mid);">
            <span>Done <strong style="color:var(--ink);">{{ number_format($tele['completed']) }}</strong></span>
            <span style="color:var(--teal); font-weight:600;">{{ $tele['completion_rate'] }}%</span>
        </div>
    </div>

    {{-- Field Visits --}}
    <div class="card cf fu">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin-bottom:12px;">
            <div>
                <p style="font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--slate-mid); margin-bottom:8px;">Field Visits</p>
                <div class="kpi-num" style="font-size:28px;">{{ number_format($field['total']) }}</div>
            </div>
            <div class="ib ib-amber"><i class="fas fa-people-arrows"></i></div>
        </div>
        <div class="pbar-track" style="margin-bottom:7px;">
            <div class="pbar-fill" style="width:{{ $field['completion_rate'] }}%; background:linear-gradient(90deg,#f59e0b,#f97316);"></div>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--slate-mid);">
            <span>Done <strong style="color:var(--ink);">{{ number_format($field['completed']) }}</strong></span>
            <span style="color:var(--amber); font-weight:600;">{{ $field['completion_rate'] }}%</span>
        </div>
    </div>

    {{-- Live Audits --}}
    <div class="card cl fu">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin-bottom:12px;">
            <div>
                <p style="font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--slate-mid); margin-bottom:8px;">Live Audits</p>
                <div class="kpi-num" style="font-size:28px;">{{ number_format($live['total']) }}</div>
            </div>
            <div class="ib ib-violet"><i class="fas fa-hospital-user"></i></div>
        </div>
        <div class="pbar-track" style="margin-bottom:7px;">
            <div class="pbar-fill" style="width:{{ $live['ai_pass_rate'] }}%; background:linear-gradient(90deg,#8b5cf6,#6366f1);"></div>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--slate-mid);">
            <span>AI Pass <strong style="color:var(--ink);">{{ number_format($live['ai_passed']) }}</strong></span>
            <span style="color:var(--violet); font-weight:600;">{{ $live['ai_pass_rate'] }}%</span>
        </div>
    </div>

    {{-- Infra Audits --}}
    <div class="card cr fu">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin-bottom:12px;">
            <div>
                <p style="font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--slate-mid); margin-bottom:8px;">Infra Audits</p>
                <div class="kpi-num" style="font-size:28px;">{{ number_format($infra['total']) }}</div>
                <div style="margin-top:6px;">
                    <span class="badge {{ $infra['growth_up'] ? 'badge-up' : 'badge-down' }}">
                        <i class="fas fa-arrow-{{ $infra['growth_up'] ? 'up' : 'down' }}" style="font-size:7px;"></i>
                        {{ $infra['growth_pct'] }}%
                    </span>
                </div>
            </div>
            <div class="ib ib-rose"><i class="fas fa-building-columns"></i></div>
        </div>
        <div class="pbar-track" style="margin-bottom:7px;">
            <div class="pbar-fill" style="width:{{ $infra['banner_pass_rate'] }}%; background:linear-gradient(90deg,#f43f5e,#fb7185);"></div>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--slate-mid);">
            <span>Banner ✓ <strong style="color:var(--ink);">{{ number_format($infra['banner_passed']) }}</strong></span>
            <span style="color:var(--rose); font-weight:600;">{{ $infra['banner_pass_rate'] }}%</span>
        </div>
    </div>
</div>

{{-- ══ ROW 2 — Timeline + Infra Breakdown ══ --}}
<div style="display:grid; grid-template-columns:1fr 320px; gap:14px; margin-bottom:18px;">

    <div class="card" style="padding:22px;">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:18px;">
            <div>
                <div class="sec-head">Audit Activity Timeline</div>
                <div class="sec-sub">Daily submissions across all types</div>
            </div>
            <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                @foreach([['Telephonic','#06b6d4'],['Field','#f59e0b'],['Live','#8b5cf6'],['Infra','#f43f5e']] as [$lbl,$clr])
                <span style="display:flex; align-items:center; gap:5px; font-size:11px; font-weight:600; color:var(--slate-mid);">
                    <span style="width:14px; height:3px; border-radius:9999px; background:{{ $clr }}; display:inline-block;"></span>
                    {{ $lbl }}
                </span>
                @endforeach
            </div>
        </div>
        <canvas id="activityChart" height="190"></canvas>
    </div>

    <div class="card" style="padding:22px; display:flex; flex-direction:column; gap:18px;">
        <div>
            <div class="sec-head">Infra Breakdown</div>
            <div class="sec-sub">Banner · Hygiene · Hospital type</div>
        </div>

        {{-- Banner donut --}}
        <div>
            <p style="font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--slate-mid); margin-bottom:10px;">Banner AI Check</p>
            <div style="display:flex; align-items:center; gap:14px;">
                <div style="position:relative; width:72px; height:72px; flex-shrink:0;">
                    <canvas id="bannerDonut" width="72" height="72"></canvas>
                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:var(--ink);">{{ $infra['banner_pass_rate'] }}%</div>
                </div>
                <div style="display:flex; flex-direction:column; gap:5px; font-size:11px;">
                    <span style="display:flex; align-items:center; gap:6px;"><span class="leg-dot" style="background:#10b981;"></span> Passed <strong style="margin-left:auto; padding-left:8px;">{{ $infra['banner_passed'] }}</strong></span>
                    <span style="display:flex; align-items:center; gap:6px;"><span class="leg-dot" style="background:#f43f5e;"></span> Failed <strong style="margin-left:auto; padding-left:8px;">{{ $infra['banner_failed'] }}</strong></span>
                    <span style="display:flex; align-items:center; gap:6px;"><span class="leg-dot" style="background:#e2e8f0;"></span> Unchecked <strong style="margin-left:auto; padding-left:8px;">{{ $infra['banner_unchecked'] }}</strong></span>
                </div>
            </div>
        </div>

        <div style="border-top:1px solid var(--slate-line); padding-top:16px;">
            <p style="font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--slate-mid); margin-bottom:10px;">Hygiene Rating</p>
            <div style="display:flex; align-items:center; gap:14px;">
                <div style="position:relative; width:72px; height:72px; flex-shrink:0;">
                    <canvas id="hygieneDonut" width="72" height="72"></canvas>
                </div>
                @php $hygieneAvg = $infra['total'] - $infra['hygiene_good'] - $infra['hygiene_poor']; @endphp
                <div style="display:flex; flex-direction:column; gap:5px; font-size:11px;">
                    <span style="display:flex; align-items:center; gap:6px;"><span class="leg-dot" style="background:#10b981;"></span> Good <strong style="margin-left:auto; padding-left:8px;">{{ $infra['hygiene_good'] }}</strong></span>
                    <span style="display:flex; align-items:center; gap:6px;"><span class="leg-dot" style="background:#f59e0b;"></span> Average <strong style="margin-left:auto; padding-left:8px;">{{ $hygieneAvg }}</strong></span>
                    <span style="display:flex; align-items:center; gap:6px;"><span class="leg-dot" style="background:#f43f5e;"></span> Poor <strong style="margin-left:auto; padding-left:8px;">{{ $infra['hygiene_poor'] }}</strong></span>
                </div>
            </div>
        </div>

        <div style="border-top:1px solid var(--slate-line); padding-top:16px;">
            <p style="font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--slate-mid); margin-bottom:8px;">Hospital Type</p>
            @php
                $pubPct  = $infra['total'] > 0 ? round($infra['public_count']/$infra['total']*100) : 0;
                $privPct = $infra['total'] > 0 ? round($infra['private_count']/$infra['total']*100) : 0;
            @endphp
            <div style="display:flex; height:8px; border-radius:9999px; overflow:hidden; gap:2px; margin-bottom:8px;">
                <div style="background:#3b82f6; width:{{ $pubPct }}%; transition:width 1s;" data-tip="Public {{ $pubPct }}%"></div>
                <div style="background:#c084fc; width:{{ $privPct }}%; transition:width 1s;" data-tip="Private {{ $privPct }}%"></div>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--slate-mid);">
                <span style="display:flex; align-items:center; gap:4px;"><span class="leg-dot" style="background:#3b82f6; width:8px; height:8px;"></span> Public {{ $pubPct }}%</span>
                <span style="display:flex; align-items:center; gap:4px;"><span class="leg-dot" style="background:#c084fc; width:8px; height:8px;"></span> Private {{ $privPct }}%</span>
            </div>
        </div>
    </div>
</div>

{{-- ══ ROW 3 — DMO Performance Table ══ --}}
<div class="card" style="padding:22px; margin-bottom:18px;">
    <div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:18px;">
        <div>
            <div class="sec-head">DMO Performance</div>
            <div class="sec-sub">Individual officer metrics · ranked by completion</div>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <div style="position:relative;">
                <i class="fas fa-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--slate-mid); font-size:11px; pointer-events:none;"></i>
                <input type="text" id="dmo-search" placeholder="Search officer…"
                       style="padding:7px 12px 7px 30px; font-size:12px; border:1px solid var(--slate-line); border-radius:10px; background:var(--white); color:var(--ink); outline:none; width:180px; font-family:var(--font); transition:border-color .15s;"
                       onfocus="this.style.borderColor='#0ea5e9'" onblur="this.style.borderColor='var(--slate-line)'">
            </div>
            <select id="sort-select"
                    style="padding:7px 12px; font-size:12px; border:1px solid var(--slate-line); border-radius:10px; background:var(--white); color:var(--ink); outline:none; cursor:pointer; font-family:var(--font);">
                <option value="total">Total Audits</option>
                <option value="completion">Completion %</option>
                <option value="live">Live Audits</option>
                <option value="infra">Infra Audits</option>
            </select>
        </div>
    </div>

    {{-- Header row --}}
    <div class="dmo-grid dmo-hdr" style="background:var(--slate-soft); border-radius:10px; margin-bottom:4px;">
        <span>Officer</span>
        <span style="text-align:center;">Telephonic</span>
        <span style="text-align:center;">Field</span>
        <span style="text-align:center;">Live</span>
        <span style="text-align:center; color:var(--rose);">Infra</span>
        <span style="text-align:center;">Total</span>
        <span style="text-align:center;">Completion</span>
    </div>

    <div id="dmo-table-body">
        @foreach($dmoStats as $dmo)
        @php $compPct = $dmo['total'] > 0 ? round($dmo['completed']/$dmo['total']*100) : 0;
             $compClr = $compPct>=80 ? '#059669' : ($compPct>=50 ? '#d97706' : '#e11d48');
        @endphp
        <div class="dmo-grid"
             data-name="{{ strtolower($dmo['name']) }}"
             data-total="{{ $dmo['total'] }}"
             data-completion="{{ $compPct }}"
             data-live="{{ $dmo['live'] }}"
             data-infra="{{ $dmo['infra'] }}">

            <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                <div style="width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:800; flex-shrink:0; background:{{ $dmo['avatar_bg'] }}; color:{{ $dmo['avatar_color'] }};">
                    {{ strtoupper(substr($dmo['name'],0,2)) }}
                </div>
                <div style="min-width:0;">
                    <p style="font-size:12.5px; font-weight:600; color:var(--ink); margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $dmo['name'] }}</p>
                    <p style="font-size:10.5px; color:var(--slate-mid); margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $dmo['district'] }}</p>
                </div>
                @if($loop->index < 3)
                <span style="font-size:14px; flex-shrink:0;">
                    @if($loop->index===0)🥇@elseif($loop->index===1)🥈@else🥉@endif
                </span>
                @endif
            </div>

            <div style="text-align:center;"><span style="font-size:13px; font-weight:700; color:#0284c7;">{{ $dmo['tele_completed'] }}</span><span style="font-size:11px; color:var(--slate-mid);"> / {{ $dmo['tele_total'] }}</span></div>
            <div style="text-align:center;"><span style="font-size:13px; font-weight:700; color:#d97706;">{{ $dmo['field_completed'] }}</span><span style="font-size:11px; color:var(--slate-mid);"> / {{ $dmo['field_total'] }}</span></div>
            <div style="text-align:center;"><span style="font-size:13px; font-weight:700; color:#7c3aed;">{{ $dmo['live'] }}</span></div>
            <div style="text-align:center;"><span style="font-size:13px; font-weight:700; color:var(--rose);">{{ $dmo['infra'] }}</span></div>
            <div style="text-align:center;"><span style="font-size:13px; font-weight:700; color:var(--ink);">{{ $dmo['total'] }}</span></div>

            <div style="display:flex; flex-direction:column; align-items:center; gap:4px;">
                <span style="font-size:11px; font-weight:700; color:{{ $compClr }};">{{ $compPct }}%</span>
                <div class="pbar-track" style="width:100%;">
                    <div class="pbar-fill" style="width:{{ $compPct }}%; background:{{ $compClr }};"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ══ ROW 4 — Activity Feed + Top Hospitals ══ --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:18px;">

    <div class="card" style="padding:22px;">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:16px;">
            <div>
                <div class="sec-head">Recent Activity</div>
                <div class="sec-sub">Latest submissions across all types</div>
            </div>
            <span style="width:8px; height:8px; background:var(--green); border-radius:50%; margin-top:4px; animation:pulse 2s infinite; display:inline-block;"></span>
        </div>
        <div>
            @foreach($recentActivity as $act)
            @php
                $dotClr = $act['type']==='telephonic' ? '#06b6d4'
                        : ($act['type']==='field'      ? '#f59e0b'
                        : ($act['type']==='live'        ? '#8b5cf6' : '#f43f5e'));
                $typeLabel = match($act['type']) {
                    'telephonic' => '📞 Telephonic',
                    'field'      => '🏥 Field Visit',
                    'live'       => '⚡ Live Audit',
                    default      => '🏛 Infra Audit',
                };
            @endphp
            <div class="feed-row">
                <div class="feed-dot" style="background:{{ $dotClr }};"></div>
                <div style="flex:1; min-width:0;">
                    <p style="font-size:12.5px; font-weight:600; color:var(--ink); margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $act['dmo_name'] }}</p>
                    <p style="font-size:11px; color:var(--slate-mid); margin:2px 0 0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $typeLabel }}
                        @if(!empty($act['patient_name'])) · {{ $act['patient_name'] }} @endif
                        @if(!empty($act['hospital_name'])) · {{ $act['hospital_name'] }} @endif
                    </p>
                </div>
                <span style="font-size:10px; color:var(--slate-mid); flex-shrink:0; margin-top:2px;">{{ $act['created_at']->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card" style="padding:22px;">
        <div style="margin-bottom:16px;">
            <div class="sec-head">Most Audited Hospitals</div>
            <div class="sec-sub">By total volume this period</div>
        </div>
        <div style="display:flex; flex-direction:column; gap:11px;">
            @foreach($topHospitals as $idx => $hosp)
            @php $maxCount = $topHospitals->first()['count'];
                 $gradients = ['#06b6d4,#0ea5e9','#f59e0b,#f97316','#8b5cf6,#6366f1','#10b981,#14b8a6','#f43f5e,#fb7185','#0ea5e9,#38bdf8','#a16207,#d97706'];
            @endphp
            <div>
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:5px;">
                    <div style="display:flex; align-items:center; gap:7px; min-width:0;">
                        <span style="font-size:10px; font-weight:800; color:#c8d0de; width:16px; flex-shrink:0;">{{ $idx+1 }}</span>
                        <span style="font-size:12.5px; font-weight:600; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $hosp['name'] }}</span>
                    </div>
                    <span style="font-size:12px; font-weight:700; color:var(--ink); flex-shrink:0; margin-left:8px;">{{ number_format($hosp['count']) }}</span>
                </div>
                <div class="pbar-track" style="margin-left:23px; height:6px;">
                    <div class="hosp-bar" style="width:{{ $maxCount>0 ? round($hosp['count']/$maxCount*100) : 0 }}%; background:linear-gradient(90deg,{{ $gradients[$idx%7] }});"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Toast container --}}
<div id="toast-container" style="position:fixed; top:16px; right:16px; z-index:9999; display:flex; flex-direction:column; gap:8px; pointer-events:none; max-width:min(380px,calc(100vw - 32px));"></div>

@endsection

@section('pageJs')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
Chart.defaults.font.size   = 11;
Chart.defaults.color       = '#8c96ab';

/* Activity Timeline */
(function(){
    const ctx = document.getElementById('activityChart').getContext('2d');
    const mk  = (r,g,b) => { const g2=ctx.createLinearGradient(0,0,0,190); g2.addColorStop(0,`rgba(${r},${g},${b},.18)`); g2.addColorStop(1,`rgba(${r},${g},${b},0)`); return g2; };
    new Chart(ctx,{
        type:'line',
        data:{
            labels:@json($chartDates),
            datasets:[
                {label:'Telephonic',data:@json($chartTele), borderColor:'#06b6d4',backgroundColor:mk(6,182,212),  tension:.4,fill:true,pointRadius:0,pointHoverRadius:4,borderWidth:2},
                {label:'Field',     data:@json($chartField),borderColor:'#f59e0b',backgroundColor:mk(245,158,11), tension:.4,fill:true,pointRadius:0,pointHoverRadius:4,borderWidth:2},
                {label:'Live',      data:@json($chartLive), borderColor:'#8b5cf6',backgroundColor:mk(139,92,246), tension:.4,fill:true,pointRadius:0,pointHoverRadius:4,borderWidth:2},
                {label:'Infra',     data:@json($chartInfra),borderColor:'#f43f5e',backgroundColor:mk(244,63,94),  tension:.4,fill:true,pointRadius:0,pointHoverRadius:4,borderWidth:2},
            ]
        },
        options:{
            responsive:true,
            interaction:{mode:'index',intersect:false},
            plugins:{
                legend:{display:false},
                tooltip:{backgroundColor:'#0a0f1e',titleColor:'#8c96ab',bodyColor:'#f1f5f9',padding:10,cornerRadius:10,boxPadding:4}
            },
            scales:{
                x:{grid:{display:false},ticks:{maxTicksLimit:8}},
                y:{grid:{color:'#f0f3f8'},border:{dash:[3,3]}}
            }
        }
    });
})();

/* Banner donut */
(function(){
    new Chart(document.getElementById('bannerDonut'),{
        type:'doughnut',
        data:{labels:['Passed','Failed','Unchecked'],datasets:[{data:[{{ $infra['banner_passed'] }},{{ $infra['banner_failed'] }},{{ $infra['banner_unchecked'] }}],backgroundColor:['#10b981','#f43f5e','#e2e8f0'],borderWidth:0,hoverOffset:3}]},
        options:{cutout:'72%',responsive:false,plugins:{legend:{display:false},tooltip:{backgroundColor:'#0a0f1e',bodyColor:'#f1f5f9',padding:8,cornerRadius:8}}}
    });
})();

/* Hygiene donut */
(function(){
    const avg = {{ $infra['total'] - $infra['hygiene_good'] - $infra['hygiene_poor'] }};
    new Chart(document.getElementById('hygieneDonut'),{
        type:'doughnut',
        data:{labels:['Good','Average','Poor'],datasets:[{data:[{{ $infra['hygiene_good'] }},avg,{{ $infra['hygiene_poor'] }}],backgroundColor:['#10b981','#f59e0b','#f43f5e'],borderWidth:0,hoverOffset:3}]},
        options:{cutout:'72%',responsive:false,plugins:{legend:{display:false},tooltip:{backgroundColor:'#0a0f1e',bodyColor:'#f1f5f9',padding:8,cornerRadius:8}}}
    });
})();

/* DMO table search + sort */
(function(){
    const tbody  = document.getElementById('dmo-table-body');
    const search = document.getElementById('dmo-search');
    const sortSel= document.getElementById('sort-select');
    function filterSort(){
        const q  = search.value.toLowerCase();
        const key= sortSel.value;
        [...tbody.querySelectorAll('.dmo-grid')]
            .filter(r => r.dataset.name !== undefined)
            .sort((a,b) => +b.dataset[key] - +a.dataset[key])
            .forEach(r => { r.style.display = r.dataset.name.includes(q) ? '' : 'none'; tbody.appendChild(r); });
    }
    search.addEventListener('input', filterSort);
    sortSel.addEventListener('change', filterSort);
})();

/* Toast */
function toast(type,title,msg,dur){
    dur=dur??4500;
    const colors={success:'#f0fdf4',error:'#fff1f2',info:'#eff6ff'};
    const icons ={success:'fa-check-circle',error:'fa-times-circle',info:'fa-info-circle'};
    const iconClrs={success:'#10b981',error:'#f43f5e',info:'#0ea5e9'};
    const el=document.createElement('div');
    el.style.cssText=`pointer-events:auto;display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border-radius:14px;background:${colors[type]};box-shadow:0 8px 28px rgba(10,15,30,.14);font-size:12.5px;transform:translateX(110%);opacity:0;transition:transform .3s cubic-bezier(.22,1,.36,1),opacity .25s;border:1px solid rgba(0,0,0,.06);`;
    el.innerHTML=`<i class="fas ${icons[type]??'fa-info-circle'}" style="color:${iconClrs[type]};margin-top:1px;flex-shrink:0;"></i><div><strong style="display:block;font-size:12px;margin-bottom:2px;">${title}</strong><span style="color:#64748b;">${msg}</span></div><button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;opacity:.4;font-size:11px;padding:0;"><i class="fas fa-times"></i></button>`;
    document.getElementById('toast-container').appendChild(el);
    requestAnimationFrame(()=>requestAnimationFrame(()=>{el.style.transform='translateX(0)';el.style.opacity='1';}));
    setTimeout(()=>{el.style.transform='translateX(110%)';el.style.opacity='0';setTimeout(()=>el.remove(),300);},dur);
}
@if(session('success')) toast('success','Success','{{ session("success") }}'); @endif
@if(session('error'))   toast('error','Error','{{ session("error") }}');       @endif
</script>
@endsection

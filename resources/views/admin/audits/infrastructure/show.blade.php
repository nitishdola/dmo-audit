@extends('admin.layout.layout')

@section('main_title')
<div class="flex flex-wrap items-center justify-between gap-4 mb-8">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <div class="h-8 w-1 rounded-full" style="background:linear-gradient(180deg,#ec4899,#f43f5e)"></div>
            <span class="text-xs font-bold tracking-[.2em] uppercase text-slate-400">PMJAY Assam · Admin Console</span>
        </div>
        <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight leading-none flex items-center gap-3">
            <i class="fas fa-building-columns text-rose-400 text-2xl"></i>
            {{ $audit->hospital_name }}
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            Infrastructure Audit · {{ $audit->investigation_date?->format('d M Y') ?? '—' }}
            @if($audit->submittedBy) &nbsp;·&nbsp; {{ $audit->submittedBy->name }} @endif
        </p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('admin.audits.infra-audit.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition">
            <i class="fas fa-arrow-left text-xs"></i> All Audits
        </a>
        <span class="badge {{ $audit->hospital_type === 'Public' ? 'badge-public' : 'badge-private' }} text-xs px-3 py-1.5">
            {{ $audit->hospital_type }}
        </span>
        @if($audit->ai_banner_pass === true)
            <span class="badge badge-pass text-xs px-3 py-1.5"><i class="fas fa-robot text-[10px]"></i> Banner Verified</span>
        @elseif($audit->ai_banner_pass === false)
            <span class="badge badge-fail text-xs px-3 py-1.5"><i class="fas fa-exclamation-triangle text-[10px]"></i> Banner Failed</span>
        @endif
    </div>
</div>
@endsection

@section('pageCss')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap');
    * { font-family:'DM Sans',sans-serif; }
    h1,.sec-head { font-family:"Roboto",sans-serif; }

    .chart-card { background:#fff; border:1px solid #e2e8f0; border-radius:1.25rem; padding:1.5rem; }
    .sec-head { font-family:"Roboto",sans-serif; font-size:1rem; font-weight:800; color:#0f172a; letter-spacing:-.01em; }
    .section-divider { display:flex; align-items:center; gap:.5rem; font-size:.65rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#94a3b8; margin:1.5rem 0 .875rem; }
    .section-divider::before,.section-divider::after { content:''; flex:1; height:1px; background:#e2e8f0; }

    /* ── Flags ── */
    .flag-strip { display:flex; align-items:flex-start; gap:.75rem; padding:.875rem 1.1rem; border-radius:.875rem; border:1.5px solid; margin-bottom:.5rem; font-size:.82rem; font-weight:500; }
    .flag-error   { background:#fff1f2; border-color:#fda4af; color:#9f1239; }
    .flag-warning { background:#fffbeb; border-color:#fde68a; color:#78350f; }
    .flag-icon    { flex-shrink:0; margin-top:.1rem; }

    /* ── Data cells ── */
    .ro-cell { background:#f8fafc; border:1px solid #e2e8f0; border-radius:.75rem; padding:.625rem .875rem; }
    .ro-label { font-size:.68rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:#94a3b8; display:block; margin-bottom:.25rem; }
    .ro-value { font-size:.875rem; font-weight:600; color:#1e293b; }
    .ro-value.empty { color:#cbd5e1; font-style:italic; font-weight:400; }
    .ro-remark { font-size:.72rem; color:#94a3b8; margin-top:.25rem; font-style:italic; }

    /* ── YN badges ── */
    .badge { display:inline-flex; align-items:center; gap:.3rem; font-size:.75rem; font-weight:700; padding:.3rem .8rem; border-radius:9999px; white-space:nowrap; }
    .badge-public   { background:#dbeafe; color:#1e40af; }
    .badge-private  { background:#fce7f3; color:#9d174d; }
    .badge-pass,.yn-yes  { background:#d1fae5; color:#065f46; }
    .badge-fail,.yn-no   { background:#fee2e2; color:#991b1b; }
    .yn-na  { background:#f1f5f9; color:#94a3b8; }
    .yn-good    { background:#d1fae5; color:#065f46; }
    .yn-average { background:#fef3c7; color:#92400e; }
    .yn-poor    { background:#fee2e2; color:#991b1b; }

    /* ── Equipment chips ── */
    .equip-grid { display:flex; flex-wrap:wrap; gap:.375rem; margin-top:.5rem; }
    .equip-chip { display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem; font-weight:600; padding:.25rem .65rem; border-radius:9999px; }
    .equip-yes  { background:#d1fae5; color:#065f46; }
    .equip-no   { background:#fee2e2; color:#991b1b; }

    /* ── Previous audits list ── */
    .prev-row { display:flex; align-items:center; justify-content:space-between; gap:.75rem; padding:.65rem .875rem; border-radius:.75rem; text-decoration:none; transition:background .15s; }
    .prev-row:hover { background:#fff1f4; }
    .prev-row + .prev-row { border-top:1px solid #f1f5f9; }

    /* ── Banner image ── */
    .banner-img-wrap { border-radius:.875rem; overflow:hidden; border:2px solid #34d399; max-width:480px; }
    .banner-img-wrap img { width:100%; display:block; }

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

@php
    $a = $audit;

    $ynBadge = fn (?string $v): string =>
        match ($v ?? '') {
            'Yes'  => '<span class="badge yn-yes"><i class="fas fa-check text-[10px]"></i> Yes</span>',
            'No'   => '<span class="badge yn-no"><i class="fas fa-times text-[10px]"></i> No</span>',
            default=> '<span class="badge yn-na">—</span>',
        };

    $icuLabels = ['A'=>'Standard ICU bed','B'=>'Vitals monitor','C'=>'Crash cart','D'=>'Defibrillator','E'=>'Ventilators','F'=>'Suction pumps','G'=>'Bedside oxygen','H'=>'Air conditioning'];
    $otLabels  = ['A'=>'Anesthetic machine','B'=>'Ventilator','C'=>'Laryngoscopes','D'=>'Endotracheal tubes','E'=>'Airways/Nasal tubes','F'=>'Suction apparatus','G'=>'Oxygen','H'=>'Emergency drugs','I'=>'ECG/ETCO2','J'=>'Pulse ox/BP','K'=>'Cardiac monitor','L'=>'Defibrillator'];
@endphp

{{-- ══════════════════════════════════════
     FLAGS STRIP  (only if issues exist)
     ══════════════════════════════════════ --}}
@if(count($flags) > 0)
<div class="mb-6">
    <div class="flex items-center gap-2 mb-3">
        <i class="fas fa-shield-exclamation text-rose-500"></i>
        <p class="text-sm font-bold text-slate-700">{{ count($flags) }} concern{{ count($flags) > 1 ? 's' : '' }} flagged for this audit</p>
    </div>
    @foreach($flags as $flag)
    <div class="flag-strip flag-{{ $flag['level'] }}">
        <i class="fas {{ $flag['icon'] }} flag-icon"></i>
        <span>{{ $flag['text'] }}</span>
    </div>
    @endforeach
</div>
@endif

{{-- ══════════════════════════════════════
     MAIN GRID  (content + sidebar)
     ══════════════════════════════════════ --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ── LEFT: full audit detail ── --}}
    <div class="xl:col-span-2 space-y-5">

        {{-- A. Hospital Details --}}
        <div class="chart-card">
            <p class="sec-head mb-4"><i class="fas fa-hospital text-blue-500 mr-2"></i>A. Hospital Details</p>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="ro-cell">
                    <span class="ro-label">Date of Investigation</span>
                    <span class="ro-value">{{ $a->investigation_date?->format('d M Y') ?? '—' }}</span>
                </div>
                <div class="ro-cell">
                    <span class="ro-label">Hospital ID</span>
                    <span class="ro-value {{ $a->hospital_id ? '' : 'empty' }} font-mono">{{ $a->hospital_id ?? '—' }}</span>
                </div>
                <div class="ro-cell">
                    <span class="ro-label">Type</span>
                    <span class="badge {{ $a->hospital_type === 'Public' ? 'badge-public' : 'badge-private' }}">{{ $a->hospital_type }}</span>
                </div>
                <div class="ro-cell col-span-2 md:col-span-3">
                    <span class="ro-label">Hospital Name</span>
                    <span class="ro-value text-base">{{ $a->hospital_name }}</span>
                </div>
                <div class="ro-cell col-span-2 md:col-span-3">
                    <span class="ro-label">Address</span>
                    <span class="ro-value">{{ $a->hospital_address }}</span>
                </div>
                <div class="ro-cell">
                    <span class="ro-label">Beneficiaries (TMS)</span>
                    <span class="ro-value {{ $a->pmjay_beneficiaries_tms !== null ? '' : 'empty' }}">{{ $a->pmjay_beneficiaries_tms ?? '—' }}</span>
                </div>
                <div class="ro-cell">
                    <span class="ro-label">Beneficiaries (Actual)</span>
                    <span class="ro-value {{ $a->pmjay_beneficiaries_actual !== null ? '' : 'empty' }}">{{ $a->pmjay_beneficiaries_actual ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- B. Infrastructure --}}
        <div class="chart-card">
            <p class="sec-head mb-1"><i class="fas fa-building text-emerald-500 mr-2"></i>B. Hospital Infrastructure</p>

            {{-- Existence & Registration --}}
            <div class="section-divider"><span>Existence &amp; Registration</span></div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="ro-cell">
                    <span class="ro-label">Hospital Existence</span>
                    {!! $ynBadge($a->hospital_existence) !!}
                    @if($a->hospital_existence_remarks)<p class="ro-remark">{{ $a->hospital_existence_remarks }}</p>@endif
                </div>
                <div class="ro-cell">
                    <span class="ro-label">Hospital Response</span>
                    <span class="ro-value {{ $a->hospital_response ? '' : 'empty' }}">{{ $a->hospital_response ?? '—' }}</span>
                    @if($a->hospital_response_remarks)<p class="ro-remark">{{ $a->hospital_response_remarks }}</p>@endif
                </div>
                <div class="ro-cell">
                    <span class="ro-label">DGHS Registered</span>
                    {!! $ynBadge($a->dghs_registered) !!}
                </div>
            </div>

            {{-- Banner photo --}}
            @if($a->banner_photo_path)
            <div class="section-divider"><span>Hospital Banner Photo</span></div>
            <div class="banner-img-wrap mb-2">
                <img src="{{ $a->banner_photo_url }}" alt="Hospital banner">
            </div>
            @if($a->ai_banner_details)
            <p class="text-xs text-slate-500 mb-3 max-w-prose">{{ $a->ai_banner_details }}</p>
            @endif
            @endif

            {{-- PMAM Kiosk --}}
            <div class="section-divider"><span>PMAM Kiosk &amp; Boards</span></div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="ro-cell"><span class="ro-label">PMAM Kiosk Available</span>{!! $ynBadge($a->pmam_kiosk_available) !!}</div>
                <div class="ro-cell"><span class="ro-label">Kiosk Location</span><span class="ro-value {{ $a->pmam_kiosk_location ? '' : 'empty' }}">{{ $a->pmam_kiosk_location ?? '—' }}</span></div>
                <div class="ro-cell"><span class="ro-label">Promo Boards Displayed</span>{!! $ynBadge($a->promo_boards_displayed) !!}</div>
            </div>

            {{-- Beds --}}
            <div class="section-divider"><span>Beds &amp; Wards</span></div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="ro-cell"><span class="ro-label">Total Beds</span><span class="ro-value">{{ $a->total_beds ?? '—' }}</span></div>
                <div class="ro-cell"><span class="ro-label">General Ward Beds</span><span class="ro-value">{{ $a->general_ward_beds ?? '—' }}</span></div>
                <div class="ro-cell">
                    <span class="ro-label">4 ft Distance Maintained</span>
                    {!! $ynBadge($a->bed_distance_adequate) !!}
                    @if($a->bed_distance_remarks)<p class="ro-remark">{{ $a->bed_distance_remarks }}</p>@endif
                </div>
            </div>

            {{-- HDU --}}
            <div class="section-divider"><span>HDU</span></div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="ro-cell"><span class="ro-label">HDU Available</span>{!! $ynBadge($a->hdu_available) !!}</div>
                <div class="ro-cell"><span class="ro-label">HDU Beds</span><span class="ro-value">{{ $a->hdu_beds ?? '—' }}</span></div>
            </div>

            {{-- ICU --}}
            <div class="section-divider"><span>ICU</span></div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="ro-cell"><span class="ro-label">ICU Available</span>{!! $ynBadge($a->icu_available) !!}</div>
                <div class="ro-cell"><span class="ro-label">ICU Beds</span><span class="ro-value">{{ $a->icu_beds ?? '—' }}</span></div>
                <div class="ro-cell"><span class="ro-label">ICU Well Equipped</span>{!! $ynBadge($a->icu_well_equipped) !!}</div>
            </div>
            @if($a->icu_equipment)
            <div class="ro-cell mt-3">
                <span class="ro-label">ICU Equipment (A–H)</span>
                <div class="equip-grid">
                    @foreach($icuLabels as $k => $lbl)
                    @php $v = $a->icu_equipment[$k] ?? null; @endphp
                    <span class="equip-chip {{ $v === 'Yes' ? 'equip-yes' : 'equip-no' }}">
                        {{ $v === 'Yes' ? '✓' : '✗' }} {{ $k }}: {{ $lbl }}
                    </span>
                    @endforeach
                </div>
                @if($a->icu_equipment_remarks)<p class="ro-remark mt-2">{{ $a->icu_equipment_remarks }}</p>@endif
            </div>
            @endif

            {{-- OT --}}
            <div class="section-divider"><span>Operation Theatre (OT)</span></div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="ro-cell"><span class="ro-label">OT Available</span>{!! $ynBadge($a->ot_available) !!}</div>
                <div class="ro-cell"><span class="ro-label">No. of OTs</span><span class="ro-value">{{ $a->ot_count ?? '—' }}</span></div>
                <div class="ro-cell"><span class="ro-label">No. of OT Tables</span><span class="ro-value">{{ $a->ot_tables ?? '—' }}</span></div>
                <div class="ro-cell">
                    <span class="ro-label">OT Sterilization</span>{!! $ynBadge($a->ot_sterilization) !!}
                    @if($a->ot_sterilization_remarks)<p class="ro-remark">{{ $a->ot_sterilization_remarks }}</p>@endif
                </div>
                <div class="ro-cell"><span class="ro-label">OT Lighting</span>{!! $ynBadge($a->ot_lighting) !!}</div>
                <div class="ro-cell"><span class="ro-label">OT Air Conditioning</span>{!! $ynBadge($a->ot_ac) !!}</div>
                <div class="ro-cell"><span class="ro-label">OT Well Equipped</span>{!! $ynBadge($a->ot_well_equipped) !!}</div>
            </div>
            @if($a->ot_equipment)
            <div class="ro-cell mt-3">
                <span class="ro-label">OT Equipment (A–L)</span>
                <div class="equip-grid">
                    @foreach($otLabels as $k => $lbl)
                    @php $v = $a->ot_equipment[$k] ?? null; @endphp
                    <span class="equip-chip {{ $v === 'Yes' ? 'equip-yes' : 'equip-no' }}">
                        {{ $v === 'Yes' ? '✓' : '✗' }} {{ $k }}: {{ $lbl }}
                    </span>
                    @endforeach
                </div>
                @if($a->ot_equipment_remarks)<p class="ro-remark mt-2">{{ $a->ot_equipment_remarks }}</p>@endif
            </div>
            @endif

            {{-- Diagnostics & Hygiene --}}
            <div class="section-divider"><span>Diagnostics &amp; Hygiene</span></div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="ro-cell">
                    <span class="ro-label">Pathology / Diagnostics</span>
                    <span class="ro-value {{ $a->pathology_diagnostics ? '' : 'empty' }}">{{ $a->pathology_diagnostics ?? '—' }}</span>
                    @if($a->pathology_remarks)<p class="ro-remark">{{ $a->pathology_remarks }}</p>@endif
                </div>
                <div class="ro-cell">
                    <span class="ro-label">Biomedical Waste Mgmt</span>
                    {!! $ynBadge($a->biomedical_waste) !!}
                </div>
                <div class="ro-cell">
                    <span class="ro-label">Overall Hygiene</span>
                    @if($a->overall_hygiene)
                        <span class="badge yn-{{ strtolower($a->overall_hygiene) }}">{{ $a->overall_hygiene }}</span>
                    @else
                        <span class="badge yn-na">—</span>
                    @endif
                    @if($a->overall_hygiene_remarks)<p class="ro-remark">{{ $a->overall_hygiene_remarks }}</p>@endif
                </div>
            </div>
            @if($a->infra_other_remarks)
            <div class="ro-cell mt-3">
                <span class="ro-label">Other Remarks (Infrastructure)</span>
                <span class="ro-value">{{ $a->infra_other_remarks }}</span>
            </div>
            @endif
        </div>

        {{-- C. Human Resource --}}
        <div class="chart-card">
            <p class="sec-head mb-4"><i class="fas fa-user-doctor text-violet-500 mr-2"></i>C. Human Resource</p>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="ro-cell">
                    <span class="ro-label">PMAM Available</span>
                    {!! $ynBadge($a->pmam_available) !!}
                    @if($a->pmam_available_remarks)<p class="ro-remark">{{ $a->pmam_available_remarks }}</p>@endif
                </div>
                <div class="ro-cell">
                    <span class="ro-label">On-duty Doctors</span>
                    {!! $ynBadge($a->onduty_doctors) !!}
                </div>
                @if($a->onduty_doctor_types)
                <div class="ro-cell">
                    <span class="ro-label">Doctor Types Present</span>
                    <div class="flex flex-wrap gap-1 mt-1">
                        @foreach(['A'=>'RMO','B'=>'Emergency','C'=>'ICU'] as $k => $lbl)
                        @php $v = $a->onduty_doctor_types[$k] ?? null; @endphp
                        <span class="badge text-[10px] {{ $v === 'Yes' ? 'yn-yes' : 'yn-no' }}">{{ $lbl }}: {{ $v ?? '—' }}</span>
                        @endforeach
                    </div>
                    @if($a->onduty_doctors_remarks)<p class="ro-remark mt-1">{{ $a->onduty_doctors_remarks }}</p>@endif
                </div>
                @endif
                <div class="ro-cell">
                    <span class="ro-label">Adequate Nurses</span>
                    {!! $ynBadge($a->adequate_nurses) !!}
                    @if($a->adequate_nurses_remarks)<p class="ro-remark">{{ $a->adequate_nurses_remarks }}</p>@endif
                </div>
                <div class="ro-cell">
                    <span class="ro-label">Nurses Qualified</span>
                    {!! $ynBadge($a->nurses_qualified) !!}
                </div>
                <div class="ro-cell"><span class="ro-label">Technicians Available</span>{!! $ynBadge($a->technicians_available) !!}</div>
                <div class="ro-cell"><span class="ro-label">Pharmacists Available</span>{!! $ynBadge($a->pharmacists_available) !!}</div>
                <div class="ro-cell">
                    <span class="ro-label">Specialists Available</span>
                    {!! $ynBadge($a->specialists_available) !!}
                    @if($a->specialists_remarks)<p class="ro-remark">{{ $a->specialists_remarks }}</p>@endif
                </div>
            </div>
            @if($a->hr_other_remarks)
            <div class="ro-cell mt-3">
                <span class="ro-label">Other Remarks (HR)</span>
                <span class="ro-value">{{ $a->hr_other_remarks }}</span>
            </div>
            @endif
        </div>

    </div>{{-- /left col --}}

    {{-- ── RIGHT SIDEBAR ── --}}
    <div class="space-y-5">

        {{-- Submission Meta --}}
        <div class="chart-card">
            <p class="sec-head mb-4">Submission Details</p>
            <div class="space-y-3">
                <div class="ro-cell">
                    <span class="ro-label">Submitted By</span>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="h-7 w-7 rounded-lg bg-rose-100 text-rose-600 text-xs font-black flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($a->submittedBy?->name ?? '?', 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $a->submittedBy?->name ?? '—' }}</p>
                            <p class="text-xs text-slate-400">{{ $a->created_at?->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                </div>
                <div class="ro-cell">
                    <span class="ro-label">Record ID</span>
                    <span class="ro-value font-mono text-xs">#{{ $a->id }}</span>
                </div>
                <div class="ro-cell">
                    <span class="ro-label">Last Updated</span>
                    <span class="ro-value text-xs">{{ $a->updated_at?->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        {{-- AI Banner Verification --}}
        <div class="chart-card" style="border-color:{{ is_null($a->ai_banner_pass) ? '#e2e8f0' : ($a->ai_banner_pass ? '#86efac' : '#fda4af') }};">
            <p class="sec-head mb-3"><i class="fas fa-robot mr-2 text-violet-500"></i>Banner AI Check</p>
            @if(is_null($a->ai_banner_pass))
                <div class="flex items-center gap-2 text-slate-400 text-sm">
                    <i class="fas fa-circle-info"></i>
                    <span>Not performed for this submission.</span>
                </div>
            @else
                <div class="flex items-center gap-2 mb-3">
                    @if($a->ai_banner_pass)
                        <span class="badge badge-pass text-xs"><i class="fas fa-check-circle"></i> Passed</span>
                    @else
                        <span class="badge badge-fail text-xs"><i class="fas fa-times-circle"></i> Failed</span>
                    @endif
                    <span class="badge {{ $a->ai_pmjay_branding ? 'badge-pass' : 'badge-fail' }} text-xs">
                        PMJAY Branding: {{ $a->ai_pmjay_branding ? '✓' : '✗' }}
                    </span>
                </div>
                @if($a->ai_banner_summary)
                <p class="text-sm font-semibold text-slate-700 mb-1">{{ $a->ai_banner_summary }}</p>
                @endif
                @if($a->ai_banner_details)
                <p class="text-xs text-slate-500 leading-relaxed">{{ $a->ai_banner_details }}</p>
                @endif
            @endif
            @if($a->banner_remarks)
            <p class="text-xs text-slate-400 italic mt-2 pt-2 border-t border-slate-100">{{ $a->banner_remarks }}</p>
            @endif
        </div>

        {{-- Infrastructure Quick Scores --}}
        <div class="chart-card">
            <p class="sec-head mb-4">Infrastructure Scores</p>
            @php
                $checks = [
                    ['Hospital Exists',    $a->hospital_existence],
                    ['DGHS Registered',    $a->dghs_registered],
                    ['PMAM Kiosk',         $a->pmam_kiosk_available],
                    ['ICU Available',      $a->icu_available],
                    ['OT Available',       $a->ot_available],
                    ['OT Sterilization',   $a->ot_sterilization],
                    ['Biomedical Waste',   $a->biomedical_waste],
                    ['Adequate Nurses',    $a->adequate_nurses],
                    ['Specialists On-site',$a->specialists_available],
                ];
                $yesCount = collect($checks)->filter(fn($c) => $c[1] === 'Yes')->count();
                $score = count($checks) > 0 ? round($yesCount / count($checks) * 100) : 0;
                $scoreColor = $score >= 80 ? '#10b981' : ($score >= 50 ? '#f59e0b' : '#f43f5e');
            @endphp
            {{-- Score ring --}}
            <div class="flex items-center gap-4 mb-4">
                <div class="relative w-16 h-16 shrink-0">
                    <svg viewBox="0 0 64 64" width="64" height="64" style="transform:rotate(-90deg)">
                        <circle fill="none" stroke="#f1f5f9" stroke-width="6" cx="32" cy="32" r="29"/>
                        <circle fill="none" stroke="{{ $scoreColor }}" stroke-width="6" stroke-linecap="round"
                                cx="32" cy="32" r="29"
                                stroke-dasharray="{{ round(2*M_PI*29) }}"
                                stroke-dashoffset="{{ round(2*M_PI*29*(1-$score/100)) }}"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center text-xs font-black" style="color:{{ $scoreColor }}">
                        {{ $score }}%
                    </div>
                </div>
                <div>
                    <p class="font-bold text-slate-800">{{ $yesCount }} / {{ count($checks) }} checks passed</p>
                    <p class="text-xs text-slate-400">Based on key infrastructure criteria</p>
                </div>
            </div>
            <div class="space-y-1.5">
                @foreach($checks as [$label, $val])
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-600">{{ $label }}</span>
                    @if($val === 'Yes')
                        <i class="fas fa-check-circle text-emerald-500"></i>
                    @elseif($val === 'No')
                        <i class="fas fa-times-circle text-rose-400"></i>
                    @else
                        <i class="fas fa-minus text-slate-300"></i>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Previous Audits for same hospital --}}
        @if($previousAudits->isNotEmpty())
        <div class="chart-card">
            <p class="sec-head mb-3">Previous Audits — Same Hospital</p>
            @foreach($previousAudits as $prev)
            <a href="{{ route('admin.audits.infra-audit.show', $prev->id) }}" class="prev-row">
                <div>
                    <p class="text-xs font-semibold text-slate-700">{{ $prev->investigation_date?->format('d M Y') ?? '—' }}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">
                        @if(!is_null($prev->ai_banner_pass))
                            Banner: {{ $prev->ai_banner_pass ? '✓ Pass' : '✗ Fail' }}
                        @else
                            Banner: —
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    @if($prev->overall_hygiene)
                    <span class="badge yn-{{ strtolower($prev->overall_hygiene) }} text-[10px]">{{ $prev->overall_hygiene }}</span>
                    @endif
                    <i class="fas fa-chevron-right text-slate-300 text-[10px]"></i>
                </div>
            </a>
            @endforeach
        </div>
        @endif

    </div>{{-- /sidebar --}}
</div>

<div id="toast-container"></div>

<script>
function toast(type, title, msg, dur) {
    dur = dur ?? 4500;
    const icons  = { success:'fa-check-circle', error:'fa-times-circle' };
    const el = document.createElement('div');
    el.className = 'toast toast-' + type;
    el.innerHTML = `<div class="toast-icon"><i class="fas ${icons[type]}"></i></div>
        <div class="toast-body"><div class="toast-title">${title}</div>${msg}</div>
        <button class="toast-close" onclick="this.closest('.toast').remove()"><i class="fas fa-times"></i></button>
        <div class="toast-progress" style="animation-duration:${dur}ms;"></div>`;
    document.getElementById('toast-container').appendChild(el);
    requestAnimationFrame(() => requestAnimationFrame(() => el.classList.add('toast-in')));
    const t = setTimeout(() => { el.classList.replace('toast-in','toast-out'); el.addEventListener('transitionend',()=>el.remove(),{once:true}); }, dur);
    el.querySelector('.toast-close').addEventListener('click', () => { clearTimeout(t); el.classList.replace('toast-in','toast-out'); });
}
@if(session('success')) toast('success', 'Success', '{{ session("success") }}'); @endif
@if(session('error'))   toast('error',   'Error',   '{{ session("error") }}');   @endif
</script>

@endsection

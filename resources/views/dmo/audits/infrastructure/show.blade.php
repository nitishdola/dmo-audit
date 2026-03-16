@extends('dmo.layout.layout')

@section('main_title')
<div class="flex items-center gap-2 text-sm text-slate-400 mb-5">
    <a href="{{ route('dmo.dashboard') }}" class="hover:text-emerald-600 transition-colors">
        <i class="fas fa-arrow-left mr-1 text-xs"></i> Dashboard
    </a>
    <span class="text-slate-300">/</span>
    <a href="{{ route('dmo.audits.infra-audit.index') }}" class="hover:text-emerald-600 transition-colors">Infrastructure Audits</a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-600 font-medium">{{ $infraAudit->hospital_name }}</span>
</div>
<div class="flex items-start justify-between flex-wrap gap-3 mb-7">
    <div>
        <h2 class="text-xl md:text-3xl font-semibold text-slate-800 tracking-tight flex items-center gap-2">
            <i class="fas fa-building-columns text-emerald-600"></i>
            Infrastructure Audit
            <span class="text-sm font-normal text-slate-400">· Read-only</span>
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            Annexure 2.2 · Hospital Infrastructure &amp; Human Resource · Sections A–C
        </p>
    </div>
    @can('update', $infraAudit)
    <a href="{{ route('dmo.audits.infra-audit.edit', $infraAudit) }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold shadow transition-all">
        <i class="fas fa-pen-to-square"></i> Edit
    </a>
    @endcan
</div>
@endsection

@section('pageCss')
<style>
    .completed-banner { display:flex; align-items:center; gap:.75rem; background:linear-gradient(135deg,#ecfdf5,#d1fae5); border:1.5px solid #6ee7b7; border-radius:1rem; padding:1rem 1.25rem; margin-bottom:1.5rem; }
    .completed-banner .icon-wrap { width:2.5rem; height:2.5rem; border-radius:9999px; background:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0; }

    .ro-cell  { background:#f8fafc; border:1px solid #e2e8f0; border-radius:.75rem; padding:.625rem .875rem; }
    .ro-label { font-size:.7rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:#94a3b8; display:block; margin-bottom:.25rem; }
    .ro-value { font-size:.875rem; font-weight:600; color:#1e293b; }
    .ro-value.empty { color:#cbd5e1; font-style:italic; font-weight:400; }

    .yn-badge { display:inline-flex; align-items:center; gap:.35rem; font-size:.78rem; font-weight:700; padding:.28rem .8rem; border-radius:9999px; white-space:nowrap; }
    .yn-yes { background:#d1fae5; color:#065f46; }
    .yn-no  { background:#fee2e2; color:#991b1b; }
    .yn-na  { background:#f1f5f9; color:#94a3b8; }

    .ai-result-strip { display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; padding:.75rem 1rem; border-radius:.875rem; border:1px solid; font-size:.8rem; font-weight:500; margin-bottom:1.5rem; }
    .ai-pass-strip { background:#f0fdf4; border-color:#86efac; color:#166534; }
    .ai-fail-strip { background:#fff1f2; border-color:#fda4af; color:#9f1239; }
    .ai-skip-strip { background:#f8fafc; border-color:#e2e8f0; color:#64748b; }

    .section-badge { display:flex; align-items:center; gap:.5rem; font-size:.65rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#94a3b8; margin:1.5rem 0 .75rem; }
    .section-badge::before,.section-badge::after { content:''; flex:1; height:1px; background:#e2e8f0; }

    .equip-grid { display:flex; flex-wrap:wrap; gap:.375rem; margin-top:.5rem; }
    .equip-chip { display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem; font-weight:600; padding:.25rem .65rem; border-radius:9999px; }
    .equip-yes  { background:#d1fae5; color:#065f46; }
    .equip-no   { background:#fee2e2; color:#991b1b; }

    #toast-container { position:fixed; top:1.25rem; right:1.25rem; z-index:9999; display:flex; flex-direction:column; gap:.625rem; pointer-events:none; max-width:min(420px,calc(100vw - 2.5rem)); }
    .toast { pointer-events:auto; display:flex; align-items:flex-start; gap:.75rem; padding:.875rem 1rem; border-radius:.875rem; border:1.5px solid transparent; box-shadow:0 8px 24px rgba(0,0,0,.14); font-size:.8rem; font-weight:500; line-height:1.45; transform:translateX(120%); opacity:0; transition:transform .32s cubic-bezier(.22,1,.36,1),opacity .28s ease; cursor:default; position:relative; overflow:hidden; }
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
    @keyframes toast-drain { from { width:100%; } to { width:0%; } }
</style>
@endsection

@section('main_content')

@php
    $ia = $infraAudit;

    $ynBadge = fn(?string $v): string =>
        $v ? '<span class="yn-badge ' . match($v) { 'Yes' => 'yn-yes', 'No' => 'yn-no', default => 'yn-na' } . '">' . $v . '</span>'
           : '<span class="yn-badge yn-na">—</span>';

    $icuLabels = ['A'=>'Standard ICU bed','B'=>'Vitals monitor','C'=>'Crash cart','D'=>'Defibrillator','E'=>'Ventilators','F'=>'Suction pumps','G'=>'Bedside oxygen','H'=>'Air conditioning'];
    $otLabels  = ['A'=>'Anesthetic machine','B'=>'Ventilator','C'=>'Laryngoscopes','D'=>'Endotracheal tubes','E'=>'Airways/Nasal tubes','F'=>'Suction apparatus','G'=>'Oxygen','H'=>'Emergency drugs','I'=>'ECG/ETCO2','J'=>'Pulse ox/BP','K'=>'Cardiac monitor','L'=>'Defibrillator'];
@endphp

{{-- Submission banner --}}
<div class="completed-banner">
    <div class="icon-wrap"><i class="fas fa-check text-white text-sm"></i></div>
    <div>
        <p class="font-semibold text-emerald-800 text-sm">Infrastructure Audit submitted</p>
        <p class="text-emerald-700 text-xs mt-0.5">
            {{ $ia->created_at?->format('d M Y, h:i A') ?? '—' }}
            @if($ia->submittedBy) &nbsp;·&nbsp; {{ $ia->submittedBy->name }} @endif
        </p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-md p-4 md:p-8">

    

    {{-- ── A. Hospital Details ── --}}
    <div class="section-badge"><span>A. Hospital Details</span></div>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="ro-cell">
            <span class="ro-label">Date of Investigation</span>
            <span class="ro-value">{{ $ia->investigation_date?->format('d M Y') ?? '—' }}</span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Hospital ID</span>
            <span class="ro-value {{ $ia->hospital_id ? '' : 'empty' }}">{{ $ia->hospital_id ?? '—' }}</span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Type</span>
            <span class="ro-value">{{ $ia->hospital_type }}</span>
        </div>
        <div class="ro-cell col-span-2 lg:col-span-3">
            <span class="ro-label">Hospital Name</span>
            <span class="ro-value">{{ $ia->hospital_name }}</span>
        </div>
        <div class="ro-cell col-span-2 lg:col-span-3">
            <span class="ro-label">Address</span>
            <span class="ro-value">{{ $ia->hospital_address }}</span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Beneficiaries (TMS)</span>
            <span class="ro-value {{ $ia->pmjay_beneficiaries_tms !== null ? '' : 'empty' }}">{{ $ia->pmjay_beneficiaries_tms ?? '—' }}</span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Beneficiaries (Actual)</span>
            <span class="ro-value {{ $ia->pmjay_beneficiaries_actual !== null ? '' : 'empty' }}">{{ $ia->pmjay_beneficiaries_actual ?? '—' }}</span>
        </div>
    </div>

    {{-- Banner photo --}}
    @if($ia->banner_photo_path)
    <div class="section-badge"><span>Hospital Banner Photo</span></div>
    <div style="max-width:480px;border-radius:.875rem;overflow:hidden;border:2px solid #34d399;">
        <img src="{{ $ia->banner_photo_url }}" alt="Hospital banner" style="width:100%;display:block;">
    </div>
    @if($ia->ai_banner_details)
        <p class="text-xs text-slate-500 mt-2 max-w-prose">{{ $ia->ai_banner_details }}</p>
    @endif
    @endif

    {{-- ── B. Infrastructure: Existence & Registration ── --}}
    <div class="section-badge"><span>B. Infrastructure — Existence &amp; Registration</span></div>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="ro-cell">
            <span class="ro-label">Hospital Existence</span>
            {!! $ynBadge($ia->hospital_existence) !!}
            @if($ia->hospital_existence_remarks)<p class="text-xs text-slate-500 mt-1">{{ $ia->hospital_existence_remarks }}</p>@endif
        </div>
        <div class="ro-cell">
            <span class="ro-label">Hospital Response</span>
            <span class="ro-value {{ $ia->hospital_response ? '' : 'empty' }}">{{ $ia->hospital_response ?? '—' }}</span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">DGHS Registered</span>
            {!! $ynBadge($ia->dghs_registered) !!}
        </div>
    </div>

    {{-- PMAM Kiosk --}}
    <div class="section-badge"><span>PMAM Kiosk &amp; Boards</span></div>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="ro-cell">
            <span class="ro-label">PMAM Kiosk Available</span>
            {!! $ynBadge($ia->pmam_kiosk_available) !!}
        </div>
        <div class="ro-cell">
            <span class="ro-label">Kiosk Location</span>
            <span class="ro-value {{ $ia->pmam_kiosk_location ? '' : 'empty' }}">{{ $ia->pmam_kiosk_location ?? '—' }}</span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Promo Boards Displayed</span>
            {!! $ynBadge($ia->promo_boards_displayed) !!}
            @if($ia->promo_boards_remarks)<p class="text-xs text-slate-500 mt-1">{{ $ia->promo_boards_remarks }}</p>@endif
        </div>
    </div>

    {{-- Beds --}}
    <div class="section-badge"><span>Beds &amp; Wards</span></div>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="ro-cell"><span class="ro-label">Total Beds</span><span class="ro-value">{{ $ia->total_beds ?? '—' }}</span></div>
        <div class="ro-cell"><span class="ro-label">General Ward Beds</span><span class="ro-value">{{ $ia->general_ward_beds ?? '—' }}</span></div>
        <div class="ro-cell">
            <span class="ro-label">4 ft Distance Maintained</span>
            {!! $ynBadge($ia->bed_distance_adequate) !!}
            @if($ia->bed_distance_remarks)<p class="text-xs text-slate-500 mt-1">{{ $ia->bed_distance_remarks }}</p>@endif
        </div>
    </div>

    {{-- HDU --}}
    <div class="section-badge"><span>HDU</span></div>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="ro-cell"><span class="ro-label">HDU Available</span>{!! $ynBadge($ia->hdu_available) !!}</div>
        <div class="ro-cell"><span class="ro-label">HDU Beds</span><span class="ro-value">{{ $ia->hdu_beds ?? '—' }}</span></div>
    </div>

    {{-- ICU --}}
    <div class="section-badge"><span>ICU</span></div>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="ro-cell"><span class="ro-label">ICU Available</span>{!! $ynBadge($ia->icu_available) !!}</div>
        <div class="ro-cell"><span class="ro-label">ICU Beds</span><span class="ro-value">{{ $ia->icu_beds ?? '—' }}</span></div>
        <div class="ro-cell">
            <span class="ro-label">ICU Well Equipped</span>
            {!! $ynBadge($ia->icu_well_equipped) !!}
        </div>
        @if($ia->icu_equipment)
        <div class="ro-cell col-span-2 lg:col-span-3">
            <span class="ro-label">ICU Equipment (A–H)</span>
            <div class="equip-grid">
                @foreach($icuLabels as $k => $lbl)
                    @php $v = $ia->icu_equipment[$k] ?? null; @endphp
                    <span class="equip-chip {{ $v === 'Yes' ? 'equip-yes' : 'equip-no' }}">
                        {{ $v === 'Yes' ? '✓' : '✗' }} {{ $k }}: {{ $lbl }}
                    </span>
                @endforeach
            </div>
            @if($ia->icu_equipment_remarks)<p class="text-xs text-slate-500 mt-2">{{ $ia->icu_equipment_remarks }}</p>@endif
        </div>
        @endif
    </div>

    {{-- OT --}}
    <div class="section-badge"><span>Operation Theatre (OT)</span></div>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="ro-cell"><span class="ro-label">OT Available</span>{!! $ynBadge($ia->ot_available) !!}</div>
        <div class="ro-cell"><span class="ro-label">No. of OTs</span><span class="ro-value">{{ $ia->ot_count ?? '—' }}</span></div>
        <div class="ro-cell"><span class="ro-label">No. of OT Tables</span><span class="ro-value">{{ $ia->ot_tables ?? '—' }}</span></div>
        <div class="ro-cell">
            <span class="ro-label">OT Sterilization</span>
            {!! $ynBadge($ia->ot_sterilization) !!}
            @if($ia->ot_sterilization_remarks)<p class="text-xs text-slate-500 mt-1">{{ $ia->ot_sterilization_remarks }}</p>@endif
        </div>
        <div class="ro-cell"><span class="ro-label">OT Lighting</span>{!! $ynBadge($ia->ot_lighting) !!}</div>
        <div class="ro-cell"><span class="ro-label">OT Air Conditioning</span>{!! $ynBadge($ia->ot_ac) !!}</div>
        <div class="ro-cell"><span class="ro-label">OT Well Equipped</span>{!! $ynBadge($ia->ot_well_equipped) !!}</div>
        @if($ia->ot_equipment)
        <div class="ro-cell col-span-2 lg:col-span-3">
            <span class="ro-label">OT Equipment (A–L)</span>
            <div class="equip-grid">
                @foreach($otLabels as $k => $lbl)
                    @php $v = $ia->ot_equipment[$k] ?? null; @endphp
                    <span class="equip-chip {{ $v === 'Yes' ? 'equip-yes' : 'equip-no' }}">
                        {{ $v === 'Yes' ? '✓' : '✗' }} {{ $k }}: {{ $lbl }}
                    </span>
                @endforeach
            </div>
            @if($ia->ot_equipment_remarks)<p class="text-xs text-slate-500 mt-2">{{ $ia->ot_equipment_remarks }}</p>@endif
        </div>
        @endif
    </div>

    {{-- Diagnostics & Hygiene --}}
    <div class="section-badge"><span>Diagnostics &amp; Hygiene</span></div>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="ro-cell">
            <span class="ro-label">Pathology / Diagnostics</span>
            <span class="ro-value {{ $ia->pathology_diagnostics ? '' : 'empty' }}">{{ $ia->pathology_diagnostics ?? '—' }}</span>
            @if($ia->pathology_remarks)<p class="text-xs text-slate-500 mt-1">{{ $ia->pathology_remarks }}</p>@endif
        </div>
        <div class="ro-cell">
            <span class="ro-label">Biomedical Waste Mgmt</span>
            {!! $ynBadge($ia->biomedical_waste) !!}
        </div>
        <div class="ro-cell">
            <span class="ro-label">Overall Hygiene</span>
            @php
                $hygCls = match($ia->overall_hygiene) {
                    'Good'    => 'yn-yes',
                    'Average' => 'background:#fef3c7;color:#92400e',
                    'Poor'    => 'yn-no',
                    default   => 'yn-na',
                };
            @endphp
            @if($ia->overall_hygiene)
                @if(str_contains($hygCls, ':'))
                    <span class="yn-badge" style="{{ $hygCls }}">{{ $ia->overall_hygiene }}</span>
                @else
                    <span class="yn-badge {{ $hygCls }}">{{ $ia->overall_hygiene }}</span>
                @endif
            @else
                <span class="yn-badge yn-na">—</span>
            @endif
        </div>
    </div>
    @if($ia->infra_other_remarks)
    <div class="ro-cell mt-3">
        <span class="ro-label">Infrastructure — Other Remarks</span>
        <span class="ro-value">{{ $ia->infra_other_remarks }}</span>
    </div>
    @endif

    {{-- ── C. Human Resource ── --}}
    <div class="section-badge"><span>C. Human Resource</span></div>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="ro-cell">
            <span class="ro-label">PMAM Available</span>
            {!! $ynBadge($ia->pmam_available) !!}
        </div>
        <div class="ro-cell">
            <span class="ro-label">On-duty Doctors</span>
            {!! $ynBadge($ia->onduty_doctors) !!}
        </div>
        @if($ia->onduty_doctor_types)
        <div class="ro-cell">
            <span class="ro-label">Doctor Types</span>
            <div class="flex flex-wrap gap-1 mt-1">
                @foreach(['A'=>'RMO','B'=>'Emergency','C'=>'ICU'] as $key => $label)
                    @php $v = $ia->onduty_doctor_types[$key] ?? null; @endphp
                    <span class="yn-badge {{ $v === 'Yes' ? 'yn-yes' : 'yn-no' }} text-xs">
                        {{ $label }}: {{ $v ?? '—' }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif
        <div class="ro-cell">
            <span class="ro-label">Adequate Nurses</span>
            {!! $ynBadge($ia->adequate_nurses) !!}
            @if($ia->adequate_nurses_remarks)<p class="text-xs text-slate-500 mt-1">{{ $ia->adequate_nurses_remarks }}</p>@endif
        </div>
        <div class="ro-cell">
            <span class="ro-label">Nurses Qualified</span>
            {!! $ynBadge($ia->nurses_qualified) !!}
        </div>
        <div class="ro-cell"><span class="ro-label">Technicians Available</span>{!! $ynBadge($ia->technicians_available) !!}</div>
        <div class="ro-cell"><span class="ro-label">Pharmacists Available</span>{!! $ynBadge($ia->pharmacists_available) !!}</div>
        <div class="ro-cell">
            <span class="ro-label">Specialists Available</span>
            {!! $ynBadge($ia->specialists_available) !!}
            @if($ia->specialists_remarks)<p class="text-xs text-slate-500 mt-1">{{ $ia->specialists_remarks }}</p>@endif
        </div>
    </div>
    @if($ia->hr_other_remarks)
    <div class="ro-cell mt-3">
        <span class="ro-label">HR — Other Remarks</span>
        <span class="ro-value">{{ $ia->hr_other_remarks }}</span>
    </div>
    @endif

</div>{{-- /main card --}}

<div id="toast-container"></div>

<script>
function toast(type, title, message, duration) {
    duration = duration ?? 4500;
    const icons = { error:'fa-times-circle', success:'fa-check-circle' };
    const el = document.createElement('div');
    el.className = 'toast toast-' + type;
    el.innerHTML = '<div class="toast-icon"><i class="fas ' + icons[type] + '"></i></div>'
        + '<div class="toast-body"><div class="toast-title">' + title + '</div><div>' + message + '</div></div>'
        + '<button class="toast-close"><i class="fas fa-times"></i></button>'
        + '<div class="toast-progress" style="animation-duration:' + duration + 'ms;"></div>';
    document.getElementById('toast-container').appendChild(el);
    requestAnimationFrame(() => requestAnimationFrame(() => el.classList.add('toast-in')));
    const dismiss = () => { el.classList.replace('toast-in','toast-out'); el.addEventListener('transitionend', () => el.remove(), {once:true}); };
    const t = setTimeout(dismiss, duration);
    el.querySelector('.toast-close').addEventListener('click', () => { clearTimeout(t); dismiss(); });
}

@if(session('success'))
toast('success', 'Success', '{{ session("success") }}');
@endif
@if(session('error'))
toast('error', 'Error', '{{ session("error") }}');
@endif
</script>

@endsection

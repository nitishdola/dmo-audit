@extends('dmo.layout.layout')

@section('main_title')
<div class="flex items-center gap-2 text-sm text-slate-400 mb-5">
    <a href="{{ route('dmo.dashboard') }}" class="hover:text-emerald-600 transition-colors">
        <i class="fas fa-arrow-left mr-1 text-xs"></i> Dashboard
    </a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-600 font-medium">Live Audit</span>
</div>
<div class="mb-7">
    <h2 class="text-xl md:text-3xl font-semibold text-slate-800 tracking-tight flex items-center gap-2">
        <i class="fas fa-hospital-user text-emerald-600"></i>
        PMJAY Live Audit
    </h2>
    <p class="text-sm text-slate-500 mt-1">
        Independent beneficiary verification · conducted on-site by DMO
    </p>
</div>
@endsection

@section('pageCss')
<style>
    .invalid-photo {
        border: 6px solid #dc2626 !important;
        box-shadow: 0 0 0 3px rgba(220,38,38,.2);
    }
    .valid-photo {
        border: 6px solid #16a34a !important;
        box-shadow: 0 0 0 3px rgba(22,163,74,.15);
    }

    /* ── Toast notifications ── */
    #toast-container {
        position: fixed;
        top: 1.25rem;
        right: 1.25rem;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: .625rem;
        pointer-events: none;
        max-width: min(420px, calc(100vw - 2.5rem));
    }
    .toast {
        pointer-events: auto;
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding: .875rem 1rem;
        border-radius: .875rem;
        border: 1.5px solid transparent;
        box-shadow: 0 8px 24px rgba(0,0,0,.14), 0 2px 6px rgba(0,0,0,.08);
        font-size: .8125rem;
        font-weight: 500;
        line-height: 1.45;
        backdrop-filter: blur(6px);
        transform: translateX(120%);
        opacity: 0;
        transition: transform .32s cubic-bezier(.22,1,.36,1), opacity .28s ease;
        cursor: default;
    }
    .toast.toast-in  { transform: translateX(0); opacity: 1; }
    .toast.toast-out { transform: translateX(120%); opacity: 0; transition: transform .25s ease-in, opacity .22s ease-in; }

    .toast-error   { background: rgba(255,241,242,.97); border-color: #fda4af; color: #9f1239; }
    .toast-success { background: rgba(240,253,244,.97); border-color: #86efac; color: #14532d; }
    .toast-warning { background: rgba(255,251,235,.97); border-color: #fde68a; color: #78350f; }

    .toast-icon { width: 1.75rem; height: 1.75rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: .8rem; margin-top: .05rem; }
    .toast-error   .toast-icon { background: #fee2e2; color: #dc2626; }
    .toast-success .toast-icon { background: #dcfce7; color: #16a34a; }
    .toast-warning .toast-icon { background: #fef3c7; color: #d97706; }

    .toast-body { flex: 1; min-width: 0; }
    .toast-title { font-weight: 700; font-size: .8125rem; margin-bottom: .15rem; }
    .toast-msg ul { margin: .25rem 0 0 .1rem; padding-left: 1rem; list-style: disc; display: flex; flex-direction: column; gap: .2rem; }

    .toast-close { flex-shrink: 0; background: none; border: none; cursor: pointer; opacity: .45; font-size: .75rem; padding: .1rem .2rem; margin-top: .05rem; transition: opacity .15s; line-height: 1; color: inherit; }
    .toast-close:hover { opacity: .9; }

    .toast-progress {
        position: absolute;
        bottom: 0; left: 0;
        height: 3px;
        border-radius: 0 0 .875rem .875rem;
        animation: toast-drain linear forwards;
    }
    .toast-error   .toast-progress { background: #f43f5e; }
    .toast-success .toast-progress { background: #22c55e; }
    .toast-warning .toast-progress { background: #f59e0b; }
    @keyframes toast-drain { from { width: 100%; } to { width: 0%; } }
    .toast { position: relative; overflow: hidden; }

    /* ── Radio pill group (Yes / No / NA) ── */
    .radio-group { display:inline-flex; border-radius:9999px; overflow:hidden; border:2px solid #e2e8f0; background:#f8fafc; }
    .radio-group input[type="radio"] { display:none; }
    .radio-group label { padding:.5rem 1.1rem; font-size:.8rem; font-weight:500; color:#64748b; cursor:pointer; transition:background .18s,color .18s; user-select:none; white-space:nowrap; border-right:1px solid #e2e8f0; }
    .radio-group label:last-child { border-right:none; }
    .radio-group input[value="Yes"]:checked + label { background:#059669; color:#fff; }
    .radio-group input[value="No"]:checked  + label { background:#e11d48; color:#fff; }
    .radio-group input[value="NA"]:checked  + label { background:#64748b; color:#fff; }

    /* ── Question cards ── */
    .obs-card { background:#fff; border:1px solid #e2e8f0; border-radius:1rem; padding:1rem 1.125rem; display:flex; flex-direction:column; gap:.75rem; }
    .obs-card + .obs-card { margin-top:.625rem; }
    .obs-label { font-size:.875rem; font-weight:600; color:#334155; line-height:1.4; }
    .obs-sub   { font-size:.75rem; color:#64748b; margin-top:.125rem; line-height:1.5; }
    .obs-row   { display:flex; align-items:flex-start; gap:.75rem; flex-wrap:wrap; }

    /* ── Inputs ── */
    .field-input { width:100%; padding:.65rem .875rem; border:2px solid #e2e8f0; border-radius:.75rem; background:#f8fafc; font-size:.9rem; color:#1e293b; transition:border-color .15s,background .15s; outline:none; }
    .field-input:focus { border-color:#34d399; background:#fff; }
    textarea.field-input { resize:vertical; min-height:80px; }

    /* ── Section dividers ── */
    .section-badge { display:flex; align-items:center; gap:.5rem; font-size:.7rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#94a3b8; margin:1.5rem 0 .625rem; }
    .section-badge::before,.section-badge::after { content:''; flex:1; height:1px; background:#e2e8f0; }

    /* ── GPS pill ── */
    .gps-pill { display:inline-flex; align-items:center; gap:.4rem; font-size:.75rem; font-weight:500; padding:.35rem .75rem; border-radius:9999px; }
    .gps-acquiring { background:#fef9c3; color:#a16207; }
    .gps-ready     { background:#dcfce7; color:#166534; }
    .gps-error     { background:#fee2e2; color:#991b1b; }

    /* ── Camera & photo ── */
    #camera-preview { width:100%; max-width:480px; aspect-ratio:4/3; border-radius:.875rem; background:#0f172a; display:block; object-fit:cover; }
    #photo-preview-wrap { display:none; position:relative; border-radius:.875rem; overflow:hidden; border:2px solid #34d399; max-width:480px; }
    #photo-preview-wrap img { width:100%; display:block; }
    .wm-strip { position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,.58); color:#fff; font-size:.68rem; padding:.4rem .65rem; display:flex; flex-direction:column; gap:.1rem; backdrop-filter:blur(2px); }
    #map-thumb { width:100%; max-width:480px; height:160px; border-radius:.875rem; object-fit:cover; border:2px solid #e2e8f0; display:none; }

    /* ── Badges ── */
    .mandatory-badge { display:inline-flex; align-items:center; gap:.3rem; background:#fef3c7; color:#92400e; font-size:.65rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; padding:.25rem .6rem; border-radius:9999px; }
    .ai-badge        { display:inline-flex; align-items:center; gap:.3rem; background:#ede9fe; color:#5b21b6; font-size:.65rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; padding:.25rem .6rem; border-radius:9999px; }

    /* ── Camera buttons ── */
    .cam-btn { display:inline-flex; align-items:center; gap:.5rem; padding:.7rem 1.4rem; border-radius:9999px; font-size:.8125rem; font-weight:600; cursor:pointer; transition:all .18s; border:none; outline:none; }
    .cam-btn-dark   { background:#0f172a; color:#fff; }
    .cam-btn-dark:hover   { background:#1e293b; }
    .cam-btn-danger { background:#fee2e2; color:#991b1b; }
    .cam-btn-danger:hover { background:#fecaca; }

    /* ── Treatment type toggle ── */
    .tt-group { display:inline-flex; border-radius:9999px; overflow:hidden; border:2px solid #e2e8f0; background:#f8fafc; }
    .tt-group label { display:inline-flex; align-items:center; gap:.4rem; padding:.55rem 1.3rem; font-size:.8rem; font-weight:500; color:#64748b; cursor:pointer; transition:background .18s,color .18s; user-select:none; white-space:nowrap; border-right:1px solid #e2e8f0; }
    .tt-group label:last-child { border-right:none; }
    .tt-group input[type="radio"] { position:absolute; opacity:0; pointer-events:none; width:0; height:0; }
    .tt-group label.tt-active-surgical { background:#7c3aed; color:#fff; }
    .tt-group label.tt-active-medical  { background:#0369a1; color:#fff; }

    /* ── Attachments ── */
    .attach-row { display:grid; grid-template-columns:1fr auto; gap:.625rem; align-items:start; background:#f8fafc; border:1px solid #e2e8f0; border-radius:.875rem; padding:.75rem .875rem; transition:border-color .15s; }
    .attach-row:focus-within { border-color:#34d399; background:#fff; }
    .attach-row + .attach-row { margin-top:.5rem; }
    .attach-inner { display:flex; flex-direction:column; gap:.5rem; }
    .drop-zone { border:2px dashed #cbd5e1; border-radius:.75rem; padding:1rem; text-align:center; cursor:pointer; transition:border-color .18s,background .18s; position:relative; overflow:hidden; }
    .drop-zone.drag-over { border-color:#34d399; background:#f0fdf4; }
    .drop-zone input[type="file"] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
    .file-chip { display:inline-flex; align-items:center; gap:.4rem; font-size:.75rem; font-weight:600; padding:.3rem .7rem; border-radius:9999px; max-width:100%; overflow:hidden; }
    .file-chip.is-pdf { background:#fef3c7; color:#92400e; }
    .file-chip.is-img { background:#d1fae5; color:#065f46; }
    .file-chip span   { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:160px; }
    .btn-remove-attach { width:2rem; height:2rem; border-radius:9999px; display:flex; align-items:center; justify-content:center; background:#fee2e2; color:#991b1b; border:none; cursor:pointer; flex-shrink:0; margin-top:.25rem; transition:background .15s; }
    .btn-remove-attach:hover { background:#fecaca; }
    #btn-add-attach { display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.2rem; border-radius:9999px; font-size:.8rem; font-weight:600; cursor:pointer; border:2px dashed #94a3b8; background:transparent; color:#64748b; transition:all .18s; margin-top:.5rem; }
    #btn-add-attach:hover { border-color:#059669; color:#059669; background:#f0fdf4; }

    /* ── Conditional rows ── */
    .cond-row { display:none; }

    /* ── Read-only styles ── */
    .completed-banner { display:flex; align-items:center; gap:.75rem; background:linear-gradient(135deg,#ecfdf5,#d1fae5); border:1.5px solid #6ee7b7; border-radius:1rem; padding:1rem 1.25rem; margin-bottom:1.5rem; }
    .completed-banner .icon-wrap { width:2.5rem; height:2.5rem; border-radius:9999px; background:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .ro-cell { background:#f8fafc; border:1px solid #e2e8f0; border-radius:.75rem; padding:.625rem .875rem; }
    .ro-label { font-size:.7rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:#94a3b8; display:block; margin-bottom:.25rem; }
    .ro-value { font-size:.875rem; font-weight:600; color:#1e293b; }
    .ro-value.empty { color:#cbd5e1; font-style:italic; font-weight:400; }
    .yn-badge { display:inline-flex; align-items:center; gap:.35rem; font-size:.78rem; font-weight:700; padding:.28rem .8rem; border-radius:9999px; white-space:nowrap; }
    .yn-yes { background:#d1fae5; color:#065f46; }
    .yn-no  { background:#fee2e2; color:#991b1b; }
    .yn-na  { background:#f1f5f9; color:#94a3b8; }
    .ro-photo { border-radius:.875rem; overflow:hidden; border:2px solid #34d399; max-width:480px; position:relative; }
    .ro-photo img { width:100%; display:block; }
    .ro-map { width:100%; max-width:480px; height:160px; border-radius:.875rem; object-fit:cover; border:2px solid #e2e8f0; display:block; }
    .ro-attach-item { display:flex; align-items:center; gap:.75rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:.875rem; padding:.75rem 1rem; }
    .ro-attach-item + .ro-attach-item { margin-top:.5rem; }
    .ro-attach-icon { width:2.25rem; height:2.25rem; border-radius:.625rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:.9rem; }
    .ro-attach-icon.pdf { background:#fef3c7; color:#92400e; }
    .ro-attach-icon.img { background:#d1fae5; color:#065f46; }
    .ro-attach-meta { flex:1; min-width:0; }
    .ro-attach-name { font-size:.875rem; font-weight:600; color:#334155; }
    .ro-attach-link { display:inline-flex; align-items:center; gap:.3rem; font-size:.75rem; font-weight:600; padding:.3rem .75rem; border-radius:9999px; text-decoration:none; transition:background .15s; flex-shrink:0; }
    .ro-attach-link     { color:#0369a1; background:#e0f2fe; }
    .ro-attach-link:hover { background:#bae6fd; }
    .ro-attach-link.pdf-link { color:#92400e; background:#fef3c7; }
    .ro-attach-link.pdf-link:hover { background:#fde68a; }
    .gps-chip { display:inline-flex; align-items:center; gap:.4rem; background:#dcfce7; color:#166534; font-size:.75rem; font-weight:600; padding:.35rem .85rem; border-radius:9999px; }
    .submitted-strip { display:flex; align-items:center; gap:.5rem; font-size:.75rem; color:#64748b; background:#f8fafc; border:1px solid #e2e8f0; border-radius:.75rem; padding:.5rem .875rem; flex-wrap:wrap; }
    .ai-result-strip { display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; padding:.75rem 1rem; border-radius:.875rem; border:1px solid; font-size:.8rem; font-weight:500; }
    .ai-pass { background:#f0fdf4; border-color:#86efac; color:#166534; }
    .ai-fail { background:#fff1f2; border-color:#fda4af; color:#9f1239; }
    .ai-skip { background:#f8fafc; border-color:#cbd5e1; color:#64748b; }
</style>
@endsection

@section('main_content')

@php $isCompleted = isset($liveAudit); @endphp

{{-- ════════════════════════════════════════
     BRANCH A — READ-ONLY (after submission)
     ════════════════════════════════════════ --}}
@if($isCompleted)
@php $la = $liveAudit; @endphp

<div class="completed-banner">
    <div class="icon-wrap"><i class="fas fa-check text-white text-sm"></i></div>
    <div>
        <p class="font-semibold text-emerald-800 text-sm">Live Audit submitted</p>
        <p class="text-emerald-700 text-xs mt-0.5">
            {{ $la->created_at?->format('d M Y, h:i A') ?? '—' }}
            @if($la->submittedBy) &nbsp;·&nbsp; {{ $la->submittedBy->name }} @endif
        </p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-md p-4 md:p-8">

    {{-- AI Strip --}}
    @php
        $aiPass = $la->ai_bed_detected && $la->ai_patient_detected;
        $aiSkip = $la->ai_validation_message === 'AI check skipped (service unavailable)';
    @endphp
    <div class="ai-result-strip mb-6 {{ $aiSkip ? 'ai-skip' : ($aiPass ? 'ai-pass' : 'ai-fail') }}">
        <i class="fas {{ $aiSkip ? 'fa-question-circle' : ($aiPass ? 'fa-robot' : 'fa-exclamation-triangle') }}"></i>
        <span><strong>AI Verification:</strong> {{ $la->ai_validation_message ?? '—' }}</span>
        @if(!$aiSkip)
        <span class="ml-auto text-xs opacity-70">
            Bed: {{ $la->ai_bed_detected ? '✓' : '✗' }} &nbsp;·&nbsp;
            Patient: {{ $la->ai_patient_detected ? '✓' : '✗' }} &nbsp;·&nbsp;
            PMJAY Card: {{ $la->ai_pmjay_card_detected ? '✓' : '—' }} &nbsp;·&nbsp;
            Faces: {{ $la->ai_face_count }}
        </span>
        @endif
    </div>

    {{-- Patient & Case Details --}}
    <div class="section-badge"><span>Patient &amp; Case Details</span></div>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="ro-cell col-span-2 lg:col-span-1"><span class="ro-label">Patient Name</span><span class="ro-value {{ $la->patient_name ? '' : 'empty' }}">{{ $la->patient_name ?? '—' }}</span></div>
        <div class="ro-cell"><span class="ro-label">Contact Number</span><span class="ro-value {{ $la->contact_number ? '' : 'empty' }}">{{ $la->contact_number ?? '—' }}</span></div>
        <div class="ro-cell"><span class="ro-label">Hospital</span><span class="ro-value {{ $la->hospital_name ? '' : 'empty' }}">{{ $la->hospital_name ?? '—' }}</span></div>
        <div class="ro-cell"><span class="ro-label">PMJAY ID</span><span class="ro-value {{ $la->pmjay_id ? '' : 'empty' }}">{{ $la->pmjay_id ?? '—' }}</span></div>
        <div class="ro-cell"><span class="ro-label">Registration Number</span><span class="ro-value {{ $la->registration_number ? '' : 'empty' }}">{{ $la->registration_number ?? '—' }}</span></div>
        <div class="ro-cell"><span class="ro-label">Package Booked</span><span class="ro-value {{ $la->package_booked ? '' : 'empty' }}">{{ $la->package_booked ?? '—' }}</span></div>
        <div class="ro-cell"><span class="ro-label">Treating Doctor</span><span class="ro-value {{ $la->treating_doctor ? '' : 'empty' }}">{{ $la->treating_doctor ?? '—' }}</span></div>
        <div class="ro-cell"><span class="ro-label">Specialization</span><span class="ro-value {{ $la->doctor_specialization ? '' : 'empty' }}">{{ $la->doctor_specialization ?? '—' }}</span></div>
        <div class="ro-cell"><span class="ro-label">Admission</span><span class="ro-value {{ $la->admission_datetime ? '' : 'empty' }}">{{ $la->admission_datetime?->format('d M Y, h:i A') ?? '—' }}</span></div>
        <div class="ro-cell"><span class="ro-label">Discharge</span><span class="ro-value {{ $la->discharge_datetime ? '' : 'empty' }}">{{ $la->discharge_datetime?->format('d M Y, h:i A') ?? '—' }}</span></div>
        <div class="ro-cell">
            <span class="ro-label">Treatment Type</span>
            @if($la->treatment_type)
                <span class="yn-badge {{ $la->treatment_type === 'Surgical' ? 'bg-violet-100 text-violet-800' : 'bg-blue-100 text-blue-800' }}">
                    <i class="fas {{ $la->treatment_type === 'Surgical' ? 'fa-scalpel' : 'fa-pills' }} text-xs"></i>
                    {{ $la->treatment_type }}
                </span>
            @else <span class="ro-value empty">—</span> @endif
        </div>
    </div>

    {{-- On-Bed Photograph --}}
    <div class="section-badge"><span>On-Bed Patient Photograph (AI Verified)</span></div>
    @if($la->bed_photo_path)
    <div class="flex flex-col gap-3">
        <div class="ro-photo"><img src="{{ Storage::disk('public')->url($la->bed_photo_path) }}" alt="On-bed patient photo"></div>
        <div class="flex flex-wrap gap-2 items-center">
            <span class="gps-chip"><i class="fas fa-map-marker-alt"></i> {{ number_format($la->bed_photo_latitude, 6) }}°N, {{ number_format($la->bed_photo_longitude, 6) }}°E</span>
            @if($la->bed_photo_address)<span class="text-xs text-slate-500">{{ $la->bed_photo_address }}</span>@endif
        </div>
        @php $mapUrl = 'https://maps.googleapis.com/maps/api/staticmap?center='.$la->bed_photo_latitude.','.$la->bed_photo_longitude.'&zoom=15&size=480x160&maptype=roadmap&markers=color:red%7C'.$la->bed_photo_latitude.','.$la->bed_photo_longitude.'&key='.config('services.google_maps.key'); @endphp
        <img src="{{ $mapUrl }}" alt="Location map" class="ro-map">
        @if($la->bed_photo_taken_at)<p class="text-xs text-slate-400"><i class="fas fa-clock mr-1"></i> {{ $la->bed_photo_taken_at->format('d M Y, h:i A') }}</p>@endif
    </div>
    @else<div class="ro-cell"><span class="ro-value empty">No photograph recorded</span></div>@endif

    {{-- Patient ID Proof --}}
    <div class="section-badge"><span>Patient ID Proof</span></div>
    <div class="ro-cell">
        <span class="ro-label">Patient ID Collected</span>
        @if($la->patient_id_collected === 'Yes') <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
        @elseif($la->patient_id_collected === 'No') <span class="yn-badge yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
        @elseif($la->patient_id_collected === 'NA') <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span>
        @else <span class="ro-value empty">—</span> @endif
        @if($la->patient_id_remarks)<p class="text-xs text-slate-500 mt-1">{{ $la->patient_id_remarks }}</p>@endif
    </div>

    {{-- Clinical Interview --}}
    <div class="section-badge"><span>Clinical Interview</span></div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="ro-cell sm:col-span-2"><span class="ro-label">Presenting Complaints</span><span class="ro-value {{ $la->presenting_complaints ? '' : 'empty' }}">{{ $la->presenting_complaints ?? '—' }}</span></div>
        <div class="ro-cell"><span class="ro-label">Duration of Symptoms</span><span class="ro-value {{ $la->symptoms_duration ? '' : 'empty' }}">{{ $la->symptoms_duration ?? '—' }}</span></div>
        <div class="ro-cell">
            <span class="ro-label">Referred from Another Source</span>
            @if($la->referred_from_other === 'Yes') <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
            @elseif($la->referred_from_other === 'No') <span class="yn-badge yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
            @else <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span> @endif
            @if($la->referred_from_name)<p class="text-xs text-slate-500 mt-1">{{ $la->referred_from_name }}</p>@endif
        </div>
        <div class="ro-cell"><span class="ro-label">Patient Admitted When</span><span class="ro-value {{ $la->patient_admitted_when ? '' : 'empty' }}">{{ $la->patient_admitted_when?->format('d M Y, h:i A') ?? '—' }}</span></div>
        <div class="ro-cell">
            <span class="ro-label">Still Admitted</span>
            @if($la->patient_still_admitted === 'Yes') <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
            @elseif($la->patient_still_admitted === 'No') <span class="yn-badge yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
            @else <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span> @endif
            @if($la->patient_still_admitted_remarks)<p class="text-xs text-slate-500 mt-1">{{ $la->patient_still_admitted_remarks }}</p>@endif
        </div>
        <div class="ro-cell sm:col-span-2"><span class="ro-label">Diagnostic Tests</span><span class="ro-value {{ $la->diagnostic_tests_done ? '' : 'empty' }}">{{ $la->diagnostic_tests_done ?? '—' }}</span></div>
        <div class="ro-cell">
            <span class="ro-label">Surgery Conducted</span>
            @if($la->surgery_conducted === 'Yes') <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
            @elseif($la->surgery_conducted === 'No') <span class="yn-badge yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
            @else <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span> @endif
        </div>
        <div class="ro-cell">
            <span class="ro-label">Scar Present</span>
            @if($la->surgery_scar_present === 'Yes') <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
            @elseif($la->surgery_scar_present === 'No') <span class="yn-badge yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
            @else <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span> @endif
            @if($la->surgery_scar_remarks)<p class="text-xs text-slate-500 mt-1">{{ $la->surgery_scar_remarks }}</p>@endif
        </div>
    </div>

    {{-- Money Charged --}}
    <div class="section-badge"><span>Money Charged</span></div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="ro-cell">
            <span class="ro-label">Money Charged</span>
            @if($la->money_charged === 'Yes') <span class="yn-badge yn-no"><i class="fas fa-exclamation-circle text-xs"></i> Yes — flagged</span>
            @elseif($la->money_charged === 'No') <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> No</span>
            @else <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span> @endif
        </div>
        @if($la->money_charged === 'Yes')
        <div class="ro-cell"><span class="ro-label">Amount</span><span class="ro-value {{ $la->money_charged_amount ? 'text-rose-700' : 'empty' }}">{{ $la->money_charged_amount ? '₹ '.number_format($la->money_charged_amount, 2) : '—' }}</span></div>
        <div class="ro-cell">
            <span class="ro-label">Receipt Available</span>
            @if($la->receipt_available === 'Yes') <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
            @elseif($la->receipt_available === 'No') <span class="yn-badge yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
            @else <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span> @endif
        </div>
        @if($la->receipt_path)
        <div class="ro-cell"><span class="ro-label">Receipt</span><a href="{{ Storage::disk('public')->url($la->receipt_path) }}" target="_blank" class="ro-attach-link mt-1"><i class="fas fa-eye"></i> View Receipt</a></div>
        @endif
        @endif
    </div>

    {{-- Previous Hospitalisation --}}
    <div class="section-badge"><span>Previous Hospitalisation</span></div>
    <div class="ro-cell">
        <span class="ro-label">Previous hospitalisation at same hospital</span>
        @if($la->previous_hospitalisation === 'Yes') <span class="yn-badge yn-no"><i class="fas fa-exclamation-circle text-xs"></i> Yes — review needed</span>
        @elseif($la->previous_hospitalisation === 'No') <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> No</span>
        @else <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span> @endif
        @if($la->previous_hospitalisation_remarks)<p class="text-xs text-slate-500 mt-1">{{ $la->previous_hospitalisation_remarks }}</p>@endif
    </div>

    {{-- Other Remarks --}}
    @if($la->other_remarks)
    <div class="section-badge"><span>Other Remarks</span></div>
    <div class="ro-cell"><span class="ro-value">{{ $la->other_remarks }}</span></div>
    @endif

    {{-- Attachments --}}
    <div class="section-badge"><span>Supporting Attachments</span></div>
    @if($la->attachments && $la->attachments->count())
    <div>
        @foreach($la->attachments->sortBy('sort_order') as $att)
        @php $isPdf = str_ends_with(strtolower($att->file_path), '.pdf'); @endphp
        <div class="ro-attach-item">
            <div class="ro-attach-icon {{ $isPdf ? 'pdf' : 'img' }}"><i class="fas {{ $isPdf ? 'fa-file-pdf' : 'fa-file-image' }}"></i></div>
            <div class="ro-attach-meta"><div class="ro-attach-name">{{ $att->name }}</div></div>
            <a href="{{ Storage::disk('public')->url($att->file_path) }}" target="_blank" class="ro-attach-link {{ $isPdf ? 'pdf-link' : '' }}">
                <i class="fas {{ $isPdf ? 'fa-file-pdf' : 'fa-eye' }}"></i> {{ $isPdf ? 'Open PDF' : 'View' }}
            </a>
        </div>
        @endforeach
    </div>
    @else
    <div class="ro-cell"><span class="ro-value empty">No attachments</span></div>
    @endif

    <div class="submitted-strip mt-6">
        <i class="fas fa-shield-alt text-emerald-500"></i>
        <span>Submitted by <strong>{{ $la->submittedBy?->name ?? '—' }}</strong> on {{ $la->created_at?->format('d M Y, h:i A') ?? '—' }}</span>
        <span class="ml-auto text-slate-400">PMJAY Assam · DMO Live Audit</span>
    </div>
</div>

{{-- ════════════════════════════════════════
     BRANCH B — FORM (independent, no case)
     ════════════════════════════════════════ --}}
@else

@if($errors->any())
<div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 rounded-xl flex items-start gap-2">
    <i class="fas fa-exclamation-circle mt-0.5 shrink-0"></i>
    <ul class="list-disc list-inside space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<div class="bg-white rounded-2xl border border-slate-200 shadow-md p-4 md:p-8">
    <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-100">
        <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
            <i class="fas fa-clipboard-list text-emerald-600 text-sm"></i>
        </div>
        <div>
            <h3 class="font-semibold text-slate-800 text-base">Independent Live Audit Form</h3>
            <p class="text-xs text-slate-400 mt-0.5">Complete all sections · Photograph is AI-verified</p>
        </div>
        <span class="ml-auto text-xs text-slate-400 hidden sm:block">{{ now()->format('d M Y') }}</span>
    </div>

    <form id="laForm"
          method="POST"
          action="{{ route('dmo.audits.live-audit.store') }}"
          enctype="multipart/form-data">
        @csrf

        {{-- ── Section 1: Patient & Case Details ── --}}
        <div class="section-badge"><span>Patient &amp; Case Details</span></div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

            <div class="obs-card">
                <span class="obs-label">Name of patient <span class="text-rose-500">*</span></span>
                <input type="text" name="patient_name" value="{{ old('patient_name') }}"
                       placeholder="Full name of the patient" class="field-input" autocomplete="off">
                @error('patient_name')<p class="text-rose-500 text-xs">{{ $message }}</p>@enderror
            </div>

            <div class="obs-card">
                <span class="obs-label">Contact number</span>
                <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                       placeholder="Patient's mobile number" class="field-input" autocomplete="off">
            </div>

            <div class="obs-card sm:col-span-2">
                <span class="obs-label">Select hospital <span class="text-rose-500">*</span></span>
                <input type="text" name="hospital_name" value="{{ old('hospital_name') }}"
                       placeholder="Name of the hospital being audited" class="field-input" autocomplete="off">
                @error('hospital_name')<p class="text-rose-500 text-xs">{{ $message }}</p>@enderror
            </div>

            <div class="obs-card">
                <span class="obs-label">PMJAY ID</span>
                <input type="text" name="pmjay_id" value="{{ old('pmjay_id') }}"
                       placeholder="Ayushman Bharat / PMJAY ID" class="field-input" autocomplete="off">
            </div>

            <div class="obs-card">
                <span class="obs-label">Registration number</span>
                <input type="text" name="registration_number" value="{{ old('registration_number') }}"
                       placeholder="Hospital registration number" class="field-input" autocomplete="off">
            </div>

            <div class="obs-card">
                <span class="obs-label">Package booked</span>
                <input type="text" name="package_booked" value="{{ old('package_booked') }}"
                       placeholder="Package / procedure name" class="field-input" autocomplete="off">
            </div>

            <div class="obs-card">
                <span class="obs-label">Name of treating doctor</span>
                <input type="text" name="treating_doctor" value="{{ old('treating_doctor') }}"
                       placeholder="Doctor's full name" class="field-input" autocomplete="off">
            </div>

            <div class="obs-card">
                <span class="obs-label">Specialization of treating doctor</span>
                <input type="text" name="doctor_specialization" value="{{ old('doctor_specialization') }}"
                       placeholder="e.g. Orthopaedics, Cardiology" class="field-input" autocomplete="off">
            </div>

            <div class="obs-card">
                <span class="obs-label">Date &amp; time of admission <span class="text-slate-400 text-xs">(as per hospital file)</span></span>
                <input type="datetime-local" name="admission_datetime" value="{{ old('admission_datetime') }}" class="field-input">
            </div>

            <div class="obs-card">
                <span class="obs-label">Date &amp; time of discharge <span class="text-slate-400 text-xs">(as per hospital file)</span></span>
                <input type="datetime-local" name="discharge_datetime" value="{{ old('discharge_datetime') }}" class="field-input">
                @error('discharge_datetime')<p class="text-rose-500 text-xs">{{ $message }}</p>@enderror
            </div>

            <div class="obs-card">
                <span class="obs-label">Type of treatment <span class="text-rose-500">*</span></span>
                <div class="tt-group">
                    <label id="lbl-surgical" onclick="selectTT('Surgical')">
                        <input type="radio" name="treatment_type" value="Surgical" {{ old('treatment_type') === 'Surgical' ? 'checked' : '' }}>
                        <i class="fas fa-scalpel text-xs"></i> Surgical
                    </label>
                    <label id="lbl-medical" onclick="selectTT('Medical')">
                        <input type="radio" name="treatment_type" value="Medical" {{ old('treatment_type') === 'Medical' ? 'checked' : '' }}>
                        <i class="fas fa-pills text-xs"></i> Medical
                    </label>
                </div>
                @error('treatment_type')<p class="text-rose-500 text-xs">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- ── Section 2: On-Bed Photo (AI) ── --}}
        <div class="section-badge"><span>On-Bed Patient Photograph with PMJAY Card</span></div>
        <div id="camera-section" class="obs-card" style="border:2px solid #a78bfa; background:#faf5ff;">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div>
                    <span class="obs-label">On-bed patient photograph collected with PMJAY card <span class="text-rose-500">*</span></span>
                    <p class="text-xs text-slate-500 mt-0.5">AI will verify: hospital bed + patient visible. GPS &amp; timestamp will be stamped automatically.</p>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <span class="mandatory-badge"><i class="fas fa-exclamation-circle"></i> Mandatory</span>
                    <span class="ai-badge"><i class="fas fa-robot"></i> AI Verified</span>
                </div>
            </div>

            <div id="gps-pill" class="gps-pill gps-acquiring self-start">
                <i class="fas fa-satellite-dish fa-spin"></i>
                <span id="gps-text">Acquiring GPS…</span>
            </div>

            {{-- Live camera viewfinder --}}
            <div id="viewfinder-wrap" style="display:none; flex-direction:column; gap:.75rem;">
                <div class="relative">
                    <video id="camera-preview" autoplay playsinline muted></video>
                    <div id="live-overlay" style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,.5);color:#fff;font-size:.68rem;padding:.4rem .65rem;border-radius:0 0 .875rem .875rem;backdrop-filter:blur(2px);">
                        <span id="live-coords">Waiting for GPS…</span>
                    </div>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <button type="button" id="btn-snap"      class="cam-btn cam-btn-dark"><i class="fas fa-circle-dot"></i> Capture Photo</button>
                    <button type="button" id="btn-close-cam" class="cam-btn cam-btn-danger"><i class="fas fa-times"></i> Cancel</button>
                </div>
            </div>

            {{-- Photo preview with watermark strip --}}
            <div id="photo-preview-wrap">
                <img id="photo-preview-img" src="" alt="Captured photo">
                <div class="wm-strip">
                    <span id="wm-line1" style="color:#a78bfa;font-weight:700;"></span>
                    <span id="wm-line2"></span>
                    <span id="wm-line3"></span>
                </div>
            </div>
            <img id="map-thumb" src="" alt="Location map">

            <div class="flex gap-2 flex-wrap">
                <button type="button" id="btn-open-cam" class="cam-btn cam-btn-dark"><i class="fas fa-camera"></i> Open Camera</button>
                <button type="button" id="btn-retake"   class="cam-btn cam-btn-danger" style="display:none;"><i class="fas fa-redo"></i> Retake</button>
            </div>

            <p  id="photo-error"     class="text-rose-600 text-xs font-medium" style="display:none;"></p>
            <div id="ai-checking-msg" class="gps-pill gps-acquiring" style="display:none;">
                <i class="fas fa-spinner fa-spin"></i>
                <span>AI verifying hospital bed &amp; patient…</span>
            </div>
            <span id="ai-face-badge" class="gps-pill gps-ready" style="display:none; font-size:.75rem;"></span>

            {{-- Hidden AI fields (populated by JS) --}}
            <input type="hidden" name="bed_photo_latitude"     id="field-lat">
            <input type="hidden" name="bed_photo_longitude"    id="field-lng">
            <input type="hidden" name="bed_photo_address"      id="field-address">
            <input type="hidden" name="ai_bed_detected"        id="field-ai-bed"     value="0">
            <input type="hidden" name="ai_patient_detected"    id="field-ai-patient" value="0">
            <input type="hidden" name="ai_pmjay_card_detected" id="field-ai-card"    value="0">
            <input type="hidden" name="ai_face_count"          id="field-ai-faces"   value="0">
            <input type="hidden" name="ai_labels"              id="field-ai-labels"  value="[]">
            <input type="hidden" name="ai_objects"             id="field-ai-objects" value="[]">
            <input type="hidden" name="ai_validation_message"  id="field-ai-message" value="">
            <input type="file"   name="bed_photo"              id="field-photo-file" class="hidden" accept="image/*">
        </div>
        <canvas id="capture-canvas" style="display:none;"></canvas>

        {{-- ── Section 3: Patient ID Proof ── --}}
        <div class="section-badge"><span>Patient ID Proof</span></div>
        <div class="obs-card">
            <span class="obs-label">Patient ID proof collected</span>
            <div class="obs-row">
                <div class="radio-group">
                    <input type="radio" name="patient_id_collected" id="pic_yes" value="Yes" {{ old('patient_id_collected') === 'Yes' ? 'checked' : '' }}>
                    <label for="pic_yes">Yes</label>
                    <input type="radio" name="patient_id_collected" id="pic_no"  value="No"  {{ old('patient_id_collected') === 'No'  ? 'checked' : '' }}>
                    <label for="pic_no">No</label>
                    <input type="radio" name="patient_id_collected" id="pic_na"  value="NA"  {{ old('patient_id_collected') === 'NA'  ? 'checked' : '' }}>
                    <label for="pic_na">N/A</label>
                </div>
                <input type="text" name="patient_id_remarks" value="{{ old('patient_id_remarks') }}"
                       placeholder="Remarks" class="field-input" style="flex:1;min-width:160px;">
            </div>
        </div>

        {{-- ── Section 4: Clinical Interview ── --}}
        <div class="section-badge"><span>Clinical Interview</span></div>

        <div class="obs-card">
            <span class="obs-label">What were the presenting complaints at the time of admission?</span>
            <textarea name="presenting_complaints" rows="3" placeholder="Describe the patient's complaints on admission…" class="field-input">{{ old('presenting_complaints') }}</textarea>
        </div>

        <div class="obs-card" style="margin-top:.625rem;">
            <span class="obs-label">Since when was the patient suffering from the symptoms?</span>
            <input type="text" name="symptoms_duration" value="{{ old('symptoms_duration') }}"
                   placeholder="e.g. 3 days, 2 weeks" class="field-input" autocomplete="off">
        </div>

        <div class="obs-card" style="margin-top:.625rem;">
            <span class="obs-label">Was the patient referred from another hospital / clinic / doctor?</span>
            <div class="radio-group">
                <input type="radio" name="referred_from_other" id="ref_yes" value="Yes" {{ old('referred_from_other') === 'Yes' ? 'checked' : '' }} onchange="toggleCond('ref-name-row', this.value==='Yes')">
                <label for="ref_yes">Yes</label>
                <input type="radio" name="referred_from_other" id="ref_no"  value="No"  {{ old('referred_from_other') === 'No'  ? 'checked' : '' }} onchange="toggleCond('ref-name-row', false)">
                <label for="ref_no">No</label>
                <input type="radio" name="referred_from_other" id="ref_na"  value="NA"  {{ old('referred_from_other') === 'NA'  ? 'checked' : '' }} onchange="toggleCond('ref-name-row', false)">
                <label for="ref_na">N/A</label>
            </div>
        </div>

        <div class="obs-card cond-row" id="ref-name-row" style="margin-top:.625rem; border-color:#bfdbfe; background:#eff6ff; {{ old('referred_from_other')==='Yes' ? 'display:flex;' : '' }}">
            <span class="obs-label">If yes, please name the hospital / clinic / doctor</span>
            <input type="text" name="referred_from_name" value="{{ old('referred_from_name') }}"
                   placeholder="Name of the referral source" class="field-input" autocomplete="off">
        </div>

        <div class="obs-card" style="margin-top:.625rem;">
            <span class="obs-label">When did the patient get admitted?</span>
            <input type="datetime-local" name="patient_admitted_when" value="{{ old('patient_admitted_when') }}" class="field-input">
        </div>

        <div class="obs-card" style="margin-top:.625rem;">
            <span class="obs-label">Is the patient admitted since then?</span>
            <div class="obs-row">
                <div class="radio-group">
                    <input type="radio" name="patient_still_admitted" id="psa_yes" value="Yes" {{ old('patient_still_admitted') === 'Yes' ? 'checked' : '' }}>
                    <label for="psa_yes">Yes</label>
                    <input type="radio" name="patient_still_admitted" id="psa_no"  value="No"  {{ old('patient_still_admitted') === 'No'  ? 'checked' : '' }}>
                    <label for="psa_no">No</label>
                    <input type="radio" name="patient_still_admitted" id="psa_na"  value="NA"  {{ old('patient_still_admitted') === 'NA'  ? 'checked' : '' }}>
                    <label for="psa_na">N/A</label>
                </div>
                <input type="text" name="patient_still_admitted_remarks" value="{{ old('patient_still_admitted_remarks') }}"
                       placeholder="Remarks" class="field-input" style="flex:1;min-width:160px;">
            </div>
        </div>

        <div class="obs-card" style="margin-top:.625rem;">
            <span class="obs-label">What diagnostic tests (if any) were performed on the patient?</span>
            <textarea name="diagnostic_tests_done" rows="2" placeholder="List tests performed, or write 'None'…" class="field-input">{{ old('diagnostic_tests_done') }}</textarea>
        </div>

        <div class="obs-card" style="margin-top:.625rem;">
            <span class="obs-label">Was any surgery conducted for the patient?</span>
            <div class="radio-group">
                <input type="radio" name="surgery_conducted" id="sc_yes" value="Yes" {{ old('surgery_conducted') === 'Yes' ? 'checked' : '' }} onchange="toggleCond('scar-row', this.value==='Yes')">
                <label for="sc_yes">Yes</label>
                <input type="radio" name="surgery_conducted" id="sc_no"  value="No"  {{ old('surgery_conducted') === 'No'  ? 'checked' : '' }} onchange="toggleCond('scar-row', false)">
                <label for="sc_no">No</label>
                <input type="radio" name="surgery_conducted" id="sc_na"  value="NA"  {{ old('surgery_conducted') === 'NA'  ? 'checked' : '' }} onchange="toggleCond('scar-row', false)">
                <label for="sc_na">N/A</label>
            </div>
        </div>

        <div class="obs-card cond-row" id="scar-row" style="margin-top:.625rem; border-color:#ede9fe; background:#faf5ff; {{ old('surgery_conducted')==='Yes' ? 'display:flex;' : '' }}">
            <span class="obs-label">If yes, is there a scar on the body?</span>
            <div class="obs-row">
                <div class="radio-group">
                    <input type="radio" name="surgery_scar_present" id="ssp_yes" value="Yes" {{ old('surgery_scar_present') === 'Yes' ? 'checked' : '' }}>
                    <label for="ssp_yes">Yes</label>
                    <input type="radio" name="surgery_scar_present" id="ssp_no"  value="No"  {{ old('surgery_scar_present') === 'No'  ? 'checked' : '' }}>
                    <label for="ssp_no">No</label>
                    <input type="radio" name="surgery_scar_present" id="ssp_na"  value="NA"  {{ old('surgery_scar_present') === 'NA'  ? 'checked' : '' }}>
                    <label for="ssp_na">N/A</label>
                </div>
                <input type="text" name="surgery_scar_remarks" value="{{ old('surgery_scar_remarks') }}"
                       placeholder="Remarks" class="field-input" style="flex:1;min-width:160px;">
            </div>
        </div>

        {{-- ── Section 5: Money Charged ── --}}
        <div class="section-badge"><span>Money Charged</span></div>
        <div class="obs-card">
            <span class="obs-label">Has any money been charged so far?</span>
            <p class="obs-sub">PMJAY treatment must be free of cost. Flag any payment collected from the beneficiary.</p>
            <div class="radio-group">
                <input type="radio" name="money_charged" id="mc_yes" value="Yes" {{ old('money_charged') === 'Yes' ? 'checked' : '' }} onchange="toggleCond('money-details-row', this.value==='Yes')">
                <label for="mc_yes">Yes</label>
                <input type="radio" name="money_charged" id="mc_no"  value="No"  {{ old('money_charged') === 'No'  ? 'checked' : '' }} onchange="toggleCond('money-details-row', false)">
                <label for="mc_no">No</label>
                <input type="radio" name="money_charged" id="mc_na"  value="NA"  {{ old('money_charged') === 'NA'  ? 'checked' : '' }} onchange="toggleCond('money-details-row', false)">
                <label for="mc_na">N/A</label>
            </div>
        </div>

        <div class="cond-row" id="money-details-row" style="{{ old('money_charged')==='Yes' ? 'display:flex; flex-direction:column; gap:.625rem;' : '' }}">
            <div class="obs-card" style="margin-top:.625rem; border-color:#fecaca; background:#fff1f2;">
                <span class="obs-label">If yes, how much?</span>
                <div class="flex items-center gap-2">
                    <span class="text-slate-500 font-semibold text-sm">₹</span>
                    <input type="number" name="money_charged_amount" value="{{ old('money_charged_amount') }}"
                           placeholder="0.00" step="0.01" min="0" class="field-input">
                </div>
            </div>
            <div class="obs-card" style="border-color:#fecaca; background:#fff1f2;">
                <span class="obs-label">Do they have receipts of the same?</span>
                <div class="obs-row">
                    <div class="radio-group">
                        <input type="radio" name="receipt_available" id="ra_yes" value="Yes" {{ old('receipt_available') === 'Yes' ? 'checked' : '' }} onchange="toggleCond('receipt-upload-row', this.value==='Yes')">
                        <label for="ra_yes">Yes</label>
                        <input type="radio" name="receipt_available" id="ra_no"  value="No"  {{ old('receipt_available') === 'No'  ? 'checked' : '' }} onchange="toggleCond('receipt-upload-row', false)">
                        <label for="ra_no">No</label>
                        <input type="radio" name="receipt_available" id="ra_na"  value="NA"  {{ old('receipt_available') === 'NA'  ? 'checked' : '' }} onchange="toggleCond('receipt-upload-row', false)">
                        <label for="ra_na">N/A</label>
                    </div>
                </div>
            </div>
            <div class="obs-card cond-row" id="receipt-upload-row" style="border-color:#fecaca; background:#fff1f2; {{ old('receipt_available')==='Yes' ? 'display:flex;' : '' }}">
                <span class="obs-label">If yes, upload the receipt</span>
                <input type="file" name="receipt_file" accept="image/*,application/pdf"
                       class="field-input" style="padding:.5rem;">
            </div>
        </div>

        {{-- ── Section 6: Previous Hospitalisation ── --}}
        <div class="section-badge"><span>Previous Hospitalisation</span></div>
        <div class="obs-card">
            <span class="obs-label">Is there any previous hospitalisation of the same patient at the same hospital?</span>
            <div class="obs-row">
                <div class="radio-group">
                    <input type="radio" name="previous_hospitalisation" id="ph_yes" value="Yes" {{ old('previous_hospitalisation') === 'Yes' ? 'checked' : '' }}>
                    <label for="ph_yes">Yes</label>
                    <input type="radio" name="previous_hospitalisation" id="ph_no"  value="No"  {{ old('previous_hospitalisation') === 'No'  ? 'checked' : '' }}>
                    <label for="ph_no">No</label>
                    <input type="radio" name="previous_hospitalisation" id="ph_na"  value="NA"  {{ old('previous_hospitalisation') === 'NA'  ? 'checked' : '' }}>
                    <label for="ph_na">N/A</label>
                </div>
                <input type="text" name="previous_hospitalisation_remarks" value="{{ old('previous_hospitalisation_remarks') }}"
                       placeholder="Remarks" class="field-input" style="flex:1;min-width:160px;">
            </div>
        </div>

        {{-- ── Section 7: Other Remarks ── --}}
        <div class="section-badge"><span>Other Remarks</span></div>
        <div class="obs-card">
            <span class="obs-label">Any other remark or observation</span>
            <textarea name="other_remarks" rows="4" placeholder="Enter any additional observations about this audit…" class="field-input">{{ old('other_remarks') }}</textarea>
        </div>

        {{-- ── Supporting Attachments ── --}}
        <div class="section-badge"><span>Supporting Attachments</span></div>
        <div class="obs-card" style="border:2px solid #bfdbfe; background:#eff6ff;">
            <div>
                <span class="obs-label">Attachments</span>
                <p class="text-xs text-slate-500 mt-0.5">Upload supporting documents — image or PDF, max 10 MB each.</p>
            </div>
            <div id="attachment-list"></div>
            <div><button type="button" id="btn-add-attach"><i class="fas fa-plus-circle"></i> Add attachment</button></div>
        </div>

        {{-- ── Submit ── --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-6 mt-6 border-t border-slate-200">
            <button type="submit"
                    id="btn-submit"
                    disabled
                    class="flex-1 sm:flex-none px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 disabled:bg-slate-300 disabled:cursor-not-allowed disabled:shadow-none text-white font-medium text-sm flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition-all">
                <i class="fas fa-check-circle"></i> Submit Live Audit
            </button>
            <button type="reset"
                    class="flex-1 sm:flex-none px-5 py-3.5 rounded-xl border-2 border-slate-300 text-slate-600 hover:bg-slate-50 font-medium text-sm flex items-center justify-center gap-2 transition">
                <i class="fas fa-undo-alt"></i> Reset Form
            </button>
        </div>
    </form>
</div>
@endif

<div id="toast-container"></div>

<div class="text-xs text-slate-400 text-center mt-6 border-t border-slate-200 pt-5">
    <i class="fas fa-shield-alt text-emerald-500 mr-1"></i>
    All Live Audit entries are logged with DMO credentials · PMJAY Assam
</div>

{{-- ── JavaScript (form branch only) ── --}}
@if(!$isCompleted)
<script>
/* Treatment type toggle */
function selectTT(val) {
    document.querySelectorAll('input[name="treatment_type"]').forEach(r => r.checked = (r.value === val));
    document.getElementById('lbl-surgical').className = val === 'Surgical' ? 'tt-active-surgical' : '';
    document.getElementById('lbl-medical').className  = val === 'Medical'  ? 'tt-active-medical'  : '';
}
(function () {
    const checked = document.querySelector('input[name="treatment_type"]:checked');
    if (checked) selectTT(checked.value);
})();

/* Conditional row toggle */
function toggleCond(id, show) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.display = show ? 'flex' : 'none';
    if (show) el.style.flexDirection = 'column';
}

(function () {
    const GMAPS_KEY = '{{ config("services.google_maps.key") }}';

    let gpsReady = false, gpsLat = null, gpsLng = null, gpsAddr = '', mediaStream = null, photoTaken = false;

    const gpsPill        = document.getElementById('gps-pill');
    const gpsText        = document.getElementById('gps-text');
    const liveCoords     = document.getElementById('live-coords');
    const viewfinderWrap = document.getElementById('viewfinder-wrap');
    const video          = document.getElementById('camera-preview');
    const canvas         = document.getElementById('capture-canvas');
    const btnOpenCam     = document.getElementById('btn-open-cam');
    const btnCloseCam    = document.getElementById('btn-close-cam');
    const btnSnap        = document.getElementById('btn-snap');
    const btnRetake      = document.getElementById('btn-retake');
    const previewWrap    = document.getElementById('photo-preview-wrap');
    const previewImg     = document.getElementById('photo-preview-img');
    const mapThumb       = document.getElementById('map-thumb');
    const wmLine1        = document.getElementById('wm-line1');
    const wmLine2        = document.getElementById('wm-line2');
    const wmLine3        = document.getElementById('wm-line3');
    const fieldLat       = document.getElementById('field-lat');
    const fieldLng       = document.getElementById('field-lng');
    const fieldAddress   = document.getElementById('field-address');
    const fieldPhotoFile = document.getElementById('field-photo-file');
    const photoError     = document.getElementById('photo-error');
    const checkingMsg    = document.getElementById('ai-checking-msg');
    const faceBadge      = document.getElementById('ai-face-badge');
    const fAiBed         = document.getElementById('field-ai-bed');
    const fAiPatient     = document.getElementById('field-ai-patient');
    const fAiCard        = document.getElementById('field-ai-card');
    const fAiFaces       = document.getElementById('field-ai-faces');
    const fAiLabels      = document.getElementById('field-ai-labels');
    const fAiObjects     = document.getElementById('field-ai-objects');
    const fAiMessage     = document.getElementById('field-ai-message');
    const form           = document.getElementById('laForm');

    /* ── GPS ── */
    function setGps(state, text) {
        gpsPill.className = 'gps-pill gps-' + state + ' self-start';
        gpsText.textContent = text;
    }
    function updateOverlay() {
        if (gpsLat === null) return;
        const short = gpsAddr ? gpsAddr.split(',').slice(-3).join(',').trim() : '';
        liveCoords.textContent = gpsLat.toFixed(6) + ', ' + gpsLng.toFixed(6) + (short ? ' · ' + short : '');
        if (gpsReady) setGps('ready', gpsLat.toFixed(5) + '°N  ' + gpsLng.toFixed(5) + '°E');
    }
    function reverseGeocode(lat, lng) {
        fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${GMAPS_KEY}`)
            .then(r => r.json())
            .then(d => { if (d.results?.[0]) { gpsAddr = d.results[0].formatted_address; fieldAddress.value = gpsAddr; updateOverlay(); } })
            .catch(() => {});
    }
    if ('geolocation' in navigator) {
        navigator.geolocation.watchPosition(
            pos => { gpsLat = pos.coords.latitude; gpsLng = pos.coords.longitude; gpsReady = true; fieldLat.value = gpsLat; fieldLng.value = gpsLng; updateOverlay(); if (!gpsAddr) reverseGeocode(gpsLat, gpsLng); },
            ()  => setGps('error', 'GPS unavailable — check browser permissions'),
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    } else { setGps('error', 'Geolocation not supported'); }

    /* ── Camera ── */
    async function openCamera() {
        try {
            mediaStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 960 } }, audio: false
            });
            video.srcObject = mediaStream;
            viewfinderWrap.style.display = 'flex';
            btnOpenCam.style.display     = 'none';
            previewWrap.style.display    = 'none';
            mapThumb.style.display       = 'none';
            btnRetake.style.display      = 'none';
        } catch (err) { alert('Camera access denied: ' + err.message); }
    }
    function closeCamera() {
        if (mediaStream) { mediaStream.getTracks().forEach(t => t.stop()); mediaStream = null; }
        viewfinderWrap.style.display = 'none';
        btnOpenCam.style.display     = 'inline-flex';
    }
    btnOpenCam.addEventListener('click',  openCamera);
    btnCloseCam.addEventListener('click', function () { closeCamera(); if (photoTaken) btnRetake.style.display = 'inline-flex'; });

    /* ── Capture + watermark + AI validate ── */
    btnSnap.addEventListener('click', function () {
        if (!gpsReady) { alert('GPS not ready yet. Please wait a moment.'); return; }

        const vw = video.videoWidth || 1280, vh = video.videoHeight || 960;
        canvas.width = vw; canvas.height = vh;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, vw, vh);

        const now      = new Date();
        const dtStr    = now.toLocaleString('en-IN', { dateStyle: 'medium', timeStyle: 'short' });
        const coordStr = gpsLat.toFixed(6) + '° N,  ' + gpsLng.toFixed(6) + '° E';
        const addrStr  = gpsAddr ? gpsAddr.split(',').slice(-4).join(',').trim() : 'Resolving address…';
        const tag      = 'PMJAY Assam · DMO Live Audit';
        const fs       = Math.max(14, Math.round(vh * 0.022));
        const lh       = Math.round(fs * 1.5), pad = Math.round(vw * 0.018);
        const lines    = [tag, coordStr, addrStr, dtStr];
        const stripH   = lines.length * lh + pad * 2;

        ctx.fillStyle = 'rgba(0,0,0,0.62)'; ctx.fillRect(0, vh - stripH, vw, stripH);
        ctx.font = '600 ' + fs + 'px "Segoe UI", Arial, sans-serif'; ctx.textBaseline = 'top';
        lines.forEach((line, i) => {
            const y = vh - stripH + pad + i * lh;
            ctx.fillStyle = 'rgba(0,0,0,0.4)'; ctx.fillText(line, pad + 1, y + 1);
            ctx.fillStyle = i === 0 ? '#a78bfa' : '#fff'; ctx.fillText(line, pad, y);
        });
        const bw = Math.max(3, Math.round(vw * 0.004));
        ctx.strokeStyle = '#a78bfa'; ctx.lineWidth = bw;
        ctx.strokeRect(bw / 2, bw / 2, vw - bw, vh - bw);

        previewImg.src            = canvas.toDataURL('image/jpeg', 0.92);
        previewWrap.style.display = 'block';
        wmLine1.textContent = tag; wmLine2.textContent = coordStr; wmLine3.textContent = dtStr;

        const mapUrl = 'https://maps.googleapis.com/maps/api/staticmap?center=' + gpsLat + ',' + gpsLng + '&zoom=15&size=480x160&maptype=roadmap&markers=color:red%7C' + gpsLat + ',' + gpsLng + '&key=' + GMAPS_KEY;
        mapThumb.src = mapUrl; mapThumb.style.display = 'block';
        fieldLat.value = gpsLat; fieldLng.value = gpsLng; fieldAddress.value = gpsAddr;

        canvas.toBlob(blob => {
            const f = new File([blob], 'live_audit_' + Date.now() + '.jpg', { type: 'image/jpeg' });
            const dt = new DataTransfer(); dt.items.add(f);
            fieldPhotoFile.files = dt.files;
        }, 'image/jpeg', 0.92);

        closeCamera();
        photoTaken = false;
        btnRetake.style.display   = 'none';
        photoError.style.display  = 'none';
        faceBadge.style.display   = 'none';
        checkingMsg.style.display = 'flex';

        fetch('{{ route("dmo.audits.validate.bed.photo") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
            body: JSON.stringify({ image: canvas.toDataURL('image/jpeg', 0.92) })
        })
        .then(r => r.json())
        .then(data => {
            checkingMsg.style.display = 'none';
            btnRetake.style.display   = 'inline-flex';

            const submitBtn = document.getElementById('btn-submit');

            fAiBed.value     = data.ai_bed_detected        ? '1' : '0';
            fAiPatient.value = data.ai_patient_detected    ? '1' : '0';
            fAiCard.value    = data.ai_pmjay_card_detected ? '1' : '0';
            fAiFaces.value   = data.face_count  ?? 0;
            fAiLabels.value  = JSON.stringify(data.ai_labels  ?? []);
            fAiObjects.value = JSON.stringify(data.ai_objects ?? []);
            fAiMessage.value = data.message ?? '';

            if (data.valid) {

                photoTaken         = true;
                submitBtn.disabled = false;

                previewImg.classList.remove('invalid-photo');
                previewImg.classList.add('valid-photo');

                photoError.style.display = 'none';

                faceBadge.className     = 'gps-pill gps-ready';
                faceBadge.innerHTML     = '<i class="fas fa-check-circle mr-1"></i>' + data.message;
                faceBadge.style.display = 'inline-flex';

                toast('success', 'AI Verification Passed', data.message);

            } else {

                photoTaken         = false;
                submitBtn.disabled = true;

                previewImg.classList.remove('valid-photo');
                previewImg.classList.add('invalid-photo');

                // Split multi-sentence errors into bullet lines
                const sentences = (data.message || 'AI validation failed.')
                    .split(/(?<=\.)\s+/)
                    .map(s => s.trim())
                    .filter(Boolean);

                const bulletHtml = sentences.length > 1
                    ? '<ul style="margin:.3rem 0 0 .1rem; padding-left:1.1rem; list-style:disc; display:flex; flex-direction:column; gap:.25rem;">'
                        + sentences.map(s => `<li>${s}</li>`).join('')
                        + '</ul>'
                    : sentences[0];

                photoError.innerHTML =
                    '<div style="display:flex; align-items:flex-start; gap:.5rem;">'
                    + '<i class="fas fa-exclamation-triangle" style="margin-top:.15rem; flex-shrink:0;"></i>'
                    + '<div>' + bulletHtml + '</div>'
                    + '</div>';

                photoError.style.display = 'block';
                faceBadge.style.display  = 'none';

                document.getElementById('camera-section')
                    .scrollIntoView({ behavior: 'smooth', block: 'center' });

                toast('error', 'AI Verification Failed', data.message, 8000);
            }
        })
        .catch(() => {
            // Fail-open: Vision API down / network error — warn but allow submission
            checkingMsg.style.display = 'none';
            btnRetake.style.display   = 'inline-flex';

            const submitBtn = document.getElementById('btn-submit');

            photoTaken         = true;
            submitBtn.disabled = false;
            fAiMessage.value   = 'AI check skipped (service unavailable)';

            previewImg.classList.remove('invalid-photo');
            previewImg.classList.add('valid-photo');

            faceBadge.className     = 'gps-pill gps-acquiring';
            faceBadge.innerHTML     = '<i class="fas fa-exclamation-circle mr-1"></i> AI check skipped — photo accepted, flagged for manual review';
            faceBadge.style.display = 'inline-flex';

            photoError.style.display = 'none';

            toast('warning', 'AI Check Skipped', 'Vision service unavailable. Photo accepted — flagged for manual review by supervisor.');
        });
    });

    /* ── Retake ── */
    btnRetake.addEventListener('click', function () {
        photoTaken = false;
        previewWrap.style.display = 'none';
        mapThumb.style.display    = 'none';
        btnRetake.style.display   = 'none';
        faceBadge.style.display   = 'none';
        checkingMsg.style.display = 'none';
        photoError.style.display  = 'none';
        fAiBed.value = fAiPatient.value = fAiCard.value = '0';
        fAiFaces.value = '0'; fAiLabels.value = '[]'; fAiObjects.value = '[]'; fAiMessage.value = '';
        openCamera();
    });

    /* ── Attachments ── */
    const attachList = document.getElementById('attachment-list');
    const btnAdd     = document.getElementById('btn-add-attach');
    let   attachIdx  = 0;

    function addAttachmentRow() {
        const idx = attachIdx++;
        const row = document.createElement('div');
        row.className = 'attach-row';
        row.innerHTML = `
            <div class="attach-inner">
                <input type="text" name="attachments[${idx}][name]"
                       placeholder="Document name (e.g. ID proof, Prescription)" class="field-input"
                       style="padding:.55rem .8rem; font-size:.875rem;" autocomplete="off">
                <div class="drop-zone" id="dz-${idx}">
                    <input type="file" name="attachments[${idx}][file]" id="af-${idx}" accept="image/*,application/pdf">
                    <div id="dz-inner-${idx}" class="pointer-events-none">
                        <i class="fas fa-cloud-upload-alt text-slate-400 text-xl mb-1"></i>
                        <p class="text-xs text-slate-500 font-medium">Tap to upload or drag &amp; drop</p>
                        <p class="text-xs text-slate-400 mt-0.5">Image or PDF · max 10 MB</p>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-remove-attach" title="Remove">
                <i class="fas fa-times text-xs"></i>
            </button>
        `;
        const fi = row.querySelector('input[type="file"]');
        const di = row.querySelector(`#dz-inner-${idx}`);
        const dz = row.querySelector(`#dz-${idx}`);

        fi.addEventListener('change', function () {
            const file = this.files[0]; if (!file) return;
            const isPdf = file.type === 'application/pdf';
            di.innerHTML = `<span class="file-chip ${isPdf ? 'is-pdf' : 'is-img'}">
                <i class="fas ${isPdf ? 'fa-file-pdf' : 'fa-file-image'}"></i>
                <span title="${file.name}">${file.name}</span>
            </span><p class="text-xs text-slate-400 mt-1">${(file.size / 1024).toFixed(0)} KB</p>`;
        });
        dz.addEventListener('dragover',  e => { e.preventDefault(); dz.classList.add('drag-over'); });
        dz.addEventListener('dragleave', ()  => dz.classList.remove('drag-over'));
        dz.addEventListener('drop', e => {
            e.preventDefault(); dz.classList.remove('drag-over');
            fi.files = e.dataTransfer.files; fi.dispatchEvent(new Event('change'));
        });
        row.querySelector('.btn-remove-attach').addEventListener('click', () => row.remove());
        attachList.appendChild(row);
    }
    btnAdd.addEventListener('click', addAttachmentRow);

    /* ── Form submit guard ── */
    form.addEventListener('submit', function (e) {
        if (!photoTaken) {
            photoError.innerHTML     = '<i class="fas fa-exclamation-triangle mr-1"></i> An AI-verified on-bed patient photograph is required before submitting.';
            photoError.style.display = 'block';
            document.getElementById('camera-section').scrollIntoView({ behavior: 'smooth', block: 'center' });
            toast('error', 'Photo Required', 'Please capture and pass AI verification before submitting.');
            e.preventDefault();
        }
    });

})();

/* ═══════════════════════════════════════════
   Toast notification system
   toast(type, title, message, duration)
   type: 'error' | 'success' | 'warning'
   ═══════════════════════════════════════════ */
function toast(type, title, message, duration) {
    duration = duration ?? (type === 'error' ? 7000 : 4500);

    const icons = { error: 'fa-times-circle', success: 'fa-check-circle', warning: 'fa-exclamation-circle' };

    // Split multi-sentence messages into bullet list
    const sentences = (message || '')
        .split(/(?<=\.)\s+/)
        .map(s => s.trim())
        .filter(Boolean);

    const msgHtml = sentences.length > 1
        ? '<ul>' + sentences.map(s => `<li>${s}</li>`).join('') + '</ul>'
        : `<span>${sentences[0] ?? ''}</span>`;

    const el = document.createElement('div');
    el.className = `toast toast-${type}`;
    el.innerHTML = `
        <div class="toast-icon"><i class="fas ${icons[type]}"></i></div>
        <div class="toast-body">
            <div class="toast-title">${title}</div>
            <div class="toast-msg">${msgHtml}</div>
        </div>
        <button class="toast-close" title="Dismiss"><i class="fas fa-times"></i></button>
        <div class="toast-progress" style="animation-duration:${duration}ms;"></div>
    `;

    const container = document.getElementById('toast-container');
    container.appendChild(el);

    // Slide in
    requestAnimationFrame(() => requestAnimationFrame(() => el.classList.add('toast-in')));

    // Auto-dismiss
    const dismiss = () => {
        el.classList.remove('toast-in');
        el.classList.add('toast-out');
        el.addEventListener('transitionend', () => el.remove(), { once: true });
    };
    const timer = setTimeout(dismiss, duration);

    // Manual close
    el.querySelector('.toast-close').addEventListener('click', () => { clearTimeout(timer); dismiss(); });
}
</script>
@endif
@endsection

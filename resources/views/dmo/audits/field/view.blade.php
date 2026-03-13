@extends('dmo.layout.layout')

@section('main_title')

<div class="flex items-center gap-2 text-sm text-slate-400 mb-5">
    <a href="{{ route('dmo.audits.field.all') }}" class="hover:text-emerald-600 transition-colors">
        <i class="fas fa-arrow-left mr-1 text-xs"></i> Back to audits
    </a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-600 font-medium">Medical Audit</span>
</div>

<div class="mb-7">
    <h2 class="text-xl md:text-3xl font-semibold text-slate-800 tracking-tight flex items-center gap-2">
        <i class="fas fa-hospital-user text-emerald-600"></i>
        Medical Audit
    </h2>
    <p class="text-sm text-slate-500 mt-1">
        Physical hospital verification · PMJAY Assam audit
    </p>
</div>

@endsection

@section('pageCss')
<style>
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
        position: relative;
        overflow: hidden;
    }
    .toast.toast-in  { transform: translateX(0); opacity: 1; }
    .toast.toast-out { transform: translateX(120%); opacity: 0; transition: transform .25s ease-in, opacity .22s ease-in; }

    .toast-error   { background: rgba(255,241,242,.97); border-color: #fda4af; color: #9f1239; }
    .toast-success { background: rgba(240,253,244,.97); border-color: #86efac; color: #14532d; }
    .toast-warning { background: rgba(255,251,235,.97); border-color: #fde68a; color: #78350f; }
    .toast-info    { background: rgba(239,246,255,.97); border-color: #93c5fd; color: #1e3a8a; }

    .toast-icon { width: 1.75rem; height: 1.75rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: .8rem; margin-top: .05rem; }
    .toast-error   .toast-icon { background: #fee2e2; color: #dc2626; }
    .toast-success .toast-icon { background: #dcfce7; color: #16a34a; }
    .toast-warning .toast-icon { background: #fef3c7; color: #d97706; }
    .toast-info    .toast-icon { background: #dbeafe; color: #2563eb; }

    .toast-body  { flex: 1; min-width: 0; }
    .toast-title { font-weight: 700; font-size: .8125rem; margin-bottom: .15rem; }
    .toast-msg ul { margin: .25rem 0 0 .1rem; padding-left: 1rem; list-style: disc; display: flex; flex-direction: column; gap: .2rem; }

    .toast-close { flex-shrink: 0; background: none; border: none; cursor: pointer; opacity: .45; font-size: .75rem; padding: .1rem .2rem; margin-top: .05rem; transition: opacity .15s; line-height: 1; color: inherit; }
    .toast-close:hover { opacity: .9; }

    .toast-progress { position: absolute; bottom: 0; left: 0; height: 3px; border-radius: 0 0 .875rem .875rem; animation: toast-drain linear forwards; }
    .toast-error   .toast-progress { background: #f43f5e; }
    .toast-success .toast-progress { background: #22c55e; }
    .toast-warning .toast-progress { background: #f59e0b; }
    .toast-info    .toast-progress { background: #3b82f6; }
    @keyframes toast-drain { from { width: 100%; } to { width: 0%; } }

    /* ── Radio pill ── */
    .radio-group { display:inline-flex; border-radius:9999px; overflow:hidden; border:2px solid #e2e8f0; background:#f8fafc; }
    .radio-group input[type="radio"] { display:none; }
    .radio-group label { padding:.5rem 1.1rem; font-size:.8rem; font-weight:500; color:#64748b; cursor:pointer; transition:background .18s,color .18s; user-select:none; white-space:nowrap; }
    .radio-group label:first-of-type { border-right:1px solid #e2e8f0; }
    .radio-group input[value="Yes"]:checked + label { background:#059669; color:#fff; }
    .radio-group input[value="No"]:checked  + label { background:#e11d48; color:#fff; }
    .radio-group input[value="NA"]:checked  + label { background:#64748b; color:#fff; }

    /* ── Surgical rows hidden by default until Surgical is selected ── */
    .surgical-row { display:none; }

    /* ── Treatment type segmented toggle (label wraps input) ── */
    .tt-group { display:inline-flex; border-radius:9999px; overflow:hidden; border:2px solid #e2e8f0; background:#f8fafc; }
    .tt-group label { display:inline-flex; align-items:center; gap:.4rem; padding:.55rem 1.3rem; font-size:.8rem; font-weight:500; color:#64748b; cursor:pointer; transition:background .18s,color .18s; user-select:none; white-space:nowrap; border-right:1px solid #e2e8f0; }
    .tt-group label:last-child { border-right:none; }
    .tt-group input[type="radio"] { position:absolute; opacity:0; pointer-events:none; width:0; height:0; }
    .tt-group label.tt-active-surgical { background:#7c3aed; color:#fff; border-color:#7c3aed; }
    .tt-group label.tt-active-medical  { background:#0369a1; color:#fff; border-color:#0369a1; }

    /* ── Obs cards ── */
    .obs-card { background:#fff; border:1px solid #e2e8f0; border-radius:1rem; padding:1rem 1.125rem; display:flex; flex-direction:column; gap:.75rem; }
    .obs-card + .obs-card { margin-top:.625rem; }
    .obs-label { font-size:.875rem; font-weight:600; color:#334155; line-height:1.4; }
    .obs-sub   { font-size:.75rem; color:#64748b; margin-top:.125rem; line-height:1.5; }
    .obs-row   { display:flex; align-items:flex-start; gap:.75rem; flex-wrap:wrap; }

    /* ── Inputs ── */
    .field-input { width:100%; padding:.65rem .875rem; border:2px solid #e2e8f0; border-radius:.75rem; background:#f8fafc; font-size:.9rem; color:#1e293b; transition:border-color .15s,background .15s; outline:none; }
    .field-input:focus { border-color:#34d399; background:#fff; }
    textarea.field-input { resize:vertical; min-height:80px; }

    /* ── Section badge ── */
    .section-badge { display:flex; align-items:center; gap:.5rem; font-size:.7rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#94a3b8; margin:1.5rem 0 .625rem; }
    .section-badge::before,.section-badge::after { content:''; flex:1; height:1px; background:#e2e8f0; }

    /* ── GPS status pill ── */
    .gps-pill { display:inline-flex; align-items:center; gap:.4rem; font-size:.75rem; font-weight:500; padding:.35rem .75rem; border-radius:9999px; }
    .gps-acquiring { background:#fef9c3; color:#a16207; }
    .gps-ready     { background:#dcfce7; color:#166534; }
    .gps-error     { background:#fee2e2; color:#991b1b; }

    /* ── Camera video ── */
    #camera-preview { width:100%; max-width:480px; aspect-ratio:4/3; border-radius:.875rem; background:#0f172a; display:block; object-fit:cover; }

    /* ── Photo preview ── */
    #photo-preview-wrap { display:none; position:relative; border-radius:.875rem; overflow:hidden; border:2px solid #34d399; max-width:480px; }
    #photo-preview-wrap img { width:100%; display:block; }
    .wm-strip { position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,.58); color:#fff; font-size:.68rem; padding:.4rem .65rem; display:flex; flex-direction:column; gap:.1rem; backdrop-filter:blur(2px); }

    /* ── Map thumbnail ── */
    #map-thumb { width:100%; max-width:480px; height:160px; border-radius:.875rem; object-fit:cover; border:2px solid #e2e8f0; display:none; }

    /* ── Mandatory badge ── */
    .mandatory-badge { display:inline-flex; align-items:center; gap:.3rem; background:#fef3c7; color:#92400e; font-size:.65rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; padding:.25rem .6rem; border-radius:9999px; }

    /* ── Camera action buttons ── */
    .cam-btn { display:inline-flex; align-items:center; gap:.5rem; padding:.7rem 1.4rem; border-radius:9999px; font-size:.8125rem; font-weight:600; cursor:pointer; transition:all .18s; border:none; outline:none; }
    .cam-btn-dark  { background:#0f172a; color:#fff; }
    .cam-btn-dark:hover  { background:#1e293b; }
    .cam-btn-danger { background:#fee2e2; color:#991b1b; }
    .cam-btn-danger:hover { background:#fecaca; }

    /* ── Attachment rows ── */
    .attach-row { display:grid; grid-template-columns:1fr auto; gap:.625rem; align-items:start; background:#f8fafc; border:1px solid #e2e8f0; border-radius:.875rem; padding:.75rem .875rem; transition:border-color .15s; }
    .attach-row:focus-within { border-color:#34d399; background:#fff; }
    .attach-row + .attach-row { margin-top:.5rem; }
    .attach-inner { display:flex; flex-direction:column; gap:.5rem; }
    .drop-zone { border:2px dashed #cbd5e1; border-radius:.75rem; padding:1rem; text-align:center; cursor:pointer; transition:border-color .18s,background .18s; position:relative; overflow:hidden; }
    .drop-zone.drag-over { border-color:#34d399; background:#f0fdf4; }
    .drop-zone input[type="file"] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
    .file-chip { display:inline-flex; align-items:center; gap:.4rem; background:#e0f2fe; color:#0369a1; font-size:.75rem; font-weight:600; padding:.3rem .7rem; border-radius:9999px; max-width:100%; overflow:hidden; }
    .file-chip.is-pdf { background:#fef3c7; color:#92400e; }
    .file-chip.is-img { background:#d1fae5; color:#065f46; }
    .file-chip span   { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:160px; }
    .btn-remove-attach { width:2rem; height:2rem; border-radius:9999px; display:flex; align-items:center; justify-content:center; background:#fee2e2; color:#991b1b; border:none; cursor:pointer; flex-shrink:0; margin-top:.25rem; transition:background .15s; }
    .btn-remove-attach:hover { background:#fecaca; }
    #btn-add-attach { display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.2rem; border-radius:9999px; font-size:.8rem; font-weight:600; cursor:pointer; border:2px dashed #94a3b8; background:transparent; color:#64748b; transition:all .18s; margin-top:.5rem; }
    #btn-add-attach:hover { border-color:#059669; color:#059669; background:#f0fdf4; }

    /* ── Surgical-only notice ── */
    .surgical-notice { display:none; font-size:.7rem; color:#7c3aed; background:#ede9fe; border:1px solid #c4b5fd; border-radius:.5rem; padding:.3rem .65rem; margin-top:.25rem; }

    /* ══ READ-ONLY STYLES ══ */
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
    .ro-obs-card { background:#fff; border:1px solid #e2e8f0; border-radius:1rem; padding:.875rem 1.125rem; display:flex; flex-direction:column; gap:.5rem; }
    .ro-obs-card + .ro-obs-card { margin-top:.5rem; }
    .ro-obs-row { display:flex; align-items:flex-start; justify-content:space-between; gap:.75rem; flex-wrap:wrap; }
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
    .ro-attach-link { display:inline-flex; align-items:center; gap:.3rem; font-size:.75rem; font-weight:600; color:#0369a1; background:#e0f2fe; padding:.3rem .75rem; border-radius:9999px; text-decoration:none; transition:background .15s; flex-shrink:0; }
    .ro-attach-link:hover { background:#bae6fd; }
    .ro-attach-link.pdf-link { color:#92400e; background:#fef3c7; }
    .ro-attach-link.pdf-link:hover { background:#fde68a; }
    .gps-chip { display:inline-flex; align-items:center; gap:.4rem; background:#dcfce7; color:#166534; font-size:.75rem; font-weight:600; padding:.35rem .85rem; border-radius:9999px; }
    .submitted-strip { display:flex; align-items:center; gap:.5rem; font-size:.75rem; color:#64748b; background:#f8fafc; border:1px solid #e2e8f0; border-radius:.75rem; padding:.5rem .875rem; flex-wrap:wrap; }
</style>
@endsection

@section('main_content')

@php $isCompleted = $audit->status === 'completed'; @endphp

{{-- ════════════ CASE DETAILS (always visible) ════════════ --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-slate-50 to-white px-4 md:px-6 py-3.5 border-b border-slate-200 flex items-center gap-2">
        <div class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
            <i class="fas fa-info-circle text-emerald-600 text-sm"></i>
        </div>
        <h3 class="font-medium text-slate-700 text-sm md:text-base">Case information · pre‑authorisation summary</h3>
        @if($isCompleted)
        <span class="ml-auto inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 text-xs px-3 py-1 rounded-full font-bold">
            <i class="fas fa-check-circle"></i> Completed
        </span>
        @else
        <span class="ml-auto inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 text-xs px-3 py-1 rounded-full font-bold">
            <i class="fas fa-clock"></i> Pending
        </span>
        @endif
    </div>
    <div class="p-4 md:p-6 grid grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
        <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-100">
            <span class="text-slate-400 text-xs block mb-0.5">Registration ID</span>
            <span class="font-semibold text-slate-800 break-all">{{ $audit->treatment->registration_id }}</span>
        </div>
        <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-100">
            <span class="text-slate-400 text-xs block mb-0.5">Case ID</span>
            <span class="font-semibold text-slate-800 break-all">{{ $audit->treatment->case_id }}</span>
        </div>
        <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-100 col-span-2 md:col-span-1">
            <span class="text-slate-400 text-xs block mb-0.5">Patient name</span>
            <span class="font-semibold text-slate-800">{{ $audit->treatment->patient_name }}</span>
        </div>
        <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-100">
            <span class="text-slate-400 text-xs block mb-0.5">Mobile number</span>
            <span class="font-semibold text-slate-800">{{ $audit->treatment->ben_mobile_no }}</span>
        </div>
        <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-100">
            <span class="text-slate-400 text-xs block mb-0.5">Hospital</span>
            <span class="font-semibold text-slate-800">{{ $audit->treatment->hospital->name }}</span>
        </div>
        <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-100">
            <span class="text-slate-400 text-xs block mb-0.5">District</span>
            <span class="font-semibold text-slate-800">{{ $audit->district->name }}</span>
        </div>
        <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-100 col-span-2 lg:col-span-2">
            <span class="text-slate-400 text-xs block mb-0.5">Procedure</span>
            <span class="font-semibold text-slate-800">{{ $audit->treatment->procedure_details }}</span>
        </div>
        <div class="bg-emerald-50 p-3 rounded-xl border border-emerald-100 col-span-2 md:col-span-1">
            <span class="text-emerald-500 text-xs block mb-0.5">Preauth amount</span>
            <span class="font-bold text-emerald-700 text-base">₹ {{ $audit->treatment->amount_preauth_approved }}</span>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════
     BRANCH A — COMPLETED: read-only view
     ══════════════════════════════════════════ --}}
@if($isCompleted)
@php $fv = $audit->fieldVisit; @endphp

<div class="completed-banner">
    <div class="icon-wrap"><i class="fas fa-check text-white text-sm"></i></div>
    <div>
        <p class="font-semibold text-emerald-800 text-sm">Medical Audit completed</p>
        <p class="text-emerald-700 text-xs mt-0.5">
            Submitted {{ $fv?->created_at?->format('d M Y, h:i A') ?? '—' }}
            @if($fv?->submittedBy) &nbsp;·&nbsp; by {{ $fv->submittedBy->name }} @endif
        </p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-md p-4 md:p-8">
    <h3 class="text-base md:text-lg font-semibold text-slate-800 flex items-center gap-2 mb-5">
        <div class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
            <i class="fas fa-clipboard-check text-emerald-600 text-sm"></i>
        </div>
        Medical Audit observations
    </h3>

    {{-- Patient & Case Details --}}
    <div class="section-badge"><span>Patient &amp; Case Details</span></div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="ro-cell"><span class="ro-label">Name of Patient</span><span class="ro-value {{ $fv?->patient_name ? '' : 'empty' }}">{{ $fv?->patient_name ?? 'Not recorded' }}</span></div>
        <div class="ro-cell"><span class="ro-label">Package Booked</span><span class="ro-value {{ $fv?->package_booked ? '' : 'empty' }}">{{ $fv?->package_booked ?? 'Not recorded' }}</span></div>
        <div class="ro-cell"><span class="ro-label">Treating Doctor</span><span class="ro-value {{ $fv?->treating_doctor ? '' : 'empty' }}">{{ $fv?->treating_doctor ?? 'Not recorded' }}</span></div>
        <div class="ro-cell"><span class="ro-label">Specialization</span><span class="ro-value {{ $fv?->doctor_specialization ? '' : 'empty' }}">{{ $fv?->doctor_specialization ?? 'Not recorded' }}</span></div>
        <div class="ro-cell"><span class="ro-label">Date &amp; Time of Admission</span><span class="ro-value {{ $fv?->admission_datetime ? '' : 'empty' }}">{{ $fv?->admission_datetime ? \Carbon\Carbon::parse($fv->admission_datetime)->format('d M Y, h:i A') : 'Not recorded' }}</span></div>
        <div class="ro-cell"><span class="ro-label">Date &amp; Time of Discharge</span><span class="ro-value {{ $fv?->discharge_datetime ? '' : 'empty' }}">{{ $fv?->discharge_datetime ? \Carbon\Carbon::parse($fv->discharge_datetime)->format('d M Y, h:i A') : 'Not recorded' }}</span></div>
        <div class="ro-cell"><span class="ro-label">Type of Treatment</span>
            @if($fv?->treatment_type)
                <span class="yn-badge {{ $fv->treatment_type === 'Surgical' ? 'bg-violet-100 text-violet-800' : 'bg-blue-100 text-blue-800' }}" style="font-size:.78rem;">
                    <i class="fas {{ $fv->treatment_type === 'Surgical' ? 'fa-scalpel' : 'fa-pills' }} text-xs"></i>
                    {{ $fv->treatment_type }}
                </span>
            @else
                <span class="ro-value empty">Not recorded</span>
            @endif
        </div>
        <div class="ro-cell sm:col-span-2"><span class="ro-label">Diagnosis</span><span class="ro-value {{ $fv?->diagnosis ? '' : 'empty' }}">{{ $fv?->diagnosis ?? 'Not recorded' }}</span></div>
    </div>

    {{-- Verification Checks --}}
    <div class="section-badge"><span>Verification Checks</span></div>
    @php
        $isSurgical = $fv?->treatment_type === 'Surgical';
        $checks = [
            ['Did the patient leave against medical advice?',                     null,                                                                                                                                           'lama',                         'lama_remarks',                        false],
            ['Entry in Outdoor Register found',                                   null,                                                                                                                                           'outdoor_register',             'outdoor_register_remarks',            false],
            ['Entry in Indoor Register found',                                    null,                                                                                                                                           'indoor_register',              'indoor_register_remarks',             false],
            ['Entry in OT Register found',                                        'Only applicable for surgical cases',                                                                                                           'ot_register',                  'ot_register_remarks',                 true],
            ['Entry in Hospital Lab Register found',                              null,                                                                                                                                           'lab_register',                 'lab_register_remarks',                false],
            ['Completeness of IPD papers',                                        'Should have patient details, presenting complaints, diagnosis, investigations, treatment etc.',                                               'ipd_complete',                 'ipd_complete_remarks',                false],
            ['IPD papers align with and justify the treatment given',             null,                                                                                                                                           'ipd_aligns',                   'ipd_aligns_remarks',                  false],
            ['Availability and completeness of OT notes',                         'Surgical cases only',                                                                                                                          'ot_notes_available',           'ot_notes_available_remarks',          true],
            ['OT notes completeness',                                             'Date & time of surgery, surgeon & anaesthetist name, type of anaesthesia, surgery done, post-op care, complications, surgeon signature',      'ot_notes_complete',            'ot_notes_complete_remarks',           true],
            ['OT notes align with and confirm the conduction of booked surgery',  null,                                                                                                                                           'ot_notes_align',               'ot_notes_align_remarks',              true],
            ['Availability of pre-anaesthesia documents',                         'Assessed by a qualified anaesthesiologist',                                                                                                    'pre_anaesthesia',              'pre_anaesthesia_remarks',             true],
            ['Availability of daily nursing notes',                               null,                                                                                                                                           'nursing_notes_available',      'nursing_notes_available_remarks',     false],
            ['Completeness of daily nursing notes',                               'Should have date, status/progress of patient as recorded by nurse',                                                                           'nursing_notes_complete',       'nursing_notes_complete_remarks',      false],
            ['Availability of daily doctor notes',                                null,                                                                                                                                           'doctor_notes_available',       'doctor_notes_available_remarks',      false],
            ['Completeness of daily doctor notes',                                'Should have date, status/progress of patient and further course of medication/treatment as recorded by doctor',                              'doctor_notes_complete',        'doctor_notes_complete_remarks',       false],
            ['Availability of daily progress chart',                              null,                                                                                                                                           'progress_chart_available',     'progress_chart_available_remarks',    false],
            ['Completeness of daily progress chart',                              'Should have record of vitals with date and time',                                                                                             'progress_chart_complete',      'progress_chart_complete_remarks',     false],
            ['Availability of daily treatment chart',                             null,                                                                                                                                           'treatment_chart_available',    'treatment_chart_available_remarks',   false],
            ['Completeness of daily treatment chart',                             'Should have record of medication with date and time',                                                                                          'treatment_chart_complete',     'treatment_chart_complete_remarks',    false],
            ['Availability of recorded monitoring details',                       'Heart rate, cardiac rhythm, respiratory rate, BP, O₂ saturation, airway security, anaesthesia level',                                        'monitoring_available',         'monitoring_available_remarks',        false],
            ['Completeness of Discharge Summary',                                 null,                                                                                                                                           'discharge_summary',            'discharge_summary_remarks',           false],
        ];
    @endphp

    @foreach($checks as $check)
    @if(!$check[4] || $isSurgical)
    <div class="ro-obs-card {{ $check[4] ? 'border-violet-100 bg-violet-50/30' : '' }}">
        <div class="ro-obs-row">
            <div style="flex:1;">
                <span class="obs-label">{{ $check[0] }}</span>
                @if($check[1])<p class="obs-sub">{{ $check[1] }}</p>@endif
                @if($check[4])<span class="surgical-notice" style="display:inline-flex;"><i class="fas fa-scalpel mr-1"></i> Surgical cases only</span>@endif
            </div>
            @php $val = $fv?->{$check[2]}; @endphp
            @if($val === 'Yes')
                <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
            @elseif($val === 'No')
                <span class="yn-badge yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
            @elseif($val === 'NA')
                <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span>
            @else
                <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> —</span>
            @endif
        </div>
        @if($fv?->{$check[3]})
        <p class="text-xs text-slate-500 bg-white rounded-lg px-3 py-2 border border-slate-100">
            <i class="fas fa-comment-alt text-slate-300 mr-1"></i>{{ $fv->{$check[3]} }}
        </p>
        @endif
    </div>
    @endif
    @endforeach

    {{-- Overall Justification --}}
    @if($fv?->overall_remarks)
    <div class="section-badge"><span>Overall Justification</span></div>
    <div class="ro-obs-card">
        <span class="obs-label">Do all documents align and justify the need and treatment given?</span>
        <p class="text-sm text-slate-700 whitespace-pre-line bg-slate-50 rounded-lg px-3 py-2 border border-slate-100">{{ $fv->overall_remarks }}</p>
    </div>
    @endif

    {{-- Site Photograph --}}
    <div class="section-badge"><span>Site Photograph</span></div>
    @if($fv?->photo_path)
    <div class="flex flex-col gap-3">
        <div class="ro-photo">
            <img src="{{ Storage::disk('public')->url($fv->photo_path) }}" alt="Site photograph">
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <span class="gps-chip"><i class="fas fa-map-marker-alt"></i> {{ number_format($fv->photo_latitude, 6) }}°N, {{ number_format($fv->photo_longitude, 6) }}°E</span>
            @if($fv->photo_address)<span class="text-xs text-slate-500">{{ $fv->photo_address }}</span>@endif
        </div>
        @php
            $mapUrl = 'https://maps.googleapis.com/maps/api/staticmap?center='.$fv->photo_latitude.','.$fv->photo_longitude.'&zoom=15&size=480x160&maptype=roadmap&markers=color:red%7C'.$fv->photo_latitude.','.$fv->photo_longitude.'&key='.config('services.google_maps.key');
        @endphp
        <img src="{{ $mapUrl }}" alt="Location map" class="ro-map">
        @if($fv->photo_taken_at)
        <p class="text-xs text-slate-400"><i class="fas fa-clock mr-1"></i> Photo taken at {{ $fv->photo_taken_at->format('d M Y, h:i A') }}</p>
        @endif
    </div>
    @else
    <div class="ro-obs-card"><span class="ro-value empty">No photograph recorded</span></div>
    @endif

    {{-- Attachments --}}
    <div class="section-badge"><span>Supporting Attachments</span></div>
    @if($fv?->attachments && $fv->attachments->count())
    <div>
        @foreach($fv->attachments->sortBy('sort_order') as $att)
        @php $isPdf = str_ends_with(strtolower($att->file_path), '.pdf'); $attUrl = Storage::disk('public')->url($att->file_path); @endphp
        <div class="ro-attach-item">
            <div class="ro-attach-icon {{ $isPdf ? 'pdf' : 'img' }}"><i class="fas {{ $isPdf ? 'fa-file-pdf' : 'fa-file-image' }}"></i></div>
            <div class="ro-attach-meta"><div class="ro-attach-name">{{ $att->name }}</div></div>
            <a href="{{ $attUrl }}" target="_blank" class="ro-attach-link {{ $isPdf ? 'pdf-link' : '' }}">
                <i class="fas {{ $isPdf ? 'fa-file-pdf' : 'fa-eye' }}"></i> {{ $isPdf ? 'Open PDF' : 'View' }}
            </a>
        </div>
        @endforeach
    </div>
    @else
    <div class="ro-obs-card"><span class="ro-value empty">No attachments recorded</span></div>
    @endif

    <div class="submitted-strip mt-6">
        <i class="fas fa-shield-alt text-emerald-500"></i>
        <span>Submitted by <strong>{{ $fv?->submittedBy?->name ?? '—' }}</strong> on {{ $fv?->created_at?->format('d M Y, h:i A') ?? '—' }}</span>
        <span class="ml-auto text-slate-400">PMJAY Assam · DMO Field Audit</span>
    </div>
</div>


{{-- ══════════════════════════════════════════
     BRANCH B — PENDING: entry form
     ══════════════════════════════════════════ --}}
@else

<div class="bg-white rounded-2xl border border-slate-200 shadow-md p-4 md:p-8">
    <h3 class="text-base md:text-lg font-semibold text-slate-800 flex items-center gap-2 mb-5">
        <div class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
            <i class="fas fa-clipboard-check text-emerald-600 text-sm"></i>
        </div>
        Medical Audit observations
    </h3>

    <form id="fieldVisitForm" method="POST" action="{{ route('dmo.audits.field.store', $audit->id) }}" enctype="multipart/form-data">
        @csrf

        {{-- ── Patient & Case Details ── --}}
        <div class="section-badge"><span>Patient &amp; Case Details</span></div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="obs-card">
                <span class="obs-label">Name of Patient</span>
                <input type="text" name="patient_name" value="{{ old('patient_name') }}" placeholder="Enter patient name" class="field-input">
            </div>
            <div class="obs-card">
                <span class="obs-label">Package Booked</span>
                <input type="text" name="package_booked" value="{{ old('package_booked') }}" placeholder="Enter package name" class="field-input">
            </div>
            <div class="obs-card">
                <span class="obs-label">Name of Treating Doctor</span>
                <input type="text" name="treating_doctor" value="{{ old('treating_doctor') }}" placeholder="Enter doctor's name" class="field-input">
            </div>
            <div class="obs-card">
                <span class="obs-label">Specialization of Treating Doctor</span>
                <input type="text" name="doctor_specialization" value="{{ old('doctor_specialization') }}" placeholder="e.g. Orthopaedics, Cardiology" class="field-input">
            </div>
            <div class="obs-card">
                <span class="obs-label">Date &amp; Time of Hospital Admission <span class="text-slate-400 font-normal text-xs">(as per hospital file)</span></span>
                <input type="datetime-local" name="admission_datetime" value="{{ old('admission_datetime') }}" class="field-input">
            </div>
            <div class="obs-card">
                <span class="obs-label">Date &amp; Time of Hospital Discharge <span class="text-slate-400 font-normal text-xs">(as per hospital file)</span></span>
                <input type="datetime-local" name="discharge_datetime" value="{{ old('discharge_datetime') }}" class="field-input">
            </div>
        </div>

        {{-- Treatment type + diagnosis --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
            <div class="obs-card">
                <span class="obs-label">Type of Treatment <span class="text-rose-500">*</span></span>
                <div class="obs-row">
                    <div class="tt-group" id="treatment-type-group">
                        <label id="lbl-surgical" onclick="selectTT('Surgical')">
                            <input type="radio" name="treatment_type" value="Surgical" {{ old('treatment_type') === 'Surgical' ? 'checked' : '' }}>
                            <i class="fas fa-scalpel text-xs"></i> Surgical
                        </label>
                        <label id="lbl-medical" onclick="selectTT('Medical')">
                            <input type="radio" name="treatment_type" value="Medical" {{ old('treatment_type') === 'Medical' ? 'checked' : '' }}>
                            <i class="fas fa-pills text-xs"></i> Medical
                        </label>
                    </div>
                </div>
            </div>
            <div class="obs-card">
                <span class="obs-label">Diagnosis</span>
                <input type="text" name="diagnosis" value="{{ old('diagnosis') }}" placeholder="Primary diagnosis" class="field-input">
            </div>
        </div>

        {{-- ── Verification Checks ── --}}
        <div class="section-badge"><span>Verification Checks</span></div>

        @php
        $rows = [
            ['Did the patient leave against medical advice?',                    null,                                                                                                                                           'lama',                         false],
            ['If yes, why?',                                                     'Provide reason if patient left against medical advice',                                                                                        '_lama_reason',                 false],
            ['Entry in Outdoor Register found',                                  null,                                                                                                                                           'outdoor_register',             false],
            ['Entry in Indoor Register found',                                   null,                                                                                                                                           'indoor_register',              false],
            ['Entry in OT Register found',                                       'Only applicable for surgical cases',                                                                                                           'ot_register',                  true],
            ['Entry in Hospital Lab Register found',                             null,                                                                                                                                           'lab_register',                 false],
            ['Completeness of IPD papers',                                       'Should have patient details, presenting complaints, diagnosis, investigations, treatment etc.',                                               'ipd_complete',                 false],
            ['IPD papers align with and justify the treatment given',            null,                                                                                                                                           'ipd_aligns',                   false],
            ['Availability and completeness of OT notes',                        'Surgical cases only',                                                                                                                          'ot_notes_available',           true],
            ['Completeness of OT notes',                                         'Date & time of surgery, surgeon & anaesthetist name, type of anaesthesia, surgery done, post-op care, complications, surgeon signature',      'ot_notes_complete',            true],
            ['OT notes align with and confirm the conduction of booked surgery', null,                                                                                                                                           'ot_notes_align',               true],
            ['Availability of pre-anaesthesia documents',                        'Assessed by a qualified anaesthesiologist',                                                                                                    'pre_anaesthesia',              true],
            ['Availability of daily nursing notes',                              null,                                                                                                                                           'nursing_notes_available',      false],
            ['Completeness of daily nursing notes',                              'Should have date, status/progress of patient as recorded by nurse',                                                                           'nursing_notes_complete',       false],
            ['Availability of daily doctor notes',                               null,                                                                                                                                           'doctor_notes_available',       false],
            ['Completeness of daily doctor notes',                               'Should have date, status/progress of patient and further course of medication/treatment as recorded by doctor',                              'doctor_notes_complete',        false],
            ['Availability of daily progress chart',                             null,                                                                                                                                           'progress_chart_available',     false],
            ['Completeness of daily progress chart',                             'Should have record of vitals with date and time',                                                                                             'progress_chart_complete',      false],
            ['Availability of daily treatment chart',                            null,                                                                                                                                           'treatment_chart_available',    false],
            ['Completeness of daily treatment chart',                            'Should have record of medication with date and time',                                                                                          'treatment_chart_complete',     false],
            ['Availability of recorded monitoring details',                      'Heart rate, cardiac rhythm, respiratory rate, BP, O₂ saturation, airway security, anaesthesia level',                                        'monitoring_available',         false],
            ['Completeness of Discharge Summary',                                null,                                                                                                                                           'discharge_summary',            false],
        ];
        @endphp

        @foreach($rows as $row)
        @php [$label, $sub, $name, $surgicalOnly] = $row; @endphp

        @if($name === '_lama_reason')
        <div class="obs-card" style="margin-top:.625rem;">
            <span class="obs-label">{{ $label }}</span>
            @if($sub)<p class="obs-sub">{{ $sub }}</p>@endif
            <textarea name="lama_reason" rows="2" placeholder="Reason patient left against medical advice (if applicable)…" class="field-input">{{ old('lama_reason') }}</textarea>
        </div>
        @else
        <div class="obs-card {{ $surgicalOnly ? 'surgical-row' : '' }}" style="margin-top:.625rem; {{ $surgicalOnly ? 'border-color:#ede9fe;' : '' }}">
            <span class="obs-label">{{ $label }}
                @if($surgicalOnly)<span class="surgical-notice"><i class="fas fa-scalpel mr-1"></i> Surgical only</span>@endif
            </span>
            @if($sub)<p class="obs-sub">{{ $sub }}</p>@endif
            <div class="obs-row">
                <div class="radio-group">
                    <input type="radio" name="{{ $name }}" id="{{ $name }}_yes" value="Yes" {{ old($name) === 'Yes' ? 'checked' : '' }}>
                    <label for="{{ $name }}_yes">Yes</label>
                    <input type="radio" name="{{ $name }}" id="{{ $name }}_no"  value="No"  {{ old($name) === 'No'  ? 'checked' : '' }}>
                    <label for="{{ $name }}_no">No</label>
                    <input type="radio" name="{{ $name }}" id="{{ $name }}_na"  value="NA"  {{ old($name) === 'NA'  ? 'checked' : '' }}>
                    <label for="{{ $name }}_na">N/A</label>
                </div>
                <input type="text" name="{{ $name }}_remarks" value="{{ old($name.'_remarks') }}" placeholder="Remarks (optional)" class="field-input" style="flex:1;min-width:160px;">
            </div>
        </div>
        @endif

        @endforeach

        {{-- Overall justification --}}
        <div class="section-badge"><span>Overall Justification</span></div>
        <div class="obs-card">
            <span class="obs-label">Do all documents align and justify the need and treatment given?</span>
            <p class="obs-sub">Explain with remarks. This is the summary conclusion of the Medical Audit.</p>
            <textarea name="overall_remarks" rows="5" placeholder="Enter your overall remarks and justification…" class="field-input">{{ old('overall_remarks') }}</textarea>
        </div>

        {{-- ── Site photograph — MANDATORY ── --}}
        <div class="section-badge"><span>Site Photograph</span></div>

        <div id="camera-section" class="obs-card" style="border:2px solid #fcd34d; background:#fffbeb;">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div>
                    <span class="obs-label">Site photograph <span class="text-rose-500">*</span></span>
                    <p class="text-xs text-slate-500 mt-0.5">GPS coordinates, address &amp; timestamp will be stamped onto the photo.</p>
                </div>
                <span class="mandatory-badge"><i class="fas fa-exclamation-circle"></i> Mandatory</span>
            </div>
            <div id="gps-pill" class="gps-pill gps-acquiring self-start">
                <i class="fas fa-satellite-dish fa-spin"></i>
                <span id="gps-text">Acquiring GPS…</span>
            </div>
            <div id="viewfinder-wrap" style="display:none; flex-direction:column; gap:.75rem;">
                <div class="relative">
                    <video id="camera-preview" autoplay playsinline muted></video>
                    <div id="live-overlay" style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,.5);color:#fff;font-size:.68rem;padding:.4rem .65rem;border-radius:0 0 .875rem .875rem;backdrop-filter:blur(2px);">
                        <span id="live-coords">Waiting for GPS…</span>
                    </div>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <button type="button" id="btn-snap" class="cam-btn cam-btn-dark"><i class="fas fa-circle-dot"></i> Capture Photo</button>
                    <button type="button" id="btn-close-cam" class="cam-btn cam-btn-danger"><i class="fas fa-times"></i> Cancel</button>
                </div>
            </div>
            <div id="photo-preview-wrap">
                <img id="photo-preview-img" src="" alt="Captured photo">
                <div class="wm-strip">
                    <span id="wm-line1" style="color:#34d399;font-weight:700;"></span>
                    <span id="wm-line2"></span>
                    <span id="wm-line3"></span>
                </div>
            </div>
            <img id="map-thumb" src="" alt="Location map">

            <div class="flex gap-2 flex-wrap">
                <button type="button" id="btn-open-cam" class="cam-btn cam-btn-dark"><i class="fas fa-camera"></i> Open Camera</button>
                <button type="button" id="btn-retake" class="cam-btn cam-btn-danger" style="display:none;"><i class="fas fa-redo"></i> Retake</button>
            </div>

            <p id="photo-error" class="text-rose-600 text-xs font-medium" style="display:none;"></p>

            <div id="ai-checking-msg" class="gps-pill gps-acquiring" style="display:none;">
                <i class="fas fa-spinner fa-spin"></i>
                <span>AI verifying persons in photo…</span>
            </div>
            <span id="ai-face-badge" class="gps-pill gps-ready" style="display:none; font-size:.75rem;"></span>

            <input type="hidden" name="photo_latitude"  id="field-lat">
            <input type="hidden" name="photo_longitude" id="field-lng">
            <input type="hidden" name="photo_address"   id="field-address">
            <input type="file"   name="visit_photo"     id="field-photo-file" class="hidden" accept="image/*">
        </div>

        <canvas id="capture-canvas" style="display:none;"></canvas>

        {{-- ── Attachments — MANDATORY ── --}}
        <div class="section-badge"><span>Supporting Attachments</span></div>
        <div class="obs-card" style="border:2px solid #bfdbfe; background:#eff6ff;">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div>
                    <span class="obs-label">Attachments <span class="text-rose-500">*</span></span>
                    <p class="text-xs text-slate-500 mt-0.5">Add at least one attachment. Each entry needs a name and a file (image or PDF, max 10 MB).</p>
                </div>
                <span class="mandatory-badge"><i class="fas fa-exclamation-circle"></i> Mandatory</span>
            </div>
            <div id="attachment-list"></div>
            <div><button type="button" id="btn-add-attach"><i class="fas fa-plus-circle"></i> Add attachment</button></div>
            <p id="attach-error" class="text-rose-600 text-xs font-medium" style="display:none;">
                <i class="fas fa-exclamation-triangle mr-1"></i> At least one attachment with a name and file is required.
            </p>
        </div>

        {{-- Submit row --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-6 mt-6 border-t border-slate-200">
            <button type="submit" id="btn-submit"
                    disabled
                    class="flex-1 sm:flex-none px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 disabled:bg-slate-300 disabled:cursor-not-allowed disabled:shadow-none text-white font-medium text-sm flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition-all">
                <i class="fas fa-check-circle"></i> Submit Medical Audit report
            </button>
            <button type="reset"
                    class="flex-1 sm:flex-none px-5 py-3.5 rounded-xl border-2 border-slate-300 text-slate-600 hover:bg-slate-50 active:bg-slate-100 font-medium text-sm flex items-center justify-center gap-2 transition">
                <i class="fas fa-undo-alt"></i> Reset
            </button>
        </div>
    </form>
</div>

@endif {{-- end status branch --}}

<div class="text-xs text-slate-400 text-center mt-6 border-t border-slate-200 pt-5">
    <i class="fas fa-shield-alt text-emerald-500 mr-1"></i>
    All Medical Audit entries are logged with DMO credentials · PMJAY Assam
</div>

{{-- Toast container (always present so flash toasts work on read-only view too) --}}
<div id="toast-container"></div>

@if(!$isCompleted)
<script>
/* ═══════════════════════════════════════════
   Toast notification system
   ═══════════════════════════════════════════ */
function toast(type, title, message, duration) {
    duration = duration ?? (type === 'error' ? 7000 : 4500);
    const icons = { error: 'fa-times-circle', success: 'fa-check-circle', warning: 'fa-exclamation-circle', info: 'fa-info-circle' };

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
        <div class="toast-icon"><i class="fas ${icons[type] ?? 'fa-info-circle'}"></i></div>
        <div class="toast-body">
            <div class="toast-title">${title}</div>
            <div class="toast-msg">${msgHtml}</div>
        </div>
        <button class="toast-close" title="Dismiss"><i class="fas fa-times"></i></button>
        <div class="toast-progress" style="animation-duration:${duration}ms;"></div>
    `;

    document.getElementById('toast-container').appendChild(el);
    requestAnimationFrame(() => requestAnimationFrame(() => el.classList.add('toast-in')));

    const dismiss = () => {
        el.classList.remove('toast-in');
        el.classList.add('toast-out');
        el.addEventListener('transitionend', () => el.remove(), { once: true });
    };
    const timer = setTimeout(dismiss, duration);
    el.querySelector('.toast-close').addEventListener('click', () => { clearTimeout(timer); dismiss(); });
}

/* ── Global: called from onclick on treatment type labels ── */
var _selectedTT = null;

function selectTT(val) {
    _selectedTT = val;
    document.querySelectorAll('input[name="treatment_type"]').forEach(function(r) {
        r.checked = (r.value === val);
    });
    document.getElementById('lbl-surgical').className = (val === 'Surgical') ? 'tt-active-surgical' : '';
    document.getElementById('lbl-medical').className  = (val === 'Medical')  ? 'tt-active-medical'  : '';
    if (typeof applySurgicalVisibility === 'function') applySurgicalVisibility();
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
    const form           = document.getElementById('fieldVisitForm');

    /* ═══ Surgical/Medical toggle ═══ */
    function applySurgicalVisibility() {
        const isSurgical = _selectedTT === 'Surgical';
        document.querySelectorAll('.surgical-row').forEach(function(el) {
            el.style.display = isSurgical ? 'flex' : 'none';
        });
        document.querySelectorAll('.surgical-notice').forEach(function(el) {
            el.style.display = isSurgical ? 'inline-flex' : 'none';
        });
    }
    (function() {
        var checked = document.querySelector('input[name="treatment_type"]:checked');
        if (checked) selectTT(checked.value);
    })();

    /* ═══ GPS ═══ */
    function setGps(state, text) { gpsPill.className = 'gps-pill gps-' + state + ' self-start'; gpsText.textContent = text; }
    function updateOverlay() {
        if (gpsLat === null) return;
        const short = gpsAddr ? gpsAddr.split(',').slice(-3).join(',').trim() : '';
        liveCoords.textContent = gpsLat.toFixed(6) + ', ' + gpsLng.toFixed(6) + (short ? ' · ' + short : '');
        if (gpsReady) setGps('ready', gpsLat.toFixed(5) + ', ' + gpsLng.toFixed(5));
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
            () => setGps('error', 'GPS unavailable — check browser permissions'),
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    } else { setGps('error', 'Geolocation not supported'); }

    /* ═══ Camera ═══ */
    async function openCamera() {
        try {
            mediaStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 960 } }, audio: false });
            video.srcObject = mediaStream;
            viewfinderWrap.style.display = 'flex';
            btnOpenCam.style.display = 'none';
            previewWrap.style.display = 'none';
            mapThumb.style.display    = 'none';
            btnRetake.style.display   = 'none';
        } catch (err) { alert('Camera access denied: ' + err.message); }
    }
    function closeCamera() {
        if (mediaStream) { mediaStream.getTracks().forEach(t => t.stop()); mediaStream = null; }
        viewfinderWrap.style.display = 'none';
        btnOpenCam.style.display     = 'inline-flex';
    }
    btnOpenCam.addEventListener('click',  openCamera);
    btnCloseCam.addEventListener('click', function () {
        closeCamera();
        if (photoTaken) btnRetake.style.display = 'inline-flex';
    });

    /* ═══ Capture + watermark ═══ */
    btnSnap.addEventListener('click', function () {
        if (!gpsReady) { alert('GPS not ready yet. Please wait a moment.'); return; }

        const vw = video.videoWidth || 1280, vh = video.videoHeight || 960;
        canvas.width = vw; canvas.height = vh;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, vw, vh);

        const now       = new Date();
        const dtStr     = now.toLocaleString('en-IN', { dateStyle: 'medium', timeStyle: 'short' });
        const coordStr  = gpsLat.toFixed(6) + '° N,  ' + gpsLng.toFixed(6) + '° E';
        const shortAddr = gpsAddr ? gpsAddr.split(',').slice(-4).join(',').trim() : 'Address resolving…';
        const auditTag  = 'PMJAY Assam · DMO Field Audit';

        const fontSize = Math.max(14, Math.round(vh * 0.022));
        const lineH    = Math.round(fontSize * 1.5), pad = Math.round(vw * 0.018);
        const lines    = [auditTag, coordStr, shortAddr, dtStr];
        const stripH   = lines.length * lineH + pad * 2;

        ctx.fillStyle = 'rgba(0,0,0,0.62)'; ctx.fillRect(0, vh - stripH, vw, stripH);
        ctx.font = '600 ' + fontSize + 'px "Segoe UI", Arial, sans-serif'; ctx.textBaseline = 'top';
        lines.forEach((line, i) => {
            const y = vh - stripH + pad + i * lineH;
            ctx.fillStyle = 'rgba(0,0,0,0.4)'; ctx.fillText(line, pad + 1, y + 1);
            ctx.fillStyle = i === 0 ? '#34d399' : '#ffffff'; ctx.fillText(line, pad, y);
        });
        const bw = Math.max(3, Math.round(vw * 0.004));
        ctx.strokeStyle = '#34d399'; ctx.lineWidth = bw; ctx.strokeRect(bw/2, bw/2, vw - bw, vh - bw);

        previewImg.src            = canvas.toDataURL('image/jpeg', 0.92);
        previewWrap.style.display = 'block';
        wmLine1.textContent       = auditTag;
        wmLine2.textContent       = coordStr;
        wmLine3.textContent       = dtStr;

        const mapUrl = 'https://maps.googleapis.com/maps/api/staticmap?center=' + gpsLat + ',' + gpsLng + '&zoom=15&size=480x160&maptype=roadmap&markers=color:red%7C' + gpsLat + ',' + gpsLng + '&key=' + GMAPS_KEY;
        mapThumb.src              = mapUrl;
        mapThumb.style.display    = 'block';
        fieldLat.value            = gpsLat;
        fieldLng.value            = gpsLng;
        fieldAddress.value        = gpsAddr;

        canvas.toBlob(blob => {
            const f  = new File([blob], 'field_visit_' + Date.now() + '.jpg', { type: 'image/jpeg' });
            const dt = new DataTransfer();
            dt.items.add(f);
            fieldPhotoFile.files = dt.files;
        }, 'image/jpeg', 0.92);

        closeCamera();
        photoTaken                = false;
        btnRetake.style.display   = 'none';
        photoError.style.display  = 'none';
        faceBadge.style.display   = 'none';
        checkingMsg.style.display = 'flex';

        fetch('{{ route("dmo.audits.validate.photo") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ image: canvas.toDataURL('image/jpeg', 0.92) })
        })
        .then(r => r.json())
        .then(data => {
            checkingMsg.style.display = 'none';
            btnRetake.style.display   = 'inline-flex';

            if (data.valid) {
                photoTaken               = true;
                photoError.style.display = 'none';
                faceBadge.innerHTML      = '<i class="fas fa-check-circle mr-1"></i>' + data.message;
                faceBadge.style.display  = 'inline-flex';
                document.getElementById('btn-submit').disabled = false;

                toast('success', 'AI Verification Passed', data.message);

            } else {
                photoTaken = false;
                document.getElementById('btn-submit').disabled = true;

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
            // Fail-open: Vision API down — warn but allow submission
            checkingMsg.style.display = 'none';
            btnRetake.style.display   = 'inline-flex';
            photoTaken                = true;
            document.getElementById('btn-submit').disabled = false;

            faceBadge.className     = 'gps-pill gps-acquiring';
            faceBadge.innerHTML     = '<i class="fas fa-exclamation-circle mr-1"></i> AI check skipped — photo accepted, flagged for manual review';
            faceBadge.style.display = 'inline-flex';

            photoError.style.display = 'none';

            toast('warning', 'AI Check Skipped', 'Vision service unavailable. Photo accepted — flagged for manual review by supervisor.');
        });
    });

    btnRetake.addEventListener('click', function () {
        photoTaken                = false;
        document.getElementById('btn-submit').disabled = true;
        previewWrap.style.display = 'none';
        mapThumb.style.display    = 'none';
        btnRetake.style.display   = 'none';
        faceBadge.style.display   = 'none';
        checkingMsg.style.display = 'none';
        photoError.style.display  = 'none';
        openCamera();
    });

    /* ═══ Attachments ═══ */
    const attachList = document.getElementById('attachment-list');
    const btnAdd     = document.getElementById('btn-add-attach');
    const attachErr  = document.getElementById('attach-error');
    let   attachCount = 0;

    function attachmentValid() {
        const rows = attachList.querySelectorAll('.attach-row');
        if (!rows.length) return false;
        for (const row of rows) {
            if (!row.querySelector('input[type="text"]').value.trim() || !row.querySelector('input[type="file"]').files.length) return false;
        }
        return true;
    }
    function addAttachmentRow() {
        const idx = attachCount++;
        const row = document.createElement('div');
        row.className = 'attach-row';
        row.innerHTML = `
            <div class="attach-inner">
                <input type="text" name="attachments[${idx}][name]" placeholder="Attachment name (e.g. IPD sheet, Discharge summary)" class="field-input" style="padding:.55rem .8rem;font-size:.875rem;" autocomplete="off">
                <div class="drop-zone" id="dz-${idx}">
                    <input type="file" name="attachments[${idx}][file]" id="af-${idx}" accept="image/*,application/pdf">
                    <div id="dz-inner-${idx}" class="pointer-events-none">
                        <i class="fas fa-cloud-upload-alt text-slate-400 text-xl mb-1"></i>
                        <p class="text-xs text-slate-500 font-medium">Tap to upload or drag &amp; drop</p>
                        <p class="text-xs text-slate-400 mt-0.5">Image or PDF · max 10 MB</p>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-remove-attach" title="Remove"><i class="fas fa-times text-xs"></i></button>
        `;
        const fi = row.querySelector('input[type="file"]');
        const di = row.querySelector(`#dz-inner-${idx}`);
        const dz = row.querySelector(`#dz-${idx}`);
        fi.addEventListener('change', function () {
            const file = this.files[0]; if (!file) return;
            const isPdf = file.type === 'application/pdf';
            di.innerHTML = `<span class="file-chip ${isPdf ? 'is-pdf' : 'is-img'}"><i class="fas ${isPdf ? 'fa-file-pdf' : 'fa-file-image'}"></i><span title="${file.name}">${file.name}</span></span><p class="text-xs text-slate-400 mt-1">${(file.size/1024).toFixed(0)} KB · tap to change</p>`;
            attachErr.style.display = 'none';
        });
        dz.addEventListener('dragover',  e => { e.preventDefault(); dz.classList.add('drag-over'); });
        dz.addEventListener('dragleave', ()  => dz.classList.remove('drag-over'));
        dz.addEventListener('drop',      e  => { e.preventDefault(); dz.classList.remove('drag-over'); fi.files = e.dataTransfer.files; fi.dispatchEvent(new Event('change')); });
        row.querySelector('.btn-remove-attach').addEventListener('click', () => row.remove());
        attachList.appendChild(row);
        attachErr.style.display = 'none';
    }
    btnAdd.addEventListener('click', addAttachmentRow);
    addAttachmentRow();

    /* ═══ Form validation ═══ */
    form.addEventListener('submit', function (e) {
        let blocked = false;

        if (!photoTaken) {
            photoError.innerHTML     = '<i class="fas fa-exclamation-triangle mr-1"></i> A site photograph with at least 2 people is required before submitting.';
            photoError.style.display = 'block';
            document.getElementById('camera-section').scrollIntoView({ behavior: 'smooth', block: 'center' });
            toast('error', 'Photo Required', 'A site photograph with AI verification is required before submitting.');
            blocked = true;
        }

        if (!attachmentValid()) {
            attachErr.style.display = 'block';
            if (!blocked) attachList.scrollIntoView({ behavior: 'smooth', block: 'center' });
            toast('error', 'Attachment Required', 'At least one attachment with a name and file is required.');
            blocked = true;
        }

        if (blocked) e.preventDefault();
    });

})();
</script>
@endif

@endsection

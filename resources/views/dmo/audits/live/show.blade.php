@extends('dmo.layout.layout')

@section('main_title')
<div class="flex items-center gap-2 text-sm text-slate-400 mb-5">
    <a href="{{ route('dmo.audits.live-audit.all') }}" class="hover:text-emerald-600 transition-colors">
        <i class="fas fa-arrow-left mr-1 text-xs"></i> My Live Audits
    </a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-600 font-medium">Audit #{{ $liveAudit->id }}</span>
</div>
<div class="mb-7">
    <h2 class="text-xl md:text-3xl font-semibold text-slate-800 tracking-tight flex items-center gap-2">
        <i class="fas fa-hospital-user text-emerald-600"></i>
        Live Audit — Submitted
    </h2>
    <p class="text-sm text-slate-500 mt-1">
        {{ $liveAudit->patient_name ?? '—' }}
        @if($liveAudit->hospital_name)
            &nbsp;·&nbsp; {{ $liveAudit->hospital_name }}
        @endif
        &nbsp;·&nbsp; {{ $liveAudit->created_at?->format('d M Y, h:i A') }}
    </p>
</div>
@endsection

@section('pageCss')
<style>
    /* ── Section dividers ── */
    .section-badge { display:flex; align-items:center; gap:.5rem; font-size:.7rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#94a3b8; margin:1.5rem 0 .75rem; }
    .section-badge::before, .section-badge::after { content:''; flex:1; height:1px; background:#e2e8f0; }

    /* ── Read-only cells ── */
    .ro-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:.75rem; }
    .ro-cell { background:#f8fafc; border:1px solid #e2e8f0; border-radius:.75rem; padding:.625rem .875rem; }
    .ro-cell.span-2 { grid-column: span 2; }
    .ro-label { font-size:.65rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#94a3b8; display:block; margin-bottom:.25rem; }
    .ro-value { font-size:.875rem; font-weight:600; color:#1e293b; display:block; }
    .ro-value.empty { color:#cbd5e1; font-style:italic; font-weight:400; }

    /* ── Yes/No/NA badges ── */
    .yn { display:inline-flex; align-items:center; gap:.35rem; font-size:.78rem; font-weight:700; padding:.28rem .8rem; border-radius:9999px; white-space:nowrap; }
    .yn-yes  { background:#d1fae5; color:#065f46; }
    .yn-no   { background:#fee2e2; color:#991b1b; }
    .yn-na   { background:#f1f5f9; color:#94a3b8; }
    .yn-flag { background:#fee2e2; color:#991b1b; }
    .yn-ok   { background:#d1fae5; color:#065f46; }

    /* ── AI result strip ── */
    .ai-strip { display:flex; align-items:center; gap:.625rem; flex-wrap:wrap; padding:.875rem 1rem; border-radius:.875rem; border:1.5px solid; font-size:.8rem; font-weight:500; margin-bottom:1.5rem; }
    .ai-pass  { background:#f0fdf4; border-color:#86efac; color:#166534; }
    .ai-fail  { background:#fff1f2; border-color:#fda4af; color:#9f1239; }
    .ai-skip  { background:#f8fafc; border-color:#cbd5e1; color:#64748b; }

    /* ── Photo ── */
    .ro-photo { border-radius:.875rem; overflow:hidden; border:2px solid #a78bfa; max-width:480px; }
    .ro-photo img { width:100%; display:block; }
    .ro-map { width:100%; max-width:480px; height:160px; border-radius:.875rem; object-fit:cover; border:2px solid #e2e8f0; display:block; }
    .gps-chip { display:inline-flex; align-items:center; gap:.4rem; background:#dcfce7; color:#166534; font-size:.75rem; font-weight:600; padding:.35rem .85rem; border-radius:9999px; }

    /* ── Attachments ── */
    .att-item { display:flex; align-items:center; gap:.75rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:.875rem; padding:.75rem 1rem; }
    .att-item + .att-item { margin-top:.5rem; }
    .att-icon { width:2.25rem; height:2.25rem; border-radius:.625rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:.875rem; }
    .att-icon.pdf { background:#fef3c7; color:#92400e; }
    .att-icon.img { background:#d1fae5; color:#065f46; }
    .att-link { display:inline-flex; align-items:center; gap:.3rem; font-size:.75rem; font-weight:600; padding:.3rem .75rem; border-radius:9999px; text-decoration:none; transition:background .15s; flex-shrink:0; }
    .att-link.doc { color:#0369a1; background:#e0f2fe; }
    .att-link.doc:hover { background:#bae6fd; }
    .att-link.pdf { color:#92400e; background:#fef3c7; }
    .att-link.pdf:hover { background:#fde68a; }

    /* ── Submitted footer strip ── */
    .submit-strip { display:flex; align-items:center; gap:.5rem; font-size:.75rem; color:#64748b; background:#f8fafc; border:1px solid #e2e8f0; border-radius:.75rem; padding:.5rem .875rem; flex-wrap:wrap; margin-top:1.5rem; }

    /* ── Treatment badge ── */
    .tt-badge { display:inline-flex; align-items:center; gap:.35rem; font-size:.78rem; font-weight:700; padding:.28rem .8rem; border-radius:9999px; }
</style>
@endsection

@section('main_content')
@php
    $la       = $liveAudit;
    $aiSkip   = str_contains($la->ai_validation_message ?? '', 'skipped');
    $aiPassed = $la->aiPassed();
    $aiClass  = $aiSkip ? 'ai-skip' : ($aiPassed ? 'ai-pass' : 'ai-fail');
    $aiIcon   = $aiSkip ? 'fa-question-circle' : ($aiPassed ? 'fa-robot' : 'fa-exclamation-triangle');
@endphp

{{-- ── Completion banner ── --}}
<div class="flex items-center gap-3 bg-gradient-to-r from-emerald-50 to-white border border-emerald-200 rounded-2xl px-5 py-4 mb-6">
    <div class="h-10 w-10 rounded-full bg-emerald-600 flex items-center justify-center shrink-0">
        <i class="fas fa-check text-white text-sm"></i>
    </div>
    <div>
        <p class="font-semibold text-emerald-800 text-sm">Live Audit submitted successfully</p>
        <p class="text-emerald-600 text-xs mt-0.5">
            {{ $la->created_at?->format('d M Y, h:i A') ?? '—' }}
            @if($la->submittedBy) &nbsp;·&nbsp; {{ $la->submittedBy->name }} @endif
        </p>
    </div>
    <a href="{{ route('dmo.audits.live-audit.all') }}"
       class="ml-auto text-xs font-semibold text-emerald-600 hover:text-emerald-800 transition shrink-0 hidden sm:inline-flex items-center gap-1">
        <i class="fas fa-list text-xs"></i> All audits
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-md p-4 md:p-8">

    {{-- AI verification strip --}}
    <div class="ai-strip {{ $aiClass }}">
        <i class="fas {{ $aiIcon }}"></i>
        <div>
            <strong>AI Verification:</strong> {{ $la->ai_validation_message ?? '—' }}
        </div>
        @if(!$aiSkip)
        <div class="ml-auto flex items-center gap-3 text-xs opacity-75 flex-wrap">
            <span>Bed: <strong>{{ $la->ai_bed_detected ? '✓' : '✗' }}</strong></span>
            <span>Patient: <strong>{{ $la->ai_patient_detected ? '✓' : '✗' }}</strong></span>
            <span>Card: <strong>{{ $la->ai_pmjay_card_detected ? '✓' : '—' }}</strong></span>
            <span>Faces: <strong>{{ $la->ai_face_count ?? 0 }}</strong></span>
        </div>
        @endif
    </div>

    {{-- ══ Section 1: Patient & Case Details ══ --}}
    <div class="section-badge"><span>Patient &amp; Case Details</span></div>
    <div class="ro-grid">
        <div class="ro-cell span-2">
            <span class="ro-label">Patient Name</span>
            <span class="ro-value {{ $la->patient_name ? '' : 'empty' }}">{{ $la->patient_name ?? '—' }}</span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Contact Number</span>
            <span class="ro-value {{ $la->contact_number ? '' : 'empty' }}">{{ $la->contact_number ?? '—' }}</span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">District</span>
            <span class="ro-value {{ $la->district?->name ? '' : 'empty' }}">
                {{ $la->district?->name ?? '—' }}
            </span>
        </div>
        <div class="ro-cell span-2">
            <span class="ro-label">Hospital</span>
            <span class="ro-value {{ $la->hospital_name ? '' : 'empty' }}">
                {{ $la->hospital?->name }} - {{ $la->hospital?->hospital_code }}
            </span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">PMJAY ID</span>
            <span class="ro-value {{ $la->pmjay_id ? '' : 'empty' }}">{{ $la->pmjay_id ?? '—' }}</span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Registration No.</span>
            <span class="ro-value {{ $la->registration_number ? '' : 'empty' }}">{{ $la->registration_number ?? '—' }}</span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Package Booked</span>
            <span class="ro-value {{ $la->package_booked ? '' : 'empty' }}">{{ $la->package_booked ?? '—' }}</span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Treating Doctor</span>
            <span class="ro-value {{ $la->treating_doctor ? '' : 'empty' }}">{{ $la->treating_doctor ?? '—' }}</span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Specialization</span>
            <span class="ro-value {{ $la->doctor_specialization ? '' : 'empty' }}">{{ $la->doctor_specialization ?? '—' }}</span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Admission</span>
            <span class="ro-value {{ $la->admission_datetime ? '' : 'empty' }}">
                {{ $la->admission_datetime?->format('d M Y, h:i A') ?? '—' }}
            </span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Discharge</span>
            <span class="ro-value {{ $la->discharge_datetime ? '' : 'empty' }}">
                {{ $la->discharge_datetime?->format('d M Y, h:i A') ?? '—' }}
            </span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Treatment Type</span>
            @if($la->treatment_type === 'Surgical')
                <span class="tt-badge bg-violet-100 text-violet-800">
                    <i class="fas fa-scalpel text-xs"></i> Surgical
                </span>
            @elseif($la->treatment_type === 'Medical')
                <span class="tt-badge bg-blue-100 text-blue-800">
                    <i class="fas fa-pills text-xs"></i> Medical
                </span>
            @else
                <span class="ro-value empty">—</span>
            @endif
        </div>
    </div>

    {{-- ══ Section 2: On-Bed Photograph ══ --}}
    <div class="section-badge"><span>On-Bed Patient Photograph · AI Verified</span></div>
    @if($la->bed_photo_path)
    <div class="flex flex-col gap-3 max-w-lg">
        <div class="ro-photo">
            <img src="{{ Storage::disk('public')->url($la->bed_photo_path) }}" alt="On-bed patient photo">
        </div>
        @if($la->bed_photo_latitude)
        <div class="flex flex-wrap gap-2 items-center">
            <span class="gps-chip">
                <i class="fas fa-map-marker-alt"></i>
                {{ number_format($la->bed_photo_latitude, 6) }}°N,
                {{ number_format($la->bed_photo_longitude, 6) }}°E
            </span>
            @if($la->bed_photo_address)
            <span class="text-xs text-slate-500">{{ $la->bed_photo_address }}</span>
            @endif
        </div>
        <img src="https://maps.googleapis.com/maps/api/staticmap?center={{ $la->bed_photo_latitude }},{{ $la->bed_photo_longitude }}&zoom=15&size=480x160&maptype=roadmap&markers=color:purple%7C{{ $la->bed_photo_latitude }},{{ $la->bed_photo_longitude }}&key={{ config('services.google_maps.key') }}"
             alt="Location map" class="ro-map">
        @endif
        @if($la->bed_photo_taken_at)
        <p class="text-xs text-slate-400">
            <i class="fas fa-clock mr-1"></i>
            Photo taken at {{ $la->bed_photo_taken_at->format('d M Y, h:i A') }}
        </p>
        @endif
    </div>
    @else
    <div class="ro-cell"><span class="ro-value empty">No photograph recorded</span></div>
    @endif

    {{-- ══ Section 3: Patient ID Proof ══ --}}
    <div class="section-badge"><span>Patient ID Proof</span></div>
    <div class="ro-cell" style="max-width:480px;">
        <span class="ro-label">Patient ID Collected</span>
        <div class="flex items-center gap-2 flex-wrap mt-1">
            @if($la->patient_id_collected === 'Yes')
                <span class="yn yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
            @elseif($la->patient_id_collected === 'No')
                <span class="yn yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
            @elseif($la->patient_id_collected === 'NA')
                <span class="yn yn-na"><i class="fas fa-minus text-xs"></i> N/A</span>
            @else
                <span class="ro-value empty">—</span>
            @endif
            @if($la->patient_id_remarks)
            <span class="text-xs text-slate-500">{{ $la->patient_id_remarks }}</span>
            @endif
        </div>
    </div>

    {{-- ══ Section 4: Clinical Interview ══ --}}
    <div class="section-badge"><span>Clinical Interview</span></div>
    <div class="ro-grid">
        <div class="ro-cell span-2">
            <span class="ro-label">Presenting Complaints</span>
            <span class="ro-value {{ $la->presenting_complaints ? '' : 'empty' }}">
                {{ $la->presenting_complaints ?? '—' }}
            </span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Symptoms Duration</span>
            <span class="ro-value {{ $la->symptoms_duration ? '' : 'empty' }}">{{ $la->symptoms_duration ?? '—' }}</span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Referred from Another Source</span>
            <div class="mt-1">
                @if($la->referred_from_other === 'Yes')
                    <span class="yn yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
                    @if($la->referred_from_name)
                    <p class="text-xs text-slate-500 mt-1">{{ $la->referred_from_name }}</p>
                    @endif
                @elseif($la->referred_from_other === 'No')
                    <span class="yn yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
                @else
                    <span class="yn yn-na"><i class="fas fa-minus text-xs"></i> N/A</span>
                @endif
            </div>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Patient Admitted When</span>
            <span class="ro-value {{ $la->patient_admitted_when ? '' : 'empty' }}">
                {{ $la->patient_admitted_when?->format('d M Y, h:i A') ?? '—' }}
            </span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Still Admitted</span>
            <div class="mt-1">
                @if($la->patient_still_admitted === 'Yes')
                    <span class="yn yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
                @elseif($la->patient_still_admitted === 'No')
                    <span class="yn yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
                @else
                    <span class="yn yn-na"><i class="fas fa-minus text-xs"></i> N/A</span>
                @endif
                @if($la->patient_still_admitted_remarks)
                <p class="text-xs text-slate-500 mt-1">{{ $la->patient_still_admitted_remarks }}</p>
                @endif
            </div>
        </div>
        <div class="ro-cell span-2">
            <span class="ro-label">Diagnostic Tests</span>
            <span class="ro-value {{ $la->diagnostic_tests_done ? '' : 'empty' }}">{{ $la->diagnostic_tests_done ?? '—' }}</span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Surgery Conducted</span>
            <div class="mt-1">
                @if($la->surgery_conducted === 'Yes')
                    <span class="yn yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
                @elseif($la->surgery_conducted === 'No')
                    <span class="yn yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
                @else
                    <span class="yn yn-na"><i class="fas fa-minus text-xs"></i> N/A</span>
                @endif
            </div>
        </div>
        @if($la->surgery_conducted === 'Yes')
        <div class="ro-cell">
            <span class="ro-label">Scar Present</span>
            <div class="mt-1">
                @if($la->surgery_scar_present === 'Yes')
                    <span class="yn yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
                @elseif($la->surgery_scar_present === 'No')
                    <span class="yn yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
                @else
                    <span class="yn yn-na"><i class="fas fa-minus text-xs"></i> N/A</span>
                @endif
                @if($la->surgery_scar_remarks)
                <p class="text-xs text-slate-500 mt-1">{{ $la->surgery_scar_remarks }}</p>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- ══ Section 5: Money Charged ══ --}}
    <div class="section-badge"><span>Money Charged</span></div>
    <div class="ro-grid">
        <div class="ro-cell">
            <span class="ro-label">Money Charged</span>
            <div class="mt-1">
                @if($la->money_charged === 'Yes')
                    <span class="yn yn-flag"><i class="fas fa-exclamation-circle text-xs"></i> Yes — Flagged</span>
                @elseif($la->money_charged === 'No')
                    <span class="yn yn-ok"><i class="fas fa-check-circle text-xs"></i> No</span>
                @else
                    <span class="yn yn-na"><i class="fas fa-minus text-xs"></i> N/A</span>
                @endif
            </div>
        </div>
        @if($la->money_charged === 'Yes')
        <div class="ro-cell">
            <span class="ro-label">Amount</span>
            <span class="ro-value text-rose-700">
                ₹ {{ number_format($la->money_charged_amount ?? 0, 2) }}
            </span>
        </div>
        <div class="ro-cell">
            <span class="ro-label">Receipt Available</span>
            <div class="mt-1">
                @if($la->receipt_available === 'Yes')
                    <span class="yn yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
                @elseif($la->receipt_available === 'No')
                    <span class="yn yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
                @else
                    <span class="yn yn-na"><i class="fas fa-minus text-xs"></i> N/A</span>
                @endif
            </div>
        </div>
        @if($la->receipt_path)
        <div class="ro-cell">
            <span class="ro-label">Receipt</span>
            <a href="{{ Storage::disk('public')->url($la->receipt_path) }}"
               target="_blank"
               class="att-link doc mt-1">
                <i class="fas fa-eye"></i> View Receipt
            </a>
        </div>
        @endif
        @endif
    </div>

    {{-- ══ Section 6: Previous Hospitalisation ══ --}}
    <div class="section-badge"><span>Previous Hospitalisation</span></div>
    <div class="ro-cell" style="max-width:480px;">
        <span class="ro-label">Previous hospitalisation at same hospital</span>
        <div class="flex items-start gap-2 flex-wrap mt-1">
            @if($la->previous_hospitalisation === 'Yes')
                <span class="yn yn-flag"><i class="fas fa-exclamation-circle text-xs"></i> Yes — Review needed</span>
            @elseif($la->previous_hospitalisation === 'No')
                <span class="yn yn-ok"><i class="fas fa-check-circle text-xs"></i> No</span>
            @else
                <span class="yn yn-na"><i class="fas fa-minus text-xs"></i> N/A</span>
            @endif
            @if($la->previous_hospitalisation_remarks)
            <p class="text-xs text-slate-500 w-full mt-0.5">{{ $la->previous_hospitalisation_remarks }}</p>
            @endif
        </div>
    </div>

    {{-- ══ Section 7: Other Remarks ══ --}}
    @if($la->other_remarks)
    <div class="section-badge"><span>Other Remarks</span></div>
    <div class="ro-cell" style="max-width:680px;">
        <span class="ro-value whitespace-pre-line">{{ $la->other_remarks }}</span>
    </div>
    @endif

    {{-- ══ Section 8: Supporting Attachments ══ --}}
    <div class="section-badge"><span>Supporting Attachments</span></div>
    @if($la->attachments && $la->attachments->count())
    <div style="max-width:560px;">
        @foreach($la->attachments->sortBy('sort_order') as $att)
        @php $isPdf = str_ends_with(strtolower($att->file_path), '.pdf'); @endphp
        <div class="att-item">
            <div class="att-icon {{ $isPdf ? 'pdf' : 'img' }}">
                <i class="fas {{ $isPdf ? 'fa-file-pdf' : 'fa-file-image' }}"></i>
            </div>
            <div style="flex:1; min-width:0;">
                <p class="text-sm font-semibold text-slate-700 truncate">{{ $att->name }}</p>
            </div>
            <a href="{{ Storage::disk('public')->url($att->file_path) }}"
               target="_blank"
               class="att-link {{ $isPdf ? 'pdf' : 'doc' }}">
                <i class="fas {{ $isPdf ? 'fa-file-pdf' : 'fa-eye' }}"></i>
                {{ $isPdf ? 'Open PDF' : 'View' }}
            </a>
        </div>
        @endforeach
    </div>
    @else
    <div class="ro-cell" style="max-width:480px;">
        <span class="ro-value empty">No attachments uploaded</span>
    </div>
    @endif

    {{-- ── Footer strip ── --}}
    <div class="submit-strip">
        <i class="fas fa-shield-alt text-emerald-500"></i>
        <span>
            Submitted by <strong>{{ $la->submittedBy?->name ?? auth()->user()->name }}</strong>
            on {{ $la->created_at?->format('d M Y, h:i A') ?? '—' }}
        </span>
        <span class="ml-auto text-slate-400">PMJAY Assam · DMO Live Audit #{{ $la->id }}</span>
    </div>

</div>

{{-- Conduct another audit CTA --}}
<div class="text-center mt-6 pb-2">
    <a href="{{ route('dmo.audits.live-audit.create') }}"
       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-md hover:shadow-lg transition">
        <i class="fas fa-bolt"></i> Conduct another Live Audit
    </a>
</div>

@endsection

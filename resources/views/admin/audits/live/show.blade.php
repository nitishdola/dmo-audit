@extends('admin.layout.layout')

@section('main_title')
<div class="flex items-center gap-2 text-sm text-slate-400 mb-5">
    <a href="{{ route('admin.audits.live.index') }}" class="hover:text-violet-600 transition-colors">
        <i class="fas fa-arrow-left mr-1 text-xs"></i> Live Audits
    </a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-600 font-medium">Audit #{{ $audit->id }}</span>
</div>
<h1 class="text-2xl font-black text-slate-900 tracking-tight mb-1" style="font-family:'Syne',sans-serif;">
    Live Audit
</h1>
<p class="text-sm text-slate-500 mb-7">
    {{ $audit->patient_name ?? '—' }} · {{ $audit->hospital_name ?? '—' }}
</p>
@endsection

@section('pageCss')
@include('admin.audits._shared_css')
<style>
    .detail-card { background:#fff; border:1px solid #e2e8f0; border-radius:1.25rem; overflow:hidden; margin-bottom:1.25rem; }
    .detail-card-head { padding:.875rem 1.25rem; background:#f8fafc; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; gap:.625rem; flex-wrap:wrap; }
    .detail-card-head h3 { font-size:.8rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#64748b; }
    .detail-card-body { padding:1.25rem; }
    .field-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:.875rem; }
    .field-item { background:#f8fafc; border:1px solid #f1f5f9; border-radius:.75rem; padding:.625rem .875rem; }
    .field-label { font-size:.65rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#94a3b8; display:block; margin-bottom:.2rem; }
    .field-value { font-size:.875rem; font-weight:600; color:#1e293b; }
    .field-value.empty { color:#cbd5e1; font-style:italic; font-weight:400; }
    .yn-badge { display:inline-flex; align-items:center; gap:.35rem; font-size:.75rem; font-weight:700; padding:.25rem .75rem; border-radius:9999px; }
    .yn-yes { background:#d1fae5; color:#065f46; }
    .yn-no  { background:#fee2e2; color:#991b1b; }
    .yn-na  { background:#f1f5f9; color:#64748b; }
    .ai-strip { display:flex; align-items:center; gap:.625rem; flex-wrap:wrap; padding:.875rem 1rem; border-radius:.875rem; border:1.5px solid; font-size:.8rem; font-weight:500; margin-bottom:1rem; }
    .ai-strip.pass { background:#f0fdf4; border-color:#86efac; color:#166534; }
    .ai-strip.fail { background:#fff1f2; border-color:#fda4af; color:#9f1239; }
    .ai-strip.skip { background:#f8fafc; border-color:#cbd5e1; color:#64748b; }
    .ro-photo { border-radius:.875rem; overflow:hidden; border:2px solid #a78bfa; max-width:480px; }
    .ro-photo img { width:100%; display:block; }
    .ro-map { width:100%; max-width:480px; height:160px; border-radius:.875rem; object-fit:cover; border:2px solid #e2e8f0; display:block; margin-top:.75rem; }
    .gps-chip { display:inline-flex; align-items:center; gap:.4rem; background:#dcfce7; color:#166534; font-size:.75rem; font-weight:600; padding:.35rem .85rem; border-radius:9999px; margin-top:.5rem; }
    .attach-item { display:flex; align-items:center; gap:.75rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:.875rem; padding:.75rem 1rem; }
    .attach-item + .attach-item { margin-top:.5rem; }
    .attach-icon { width:2.25rem; height:2.25rem; border-radius:.625rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:.9rem; }
    .attach-icon.pdf { background:#fef3c7; color:#92400e; }
    .attach-icon.img { background:#d1fae5; color:#065f46; }
    .attach-link { display:inline-flex; align-items:center; gap:.3rem; font-size:.75rem; font-weight:600; color:#0369a1; background:#e0f2fe; padding:.3rem .75rem; border-radius:9999px; text-decoration:none; flex-shrink:0; }
    .attach-link:hover { background:#bae6fd; }
</style>
@endsection

@section('main_content')
@php
    $aiSkipped = str_contains($audit->ai_validation_message ?? '', 'skipped');
    $aiPassed  = $audit->ai_bed_detected && $audit->ai_patient_detected;
    $aiClass   = $aiSkipped ? 'skip' : ($aiPassed ? 'pass' : 'fail');
    $aiIcon    = $aiSkipped ? 'fa-question-circle' : ($aiPassed ? 'fa-check-circle' : 'fa-exclamation-triangle');
@endphp

{{-- AI verification strip --}}
<div class="ai-strip {{ $aiClass }}">
    <i class="fas {{ $aiIcon }}"></i>
    <strong>AI Verification:</strong> {{ $audit->ai_validation_message ?? '—' }}
    @if(!$aiSkipped)
    <span class="ml-auto text-xs opacity-70">
        Bed: {{ $audit->ai_bed_detected ? '✓' : '✗' }} &nbsp;·&nbsp;
        Patient: {{ $audit->ai_patient_detected ? '✓' : '✗' }} &nbsp;·&nbsp;
        Card: {{ $audit->ai_pmjay_card_detected ? '✓' : '—' }} &nbsp;·&nbsp;
        Faces: {{ $audit->ai_face_count ?? 0 }}
    </span>
    @endif
</div>

{{-- Patient & Case Details --}}
<div class="detail-card">
    <div class="detail-card-head" style="border-top:3px solid #8b5cf6;">
        <i class="fas fa-user text-violet-500 text-sm"></i>
        <h3>Patient &amp; Case Details</h3>
        <span class="ml-auto text-xs text-slate-400">
            by <strong class="text-slate-600">{{ $audit->submittedBy?->name ?? '—' }}</strong>
            · {{ $audit->created_at?->format('d M Y, h:i A') ?? '—' }}
        </span>
    </div>
    <div class="detail-card-body">
        <div class="field-grid">
            <div class="field-item"><span class="field-label">Patient Name</span><span class="field-value {{ $audit->patient_name ? '' : 'empty' }}">{{ $audit->patient_name ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Contact Number</span><span class="field-value {{ $audit->contact_number ? '' : 'empty' }}">{{ $audit->contact_number ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Hospital</span><span class="field-value {{ $audit->hospital_name ? '' : 'empty' }}">{{ $audit->hospital_name ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">PMJAY ID</span><span class="field-value {{ $audit->pmjay_id ? '' : 'empty' }}">{{ $audit->pmjay_id ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Registration No.</span><span class="field-value {{ $audit->registration_number ? '' : 'empty' }}">{{ $audit->registration_number ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Package Booked</span><span class="field-value {{ $audit->package_booked ? '' : 'empty' }}">{{ $audit->package_booked ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Treating Doctor</span><span class="field-value {{ $audit->treating_doctor ? '' : 'empty' }}">{{ $audit->treating_doctor ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Specialization</span><span class="field-value {{ $audit->doctor_specialization ? '' : 'empty' }}">{{ $audit->doctor_specialization ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Admission</span><span class="field-value">{{ $audit->admission_datetime?->format('d M Y, h:i A') ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Discharge</span><span class="field-value">{{ $audit->discharge_datetime?->format('d M Y, h:i A') ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Treatment Type</span>
                @if($audit->treatment_type)
                    <span class="yn-badge {{ $audit->treatment_type === 'Surgical' ? 'bg-violet-100 text-violet-800' : 'bg-blue-100 text-blue-800' }}">
                        <i class="fas {{ $audit->treatment_type === 'Surgical' ? 'fa-scalpel' : 'fa-pills' }} text-xs"></i>
                        {{ $audit->treatment_type }}
                    </span>
                @else <span class="field-value empty">—</span> @endif
            </div>
        </div>
    </div>
</div>

{{-- On-Bed Photograph --}}
@if($audit->bed_photo_path)
<div class="detail-card">
    <div class="detail-card-head">
        <i class="fas fa-camera text-violet-500 text-sm"></i>
        <h3>On-Bed Patient Photograph (AI Verified)</h3>
    </div>
    <div class="detail-card-body flex flex-col gap-3">
        <div class="ro-photo">
            <img src="{{ Storage::disk('public')->url($audit->bed_photo_path) }}" alt="On-bed photo">
        </div>
        @if($audit->bed_photo_latitude)
        <span class="gps-chip"><i class="fas fa-map-marker-alt"></i> {{ number_format($audit->bed_photo_latitude, 6) }}°N, {{ number_format($audit->bed_photo_longitude, 6) }}°E</span>
        @if($audit->bed_photo_address)<p class="text-xs text-slate-400">{{ $audit->bed_photo_address }}</p>@endif
        <img src="https://maps.googleapis.com/maps/api/staticmap?center={{ $audit->bed_photo_latitude }},{{ $audit->bed_photo_longitude }}&zoom=15&size=480x160&maptype=roadmap&markers=color:purple%7C{{ $audit->bed_photo_latitude }},{{ $audit->bed_photo_longitude }}&key={{ config('services.google_maps.key') }}"
             alt="Map" class="ro-map">
        @endif
        @if($audit->bed_photo_taken_at)<p class="text-xs text-slate-400"><i class="fas fa-clock mr-1"></i> {{ $audit->bed_photo_taken_at->format('d M Y, h:i A') }}</p>@endif
    </div>
</div>
@endif

{{-- Clinical Interview --}}
<div class="detail-card">
    <div class="detail-card-head">
        <i class="fas fa-stethoscope text-violet-500 text-sm"></i>
        <h3>Clinical Interview</h3>
    </div>
    <div class="detail-card-body">
        <div class="field-grid">
            <div class="field-item col-span-full"><span class="field-label">Presenting Complaints</span><span class="field-value {{ $audit->presenting_complaints ? '' : 'empty' }}">{{ $audit->presenting_complaints ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Symptoms Duration</span><span class="field-value {{ $audit->symptoms_duration ? '' : 'empty' }}">{{ $audit->symptoms_duration ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Referred From Other</span>
                @php $ref = $audit->referred_from_other ?? null; @endphp
                @if($ref === 'Yes') <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>@if($audit->referred_from_name)<span class="text-xs text-slate-500 ml-1">{{ $audit->referred_from_name }}</span>@endif
                @elseif($ref === 'No') <span class="yn-badge yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
                @else <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span> @endif
            </div>
            <div class="field-item"><span class="field-label">Admitted When</span><span class="field-value">{{ $audit->patient_admitted_when?->format('d M Y, h:i A') ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Still Admitted</span>
                @php $sa = $audit->patient_still_admitted ?? null; @endphp
                @if($sa === 'Yes') <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
                @elseif($sa === 'No') <span class="yn-badge yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
                @else <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span> @endif
                @if($audit->patient_still_admitted_remarks)<span class="text-xs text-slate-500 ml-1">{{ $audit->patient_still_admitted_remarks }}</span>@endif
            </div>
            <div class="field-item col-span-full"><span class="field-label">Diagnostic Tests</span><span class="field-value {{ $audit->diagnostic_tests_done ? '' : 'empty' }}">{{ $audit->diagnostic_tests_done ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Surgery Conducted</span>
                @php $sc = $audit->surgery_conducted ?? null; @endphp
                @if($sc === 'Yes') <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
                @elseif($sc === 'No') <span class="yn-badge yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
                @else <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span> @endif
            </div>
            @if($audit->surgery_conducted === 'Yes')
            <div class="field-item"><span class="field-label">Scar Present</span>
                @php $sp = $audit->surgery_scar_present ?? null; @endphp
                @if($sp === 'Yes') <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
                @elseif($sp === 'No') <span class="yn-badge yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
                @else <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span> @endif
                @if($audit->surgery_scar_remarks)<span class="text-xs text-slate-500 ml-1">{{ $audit->surgery_scar_remarks }}</span>@endif
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Money & Previous Hospitalisation --}}
<div class="detail-card">
    <div class="detail-card-head">
        <i class="fas fa-rupee-sign text-violet-500 text-sm"></i>
        <h3>Money &amp; Previous Hospitalisation</h3>
    </div>
    <div class="detail-card-body">
        <div class="field-grid">
            <div class="field-item"><span class="field-label">Money Charged</span>
                @if($audit->money_charged === 'Yes')
                    <span class="yn-badge yn-no"><i class="fas fa-exclamation-circle text-xs"></i> Yes — Flagged</span>
                @elseif($audit->money_charged === 'No')
                    <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> No</span>
                @else <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span> @endif
            </div>
            @if($audit->money_charged === 'Yes')
            <div class="field-item"><span class="field-label">Amount</span><span class="field-value text-rose-600">₹ {{ number_format($audit->money_charged_amount ?? 0, 2) }}</span></div>
            <div class="field-item"><span class="field-label">Receipt Available</span>
                @if($audit->receipt_available === 'Yes') <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
                @elseif($audit->receipt_available === 'No') <span class="yn-badge yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
                @else <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span> @endif
            </div>
            @if($audit->receipt_path)
            <div class="field-item"><span class="field-label">Receipt</span>
                <a href="{{ Storage::disk('public')->url($audit->receipt_path) }}" target="_blank" class="attach-link mt-1"><i class="fas fa-eye"></i> View</a>
            </div>
            @endif
            @endif
            <div class="field-item"><span class="field-label">Previous Hospitalisation</span>
                @if($audit->previous_hospitalisation === 'Yes')
                    <span class="yn-badge yn-no"><i class="fas fa-exclamation-circle text-xs"></i> Yes — Review needed</span>
                @elseif($audit->previous_hospitalisation === 'No')
                    <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> No</span>
                @else <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span> @endif
                @if($audit->previous_hospitalisation_remarks)<p class="text-xs text-slate-500 mt-1">{{ $audit->previous_hospitalisation_remarks }}</p>@endif
            </div>
        </div>
        @if($audit->other_remarks)
        <div class="mt-3 p-3 bg-slate-50 border border-slate-100 rounded-xl">
            <span class="field-label">Other Remarks</span>
            <p class="text-sm text-slate-700 mt-1">{{ $audit->other_remarks }}</p>
        </div>
        @endif
    </div>
</div>

{{-- Attachments --}}
@if($audit->attachments && $audit->attachments->count())
<div class="detail-card">
    <div class="detail-card-head">
        <i class="fas fa-paperclip text-violet-500 text-sm"></i>
        <h3>Supporting Attachments ({{ $audit->attachments->count() }})</h3>
    </div>
    <div class="detail-card-body">
        @foreach($audit->attachments->sortBy('sort_order') as $att)
        @php $isPdf = str_ends_with(strtolower($att->file_path), '.pdf'); @endphp
        <div class="attach-item">
            <div class="attach-icon {{ $isPdf ? 'pdf' : 'img' }}"><i class="fas {{ $isPdf ? 'fa-file-pdf' : 'fa-file-image' }}"></i></div>
            <div style="flex:1; min-width:0;"><p class="text-sm font-semibold text-slate-700">{{ $att->name }}</p></div>
            <a href="{{ Storage::disk('public')->url($att->file_path) }}" target="_blank" class="attach-link">
                <i class="fas {{ $isPdf ? 'fa-file-pdf' : 'fa-eye' }}"></i> {{ $isPdf ? 'Open' : 'View' }}
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

<div class="flex items-center justify-between text-xs text-slate-400 mt-4 pt-4 border-t border-slate-200">
    <span><i class="fas fa-shield-alt text-violet-500 mr-1"></i> PMJAY Assam · Admin · Live Audit #{{ $audit->id }}</span>
    <a href="{{ route('admin.audits.live.index') }}" class="text-violet-600 hover:underline">← Back to list</a>
</div>
@endsection

@extends('admin.layout.layout')

@section('main_title')
<div class="flex items-center gap-2 text-sm text-slate-400 mb-5">
    <a href="{{ route('admin.audits.field.index') }}" class="hover:text-amber-600 transition-colors">
        <i class="fas fa-arrow-left mr-1 text-xs"></i> Field Visits
    </a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-600 font-medium">Audit #{{ $audit->id }}</span>
</div>
<h1 class="text-2xl font-black text-slate-900 tracking-tight mb-1" style="font-family:'Syne',sans-serif;">
    Field Visit
</h1>
<p class="text-sm text-slate-500 mb-7">
    {{ $audit->treatment?->patient_name ?? '—' }} · {{ $audit->treatment?->hospital?->name ?? '—' }}
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
    .ro-photo { border-radius:.875rem; overflow:hidden; border:2px solid #34d399; max-width:480px; }
    .ro-photo img { width:100%; display:block; }
    .ro-map { width:100%; max-width:480px; height:160px; border-radius:.875rem; object-fit:cover; border:2px solid #e2e8f0; display:block; margin-top:.75rem; }
    .gps-chip { display:inline-flex; align-items:center; gap:.4rem; background:#dcfce7; color:#166534; font-size:.75rem; font-weight:600; padding:.35rem .85rem; border-radius:9999px; margin-top:.5rem; }
    .attach-item { display:flex; align-items:center; gap:.75rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:.875rem; padding:.75rem 1rem; }
    .attach-item + .attach-item { margin-top:.5rem; }
    .attach-icon { width:2.25rem; height:2.25rem; border-radius:.625rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:.9rem; }
    .attach-icon.pdf { background:#fef3c7; color:#92400e; }
    .attach-icon.img { background:#d1fae5; color:#065f46; }
    .attach-link { display:inline-flex; align-items:center; gap:.3rem; font-size:.75rem; font-weight:600; color:#0369a1; background:#e0f2fe; padding:.3rem .75rem; border-radius:9999px; text-decoration:none; transition:background .15s; flex-shrink:0; }
    .attach-link:hover { background:#bae6fd; }
    .check-row { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap; padding:.625rem 0; border-bottom:1px solid #f1f5f9; }
    .check-row:last-child { border-bottom:none; }
    .check-label { font-size:.8125rem; font-weight:500; color:#334155; flex:1; }
    .check-sub { font-size:.7rem; color:#94a3b8; margin-top:.1rem; }
</style>
@endsection

@section('main_content')
@php $fv = $audit->fieldVisit; @endphp

{{-- Case info --}}
<div class="detail-card">
    <div class="detail-card-head" style="border-top:3px solid #f59e0b;">
        <i class="fas fa-info-circle text-amber-500 text-sm"></i>
        <h3>Case Information</h3>
        <span class="ml-auto">
            @if($audit->status === 'completed')
                <span class="status-badge status-completed"><i class="fas fa-check-circle text-xs"></i> Completed</span>
            @else
                <span class="status-badge status-pending"><i class="fas fa-clock text-xs"></i> Pending</span>
            @endif
        </span>
    </div>
    <div class="detail-card-body">
        <div class="field-grid">
            <div class="field-item"><span class="field-label">Registration ID</span><span class="field-value">{{ $audit->treatment?->registration_id ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Case ID</span><span class="field-value">{{ $audit->treatment?->case_id ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Patient Name</span><span class="field-value">{{ $audit->treatment?->patient_name ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Mobile</span><span class="field-value">{{ $audit->treatment?->ben_mobile_no ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Hospital</span><span class="field-value">{{ $audit->treatment?->hospital?->name ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">District</span><span class="field-value">{{ $audit->district?->name ?? '—' }}</span></div>
            <div class="field-item col-span-2"><span class="field-label">Procedure</span><span class="field-value">{{ $audit->treatment?->procedure_details ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Preauth Amount</span><span class="field-value text-emerald-600">₹ {{ number_format($audit->treatment?->amount_preauth_approved ?? 0, 2) }}</span></div>
        </div>
    </div>
</div>

@if($fv)
{{-- Patient & treatment details recorded by DMO --}}
<div class="detail-card">
    <div class="detail-card-head">
        <i class="fas fa-clipboard-check text-amber-500 text-sm"></i>
        <h3>DMO Observations · Patient &amp; Treatment</h3>
        <span class="ml-auto text-xs text-slate-400">
            by <strong class="text-slate-600">{{ $fv->submittedBy?->name ?? '—' }}</strong>
            · {{ $fv->created_at?->format('d M Y, h:i A') ?? '—' }}
        </span>
    </div>
    <div class="detail-card-body">
        <div class="field-grid">
            <div class="field-item"><span class="field-label">Patient Name</span><span class="field-value {{ $fv->patient_name ? '' : 'empty' }}">{{ $fv->patient_name ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Package Booked</span><span class="field-value {{ $fv->package_booked ? '' : 'empty' }}">{{ $fv->package_booked ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Treating Doctor</span><span class="field-value {{ $fv->treating_doctor ? '' : 'empty' }}">{{ $fv->treating_doctor ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Specialization</span><span class="field-value {{ $fv->doctor_specialization ? '' : 'empty' }}">{{ $fv->doctor_specialization ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Admission</span><span class="field-value">{{ $fv->admission_datetime ? \Carbon\Carbon::parse($fv->admission_datetime)->format('d M Y, h:i A') : '—' }}</span></div>
            <div class="field-item"><span class="field-label">Discharge</span><span class="field-value">{{ $fv->discharge_datetime ? \Carbon\Carbon::parse($fv->discharge_datetime)->format('d M Y, h:i A') : '—' }}</span></div>
            <div class="field-item"><span class="field-label">Treatment Type</span>
                @if($fv->treatment_type)
                    <span class="yn-badge {{ $fv->treatment_type === 'Surgical' ? 'bg-violet-100 text-violet-800' : 'bg-blue-100 text-blue-800' }}">
                        <i class="fas {{ $fv->treatment_type === 'Surgical' ? 'fa-scalpel' : 'fa-pills' }} text-xs"></i>
                        {{ $fv->treatment_type }}
                    </span>
                @else <span class="field-value empty">—</span> @endif
            </div>
            <div class="field-item"><span class="field-label">Diagnosis</span><span class="field-value {{ $fv->diagnosis ? '' : 'empty' }}">{{ $fv->diagnosis ?? '—' }}</span></div>
        </div>
    </div>
</div>

{{-- Verification checks --}}
<div class="detail-card">
    <div class="detail-card-head">
        <i class="fas fa-tasks text-amber-500 text-sm"></i>
        <h3>Verification Checks</h3>
    </div>
    <div class="detail-card-body">
        @php
            $checks = [
                ['Patient left against medical advice?',       'lama',                    false],
                ['Outdoor Register entry found',              'outdoor_register',         false],
                ['Indoor Register entry found',               'indoor_register',          false],
                ['OT Register entry found',                   'ot_register',              true],
                ['Lab Register entry found',                  'lab_register',             false],
                ['IPD papers complete',                       'ipd_complete',             false],
                ['IPD papers align with treatment',           'ipd_aligns',               false],
                ['OT notes available',                        'ot_notes_available',       true],
                ['OT notes complete',                         'ot_notes_complete',        true],
                ['OT notes align with booked surgery',        'ot_notes_align',           true],
                ['Pre-anaesthesia documents available',       'pre_anaesthesia',          true],
                ['Daily nursing notes available',             'nursing_notes_available',  false],
                ['Daily nursing notes complete',              'nursing_notes_complete',   false],
                ['Daily doctor notes available',              'doctor_notes_available',   false],
                ['Daily doctor notes complete',               'doctor_notes_complete',    false],
                ['Daily progress chart available',            'progress_chart_available', false],
                ['Daily progress chart complete',             'progress_chart_complete',  false],
                ['Daily treatment chart available',           'treatment_chart_available',false],
                ['Daily treatment chart complete',            'treatment_chart_complete', false],
                ['Monitoring details available',              'monitoring_available',     false],
                ['Discharge summary complete',                'discharge_summary',        false],
            ];
            $isSurgical = $fv->treatment_type === 'Surgical';
        @endphp
        @foreach($checks as [$label, $field, $surgOnly])
        @if(!$surgOnly || $isSurgical)
        @php $val = $fv->$field ?? null; $rem = $field.'_remarks'; @endphp
        <div class="check-row">
            <div>
                <div class="check-label">{{ $label }}</div>
                @if($surgOnly)<div class="check-sub">Surgical cases only</div>@endif
                @if($fv->$rem ?? null)<div class="check-sub mt-1">{{ $fv->$rem }}</div>@endif
            </div>
            @if($val === 'Yes')  <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
            @elseif($val === 'No') <span class="yn-badge yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
            @elseif($val === 'NA') <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span>
            @else <span class="text-slate-300 text-xs">—</span> @endif
        </div>
        @endif
        @endforeach

        @if($fv->overall_remarks)
        <div class="mt-4 p-3 bg-slate-50 border border-slate-100 rounded-xl">
            <span class="field-label">Overall Justification</span>
            <p class="text-sm text-slate-700 mt-1 whitespace-pre-line">{{ $fv->overall_remarks }}</p>
        </div>
        @endif
    </div>
</div>

{{-- Site photograph --}}
@if($fv->photo_path)
<div class="detail-card">
    <div class="detail-card-head">
        <i class="fas fa-camera text-amber-500 text-sm"></i>
        <h3>Site Photograph</h3>
    </div>
    <div class="detail-card-body flex flex-col gap-3">
        <div class="ro-photo">
            <img src="{{ Storage::disk('public')->url($fv->photo_path) }}" alt="Site photo">
        </div>
        @if($fv->photo_latitude)
        <span class="gps-chip"><i class="fas fa-map-marker-alt"></i> {{ number_format($fv->photo_latitude, 6) }}°N, {{ number_format($fv->photo_longitude, 6) }}°E</span>
        @if($fv->photo_address) <p class="text-xs text-slate-400">{{ $fv->photo_address }}</p> @endif
        <img src="https://maps.googleapis.com/maps/api/staticmap?center={{ $fv->photo_latitude }},{{ $fv->photo_longitude }}&zoom=15&size=480x160&maptype=roadmap&markers=color:red%7C{{ $fv->photo_latitude }},{{ $fv->photo_longitude }}&key={{ config('services.google_maps.key') }}"
             alt="Map" class="ro-map">
        @endif
    </div>
</div>
@endif

{{-- Attachments --}}
@if($fv->attachments && $fv->attachments->count())
<div class="detail-card">
    <div class="detail-card-head">
        <i class="fas fa-paperclip text-amber-500 text-sm"></i>
        <h3>Attachments ({{ $fv->attachments->count() }})</h3>
    </div>
    <div class="detail-card-body">
        @foreach($fv->attachments->sortBy('sort_order') as $att)
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

@else
<div class="detail-card">
    <div class="detail-card-body">
        <div class="empty-state"><i class="fas fa-hospital-user"></i><p>Field visit not yet completed for this case.</p></div>
    </div>
</div>
@endif

<div class="flex items-center justify-between text-xs text-slate-400 mt-4 pt-4 border-t border-slate-200">
    <span><i class="fas fa-shield-alt text-amber-500 mr-1"></i> PMJAY Assam · Admin · Field Visit #{{ $audit->id }}</span>
    <a href="{{ route('admin.audits.field.index') }}" class="text-amber-600 hover:underline">← Back to list</a>
</div>
@endsection

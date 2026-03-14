@extends('admin.layout.layout')

@section('main_title')
<div class="flex items-center gap-2 text-sm text-slate-400 mb-5">
    <a href="{{ route('admin.audits.telephonic.index') }}" class="hover:text-cyan-600 transition-colors">
        <i class="fas fa-arrow-left mr-1 text-xs"></i> Telephonic Audits
    </a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-600 font-medium">Audit #{{ $audit->id }}</span>
</div>
<h1 class="text-2xl font-black text-slate-900 tracking-tight mb-1" style="font-family:'Syne',sans-serif;">
    Telephonic Audit
</h1>
<p class="text-sm text-slate-500 mb-7">
    {{ $audit->treatment?->patient_name ?? '—' }}
    @if($audit->treatment?->hospital?->name)
    · {{ $audit->treatment->hospital->name }}
    @endif
</p>
@endsection

@section('pageCss')
@include('admin.audits._shared_css')
<style>
    .detail-card { background:#fff; border:1px solid #e2e8f0; border-radius:1.25rem; overflow:hidden; margin-bottom:1.25rem; }
    .detail-card-head { padding:.875rem 1.25rem; background:#f8fafc; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; gap:.625rem; }
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
</style>
@endsection

@section('main_content')
@php $ta = $audit->telephonicAudit; @endphp

{{-- Case information --}}
<div class="detail-card">
    <div class="detail-card-head" style="border-top:3px solid #06b6d4;">
        <i class="fas fa-info-circle text-cyan-500 text-sm"></i>
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
            <div class="field-item"><span class="field-label">Member ID</span><span class="field-value">{{ $audit->treatment?->member_id ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Patient Name</span><span class="field-value">{{ $audit->treatment?->patient_name ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Mobile</span><span class="field-value">{{ $audit->treatment?->ben_mobile_no ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Hospital</span><span class="field-value">{{ $audit->treatment?->hospital?->name ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">District</span><span class="field-value">{{ $audit->district?->name ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Procedure</span><span class="field-value">{{ $audit->treatment?->procedure_details ?? '—' }}</span></div>
            <div class="field-item"><span class="field-label">Preauth Amount</span><span class="field-value text-emerald-600">₹ {{ number_format($audit->treatment?->amount_preauth_approved ?? 0, 2) }}</span></div>
            <div class="field-item"><span class="field-label">Preauth Date</span><span class="field-value">{{ $audit->treatment?->preauth_init_date ? \Carbon\Carbon::parse($audit->treatment->preauth_init_date)->format('d M Y') : '—' }}</span></div>
        </div>
    </div>
</div>

@if($ta)
{{-- Telephonic audit responses --}}
<div class="detail-card">
    <div class="detail-card-head">
        <i class="fas fa-phone-alt text-cyan-500 text-sm"></i>
        <h3>Telephonic Verification</h3>
        <span class="ml-auto text-xs text-slate-400">
            Submitted by <strong class="text-slate-600">{{ $ta->submittedBy?->name ?? '—' }}</strong>
            on {{ $ta->created_at?->format('d M Y, h:i A') ?? '—' }}
        </span>
    </div>
    <div class="detail-card-body">
        <div class="field-grid">
            @php
                $checks = [
                    ['Patient reachable',               'patient_reachable'],
                    ['Admitted to hospital',             'patient_admitted'],
                    ['Admission date correct',           'admission_date_correct'],
                    ['Discharge date correct',           'discharge_date_correct'],
                    ['Treatment received',               'treatment_received'],
                    ['Treatment matches package',        'treatment_matches_package'],
                    ['Money charged',                    'money_charged'],
                    ['Satisfied with treatment',         'patient_satisfied'],
                ];
            @endphp
            @foreach($checks as [$label, $field])
            @php $val = $ta->$field ?? null; @endphp
            <div class="field-item">
                <span class="field-label">{{ $label }}</span>
                @if($val === 'Yes' || $val === true || $val === 1)
                    <span class="yn-badge yn-yes"><i class="fas fa-check-circle text-xs"></i> Yes</span>
                @elseif($val === 'No' || $val === false || $val === 0)
                    <span class="yn-badge yn-no"><i class="fas fa-times-circle text-xs"></i> No</span>
                @elseif($val === 'NA')
                    <span class="yn-badge yn-na"><i class="fas fa-minus text-xs"></i> N/A</span>
                @else
                    <span class="field-value empty">—</span>
                @endif
            </div>
            @endforeach
        </div>

        @if($ta->money_charged_amount)
        <div class="mt-3 p-3 bg-rose-50 border border-rose-200 rounded-xl text-sm">
            <i class="fas fa-exclamation-triangle text-rose-500 mr-1"></i>
            <strong class="text-rose-700">Amount charged:</strong>
            <span class="text-rose-700">₹ {{ number_format($ta->money_charged_amount, 2) }}</span>
        </div>
        @endif

        @if($ta->remarks)
        <div class="mt-3">
            <span class="field-label">Remarks</span>
            <p class="text-sm text-slate-700 mt-1 bg-slate-50 border border-slate-100 rounded-xl px-3 py-2">{{ $ta->remarks }}</p>
        </div>
        @endif
    </div>
</div>
@else
<div class="detail-card">
    <div class="detail-card-body">
        <div class="empty-state">
            <i class="fas fa-phone-slash"></i>
            <p>Telephonic audit not yet completed for this case.</p>
        </div>
    </div>
</div>
@endif

<div class="flex items-center justify-between text-xs text-slate-400 mt-4 pt-4 border-t border-slate-200">
    <span><i class="fas fa-shield-alt text-cyan-500 mr-1"></i> PMJAY Assam · Admin · Telephonic Audit #{{ $audit->id }}</span>
    <a href="{{ route('admin.audits.telephonic.index') }}" class="text-cyan-600 hover:underline">← Back to list</a>
</div>
@endsection

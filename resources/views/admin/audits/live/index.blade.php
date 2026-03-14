@extends('admin.layout.layout')

@section('main_title')
<div class="flex flex-wrap items-center justify-between gap-3 mb-7">
    <div>
        <div class="flex items-center gap-2 text-sm text-slate-400 mb-1">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-violet-600 transition-colors">
                <i class="fas fa-chart-bar mr-1 text-xs"></i> Audit Intelligence
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-600 font-medium">Live Audits</span>
        </div>
        <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight" style="font-family:'Syne',sans-serif;">
            Live Audits
        </h1>
        <p class="text-sm text-slate-500 mt-1">Independent on-site beneficiary verifications · admin view</p>
    </div>
</div>
@endsection

@section('pageCss')
@include('admin.audits._shared_css')
<style>
    .page-card::before { content:''; display:block; height:3px; background:linear-gradient(90deg,#8b5cf6,#6366f1); }
</style>
@endsection

@section('main_content')

<div class="flex flex-wrap items-center gap-2 mb-5">
    <span class="summary-chip chip-total"><i class="fas fa-list text-xs"></i> {{ number_format($summary->total) }} Total</span>
    <span class="summary-chip chip-ai-pass"><i class="fas fa-robot text-xs"></i> {{ number_format($summary->ai_passed) }} AI Passed</span>
    <span class="summary-chip chip-ai-fail"><i class="fas fa-exclamation-triangle text-xs"></i> {{ number_format($summary->ai_failed) }} AI Failed</span>
    <span class="summary-chip chip-ai-skip"><i class="fas fa-question-circle text-xs"></i> {{ number_format($summary->ai_skipped) }} Skipped</span>
</div>

<form method="GET" action="{{ route('admin.audits.live.index') }}" class="filter-bar">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Patient, hospital, PMJAY ID…" class="filter-input" style="min-width:220px;">
    <select name="ai_result" class="filter-input">
        <option value="">All AI results</option>
        <option value="passed"  {{ request('ai_result') === 'passed'  ? 'selected' : '' }}>AI Passed</option>
        <option value="failed"  {{ request('ai_result') === 'failed'  ? 'selected' : '' }}>AI Failed</option>
        <option value="skipped" {{ request('ai_result') === 'skipped' ? 'selected' : '' }}>AI Skipped</option>
    </select>
    <select name="treatment_type" class="filter-input">
        <option value="">All types</option>
        <option value="Surgical" {{ request('treatment_type') === 'Surgical' ? 'selected' : '' }}>Surgical</option>
        <option value="Medical"  {{ request('treatment_type') === 'Medical'  ? 'selected' : '' }}>Medical</option>
    </select>
    <select name="dmo_id" class="filter-input">
        <option value="">All DMOs</option>
        @foreach($dmos as $dmo)
        <option value="{{ $dmo->id }}" {{ request('dmo_id') == $dmo->id ? 'selected' : '' }}>{{ $dmo->name }}</option>
        @endforeach
    </select>
    <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-input">
    <input type="date" name="date_to"   value="{{ request('date_to') }}"   class="filter-input">
    <button type="submit" class="filter-btn filter-btn-primary"><i class="fas fa-search mr-1"></i> Filter</button>
    @if(request()->hasAny(['search','ai_result','treatment_type','dmo_id','date_from','date_to']))
    <a href="{{ route('admin.audits.live.index') }}" class="filter-btn filter-btn-ghost"><i class="fas fa-times mr-1"></i> Clear</a>
    @endif
</form>

<div class="page-card">
    <div class="overflow-x-auto">
        <table class="audit-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Hospital</th>
                    <th>PMJAY ID</th>
                    <th>Treatment</th>
                    <th>AI Result</th>
                    <th>DMO</th>
                    <th>Submitted</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($audits as $audit)
                @php
                    $aiSkipped = str_contains($audit->ai_validation_message ?? '', 'skipped');
                    $aiPassed  = $audit->ai_bed_detected && $audit->ai_patient_detected;
                @endphp
                <tr>
                    <td class="text-slate-400 text-xs font-mono">{{ $audit->id }}</td>
                    <td>
                        <span class="font-semibold text-slate-800">{{ $audit->patient_name ?? '—' }}</span>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $audit->contact_number ?? '' }}</p>
                    </td>
                    <td class="text-slate-600">{{ $audit->hospital_name ?? '—' }}</td>
                    <td class="font-mono text-xs text-slate-500">{{ $audit->pmjay_id ?? '—' }}</td>
                    <td>
                        @if($audit->treatment_type === 'Surgical')
                            <span class="ai-badge bg-violet-100 text-violet-700"><i class="fas fa-scalpel text-xs"></i> Surgical</span>
                        @elseif($audit->treatment_type === 'Medical')
                            <span class="ai-badge bg-blue-100 text-blue-700"><i class="fas fa-pills text-xs"></i> Medical</span>
                        @else <span class="text-slate-400 text-xs">—</span> @endif
                    </td>
                    <td>
                        @if($aiSkipped)
                            <span class="ai-badge ai-skip"><i class="fas fa-question-circle text-xs"></i> Skipped</span>
                        @elseif($aiPassed)
                            <span class="ai-badge ai-pass"><i class="fas fa-check-circle text-xs"></i> Passed</span>
                        @else
                            <span class="ai-badge ai-fail"><i class="fas fa-times-circle text-xs"></i> Failed</span>
                        @endif
                    </td>
                    <td class="text-slate-600 text-xs">{{ $audit->submittedBy?->name ?? '—' }}</td>
                    <td class="text-slate-400 text-xs whitespace-nowrap">
                        {{ $audit->created_at?->format('d M Y') }}<br>
                        <span class="text-slate-300">{{ $audit->created_at?->format('h:i A') }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.audits.live.show', $audit->id) }}" class="action-btn" title="View">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9"><div class="empty-state"><i class="fas fa-hospital-user"></i><p>No live audits found.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-footer">
        <span>{{ $audits->total() ? 'Showing '.$audits->firstItem().'–'.$audits->lastItem().' of '.number_format($audits->total()) : 'No results' }}</span>
        <div>{{ $audits->links() }}</div>
    </div>
</div>
@endsection

@extends('admin.layout.layout')

@section('main_title')
<div class="flex flex-wrap items-center justify-between gap-3 mb-7">
    <div>
        <div class="flex items-center gap-2 text-sm text-slate-400 mb-1">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-cyan-600 transition-colors">
                <i class="fas fa-chart-bar mr-1 text-xs"></i> Audit Intelligence
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-600 font-medium">Telephonic Audits</span>
        </div>
        <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight" style="font-family:'Syne',sans-serif;">
            Telephonic Audits
        </h1>
        <p class="text-sm text-slate-500 mt-1">All PMJAY telephonic verification calls · admin view</p>
    </div>
</div>
@endsection

@section('pageCss')
@include('admin.audits._shared_css')
<style>
    .page-card::before { content:''; display:block; height:3px; background:linear-gradient(90deg,#06b6d4,#0ea5e9); }
</style>
@endsection

@section('main_content')

{{-- Summary chips --}}
<div class="flex flex-wrap items-center gap-2 mb-5">
    <span class="summary-chip chip-total"><i class="fas fa-list text-xs"></i> {{ number_format($summary->total) }} Total</span>
    <span class="summary-chip chip-done"><i class="fas fa-check-circle text-xs"></i> {{ number_format($summary->completed) }} Completed</span>
    <span class="summary-chip chip-pending"><i class="fas fa-clock text-xs"></i> {{ number_format($summary->pending) }} Pending</span>
</div>

{{-- Filter bar --}}
<form method="GET" action="{{ route('admin.audits.telephonic.index') }}" class="filter-bar">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Patient name or Member ID…" class="filter-input" style="min-width:220px;">

    <select name="status" class="filter-input">
        <option value="">All statuses</option>
        <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
    </select>

    <select name="district_id" class="filter-input">
        <option value="">All districts</option>
        @foreach($districts as $d)
        <option value="{{ $d->id }}" {{ request('district_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
        @endforeach
    </select>

    <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-input" title="From date">
    <input type="date" name="date_to"   value="{{ request('date_to') }}"   class="filter-input" title="To date">

    <button type="submit" class="filter-btn filter-btn-primary">
        <i class="fas fa-search mr-1"></i> Filter
    </button>
    @if(request()->hasAny(['search','status','district_id','date_from','date_to']))
    <a href="{{ route('admin.audits.telephonic.index') }}" class="filter-btn filter-btn-ghost">
        <i class="fas fa-times mr-1"></i> Clear
    </a>
    @endif
</form>

{{-- Table --}}
<div class="page-card">
    <div class="overflow-x-auto">
        <table class="audit-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Member ID</th>
                    <th>Mobile</th>
                    <th>Hospital</th>
                    <th>District</th>
                    <th>Preauth Date</th>
                    <th>DMO</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($audits as $audit)
                <tr>
                    <td class="text-slate-400 text-xs font-mono">{{ $audit->id }}</td>
                    <td>
                        <span class="font-semibold text-slate-800">{{ $audit->treatment?->patient_name ?? '—' }}</span>
                    </td>
                    <td class="font-mono text-xs text-slate-500">{{ $audit->treatment?->member_id ?? '—' }}</td>
                    <td class="text-slate-500">{{ $audit->treatment?->ben_mobile_no ?? '—' }}</td>
                    <td class="text-slate-600">{{ $audit->treatment?->hospital?->name ?? '—' }}</td>
                    <td class="text-slate-500">{{ $audit->district?->name ?? '—' }}</td>
                    <td class="text-slate-400 text-xs whitespace-nowrap">
                        {{ $audit->treatment?->preauth_init_date ? \Carbon\Carbon::parse($audit->treatment->preauth_init_date)->format('d M Y') : '—' }}
                    </td>
                    <td class="text-slate-600 text-xs">{{ $audit->telephonicAudit?->submittedBy?->name ?? '—' }}</td>
                    <td>
                        @if($audit->status === 'completed')
                            <span class="status-badge status-completed"><i class="fas fa-check-circle text-xs"></i> Completed</span>
                        @else
                            <span class="status-badge status-pending"><i class="fas fa-clock text-xs"></i> Pending</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.audits.telephonic.show', $audit->id) }}" class="action-btn" title="View">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10">
                        <div class="empty-state">
                            <i class="fas fa-phone-slash"></i>
                            <p>No telephonic audits found matching your filters.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-footer">
        <span>
            @if($audits->total())
                Showing {{ $audits->firstItem() }}–{{ $audits->lastItem() }} of {{ number_format($audits->total()) }}
            @else
                No results
            @endif
        </span>
        <div>{{ $audits->links() }}</div>
    </div>
</div>
@endsection

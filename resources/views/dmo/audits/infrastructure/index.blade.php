@extends('dmo.layout.layout')

@section('main_title')
<div class="flex items-center gap-2 text-sm text-slate-400 mb-5">
    <a href="{{ route('dmo.dashboard') }}" class="hover:text-emerald-600 transition-colors">
        <i class="fas fa-arrow-left mr-1 text-xs"></i> Dashboard
    </a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-600 font-medium">Infrastructure Audits</span>
</div>
<div class="flex items-start justify-between flex-wrap gap-4 mb-7">
    <div>
        <h2 class="text-xl md:text-3xl font-semibold text-slate-800 tracking-tight flex items-center gap-2">
            <i class="fas fa-building-columns text-emerald-600"></i>
            Infrastructure Audits
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            Annexure 2.2 · All submitted hospital infrastructure audits
        </p>
    </div>
    <a href="{{ route('dmo.audits.infra-audit.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow transition-all">
        <i class="fas fa-plus-circle"></i> New Audit
    </a>
</div>
@endsection

@section('pageCss')
<style>
    .filter-input { padding:.55rem .875rem; border:2px solid #e2e8f0; border-radius:.75rem; background:#f8fafc; font-size:.8rem; color:#1e293b; outline:none; font-family:inherit; transition:border-color .15s; }
    .filter-input:focus { border-color:#34d399; background:#fff; }

    .audit-table { width:100%; border-collapse:collapse; }
    .audit-table th { background:#f8fafc; font-size:.7rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:#94a3b8; padding:.75rem 1rem; text-align:left; border-bottom:2px solid #e2e8f0; white-space:nowrap; }
    .audit-table td { padding:.875rem 1rem; font-size:.8rem; color:#334155; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
    .audit-table tbody tr { transition:background .12s; }
    .audit-table tbody tr:hover { background:#f8fafc; }

    .hospital-name { font-weight:700; color:#0f172a; font-size:.845rem; }
    .hospital-meta { font-size:.72rem; color:#94a3b8; margin-top:.15rem; }

    .badge { display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem; font-weight:700; padding:.25rem .65rem; border-radius:9999px; white-space:nowrap; }
    .badge-public  { background:#dbeafe; color:#1e40af; }
    .badge-private { background:#fce7f3; color:#9d174d; }
    .badge-pass    { background:#d1fae5; color:#065f46; }
    .badge-fail    { background:#fee2e2; color:#991b1b; }
    .badge-na      { background:#f1f5f9; color:#64748b; }

    .action-btn { display:inline-flex; align-items:center; gap:.3rem; padding:.35rem .75rem; border-radius:.625rem; font-size:.75rem; font-weight:600; text-decoration:none; transition:background .15s; }
    .btn-view   { background:#eff6ff; color:#1d4ed8; }
    .btn-view:hover { background:#dbeafe; }
    .btn-delete { background:#fff1f2; color:#be123c; border:none; cursor:pointer; font-family:inherit; }
    .btn-delete:hover { background:#fee2e2; }

    .empty-state { text-align:center; padding:4rem 1rem; color:#94a3b8; }
    .empty-state i { font-size:2.5rem; margin-bottom:1rem; display:block; }

    .stat-card { background:#fff; border:1.5px solid #e2e8f0; border-radius:1rem; padding:1.1rem 1.25rem; }
    .stat-num  { font-size:1.75rem; font-weight:800; color:#0f172a; line-height:1; }
    .stat-lbl  { font-size:.72rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.07em; margin-top:.3rem; }
    .stat-sub  { font-size:.75rem; color:#64748b; margin-top:.25rem; }

    /* Toast (matching existing style) */
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
    .toast-close { background:none; border:none; cursor:pointer; opacity:.4; font-size:.7rem; padding:.15rem; color:inherit; flex-shrink:0; transition:opacity .15s; }
    .toast-close:hover { opacity:.85; }
    .toast-progress { position:absolute; bottom:0; left:0; height:3px; border-radius:0 0 .875rem .875rem; animation:toast-drain linear forwards; }
    .toast-success .toast-progress { background:#22c55e; }
    .toast-error   .toast-progress { background:#f43f5e; }
    @keyframes toast-drain { from { width:100%; } to { width:0%; } }
</style>
@endsection

@section('main_content')

@php
    $total      = $audits->total();
    $passCount  = $audits->getCollection()->where('ai_banner_pass', true)->count();
    $failCount  = $audits->getCollection()->where('ai_banner_pass', false)->count();
    $publicCount  = $audits->getCollection()->where('hospital_type', 'Public')->count();
    $privateCount = $audits->getCollection()->where('hospital_type', 'Private')->count();
@endphp

{{-- ── Summary Stats ── --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-num text-emerald-700">{{ $audits->total() }}</div>
        <div class="stat-lbl">Total Audits</div>
    </div>
    <div class="stat-card">
        <div class="stat-num text-blue-700">{{ $publicCount }}</div>
        <div class="stat-lbl">Public Hospitals</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#9d174d">{{ $privateCount }}</div>
        <div class="stat-lbl">Private Hospitals</div>
    </div>
    
</div>

{{-- ── Filters ── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-5">
    <form method="GET" action="{{ route('dmo.audits.infra-audit.index') }}" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Date</label>
            <input type="date" name="date" value="{{ request('date') }}" class="filter-input">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Hospital Type</label>
            <select name="type" class="filter-input">
                <option value="">All types</option>
                <option value="Public"   {{ request('type') === 'Public'   ? 'selected' : '' }}>Public</option>
                <option value="Private"  {{ request('type') === 'Private'  ? 'selected' : '' }}>Private</option>
            </select>
        </div>
        <div class="flex gap-2 pb-0.5">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition">
                <i class="fas fa-filter"></i> Apply
            </button>
            @if(request()->hasAny(['date','type']))
            <a href="{{ route('dmo.audits.infra-audit.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border-2 border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold transition">
                <i class="fas fa-xmark"></i> Clear
            </a>
            @endif
        </div>
    </form>
</div>

{{-- ── Audit Table ── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    @if($audits->isEmpty())
    <div class="empty-state">
        <i class="fas fa-building-columns"></i>
        <p class="font-semibold text-slate-600 text-sm">No infrastructure audits found</p>
        <p class="text-xs mt-1 mb-4">
            @if(request()->hasAny(['date','type']))
                No records match your current filters.
            @else
                Start by creating your first audit.
            @endif
        </p>
        <a href="{{ route('dmo.audits.infra-audit.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow transition">
            <i class="fas fa-plus-circle"></i> New Audit
        </a>
    </div>

    @else

    <div class="overflow-x-auto">
        <table class="audit-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Hospital</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Beds</th>
                    <th>ICU</th>
                    <th>OT</th>
                    <th>AI Banner</th>
                    <th>Hygiene</th>
                    <th>Submitted By</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($audits as $audit)
                <tr>
                    {{-- Row number --}}
                    <td class="text-slate-400 font-medium text-xs">
                        {{ ($audits->currentPage() - 1) * $audits->perPage() + $loop->iteration }}
                    </td>

                    {{-- Hospital --}}
                    <td>
                        <div class="hospital-name">{{ $audit->hospital_name }}</div>
                        <div class="hospital-meta">
                            {{ Str::limit($audit->hospital_address, 45) }}
                            @if($audit->hospital_id)
                                &nbsp;·&nbsp; <span class="font-mono">{{ $audit->hospital_id }}</span>
                            @endif
                        </div>
                    </td>

                    {{-- Date --}}
                    <td class="whitespace-nowrap">
                        {{ $audit->investigation_date?->format('d M Y') ?? '—' }}
                    </td>

                    {{-- Type --}}
                    <td>
                        <span class="badge {{ $audit->hospital_type === 'Public' ? 'badge-public' : 'badge-private' }}">
                            {{ $audit->hospital_type }}
                        </span>
                    </td>

                    {{-- Beds --}}
                    <td>
                        @if($audit->total_beds !== null)
                            <span class="font-bold">{{ $audit->total_beds }}</span>
                            <span class="text-slate-400 text-xs"> total</span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>

                    {{-- ICU --}}
                    <td>
                        @if($audit->icu_available === 'Yes')
                            <span class="badge badge-pass">
                                <i class="fas fa-check text-xs"></i>
                                {{ $audit->icu_beds ?? '?' }} beds
                            </span>
                        @elseif($audit->icu_available === 'No')
                            <span class="badge badge-fail">None</span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>

                    {{-- OT --}}
                    <td>
                        @if($audit->ot_available === 'Yes')
                            <span class="badge badge-pass">
                                <i class="fas fa-check text-xs"></i>
                                {{ $audit->ot_count ?? '?' }} OT
                            </span>
                        @elseif($audit->ot_available === 'No')
                            <span class="badge badge-fail">None</span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>

                    {{-- AI Banner --}}
                    <td>
                        @if(is_null($audit->ai_banner_pass))
                            <span class="badge badge-na"><i class="fas fa-minus text-xs"></i> N/A</span>
                        @elseif($audit->ai_banner_pass)
                            <span class="badge badge-pass"><i class="fas fa-robot text-xs"></i> Passed</span>
                        @else
                            <span class="badge badge-fail"><i class="fas fa-exclamation text-xs"></i> Failed</span>
                        @endif
                    </td>

                    {{-- Hygiene --}}
                    <td>
                        @if($audit->overall_hygiene)
                            @php
                                $hygCls = match($audit->overall_hygiene) {
                                    'Good'    => 'badge-pass',
                                    'Average' => 'background:#fef3c7;color:#92400e',
                                    'Poor'    => 'badge-fail',
                                    default   => 'badge-na',
                                };
                                $hygStyle = str_contains($hygCls, ':') ? "style=\"{$hygCls}\"" : "class=\"badge {$hygCls}\"";
                            @endphp
                            @if(str_contains($hygCls, ':'))
                                <span class="badge" style="{{ $hygCls }}">{{ $audit->overall_hygiene }}</span>
                            @else
                                <span class="badge {{ $hygCls }}">{{ $audit->overall_hygiene }}</span>
                            @endif
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>

                    {{-- Submitted by --}}
                    <td class="whitespace-nowrap">
                        <div class="text-xs font-semibold text-slate-600">
                            {{ $audit->submittedBy?->name ?? '—' }}
                        </div>
                        <div class="text-xs text-slate-400 mt-0.5">
                            {{ $audit->created_at?->format('d M, h:i A') ?? '' }}
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td class="whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('dmo.audits.infra-audit.show', $audit) }}" class="action-btn btn-view">
                                <i class="fas fa-eye text-xs"></i> View
                            </a>
                            @can('delete', $audit)
                            <form method="POST"
                                  action="{{ route('dmo.audits.infra-audit.destroy', $audit) }}"
                                  onsubmit="return confirm('Delete this audit record? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn btn-delete">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($audits->hasPages())
    <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-3">
        <p class="text-xs text-slate-500">
            Showing
            <strong>{{ $audits->firstItem() }}–{{ $audits->lastItem() }}</strong>
            of <strong>{{ $audits->total() }}</strong> audits
        </p>
        <div class="flex items-center gap-1">
            {{-- Previous --}}
            @if($audits->onFirstPage())
                <span class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-300 border border-slate-200 cursor-not-allowed">
                    <i class="fas fa-chevron-left text-xs"></i>
                </span>
            @else
                <a href="{{ $audits->previousPageUrl() }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
            @endif

            {{-- Page numbers --}}
            @foreach($audits->getUrlRange(max(1, $audits->currentPage()-2), min($audits->lastPage(), $audits->currentPage()+2)) as $page => $url)
                @if($page === $audits->currentPage())
                    <span class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-emerald-600 border border-emerald-600">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            {{-- Next --}}
            @if($audits->hasMorePages())
                <a href="{{ $audits->nextPageUrl() }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            @else
                <span class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-300 border border-slate-200 cursor-not-allowed">
                    <i class="fas fa-chevron-right text-xs"></i>
                </span>
            @endif
        </div>
    </div>
    @endif

    @endif {{-- end audits empty check --}}
</div>

<div id="toast-container"></div>

<script>
function toast(type, title, message, duration) {
    duration = duration ?? (type === 'error' ? 7000 : 4500);
    const icons = { error:'fa-times-circle', success:'fa-check-circle', warning:'fa-exclamation-circle' };
    const el = document.createElement('div');
    el.className = 'toast toast-' + type;
    el.innerHTML = `
        <div class="toast-icon"><i class="fas ${icons[type]}"></i></div>
        <div class="toast-body">
            <div class="toast-title">${title}</div>
            <div class="toast-msg">${message}</div>
        </div>
        <button class="toast-close" title="Dismiss"><i class="fas fa-times"></i></button>
        <div class="toast-progress" style="animation-duration:${duration}ms;"></div>`;
    document.getElementById('toast-container').appendChild(el);
    requestAnimationFrame(() => requestAnimationFrame(() => el.classList.add('toast-in')));
    const dismiss = () => { el.classList.replace('toast-in','toast-out'); el.addEventListener('transitionend', () => el.remove(), {once:true}); };
    const t = setTimeout(dismiss, duration);
    el.querySelector('.toast-close').addEventListener('click', () => { clearTimeout(t); dismiss(); });
}

@if(session('success'))
toast('success', 'Done', '{{ session("success") }}');
@endif
@if(session('error'))
toast('error', 'Error', '{{ session("error") }}');
@endif
</script>

@endsection

@extends('dmo.layout.layout')

@section('main_title')
<div class="flex items-center gap-2 text-sm text-slate-400 mb-5">
    <a href="{{ route('dmo.dashboard') }}" class="hover:text-emerald-600 transition-colors">
        <i class="fas fa-arrow-left mr-1 text-xs"></i> Dashboard
    </a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-600 font-medium">Live Audits</span>
</div>
<div class="flex flex-wrap items-center justify-between gap-4 mb-7">
    <div>
        <h2 class="text-2xl md:text-3xl font-semibold text-slate-800 tracking-tight flex items-center gap-2">
            <i class="fas fa-hospital-user text-violet-600"></i>
            Live Audits
        </h2>
        <p class="text-sm text-slate-500 mt-1">Independent on-site beneficiary verifications</p>
    </div>
    <a href="{{ route('dmo.audits.live-audit.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold shadow-md hover:shadow-lg transition">
        <i class="fas fa-bolt"></i> New Live Audit
    </a>
</div>
@endsection

@section('pageCss')
<style>
    /* ── Toast ── */
    #toast-container { position:fixed; top:1.25rem; right:1.25rem; z-index:9999; display:flex; flex-direction:column; gap:.625rem; pointer-events:none; max-width:min(420px,calc(100vw - 2.5rem)); }
    .toast { pointer-events:auto; display:flex; align-items:flex-start; gap:.75rem; padding:.875rem 1rem; border-radius:.875rem; border:1.5px solid transparent; box-shadow:0 8px 24px rgba(0,0,0,.14),0 2px 6px rgba(0,0,0,.08); font-size:.8125rem; font-weight:500; line-height:1.45; backdrop-filter:blur(6px); transform:translateX(120%); opacity:0; transition:transform .32s cubic-bezier(.22,1,.36,1),opacity .28s ease; cursor:default; position:relative; overflow:hidden; }
    .toast.toast-in  { transform:translateX(0); opacity:1; }
    .toast.toast-out { transform:translateX(120%); opacity:0; transition:transform .25s ease-in,opacity .22s ease-in; }
    .toast-error   { background:rgba(255,241,242,.97); border-color:#fda4af; color:#9f1239; }
    .toast-success { background:rgba(240,253,244,.97); border-color:#86efac; color:#14532d; }
    .toast-warning { background:rgba(255,251,235,.97); border-color:#fde68a; color:#78350f; }
    .toast-info    { background:rgba(239,246,255,.97); border-color:#93c5fd; color:#1e3a8a; }
    .toast-icon { width:1.75rem; height:1.75rem; border-radius:9999px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:.8rem; margin-top:.05rem; }
    .toast-error   .toast-icon { background:#fee2e2; color:#dc2626; }
    .toast-success .toast-icon { background:#dcfce7; color:#16a34a; }
    .toast-warning .toast-icon { background:#fef3c7; color:#d97706; }
    .toast-info    .toast-icon { background:#dbeafe; color:#2563eb; }
    .toast-body  { flex:1; min-width:0; }
    .toast-title { font-weight:700; font-size:.8125rem; margin-bottom:.15rem; }
    .toast-close { flex-shrink:0; background:none; border:none; cursor:pointer; opacity:.45; font-size:.75rem; padding:.1rem .2rem; transition:opacity .15s; line-height:1; color:inherit; }
    .toast-close:hover { opacity:.9; }
    .toast-progress { position:absolute; bottom:0; left:0; height:3px; border-radius:0 0 .875rem .875rem; animation:toast-drain linear forwards; }
    .toast-error   .toast-progress { background:#f43f5e; }
    .toast-success .toast-progress { background:#22c55e; }
    .toast-warning .toast-progress { background:#f59e0b; }
    .toast-info    .toast-progress { background:#3b82f6; }
    @keyframes toast-drain { from { width:100%; } to { width:0%; } }
</style>
@endsection

@section('main_content')

{{-- Laravel session flash → toast --}}
@if(session('success')) <span id="flash-success" data-msg="{{ session('success') }}" hidden></span> @endif
@if(session('error'))   <span id="flash-error"   data-msg="{{ session('error') }}"   hidden></span> @endif
@if(session('warning')) <span id="flash-warning" data-msg="{{ session('warning') }}" hidden></span> @endif

{{-- Filter chips + search --}}
<form method="GET" action="{{ route('dmo.audits.live-audit.all') }}" class="flex flex-wrap items-center gap-3 mb-6">
    <span class="text-sm text-slate-400">Filter:</span>

    <a href="{{ route('dmo.audits.live-audit.all', array_merge(request()->except('filter'), [])) }}"
       class="filter-chip text-xs px-4 py-2 rounded-full border transition
              {{ !request('filter') ? 'bg-violet-600 text-white border-violet-600' : 'bg-white border-slate-200 hover:border-violet-300 text-slate-700' }}">
        All
        <span class="ml-1 px-1.5 rounded-full text-[10px]
                     {{ !request('filter') ? 'bg-violet-500 text-white' : 'bg-slate-100 text-slate-600' }}">
            {{ $stats->total }}
        </span>
    </a>

    <a href="{{ route('dmo.audits.live-audit.all', array_merge(request()->except('filter'), ['filter' => 'ai_passed'])) }}"
       class="filter-chip text-xs px-4 py-2 rounded-full border transition
              {{ request('filter') === 'ai_passed' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white border-slate-200 hover:border-emerald-300 text-slate-700' }}">
        AI Passed
        <span class="ml-1 px-1.5 rounded-full text-[10px]
                     {{ request('filter') === 'ai_passed' ? 'bg-emerald-500 text-white' : 'bg-emerald-100 text-emerald-700' }}">
            {{ $stats->ai_passed }}
        </span>
    </a>

    <a href="{{ route('dmo.audits.live-audit.all', array_merge(request()->except('filter'), ['filter' => 'ai_skipped'])) }}"
       class="filter-chip text-xs px-4 py-2 rounded-full border transition
              {{ request('filter') === 'ai_skipped' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white border-slate-200 hover:border-amber-300 text-slate-700' }}">
        AI Skipped
        <span class="ml-1 px-1.5 rounded-full text-[10px]
                     {{ request('filter') === 'ai_skipped' ? 'bg-amber-400 text-white' : 'bg-amber-100 text-amber-700' }}">
            {{ $stats->ai_skipped }}
        </span>
    </a>

    {{-- Search --}}
    <div class="ml-auto flex items-center gap-2">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Patient, hospital, PMJAY ID…"
                   class="pl-8 pr-4 py-2 text-xs border border-slate-200 rounded-full bg-white focus:border-violet-400 focus:outline-none w-56 transition">
        </div>
        <button type="submit"
                class="px-4 py-2 text-xs font-semibold rounded-full bg-violet-600 hover:bg-violet-700 text-white transition">
            Search
        </button>
        @if(request('search') || request('filter'))
        <a href="{{ route('dmo.audits.live.all') }}"
           class="px-3 py-2 text-xs rounded-full border border-slate-200 text-slate-500 hover:bg-slate-50 transition">
            Clear
        </a>
        @endif
    </div>
</form>

{{-- Table --}}
<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs font-medium border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left">Patient</th>
                    <th class="px-6 py-4 text-left">Hospital</th>
                    <th class="px-6 py-4 text-left">PMJAY ID</th>
                    <th class="px-6 py-4 text-left">Treatment</th>
                    <th class="px-6 py-4 text-left">AI Result</th>
                    <th class="px-6 py-4 text-left">Submitted</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($liveAudits as $la)
                <tr class="hover:bg-slate-50/60 transition">
                    <td class="px-6 py-4">
                        <span class="font-semibold text-slate-800">{{ $la->patient_name }}</span>
                        @if($la->contact_number)
                        <p class="text-xs text-slate-400 mt-0.5">{{ $la->contact_number }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-600">{{ $la->hospital_name ?? '—' }}</td>
                    <td class="px-6 py-4 text-slate-500 font-mono text-xs">{{ $la->pmjay_id ?? '—' }}</td>
                    <td class="px-6 py-4">
                        @if($la->treatment_type === 'Surgical')
                            <span class="inline-flex items-center gap-1 bg-violet-100 text-violet-700 text-xs px-2.5 py-1 rounded-full font-medium">
                                <i class="fas fa-scalpel text-[10px]"></i> Surgical
                            </span>
                        @elseif($la->treatment_type === 'Medical')
                            <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs px-2.5 py-1 rounded-full font-medium">
                                <i class="fas fa-pills text-[10px]"></i> Medical
                            </span>
                        @else
                            <span class="text-slate-400 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if(str_contains($la->ai_validation_message ?? '', 'skipped'))
                            <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-xs px-2.5 py-1 rounded-full font-medium">
                                <i class="fas fa-exclamation-circle text-[10px]"></i> Skipped
                            </span>
                        @elseif($la->aiPassed())
                            <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 text-xs px-2.5 py-1 rounded-full font-medium">
                                <i class="fas fa-check-circle text-[10px]"></i> Passed
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-rose-100 text-rose-700 text-xs px-2.5 py-1 rounded-full font-medium">
                                <i class="fas fa-times-circle text-[10px]"></i> Failed
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-500 text-xs">
                        {{ $la->created_at->format('d M Y') }}<br>
                        <span class="text-slate-400">{{ $la->created_at->format('h:i A') }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('dmo.live-audit.show', $la->id) }}"
                           class="text-slate-400 hover:text-violet-600 transition mx-1" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-14 text-center">
                        <i class="fas fa-hospital-user text-4xl text-slate-200 mb-3 block"></i>
                        <p class="text-slate-400 font-medium">No live audits found</p>
                        <a href="{{ route('dmo.audits.live-audit.create') }}"
                           class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition">
                            <i class="fas fa-bolt"></i> Conduct first Live Audit
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination footer --}}
    <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-sm">
        <div class="text-slate-500">
            @if($liveAudits->total() > 0)
                Showing {{ $liveAudits->firstItem() }}–{{ $liveAudits->lastItem() }} of {{ $liveAudits->total() }} audits
            @else
                No results
            @endif
        </div>
        <div>{{ $liveAudits->links() }}</div>
    </div>
</div>

<div id="toast-container"></div>

<script>
function toast(type, title, message, duration) {
    duration = duration ?? 4500;
    const icons = { error:'fa-times-circle', success:'fa-check-circle', warning:'fa-exclamation-circle', info:'fa-info-circle' };
    const el = document.createElement('div');
    el.className = `toast toast-${type}`;
    el.innerHTML = `
        <div class="toast-icon"><i class="fas ${icons[type] ?? 'fa-info-circle'}"></i></div>
        <div class="toast-body"><div class="toast-title">${title}</div><div class="toast-msg">${message}</div></div>
        <button class="toast-close" title="Dismiss"><i class="fas fa-times"></i></button>
        <div class="toast-progress" style="animation-duration:${duration}ms;"></div>
    `;
    document.getElementById('toast-container').appendChild(el);
    requestAnimationFrame(() => requestAnimationFrame(() => el.classList.add('toast-in')));
    const dismiss = () => { el.classList.remove('toast-in'); el.classList.add('toast-out'); el.addEventListener('transitionend', () => el.remove(), { once: true }); };
    const timer = setTimeout(dismiss, duration);
    el.querySelector('.toast-close').addEventListener('click', () => { clearTimeout(timer); dismiss(); });
}

document.addEventListener('DOMContentLoaded', () => {
    [
        { id: 'flash-success', type: 'success', title: 'Success'  },
        { id: 'flash-error',   type: 'error',   title: 'Error'    },
        { id: 'flash-warning', type: 'warning', title: 'Warning'  },
    ].forEach(({ id, type, title }) => {
        const el = document.getElementById(id);
        if (el) toast(type, title, el.dataset.msg);
    });
});
</script>

@endsection

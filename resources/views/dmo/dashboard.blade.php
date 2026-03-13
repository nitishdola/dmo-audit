@extends('dmo.layout.layout')

@section('main_title')
<div class="flex flex-wrap items-center justify-between gap-3 mb-7">
    <div>
        <h2 class="text-2xl md:text-3xl font-semibold text-slate-800 tracking-tight">
            Audit &amp; field overview
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            Real-time completion metrics · PMJAY Assam district dashboard
        </p>
    </div>
    <a href="{{ route('dmo.audits.live-audit.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-md hover:shadow-lg transition">
        <i class="fas fa-bolt"></i>
        Conduct Live Audit
    </a>
</div>
@endsection

@section('main_content')

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 lg:gap-8 mb-10">

    {{-- ── 1. Total Assigned ── --}}
    <div class="bg-white rounded-3xl border border-slate-200/70 p-6 shadow-sm hover:shadow-md transition">
        <div class="flex items-start justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Assigned</span>
                <div class="text-3xl font-bold text-slate-800 mt-1 flex items-baseline gap-2">
                    {{ $total_assigned }}
                    <span class="text-sm font-normal text-slate-400 ml-1">tasks</span>
                </div>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>
        <div class="mt-5 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-slate-400">completed</p>
                <p class="text-xl font-semibold text-slate-800">{{ $total_completed }}</p>
            </div>
            @php $pc = $total_assigned > 0 ? round(($total_completed / $total_assigned) * 100) : 0; @endphp
            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-indigo-500 rounded-full transition-all" style="width:{{ $pc }}%"></div>
            </div>
            <span class="text-sm font-semibold text-indigo-600 shrink-0">{{ $pc }}%</span>
        </div>
        <div class="mt-3 text-xs text-rose-500 flex items-center gap-1">
            <i class="fas fa-exclamation-triangle"></i>
            {{ $total_assigned - $total_completed }} Overdue Audits
        </div>
    </div>

    {{-- ── 2. Telephonic Audit ── --}}
    <div class="bg-white rounded-3xl border border-slate-200/70 p-6 shadow-sm hover:shadow-md transition">
        <div class="flex items-start justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Telephonic Audit</span>
                <div class="text-3xl font-bold text-slate-800 mt-1 flex items-baseline gap-2">
                    <a href="{{ route('dmo.audits.telephonic.all') }}" class="hover:text-emerald-600 transition">
                        {{ $total_tele_assigned }}
                    </a>
                    <span class="text-sm font-normal text-slate-400 ml-1">calls</span>
                </div>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-phone-alt"></i>
            </div>
        </div>
        <div class="mt-5 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-slate-400">completed</p>
                <p class="text-xl font-semibold text-slate-800">
                    <a href="{{ route('dmo.audits.telephonic.all', ['status' => 'completed']) }}" class="hover:text-emerald-600 transition">
                        {{ $total_tele_completed }}
                    </a>
                </p>
            </div>
            @php $tpc = $total_tele_assigned > 0 ? round(($total_tele_completed / $total_tele_assigned) * 100) : 0; @endphp
            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full transition-all" style="width:{{ $tpc }}%"></div>
            </div>
            <span class="text-sm font-semibold text-emerald-600 shrink-0">{{ $tpc }}%</span>
        </div>
        <div class="mt-3 text-xs text-rose-500 flex items-center gap-1">
            <i class="fas fa-exclamation-triangle"></i>
            {{ $total_tele_assigned - $total_tele_completed }} Overdue Telephonic Audits
        </div>
    </div>

    {{-- ── 3. Field Visits ── --}}
    <div class="bg-white rounded-3xl border border-slate-200/70 p-6 shadow-sm hover:shadow-md transition">
        <div class="flex items-start justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Field Visits</span>
                <div class="text-3xl font-bold text-slate-800 mt-1 flex items-baseline gap-2">
                    <a href="{{ route('dmo.audits.field.all') }}" class="hover:text-amber-600 transition">
                        {{ $total_field_assigned }}
                    </a>
                    <span class="text-sm font-normal text-slate-400 ml-1">visits</span>
                </div>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-people-arrows"></i>
            </div>
        </div>
        <div class="mt-5 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-slate-400">completed</p>
                <p class="text-xl font-semibold text-slate-800">
                    <a href="{{ route('dmo.audits.field.all', ['status' => 'completed']) }}" class="hover:text-amber-600 transition">
                        {{ $total_field_completed }}
                    </a>
                </p>
            </div>
            @php $fpc = $total_field_assigned > 0 ? round(($total_field_completed / $total_field_assigned) * 100) : 0; @endphp
            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-amber-500 rounded-full transition-all" style="width:{{ $fpc }}%"></div>
            </div>
            <span class="text-sm font-semibold text-amber-600 shrink-0">{{ $fpc }}%</span>
        </div>
        <div class="mt-3 text-xs text-rose-500 flex items-center gap-1">
            <i class="fas fa-exclamation-triangle"></i>
            {{ $total_field_assigned - $total_field_completed }} Overdue Visits
        </div>
    </div>

    {{-- ── 4. Live Audits ── --}}
    <div class="bg-white rounded-3xl border border-violet-200/70 p-6 shadow-sm hover:shadow-md transition relative overflow-hidden">
        {{-- subtle background accent --}}
        <div class="absolute inset-0 bg-gradient-to-br from-violet-50/60 to-transparent pointer-events-none rounded-3xl"></div>

        <div class="relative flex items-start justify-between">
            <div>
                <span class="text-xs font-semibold text-violet-400 uppercase tracking-wider">Live Audits</span>
                <div class="text-3xl font-bold text-slate-800 mt-1 flex items-baseline gap-2">
                    <a href="{{ route('dmo.audits.live-audit.all') }}" class="hover:text-violet-600 transition">
                        {{ $total_live_audits }}
                    </a>
                    <span class="text-sm font-normal text-slate-400 ml-1">conducted</span>
                </div>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-violet-100 text-violet-600 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-hospital-user"></i>
            </div>
        </div>

        <div class="relative mt-5 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-slate-400">AI verified</p>
                <p class="text-xl font-semibold text-slate-800">{{ $total_live_ai_passed }}</p>
            </div>
            @php $lpc = $total_live_audits > 0 ? round(($total_live_ai_passed / $total_live_audits) * 100) : 0; @endphp
            <div class="flex-1 h-2 bg-violet-100 rounded-full overflow-hidden">
                <div class="h-full bg-violet-500 rounded-full transition-all" style="width:{{ $lpc }}%"></div>
            </div>
            <span class="text-sm font-semibold text-violet-600 shrink-0">{{ $lpc }}%</span>
        </div>

        <div class="relative mt-3 text-xs text-violet-500 flex items-center gap-1">
            <i class="fas fa-bolt"></i>
            Independent on-site verifications
        </div>
    </div>

</div>

@endsection

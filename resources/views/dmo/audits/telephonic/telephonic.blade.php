@extends('dmo.layout.layout')

@section('main_title')

<div class="flex items-center gap-2 text-sm text-slate-400 mb-5">
            <a href="{{ route('dmo.dashboard') }}" class="hover:text-emerald-600"><i class="fas fa-arrow-left mr-1 text-xs"></i> Back to Dashboard</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-600">Telephonic Audits Assigned</span>
        </div>

<div class="flex flex-wrap items-center justify-between gap-4 mb-7">
            <div>
                <h2 class="text-2xl md:text-3xl font-semibold text-slate-800 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-phone-volume text-emerald-600 text-2xl"></i>
                    Telephonic Audits Assigned
                </h2>
                <p class="text-sm text-slate-500 mt-1">Detailed list of Telephonic Audits Assigned</p>
            </div>
            
        </div>
@endsection
@section('main_content')
<!-- page heading + add new button -->
        

        <!-- filter bar (quick filter chips) -->
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <span class="text-sm text-slate-400">Filter:</span>
            <button class="bg-white border border-slate-200 hover:border-emerald-300 text-slate-700 text-xs px-4 py-2 rounded-full flex items-center gap-1 transition">All audits <span class="bg-slate-100 text-slate-600 ml-1 px-1.5 rounded-full text-[10px]">48</span></button>
            <button class="bg-white border border-slate-200 hover:border-emerald-300 text-slate-700 text-xs px-4 py-2 rounded-full flex items-center gap-1">Pending <span class="bg-amber-100 text-amber-700 ml-1 px-1.5 rounded-full text-[10px]">12</span></button>
            <button class="bg-white border border-slate-200 hover:border-emerald-300 text-slate-700 text-xs px-4 py-2 rounded-full flex items-center gap-1">Completed <span class="bg-emerald-100 text-emerald-700 ml-1 px-1.5 rounded-full text-[10px]">28</span></button>
            
            
        </div>

        <!-- ========== AUDIT TABLE with requested fields ========== -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <!-- table head -->
                    <thead class="bg-slate-50 text-slate-500 text-xs font-medium border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-left">Name</th>
                            <th class="px-6 py-4 text-left">Member ID</th>
                            <th class="px-6 py-4 text-left">Mobile number</th>
                            <th class="px-6 py-4 text-left">Hospital</th>
                            <th class="px-6 py-4 text-left">Preauth date</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($audits as $k => $v)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $v->treatment->patient_name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $v->treatment->member_id }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $v->treatment->ben_mobile_no }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $v->treatment->hospital->name }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $v->treatment->preauth_init_date }}</td>
                            
                            @if($v->status === 'completed')
                            <td class="px-6 py-4">
                                <span class="bg-emerald-100 text-emerald-700 text-xs px-3 py-1.5 rounded-full font-medium">
                                    Completed
                                </span>
                            </td>
                            @endif

                            @if($v->status === 'pending')
                            <td class="px-6 py-4">
                                <span class="bg-amber-100 text-amber-700 text-xs px-3 py-1.5 rounded-full">
                                    Pending
                                </span>
                            </td>
                            @endif

                            <td class="px-6 py-4 text-right">
                                <a href="{{route('dmo.audits.telephonic.view', $v->id) }}" class="text-slate-400 hover:text-emerald-600 mx-1"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @endforeach
                        
                    </tbody>
                </table>
            </div>

            <!-- table footer with pagination and summary -->
            <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-sm">
                <div class="text-slate-500">Showing 1–8 of 48 audits</div>
                <div class="flex items-center gap-3">
                    <button class="text-slate-400 hover:text-slate-700 w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white"><i class="fas fa-chevron-left text-xs"></i></button>
                    <span class="text-slate-600 text-sm">1 · 2 · 3 · 4 · 5</span>
                    <button class="text-slate-400 hover:text-slate-700 w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white"><i class="fas fa-chevron-right text-xs"></i></button>
                </div>
            </div>
        </div>
		@endsection
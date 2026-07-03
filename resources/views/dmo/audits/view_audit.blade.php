@extends('dmo.layout.layout')

@section('main_title')
<div class="flex items-center gap-2 text-sm text-slate-400 mb-5">
            <a href="{{ route('dmo.audits.all') }}" class="hover:text-emerald-600"><i class="fas fa-arrow-left mr-1 text-xs"></i> Back to audits</a>
        </div>
        <!-- page heading -->
        <div class="mb-7">
            <h2 class="text-2xl md:text-3xl font-semibold text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-clipboard-check text-emerald-600"></i>
                Case Details
            </h2>
        </div>
@endsection
@section('main_content') 
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mb-8">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center gap-2">
                <i class="fas fa-info-circle text-emerald-600"></i>
                <h3 class="font-medium text-slate-700">Case information · PMJAY pre‑auth summary</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-5 gap-x-8 text-sm">
                <!-- left column group (using flex to pair label:value) -->
                <div><span class="text-slate-400 text-xs block">Registration ID</span><span class="font-medium text-slate-800">{{ $audit->registration_id }}</span></div>
                <div><span class="text-slate-400 text-xs block">Case ID</span><span class="font-medium text-slate-800">{{ $audit->case_id }}</span></div>
                <div><span class="text-slate-400 text-xs block">Patient name</span><span class="font-medium text-slate-800">{{ $audit->patient_name }}</span></div>
                <div><span class="text-slate-400 text-xs block">Mobile number</span><span class="font-medium text-slate-800">{{ $audit->ben_mobile_no }}</span></div>

                <div><span class="text-slate-400 text-xs block">Adderss</span><span class="font-medium text-slate-800">{{ $audit->address }}</span></div>

                <div><span class="text-slate-400 text-xs block">PMJAY ID (ABHA)</span><span class="font-medium text-slate-800">4{{ $audit->member_id }}</span></div>
                <div><span class="text-slate-400 text-xs block">Hospital name</span><span class="font-medium text-slate-800">{{ $audit->hospital->name }}</span></div>
                <div><span class="text-slate-400 text-xs block">District</span><span class="font-medium text-slate-800">{{ $audit->patientDistrict->name }}</span></div>
                <div><span class="text-slate-400 text-xs block">Policy / scheme</span><span class="font-medium text-slate-800">{{ $audit->policy_code }}</span></div>
                <div><span class="text-slate-400 text-xs block">Preauth date</span><span class="font-medium text-slate-800">{{ $audit->preauth_init_date }}</span></div>
                <div><span class="text-slate-400 text-xs block">Procedure details</span><span class="font-medium text-slate-800">{{ $audit->procedure_details }}</span></div>
                <div><span class="text-slate-400 text-xs block">Category details</span><span class="font-medium text-slate-800">{{ $audit->category_details }}</span></div>
                <div><span class="text-slate-400 text-xs block">Preauth amount approved</span><span class="font-medium text-slate-800">₹ {{ $audit->amount_preauth_approved }}</span></div>

                
            </div>
        </div>
@endsection
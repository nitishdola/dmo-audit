@extends('dmo.layout.layout')

@section('main_title')
<div class="flex items-center gap-2 text-sm text-slate-400 mb-5">
            <a href="{{ route('dmo.audits.telephonic.all') }}" class="hover:text-emerald-600"><i class="fas fa-arrow-left mr-1 text-xs"></i> Back to audits</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-600">New observation</span>
        </div>

        <!-- page heading -->
        <div class="mb-7">
            <h2 class="text-2xl md:text-3xl font-semibold text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-clipboard-check text-emerald-600"></i>
                Record audit observation
            </h2>
            <p class="text-sm text-slate-500 mt-1">Case details & conclusion · PMJAY Assam pre‑authorisation audit</p>
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
                <div><span class="text-slate-400 text-xs block">Registration ID</span><span class="font-medium text-slate-800">{{ $audit->treatment->registration_id }}</span></div>
                <div><span class="text-slate-400 text-xs block">Case ID</span><span class="font-medium text-slate-800">{{ $audit->treatment->case_id }}</span></div>
                <div><span class="text-slate-400 text-xs block">Patient name</span><span class="font-medium text-slate-800">{{ $audit->treatment->patient_name }}</span></div>
                <div><span class="text-slate-400 text-xs block">Mobile number</span><span class="font-medium text-slate-800">{{ $audit->treatment->ben_mobile_no }}</span></div>
                <div><span class="text-slate-400 text-xs block">PMJAY ID (ABHA)</span><span class="font-medium text-slate-800">4{{ $audit->treatment->member_id }}</span></div>
                <div><span class="text-slate-400 text-xs block">Hospital name</span><span class="font-medium text-slate-800">{{ $audit->treatment->hospital->name }}</span></div>
                <div><span class="text-slate-400 text-xs block">District</span><span class="font-medium text-slate-800">{{ $audit->district->name }}</span></div>
                <div><span class="text-slate-400 text-xs block">Policy / scheme</span><span class="font-medium text-slate-800">{{ $audit->treatment->policy_code }}</span></div>
                <div><span class="text-slate-400 text-xs block">Preauth date</span><span class="font-medium text-slate-800">{{ $audit->treatment->preauth_init_date }}</span></div>
                <div><span class="text-slate-400 text-xs block">Procedure details</span><span class="font-medium text-slate-800">{{ $audit->treatment->procedure_details }}</span></div>
                <div><span class="text-slate-400 text-xs block">Category details</span><span class="font-medium text-slate-800">{{ $audit->treatment->category_details }}</span></div>
                <div><span class="text-slate-400 text-xs block">Preauth amount approved</span><span class="font-medium text-slate-800">₹ {{ $audit->treatment->amount_preauth_approved }}</span></div>
            </div>
        </div>

        @if($audit->status === 'completed')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mb-8">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center gap-2">
                <i class="fas fa-info-circle text-emerald-600"></i>
                <h3 class="font-medium text-slate-700">Telephonic Audit summary</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-5 gap-x-8 text-sm">
                <!-- left column group (using flex to pair label:value) -->
                <div><span class="text-slate-400 text-xs block">Audit Conlusion</span><span class="font-medium text-slate-800">{{ $audit->telephonicAudit->audit_conclusion->name }}</span></div>

                <div><span class="text-slate-400 text-xs block">Observation</span><span class="font-medium text-slate-800">{{ $audit->telephonicAudit->observation }}</span></div>
                
                
            </div>
        </div>

        @else
        <!-- ===== OBSERVATION FORM (textarea + conclusion dropdown) ===== -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 md:p-8">
            <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2 mb-6">
                <i class="fas fa-pen-to-square text-emerald-600"></i>
                Audit observation & conclusion
            </h3>

            
            <form class="space-y-7" method="POST" action="{{ route('dmo.audits.telephonic.store', $audit->id) }}">
                @csrf
                <!-- OBSERVATION textarea (full width) -->
                <div>
                    <label for="observation" class="text-sm font-medium text-slate-700 mb-2 flex items-center gap-1">
                        <span>Observation notes</span>
                        <span class="text-xs text-slate-400 font-normal">(detailed findings, discrepancies, comments)</span>
                    </label>
                    <textarea id="observation" name="observation" rows="5" class="block w-full rounded-xl border-2 border-slate-200 bg-slate-50/50 px-5 py-4 text-sm text-slate-700 placeholder-slate-400 focus:border-emerald-300 focus:ring-0 focus:outline-none transition" placeholder="Record your audit observation here – for example: documents verified, mismatch in diagnosis, beneficiary present, etc..."></textarea>
                </div>

                <!-- CONCLUSION dropdown (genuine / not genuine / suspicious / out of pocket expenditure) + optional note -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                    <div>
                        <label for="conclusion" class="block text-sm font-medium text-slate-700 mb-2 flex items-center gap-1">
                            <i class="fas fa-gavel text-xs text-slate-400"></i>
                            <span>Conclusion / audit decision</span>
                        </label>
                        <select id="audit_conclusion_id" name="audit_conclusion_id" class="block w-full rounded-xl border-2 border-slate-200 bg-slate-50/50 px-5 py-4 text-sm text-slate-700 focus:border-emerald-300 focus:ring-0 transition">
                            <option value="">– select one option –</option>

                            @foreach($conclusions as $conclusion)
                            <option value="{{ $conclusion->id }}">
                                {{ $conclusion->name }}
                            </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400 mt-3 flex items-center gap-1">
                            <!-- <i class="fas fa-info-circle text-emerald-500"></i>  -->
                            <!-- Selecting "out of pocket" will flag for reimbursement check. -->
                        </p>
                    </div>
                    
                </div>

                <!-- action buttons (submit / save draft) -->
                <div class="flex flex-wrap items-center gap-4 pt-6 border-t border-slate-200">
                    
                    <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm transition shadow-sm flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> Submit observation
                    </button>
                </div>
            </form>
        </div>
        @endif
@endsection
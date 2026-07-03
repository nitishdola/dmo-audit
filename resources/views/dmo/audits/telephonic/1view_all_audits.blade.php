@extends('dmo.layout.layout')

@section('main_title')

<div class="flex items-center gap-2 text-sm text-slate-400 mb-5">
            <a href="{{ route('dmo.dashboard') }}" class="hover:text-emerald-600"><i class="fas fa-arrow-left mr-1 text-xs"></i> Back to Dashboard</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-600"> Audits </span>
        </div>

<div class="flex flex-wrap items-center justify-between gap-4 mb-7">
            <div>
                <h2 class="text-2xl md:text-3xl font-semibold text-slate-800 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-phone-volume text-emerald-600 text-2xl"></i>
                    Beneficiary data for Audit
                </h2>
                <p class="text-sm text-slate-500 mt-1">Detailed list of Beneficiaries</p>
            </div>
            
        </div>
@endsection
@section('main_content')
<!-- page heading + add new button -->

        <!-- ========== AUDIT TABLE with requested fields ========== -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <!-- table head -->
                    <thead class="bg-slate-50 text-slate-500 text-xs font-medium border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-left">Name</th>
                            <th class="px-6 py-4 text-left">Member ID</th>
                            <th class="px-6 py-4 text-left">Hospital</th>
                            <th class="px-6 py-4 text-left">Preauth date</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($audits as $k => $v)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $v->patient_name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $v->ben_mobile_no }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $v->hospital->name }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $v->preauth_init_date }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{route('dmo.audits.telephonic.view', $v->id) }}" class="text-slate-400 hover:text-emerald-600 mx-1"><i class="fas fa-eye"></i></a>
                            </td>

                            <td class="px-1 py-4 text-right">
                                <a href="{{route('dmo.audits.telephonic.view', $v->id) }} "title="Conduct Telephonic Audit" class="text-slate-400 hover:text-emerald-600 mx-1"><i class="fa-solid fa-phone text-gray-800"></i></a>
                            
                                <a href="{{route('dmo.audits.telephonic.view', $v->id) }} "title="Conduct Medical Audit" class="text-slate-400 hover:text-emerald-600 mx-1">
                                    <i class="fa-solid fa-book-medical text-red-600"></i>
                                </a>

                                <a href="{{route('dmo.audits.medical.view', $v->id) }} "title="Conduct Home Visit" class="text-slate-400 hover:text-emerald-600 mx-1">
                                    <i class="fa-solid fa-house text-blue-950"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        
                    </tbody>
                </table>
            </div>

            
            
        </div>
		@endsection
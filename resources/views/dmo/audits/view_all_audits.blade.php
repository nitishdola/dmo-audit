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

<!-- ========== FILTER PANEL ========== -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 md:p-6 mb-5">
            <form method="GET" action="{{ route('dmo.audits.all') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">

                    <div>
                        <label class="filter-label">Registration ID</label>
                        <input type="text" name="registration_id" value="{{ request('registration_id') }}"
                            placeholder="Reg. ID" class="field-input">
                    </div>

                    <div>
                        <label class="filter-label">Member ID</label>
                        <input type="text" name="member_id" value="{{ request('member_id') }}"
                            placeholder="Member ID" class="field-input">
                    </div>

                    <div>
                        <label class="filter-label">Hospital District</label>
                        <select name="district_id" class="field-input">
                            <option value="">All districts</option>
                            @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ request('district_id') == $district->id ? 'selected' : '' }}>
                                {{ $district->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="filter-label">Hospital</label>
                        <select name="hospital_id" class="field-input">
                            <option value="">All hospitals</option>
                            @foreach($hospitals as $hospital)
                            <option value="{{ $hospital->id }}" {{ request('hospital_id') == $hospital->id ? 'selected' : '' }}>
                                {{ $hospital->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="filter-label">Patient District</label>
                        <select name="patient_district_id" class="field-input">
                            <option value="">All districts</option>
                            @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ request('patient_district_id') == $district->id ? 'selected' : '' }}>
                                {{ $district->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="filter-label">Preauth date from</label>
                        <input type="date" name="preauth_date_from" value="{{ request('preauth_date_from') }}" class="field-input">
                    </div>

                    <div>
                        <label class="filter-label">Preauth date to</label>
                        <input type="date" name="preauth_date_to" value="{{ request('preauth_date_to') }}" class="field-input">
                    </div>

                </div>

                <div class="flex items-center gap-2 mt-4">
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium flex items-center gap-2 transition">
                        <i class="fas fa-filter"></i> Apply filters
                    </button>
                    <a href="{{ route('dmo.audits.all') }}"
                    class="px-5 py-2.5 rounded-xl border-2 border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-medium flex items-center gap-2 transition">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
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
                                <a href="{{route('dmo.audits.view', $v->id) }}" class="text-slate-400 hover:text-emerald-600 mx-1"><i class="fas fa-eye"></i></a>
                            </td>

                            <td class="px-1 py-4 text-right">
                                <a href="{{route('dmo.audits.telephonic.view', $v->id) }} "title="Conduct Telephonic Audit" class="text-slate-400 hover:text-emerald-600 mx-1"><i class="fa-solid fa-phone text-gray-800"></i></a>
                            
                                <a href="{{route('dmo.audits.medical.view', $v->id) }} "title="Conduct Medical Audit" class="text-slate-400 hover:text-emerald-600 mx-1">
                                    <i class="fa-solid fa-book-medical text-red-600"></i>
                                </a>

                                <a href="{{route('dmo.audits.beneficiary_audit.view', $v->id) }} "title="Conduct Home Visit" class="text-slate-400 hover:text-emerald-600 mx-1">
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

        @section('pageCss')
<style>
    .field-input { width:100%; padding:.6rem .8rem; border:2px solid #e2e8f0; border-radius:.75rem; background:#f8fafc; font-size:.85rem; color:#1e293b; transition:border-color .15s,background .15s; outline:none; }
    .field-input:focus { border-color:#34d399; background:#fff; }
    select.field-input { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right .7rem center; background-size:1.05em; padding-right:2.2rem; }
    .filter-label { font-size:.7rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase; color:#94a3b8; margin-bottom:.35rem; display:block; }
</style>
@endsection
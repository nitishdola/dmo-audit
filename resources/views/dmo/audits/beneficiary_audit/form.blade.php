@extends('dmo.layout.layout')

@section('main_title')

<div class="flex items-center gap-2 text-sm text-slate-400 mb-5">
    <a href="{{ route('dmo.audits.all') }}" class="hover:text-emerald-600 transition-colors">
        <i class="fas fa-arrow-left mr-1 text-xs"></i> Back to audits
    </a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-600 font-medium">Beneficiary Audit</span>
</div>

<div class="mb-7">
    <h2 class="text-xl md:text-3xl font-semibold text-slate-800 tracking-tight flex items-center gap-2">
        <i class="fas fa-user-check text-emerald-600"></i>
        Beneficiary Audit
    </h2>
    <p class="text-sm text-slate-500 mt-1">
        Annexure 1 · Ayushman Bharat PM-JAY beneficiary verification
    </p>
</div>

@endsection

@section('pageCss')
<style>
    /* ── Toast notifications ── */
    #toast-container { position: fixed; top: 1.25rem; right: 1.25rem; z-index: 9999; display: flex; flex-direction: column; gap: .625rem; pointer-events: none; max-width: min(420px, calc(100vw - 2.5rem)); }
    .toast { pointer-events: auto; display: flex; align-items: flex-start; gap: .75rem; padding: .875rem 1rem; border-radius: .875rem; border: 1.5px solid transparent; box-shadow: 0 8px 24px rgba(0,0,0,.14), 0 2px 6px rgba(0,0,0,.08); font-size: .8125rem; font-weight: 500; line-height: 1.45; backdrop-filter: blur(6px); transform: translateX(120%); opacity: 0; transition: transform .32s cubic-bezier(.22,1,.36,1), opacity .28s ease; cursor: default; position: relative; overflow: hidden; }
    .toast.toast-in  { transform: translateX(0); opacity: 1; }
    .toast.toast-out { transform: translateX(120%); opacity: 0; transition: transform .25s ease-in, opacity .22s ease-in; }
    .toast-error   { background: rgba(255,241,242,.97); border-color: #fda4af; color: #9f1239; }
    .toast-success { background: rgba(240,253,244,.97); border-color: #86efac; color: #14532d; }
    .toast-warning { background: rgba(255,251,235,.97); border-color: #fde68a; color: #78350f; }
    .toast-info    { background: rgba(239,246,255,.97); border-color: #93c5fd; color: #1e3a8a; }
    .toast-icon { width: 1.75rem; height: 1.75rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: .8rem; margin-top: .05rem; }
    .toast-error   .toast-icon { background: #fee2e2; color: #dc2626; }
    .toast-success .toast-icon { background: #dcfce7; color: #16a34a; }
    .toast-warning .toast-icon { background: #fef3c7; color: #d97706; }
    .toast-info    .toast-icon { background: #dbeafe; color: #2563eb; }
    .toast-body  { flex: 1; min-width: 0; }
    .toast-title { font-weight: 700; font-size: .8125rem; margin-bottom: .15rem; }
    .toast-msg ul { margin: .25rem 0 0 .1rem; padding-left: 1rem; list-style: disc; display: flex; flex-direction: column; gap: .2rem; }
    .toast-close { flex-shrink: 0; background: none; border: none; cursor: pointer; opacity: .45; font-size: .75rem; padding: .1rem .2rem; margin-top: .05rem; transition: opacity .15s; line-height: 1; color: inherit; }
    .toast-close:hover { opacity: .9; }
    .toast-progress { position: absolute; bottom: 0; left: 0; height: 3px; border-radius: 0 0 .875rem .875rem; animation: toast-drain linear forwards; }
    .toast-error   .toast-progress { background: #f43f5e; }
    .toast-success .toast-progress { background: #22c55e; }
    .toast-warning .toast-progress { background: #f59e0b; }
    .toast-info    .toast-progress { background: #3b82f6; }
    @keyframes toast-drain { from { width: 100%; } to { width: 0%; } }

    /* ── Radio pill ── */
    .radio-group { display:inline-flex; border-radius:9999px; overflow:hidden; border:2px solid #e2e8f0; background:#f8fafc; }
    .radio-group input[type="radio"] { display:none; }
    .radio-group label { padding:.5rem 1.1rem; font-size:.8rem; font-weight:500; color:#64748b; cursor:pointer; transition:background .18s,color .18s; user-select:none; white-space:nowrap; }
    .radio-group label:first-of-type { border-right:1px solid #e2e8f0; }
    .radio-group input[value="Yes"]:checked + label { background:#059669; color:#fff; }
    .radio-group input[value="No"]:checked  + label { background:#e11d48; color:#fff; }
    .radio-group input[value="NA"]:checked  + label { background:#64748b; color:#fff; }

    /* ── Obs cards ── */
    .obs-card { background:#fff; border:1px solid #e2e8f0; border-radius:1rem; padding:1rem 1.125rem; display:flex; flex-direction:column; gap:.75rem; }
    .obs-card + .obs-card { margin-top:.625rem; }
    .obs-label { font-size:.875rem; font-weight:600; color:#334155; line-height:1.4; }
    .obs-sub   { font-size:.75rem; color:#64748b; margin-top:.125rem; line-height:1.5; }
    .obs-row   { display:flex; align-items:flex-start; gap:.75rem; flex-wrap:wrap; }

    /* ── Inputs ── */
    .field-input { width:100%; padding:.65rem .875rem; border:2px solid #e2e8f0; border-radius:.75rem; background:#f8fafc; font-size:.9rem; color:#1e293b; transition:border-color .15s,background .15s; outline:none; }
    .field-input:focus { border-color:#34d399; background:#fff; }
    textarea.field-input { resize:vertical; min-height:80px; }
    select.field-input { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right .75rem center; background-size:1.1em; padding-right:2.25rem; }

    /* ── Section badge ── */
    .section-badge { display:flex; align-items:center; gap:.5rem; font-size:.7rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#94a3b8; margin:1.5rem 0 .625rem; }
    .section-badge::before,.section-badge::after { content:''; flex:1; height:1px; background:#e2e8f0; }

    /* ── Members table rows ── */
    .member-row { display:grid; grid-template-columns:1fr 1fr 100px 80px 1fr auto; gap:.5rem; align-items:start; background:#f8fafc; border:1px solid #e2e8f0; border-radius:.875rem; padding:.75rem .875rem; }
    .member-row:focus-within { border-color:#34d399; background:#fff; }
    .member-row + .member-row { margin-top:.5rem; }
    .member-row .field-input { padding:.5rem .65rem; font-size:.8125rem; }
    .btn-remove-member { width:2rem; height:2rem; border-radius:9999px; display:flex; align-items:center; justify-content:center; background:#fee2e2; color:#991b1b; border:none; cursor:pointer; flex-shrink:0; margin-top:.15rem; transition:background .15s; }
    .btn-remove-member:hover { background:#fecaca; }
    #btn-add-member { display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.2rem; border-radius:9999px; font-size:.8rem; font-weight:600; cursor:pointer; border:2px dashed #94a3b8; background:transparent; color:#64748b; transition:all .18s; margin-top:.5rem; }
    #btn-add-member:hover { border-color:#059669; color:#059669; background:#f0fdf4; }
    .member-header { display:grid; grid-template-columns:1fr 1fr 100px 80px 1fr auto; gap:.5rem; padding:0 .875rem; font-size:.68rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase; color:#94a3b8; margin-bottom:.4rem; }

    @media (max-width: 720px) {
        .member-row, .member-header { grid-template-columns: 1fr; }
        .member-header { display:none; }
    }
</style>
@endsection

@section('main_content')

<form id="beneficiaryAuditForm" method="POST" action="{{ route('dmo.audits.beneficiary_audit.store', $audit->id) }}">
    @csrf

    @if ($errors->any())
    <div class="obs-card" style="border:2px solid #fda4af; background:#fff1f2; margin-bottom:1.25rem;">
        <div class="flex items-start gap-3">
            <div class="h-8 w-8 rounded-full bg-rose-100 flex items-center justify-center shrink-0">
                <i class="fas fa-exclamation-triangle text-rose-600 text-sm"></i>
            </div>
            <div>
                <span class="obs-label" style="color:#9f1239;">Please fix the following before submitting</span>
                <ul class="text-xs text-rose-700 mt-1.5" style="list-style:disc; padding-left:1.1rem; display:flex; flex-direction:column; gap:.2rem;">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-md p-4 md:p-8">
        <h3 class="text-base md:text-lg font-semibold text-slate-800 flex items-center gap-2 mb-5">
            <div class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                <i class="fas fa-id-card text-emerald-600 text-sm"></i>
            </div>
            Patient Information
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="obs-card">
                <span class="obs-label">PM-JAY Family ID <span class="text-rose-500">*</span></span>
                <input type="text" name="pmjay_family_id" value="{{ old('pmjay_family_id') }}" placeholder="Enter PM-JAY Family ID"
                       class="field-input @error('pmjay_family_id') border-rose-400 bg-rose-50 @enderror" required>
                @error('pmjay_family_id') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>
            <div class="obs-card">
                <span class="obs-label">Name <span class="text-rose-500">*</span></span>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Beneficiary's full name"
                       class="field-input @error('name') border-rose-400 bg-rose-50 @enderror" required>
                @error('name') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>
            <div class="obs-card">
                <span class="obs-label">Father's or Husband's Name <span class="text-rose-500">*</span></span>
                <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" placeholder="Enter name"
                       class="field-input @error('guardian_name') border-rose-400 bg-rose-50 @enderror" required>
                @error('guardian_name') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>
            <div class="obs-card">
                <span class="obs-label">Contact No. <span class="text-rose-500">*</span></span>
                <input type="text" name="contact_no" value="{{ old('contact_no') }}" placeholder="10-digit mobile number" maxlength="10" inputmode="numeric"
                       class="field-input @error('contact_no') border-rose-400 bg-rose-50 @enderror" required>
                @error('contact_no') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>

            <div class="obs-card sm:col-span-2">
                <span class="obs-label">Address <span class="text-rose-500">*</span></span>
                <textarea name="address" rows="2" placeholder="Full address"
                          class="field-input @error('address') border-rose-400 bg-rose-50 @enderror" required>{{ old('address') }}</textarea>
                @error('address') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>

            <div class="obs-card">
                <span class="obs-label">District <span class="text-rose-500">*</span></span>
                <select name="district_id" class="field-input @error('district_id') border-rose-400 bg-rose-50 @enderror" required>
                    <option value="">Select district</option>
                    @foreach($districts as $district)
                    <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                    @endforeach
                </select>
                @error('district_id') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>
            <div class="obs-card">
                <span class="obs-label">State <span class="text-rose-500">*</span></span>
                <input type="text" name="state" value="{{ old('state', 'Assam') }}" placeholder="State"
                       class="field-input @error('state') border-rose-400 bg-rose-50 @enderror" required>
                @error('state') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>
            <div class="obs-card">
                <span class="obs-label">Pin Code <span class="text-rose-500">*</span></span>
                <input type="text" name="pin_code" value="{{ old('pin_code') }}" placeholder="6-digit PIN code" maxlength="6" inputmode="numeric"
                       class="field-input @error('pin_code') border-rose-400 bg-rose-50 @enderror" required>
                @error('pin_code') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Members registered — optional --}}
        <div class="section-badge"><span>Members Registered <span class="normal-case font-normal text-slate-400">(optional)</span></span></div>
        <div class="obs-card">
            <p class="obs-sub">Add family members registered under this PM-JAY ID, if applicable. This section is not mandatory.</p>
            <div class="member-header">
                <span>Name</span><span>PM-JAY ID Number</span><span>Gender</span><span>Age</span><span>Relationship</span><span></span>
            </div>
            <div id="member-list"></div>
            <div><button type="button" id="btn-add-member"><i class="fas fa-plus-circle"></i> Add member</button></div>
        </div>

        {{-- ── General Information ── --}}
        <div class="section-badge"><span>General Information</span></div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="obs-card sm:col-span-2">
                <span class="obs-label">Where was the E-card made? <span class="text-rose-500">*</span></span>
                <input type="text" name="ecard_made_at" value="{{ old('ecard_made_at') }}" placeholder="e.g. CSC, Hospital, Ayushman Mitra camp"
                       class="field-input @error('ecard_made_at') border-rose-400 bg-rose-50 @enderror" required>
                @error('ecard_made_at') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>

            <div class="obs-card">
                <span class="obs-label">If hospital, was the beneficiary charged any money for the E-card? <span class="text-rose-500">*</span></span>
                <div class="obs-row">
                    <div class="radio-group" id="ecard-charged-group">
                        <input type="radio" name="ecard_charged" id="ecard_charged_yes" value="Yes" {{ old('ecard_charged') === 'Yes' ? 'checked' : '' }}>
                        <label for="ecard_charged_yes">Yes</label>
                        <input type="radio" name="ecard_charged" id="ecard_charged_no" value="No" {{ old('ecard_charged') === 'No' ? 'checked' : '' }}>
                        <label for="ecard_charged_no">No</label>
                    </div>
                </div>
                @error('ecard_charged') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>
            <div class="obs-card" id="ecard-amount-wrapper" style="display:none;">
                <span class="obs-label">If yes, how much? <span class="text-rose-500" id="ecard-amount-star">*</span></span>
                <input type="number" step="0.01" min="0" name="ecard_charge_amount" id="ecard_charge_amount" value="{{ old('ecard_charge_amount') }}" placeholder="Amount in ₹"
                       class="field-input @error('ecard_charge_amount') border-rose-400 bg-rose-50 @enderror">
                @error('ecard_charge_amount') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>

            <div class="obs-card">
                <span class="obs-label">Has s/he availed services under PM-JAY? <span class="text-rose-500">*</span></span>
                <div class="obs-row">
                    <div class="radio-group" id="availed-services-group">
                        <input type="radio" name="availed_services" id="availed_yes" value="Yes" {{ old('availed_services') === 'Yes' ? 'checked' : '' }}>
                        <label for="availed_yes">Yes</label>
                        <input type="radio" name="availed_services" id="availed_no" value="No" {{ old('availed_services') === 'No' ? 'checked' : '' }}>
                        <label for="availed_no">No</label>
                    </div>
                </div>
                @error('availed_services') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Conditional block — only meaningful when availed_services = Yes; full width, sibling of the grid above --}}
        <div id="services-dependent-section" style="display:none;">

            <p class="text-xs text-slate-400 italic mt-2 mb-1">
                Note: for questions 4–11, match the information provided by the beneficiary against what is recorded in the TMS.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                <div class="obs-card">
                    <span class="obs-label">In which hospital did s/he utilize the services? <span class="text-rose-500">*</span></span>
                    <select name="hospital_id" class="field-input @error('hospital_id') border-rose-400 bg-rose-50 @enderror">
                        <option value="">Select hospital</option>
                        @foreach($hospitals as $hospital)
                        <option value="{{ $hospital->id }}" {{ old('hospital_id') == $hospital->id ? 'selected' : '' }}>{{ $hospital->name }}</option>
                        @endforeach
                    </select>
                    @error('hospital_id') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <div class="obs-card">
                    <span class="obs-label">Was s/he provided free food? <span class="text-rose-500">*</span></span>
                    <div class="obs-row">
                        <div class="radio-group">
                            <input type="radio" name="free_food" id="free_food_yes" value="Yes" {{ old('free_food') === 'Yes' ? 'checked' : '' }}>
                            <label for="free_food_yes">Yes</label>
                            <input type="radio" name="free_food" id="free_food_no" value="No" {{ old('free_food') === 'No' ? 'checked' : '' }}>
                            <label for="free_food_no">No</label>
                        </div>
                    </div>
                    @error('free_food') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <div class="obs-card sm:col-span-2">
                    <span class="obs-label">What symptoms was the patient exhibiting when s/he visited the hospital? <span class="text-rose-500">*</span></span>
                    <textarea name="symptoms" rows="2" placeholder="Describe symptoms"
                              class="field-input @error('symptoms') border-rose-400 bg-rose-50 @enderror">{{ old('symptoms') }}</textarea>
                    @error('symptoms') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <div class="obs-card">
                    <span class="obs-label">When did s/he get admitted? <span class="text-rose-500">*</span></span>
                    <input type="date" name="admission_date" id="admission_date" value="{{ old('admission_date') }}"
                           class="field-input @error('admission_date') border-rose-400 bg-rose-50 @enderror">
                    @error('admission_date') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>
                <div class="obs-card">
                    <span class="obs-label">When did s/he get discharged? <span class="text-rose-500">*</span></span>
                    <input type="date" name="discharge_date" id="discharge_date" value="{{ old('discharge_date') }}"
                           class="field-input @error('discharge_date') border-rose-400 bg-rose-50 @enderror">
                    @error('discharge_date') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <div class="obs-card">
                    <span class="obs-label">For how many days was s/he hospitalized? <span class="text-rose-500">*</span></span>
                    <input type="number" min="1" name="days_hospitalized" value="{{ old('days_hospitalized') }}" placeholder="Number of days"
                           class="field-input @error('days_hospitalized') border-rose-400 bg-rose-50 @enderror">
                    @error('days_hospitalized') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <div class="obs-card sm:col-span-2">
                    <span class="obs-label">What was the treatment given? <span class="text-rose-500">*</span></span>
                    <textarea name="treatment_given" rows="3" placeholder="Describe treatment given"
                              class="field-input @error('treatment_given') border-rose-400 bg-rose-50 @enderror">{{ old('treatment_given') }}</textarea>
                    @error('treatment_given') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <div class="obs-card sm:col-span-2">
                    <span class="obs-label">If any surgery, is there a scar on the body which could help verify the surgery? <span class="text-rose-500">*</span></span>
                    <p class="obs-sub">If yes, take a photograph of the same for the case file.</p>
                    <div class="obs-row">
                        <div class="radio-group">
                            <input type="radio" name="surgery_scar" id="scar_yes" value="Yes" {{ old('surgery_scar') === 'Yes' ? 'checked' : '' }}>
                            <label for="scar_yes">Yes</label>
                            <input type="radio" name="surgery_scar" id="scar_no" value="No" {{ old('surgery_scar') === 'No' ? 'checked' : '' }}>
                            <label for="scar_no">No</label>
                            <input type="radio" name="surgery_scar" id="scar_na" value="NA" {{ old('surgery_scar') === 'NA' ? 'checked' : '' }}>
                            <label for="scar_na">N/A</label>
                        </div>
                        <input type="text" name="surgery_scar_remarks" value="{{ old('surgery_scar_remarks') }}" placeholder="Remarks (optional)" class="field-input" style="flex:1;min-width:160px;">
                    </div>
                    @error('surgery_scar') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- Photo match — always visible, independent of availed_services --}}
            <div class="section-badge"><span>Photo Verification</span></div>
            <div class="obs-card">
                <span class="obs-label">Match the photo of the beneficiary being interviewed with the one submitted in TMS <span class="text-rose-500">*</span></span>
                <div class="obs-row">
                    <div class="radio-group">
                        <input type="radio" name="photo_match" id="photo_match_yes" value="Yes" {{ old('photo_match') === 'Yes' ? 'checked' : '' }}>
                        <label for="photo_match_yes">Yes</label>
                        <input type="radio" name="photo_match" id="photo_match_no" value="No" {{ old('photo_match') === 'No' ? 'checked' : '' }}>
                        <label for="photo_match_no">No</label>
                        <input type="radio" name="photo_match" id="photo_match_na" value="NA" {{ old('photo_match') === 'NA' ? 'checked' : '' }}>
                        <label for="photo_match_na">N/A</label>
                    </div>
                </div>
                @error('photo_match') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>
        </div>

        

        {{-- Remarks --}}
        <div class="section-badge"><span>Auditor Remarks</span></div>
        <div class="obs-card">
            <span class="obs-label">Any other remark or observation</span>
            <textarea name="other_remarks" rows="3" placeholder="Optional additional observations…"
                      class="field-input @error('other_remarks') border-rose-400 bg-rose-50 @enderror">{{ old('other_remarks') }}</textarea>
            @error('other_remarks') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
        </div>
        <div class="obs-card">
            <span class="obs-label">Recommendation of the Auditor <span class="text-rose-500">*</span></span>
            <textarea name="recommendation" rows="4" placeholder="Auditor's recommendation…"
                      class="field-input @error('recommendation') border-rose-400 bg-rose-50 @enderror" required>{{ old('recommendation') }}</textarea>
            @error('recommendation') <p class="text-rose-600 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
        </div>

        {{-- Submit row --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-6 mt-6 border-t border-slate-200">
            <button type="submit" id="btn-submit"
                    class="flex-1 sm:flex-none px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 disabled:bg-slate-300 disabled:cursor-not-allowed disabled:shadow-none text-white font-medium text-sm flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition-all">
                <i class="fas fa-check-circle"></i> Submit Beneficiary Audit report
            </button>
            <button type="reset"
                    class="flex-1 sm:flex-none px-5 py-3.5 rounded-xl border-2 border-slate-300 text-slate-600 hover:bg-slate-50 active:bg-slate-100 font-medium text-sm flex items-center justify-center gap-2 transition">
                <i class="fas fa-undo-alt"></i> Reset
            </button>
        </div>
    </div>
</form>

<div class="text-xs text-slate-400 text-center mt-6 border-t border-slate-200 pt-5">
    <i class="fas fa-shield-alt text-emerald-500 mr-1"></i>
    All Beneficiary Audit entries are logged with DMO credentials · PMJAY Assam
</div>

<div id="toast-container"></div>

<script>
function toast(type, title, message, duration) {
    duration = duration ?? (type === 'error' ? 7000 : 4500);
    const icons = { error: 'fa-times-circle', success: 'fa-check-circle', warning: 'fa-exclamation-circle', info: 'fa-info-circle' };
    const sentences = (message || '').split(/(?<=\.)\s+/).map(s => s.trim()).filter(Boolean);
    const msgHtml = sentences.length > 1
        ? '<ul>' + sentences.map(s => `<li>${s}</li>`).join('') + '</ul>'
        : `<span>${sentences[0] ?? ''}</span>`;

    const el = document.createElement('div');
    el.className = `toast toast-${type}`;
    el.innerHTML = `
        <div class="toast-icon"><i class="fas ${icons[type] ?? 'fa-info-circle'}"></i></div>
        <div class="toast-body">
            <div class="toast-title">${title}</div>
            <div class="toast-msg">${msgHtml}</div>
        </div>
        <button class="toast-close" title="Dismiss"><i class="fas fa-times"></i></button>
        <div class="toast-progress" style="animation-duration:${duration}ms;"></div>
    `;
    document.getElementById('toast-container').appendChild(el);
    requestAnimationFrame(() => requestAnimationFrame(() => el.classList.add('toast-in')));
    const dismiss = () => {
        el.classList.remove('toast-in');
        el.classList.add('toast-out');
        el.addEventListener('transitionend', () => el.remove(), { once: true });
    };
    const timer = setTimeout(dismiss, duration);
    el.querySelector('.toast-close').addEventListener('click', () => { clearTimeout(timer); dismiss(); });
}

(function () {
    const form = document.getElementById('beneficiaryAuditForm');

    /* ═══ Members (optional, repeatable) ═══ */
    const memberList = document.getElementById('member-list');
    const btnAddMember = document.getElementById('btn-add-member');
    let memberCount = 0;

    function addMemberRow() {
        const idx = memberCount++;
        const row = document.createElement('div');
        row.className = 'member-row';
        row.innerHTML = `
            <input type="text" name="members[${idx}][name]" placeholder="Name" class="field-input">
            <input type="text" name="members[${idx}][pmjay_id_number]" placeholder="PM-JAY ID number" class="field-input">
            <select name="members[${idx}][gender]" class="field-input">
                <option value="">Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <input type="number" min="0" max="120" name="members[${idx}][age]" placeholder="Age" class="field-input">
            <input type="text" name="members[${idx}][relationship]" placeholder="Relationship" class="field-input">
            <button type="button" class="btn-remove-member" title="Remove"><i class="fas fa-times text-xs"></i></button>
        `;
        row.querySelector('.btn-remove-member').addEventListener('click', () => row.remove());
        memberList.appendChild(row);
    }
    btnAddMember.addEventListener('click', addMemberRow);

    /* ═══ E-card charge amount toggle ═══ */
    const ecardAmountWrapper = document.getElementById('ecard-amount-wrapper');
    const ecardAmountInput = document.getElementById('ecard_charge_amount');
    function applyEcardAmountVisibility() {
        const chargedYes = document.getElementById('ecard_charged_yes');
        const show = chargedYes && chargedYes.checked;
        ecardAmountWrapper.style.display = show ? 'flex' : 'none';
        if (!show) ecardAmountInput.value = '';
    }
    document.querySelectorAll('input[name="ecard_charged"]').forEach(r => r.addEventListener('change', applyEcardAmountVisibility));
    applyEcardAmountVisibility(); // handle old() after validation error

    /* ═══ Services-dependent section toggle ═══ */
    const servicesSection = document.getElementById('services-dependent-section');
    const SERVICE_DEPENDENT_FIELDS = [
        'hospital_id', 'symptoms', 'admission_date', 'discharge_date',
        'days_hospitalized', 'free_food', 'treatment_given', 'surgery_scar', 'surgery_scar_remarks', 'photo_match'
    ];

    function applyServicesVisibility() {
        const availedYes = document.getElementById('availed_yes');
        const show = availedYes && availedYes.checked;
        servicesSection.style.display = show ? 'block' : 'none';

        if (!show) {
            SERVICE_DEPENDENT_FIELDS.forEach(name => {
                servicesSection.querySelectorAll(`[name="${name}"]`).forEach(el => {
                    if (el.type === 'radio') el.checked = false;
                    else el.value = '';
                });
            });
        }
    }
    document.querySelectorAll('input[name="availed_services"]').forEach(r => r.addEventListener('change', applyServicesVisibility));
    applyServicesVisibility(); // handle old() after validation error

    /* ═══ Form validation ═══ */
    form.addEventListener('submit', function (e) {
        let blocked = false;
        let firstInvalid = null;
        function flag(el) { if (!firstInvalid) firstInvalid = el; blocked = true; }

        // always-required fields
        ['pmjay_family_id','name','guardian_name','contact_no','address','district_id','state','pin_code',
         'ecard_made_at','recommendation'].forEach(n => {
            const el = form.querySelector(`[name="${n}"]`);
            if (el && !el.value.trim()) flag(el);
        });

        

        if (!document.querySelector('input[name="availed_services"]:checked')) {
            flag(document.getElementById('availed-services-group'));
        }

        // conditional ecard amount
        const chargedYes = document.getElementById('ecard_charged_yes');
        if (chargedYes && chargedYes.checked && !ecardAmountInput.value.trim()) {
            flag(ecardAmountInput);
            toast('error', 'Amount Required', 'Please enter the amount charged for the E-card.');
        }

        // conditional services-dependent fields
        const availedYes = document.getElementById('availed_yes');
        if (availedYes && availedYes.checked) {
            ['hospital_id','symptoms','admission_date','discharge_date','days_hospitalized','treatment_given'].forEach(n => {
                const el = servicesSection.querySelector(`[name="${n}"]`);
                if (el && !el.value.trim()) flag(el);
            });
            ['free_food','surgery_scar','photo_match'].forEach(n => {
                if (!servicesSection.querySelector(`input[name="${n}"]:checked`)) flag(servicesSection.querySelector(`input[name="${n}"]`));
            });

            const adm = document.getElementById('admission_date').value;
            const dis = document.getElementById('discharge_date').value;
            if (adm && dis && dis < adm) {
                flag(document.getElementById('discharge_date'));
                toast('error', 'Invalid Dates', 'Discharge date cannot be before the admission date.');
            }
        }

        if (blocked) {
            e.preventDefault();
            if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            toast('error', 'Missing Information', 'Please complete all required fields before submitting.');
        }
    });
})();
</script>

@endsection
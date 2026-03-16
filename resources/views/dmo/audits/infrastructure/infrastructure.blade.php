@extends('dmo.layout.layout')

@section('main_title')
<div class="flex items-center gap-2 text-sm text-slate-400 mb-5">
    <a href="{{ route('dmo.dashboard') }}" class="hover:text-emerald-600 transition-colors">
        <i class="fas fa-arrow-left mr-1 text-xs"></i> Dashboard
    </a>
    <span class="text-slate-300">/</span>
    <a href="{{ route('dmo.audits.infra-audit.index') }}" class="hover:text-emerald-600 transition-colors">Infrastructure Audits</a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-600 font-medium">New Audit</span>
</div>
<div class="mb-7">
    <h2 class="text-xl md:text-3xl font-semibold text-slate-800 tracking-tight flex items-center gap-2">
        <i class="fas fa-building-columns text-emerald-600"></i>
        New Infrastructure Audit
    </h2>
    <p class="text-sm text-slate-500 mt-1">
        Annexure 2.2 · Hospital Infrastructure &amp; Human Resource · Sections A – C
    </p>
</div>
@endsection

@section('pageCss')
<style>
    /* ── Radio pill group (Yes / No / NA) ── */
    .radio-group { display:inline-flex; border-radius:9999px; overflow:hidden; border:2px solid #e2e8f0; background:#f8fafc; }
    .radio-group input[type="radio"] { display:none; }
    .radio-group label { padding:.5rem 1.1rem; font-size:.8rem; font-weight:500; color:#64748b; cursor:pointer; transition:background .18s,color .18s; user-select:none; white-space:nowrap; border-right:1px solid #e2e8f0; }
    .radio-group label:last-child { border-right:none; }
    .radio-group input[value="Yes"]:checked + label { background:#059669; color:#fff; }
    .radio-group input[value="No"]:checked  + label { background:#e11d48; color:#fff; }
    .radio-group input[value="NA"]:checked  + label { background:#64748b; color:#fff; }

    /* ── Obs cards ── */
    .obs-card { background:#fff; border:1.5px solid #e2e8f0; border-radius:1rem; padding:.9rem 1.1rem; }
    .obs-card + .obs-card { margin-top:.5rem; }
    .obs-card:hover { border-color:#cbd5e1; }
    .obs-top { display:flex; align-items:flex-start; justify-content:space-between; gap:.75rem; flex-wrap:wrap; }
    .obs-label { font-size:.845rem; font-weight:600; color:#334155; line-height:1.45; flex:1; min-width:160px; }
    .obs-num { font-size:.7rem; font-weight:700; color:#94a3b8; background:#f1f5f9; padding:.2rem .55rem; border-radius:9999px; flex-shrink:0; margin-top:.1rem; }
    .obs-remark { margin-top:.625rem; }
    .obs-remark input { width:100%; padding:.5rem .75rem; border:1.5px solid #e2e8f0; border-radius:.625rem; font-size:.8rem; color:#475569; background:#f8fafc; outline:none; font-family:inherit; transition:border-color .15s; }
    .obs-remark input:focus { border-color:#34d399; background:#fff; }

    /* Sub-items (A, B, C …) */
    .sub-items { margin-top:.625rem; display:flex; flex-direction:column; gap:.375rem; padding-left:.5rem; border-left:2px solid #e2e8f0; }
    .sub-item { display:flex; align-items:center; justify-content:space-between; gap:.5rem; padding:.45rem .625rem; border-radius:.5rem; background:#f8fafc; }
    .sub-label { font-size:.775rem; font-weight:500; color:#64748b; flex:1; }
    .sub-alpha { font-size:.65rem; font-weight:700; color:#94a3b8; background:#e2e8f0; width:1.3rem; height:1.3rem; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }

    /* Inputs */
    .field-input { width:100%; padding:.65rem .875rem; border:2px solid #e2e8f0; border-radius:.75rem; background:#f8fafc; font-size:.9rem; color:#1e293b; transition:border-color .15s,background .15s; outline:none; font-family:inherit; }
    .field-input:focus { border-color:#34d399; background:#fff; }
    textarea.field-input { resize:vertical; min-height:70px; }
    .num-input  { width:5rem; padding:.42rem .65rem; border:2px solid #e2e8f0; border-radius:.625rem; font-size:.8rem; background:#f8fafc; outline:none; font-family:inherit; transition:border-color .15s; text-align:center; }
    .num-input:focus { border-color:#34d399; background:#fff; }
    .sm-select  { padding:.42rem .75rem; border:2px solid #e2e8f0; border-radius:.625rem; font-size:.78rem; background:#f8fafc; outline:none; font-family:inherit; transition:border-color .15s; cursor:pointer; color:#334155; }
    .sm-select:focus { border-color:#34d399; }

    /* Section dividers */
    .section-badge { display:flex; align-items:center; gap:.5rem; font-size:.65rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#94a3b8; margin:1.5rem 0 .75rem; }
    .section-badge::before,.section-badge::after { content:''; flex:1; height:1px; background:#e2e8f0; }

    /* Badges */
    .badge-ai  { display:inline-flex; align-items:center; gap:.3rem; background:#ede9fe; color:#5b21b6; font-size:.62rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; padding:.22rem .55rem; border-radius:9999px; }
    .badge-req { display:inline-flex; align-items:center; gap:.25rem; background:#fef3c7; color:#92400e; font-size:.62rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; padding:.22rem .55rem; border-radius:9999px; }

    /* AI upload box */
    .ai-upload-box { border:2px dashed #e2e8f0; border-radius:1rem; padding:1.5rem; text-align:center; cursor:pointer; transition:border-color .2s,background .2s; background:#f8fafc; position:relative; overflow:hidden; }
    .ai-upload-box:hover { border-color:#34d399; background:#f0fdf4; }
    .ai-upload-box.drag-over { border-color:#059669; background:#ecfdf5; }
    .ai-upload-box input[type="file"] { position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer; }

    /* AI result box */
    .ai-result-box { margin-top:.875rem; display:none; border-radius:.875rem; padding:1rem 1.1rem; }
    .ai-result-box.ai-pass    { background:#f0fdf4; border:1.5px solid #86efac; }
    .ai-result-box.ai-fail    { background:#fff1f2; border:1.5px solid #fda4af; }
    .ai-result-box.ai-loading { background:#f8fafc; border:1.5px solid #e2e8f0; }
    .ai-result-title { font-size:.825rem; font-weight:700; display:flex; align-items:center; gap:.4rem; margin-bottom:.3rem; }
    .ai-result-body  { font-size:.775rem; color:#475569; line-height:1.5; }
    .ai-spinner { display:inline-block; width:1rem; height:1rem; border:2px solid #e2e8f0; border-top-color:#059669; border-radius:50%; animation:spin .7s linear infinite; }
    @keyframes spin { to { transform:rotate(360deg); } }

    /* Toast */
    #toast-container { position:fixed; top:1.25rem; right:1.25rem; z-index:9999; display:flex; flex-direction:column; gap:.625rem; pointer-events:none; max-width:min(420px,calc(100vw - 2.5rem)); }
    .toast { pointer-events:auto; display:flex; align-items:flex-start; gap:.75rem; padding:.875rem 1rem; border-radius:.875rem; border:1.5px solid transparent; box-shadow:0 8px 24px rgba(0,0,0,.14); font-size:.8rem; font-weight:500; line-height:1.45; transform:translateX(120%); opacity:0; transition:transform .32s cubic-bezier(.22,1,.36,1),opacity .28s ease; cursor:default; position:relative; overflow:hidden; }
    .toast.toast-in  { transform:translateX(0); opacity:1; }
    .toast.toast-out { transform:translateX(120%); opacity:0; transition:transform .25s ease-in; }
    .toast-success { background:rgba(240,253,244,.97); border-color:#86efac; color:#14532d; }
    .toast-error   { background:rgba(255,241,242,.97); border-color:#fda4af; color:#9f1239; }
    .toast-warning { background:rgba(255,251,235,.97); border-color:#fde68a; color:#78350f; }
    .toast-icon { width:1.75rem; height:1.75rem; border-radius:9999px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:.75rem; }
    .toast-success .toast-icon { background:#dcfce7; color:#16a34a; }
    .toast-error   .toast-icon { background:#fee2e2; color:#dc2626; }
    .toast-warning .toast-icon { background:#fef9c3; color:#d97706; }
    .toast-body  { flex:1; }
    .toast-title { font-weight:700; font-size:.8rem; margin-bottom:.1rem; }
    .toast-close { background:none; border:none; cursor:pointer; opacity:.4; font-size:.7rem; padding:.15rem; color:inherit; flex-shrink:0; transition:opacity .15s; }
    .toast-close:hover { opacity:.85; }
    .toast-progress { position:absolute; bottom:0; left:0; height:3px; border-radius:0 0 .875rem .875rem; animation:toast-drain linear forwards; }
    .toast-success .toast-progress { background:#22c55e; }
    .toast-error   .toast-progress { background:#f43f5e; }
    .toast-warning .toast-progress { background:#f59e0b; }
    @keyframes toast-drain { from { width:100%; } to { width:0%; } }
</style>
@endsection

@section('main_content')

<form method="POST"
      id="infraForm"
      action="{{ route('dmo.audits.infra-audit.store') }}"
      enctype="multipart/form-data">
    @csrf

    {{-- ════════════════════════════════════════
         A. Hospital Details
         ════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">

        <div class="flex items-center gap-2 mb-5">
            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-sm flex-shrink-0">
                <i class="fas fa-hospital"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-sm">A. Hospital Details</p>
                <p class="text-xs text-slate-500">Basic identification and admission data</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                    <i class="fas fa-calendar-day text-emerald-600 mr-1"></i>
                    Date of Investigation <span class="text-rose-500">*</span>
                </label>
                <input type="date" name="investigation_date"
                       value="{{ old('investigation_date', now()->format('Y-m-d')) }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="field-input" required>
                @error('investigation_date')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                    <i class="fas fa-tag text-emerald-600 mr-1"></i>
                    Hospital ID <span class="text-slate-400 font-normal">(if available)</span>
                </label>
                <input type="text" name="hospital_id" value="{{ old('hospital_id') }}"
                       placeholder="e.g. HID-0042" class="field-input">
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                    <i class="fas fa-hospital-user text-emerald-600 mr-1"></i>
                    Name of Hospital <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="hospital_name" value="{{ old('hospital_name') }}"
                       placeholder="Full registered hospital name" class="field-input" required>
                @error('hospital_name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                    <i class="fas fa-location-dot text-emerald-600 mr-1"></i>
                    Address of Hospital <span class="text-rose-500">*</span>
                </label>
                <textarea name="hospital_address" rows="2"
                          placeholder="Street, locality, district, state, PIN"
                          class="field-input" required>{{ old('hospital_address') }}</textarea>
                @error('hospital_address')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                    <i class="fas fa-landmark text-emerald-600 mr-1"></i>
                    Type of Hospital <span class="text-rose-500">*</span>
                </label>
                <select name="hospital_type" class="field-input" required>
                    <option value="">Select type…</option>
                    @foreach(['Public','Private'] as $t)
                    <option value="{{ $t }}" {{ old('hospital_type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
                @error('hospital_type')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                    <i class="fas fa-bed-pulse text-emerald-600 mr-1"></i>
                    PMJAY Beneficiaries (as per TMS)
                </label>
                <input type="number" name="pmjay_beneficiaries_tms"
                       value="{{ old('pmjay_beneficiaries_tms') }}"
                       min="0" placeholder="0" class="field-input">
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                    <i class="fas fa-bed text-emerald-600 mr-1"></i>
                    PMJAY Beneficiaries (admitted in hospital)
                </label>
                <input type="number" name="pmjay_beneficiaries_actual"
                       value="{{ old('pmjay_beneficiaries_actual') }}"
                       min="0" placeholder="0" class="field-input" style="max-width:200px;">
            </div>

        </div>
    </div>

    {{-- ════════════════════════════════════════
         B. Hospital Infrastructure
         ════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">

        <div class="flex items-center gap-2 mb-5">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm flex-shrink-0">
                <i class="fas fa-building"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-sm">B. Hospital Infrastructure</p>
                <p class="text-xs text-slate-500">Physical facilities, beds, ICU, OT, diagnostics</p>
            </div>
        </div>

        {{-- Existence & Registration --}}
        <div class="section-badge"><span>Existence &amp; Registration</span></div>
        @include('dmo.audits.infrastructure.infra-yn-row',     ['num'=>8,  'label'=>'Hospital Existence',              'name'=>'hospital_existence',  'remark'=>'hospital_existence_remarks'])
        @include('dmo.audits.infrastructure.infra-select-row', ['num'=>9,  'label'=>'Response from Hospital',          'name'=>'hospital_response',   'options'=>['Co-operative','Non Co-operative','Indifferent'], 'remark'=>'hospital_response_remarks'])
        @include('dmo.audits.infrastructure.infra-yn-row',     ['num'=>10, 'label'=>'Is Hospital Registered with DGHS?','name'=>'dghs_registered',    'remark'=>'dghs_registered_remarks'])

        {{-- AI Banner Upload --}}
        <div class="section-badge">
            <span>Hospital Banner / Signage</span>
            <span class="badge-ai"><i class="fas fa-robot"></i> AI Verified</span>
        </div>

        <div class="obs-card" style="border-color:#c4b5fd;">
            <div class="obs-top mb-3">
                <div>
                    <div class="obs-label flex items-center gap-2">
                        <i class="fas fa-image text-violet-600"></i>
                        Hospital Promotional / PMAM Banner
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Upload a photo of the hospital's front banner or PMAM signage.</p>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <span class="badge-ai"><i class="fas fa-robot"></i> AI Check</span>
                    <span class="badge-req"><i class="fas fa-star"></i> Required</span>
                </div>
            </div>

            <div class="ai-upload-box" id="banner-drop" onclick="document.getElementById('banner-file-input').click()">
                <input type="file" id="banner-file-input" name="banner_photo" accept="image/*" style="display:none" />
                <div style="width:3rem;height:3rem;background:#d1fae5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto .75rem;color:#059669;font-size:1.1rem;">
                    <i class="fas fa-cloud-arrow-up"></i>
                </div>
                <p class="text-sm font-bold text-slate-700">Upload Banner Photo</p>
                <p class="text-xs text-slate-500 mt-1">JPG, PNG, WEBP · max 10 MB · drag &amp; drop or click to browse</p>
            </div>

            <img id="banner-preview" src="" alt="Banner preview"
                 style="display:none;width:100%;max-height:280px;object-fit:cover;border-radius:.875rem;border:2px solid #34d399;margin-top:.875rem;" />

            <div class="ai-result-box" id="ai-result-box">
                <div class="ai-result-title" id="ai-result-title"></div>
                <div class="ai-result-body"  id="ai-result-body"></div>
            </div>

            {{-- Hidden AI result fields submitted with the main form POST --}}
            <input type="hidden" name="ai_banner_pass"    id="f-ai-pass"     value="">
            <input type="hidden" name="ai_pmjay_branding" id="f-ai-branding" value="">
            <input type="hidden" name="ai_banner_visible" id="f-ai-visible"  value="">
            <input type="hidden" name="ai_banner_summary" id="f-ai-summary"  value="">
            <input type="hidden" name="ai_banner_details" id="f-ai-details"  value="">

            <div class="obs-remark mt-3">
                <input type="text" name="banner_remarks" value="{{ old('banner_remarks') }}" placeholder="Additional remarks (optional)" />
            </div>
        </div>
        @error('banner_photo')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror

        {{-- PMAM Kiosk --}}
        <div class="section-badge"><span>PMAM Kiosk &amp; Boards</span></div>
        @include('dmo.audits.infrastructure.infra-yn-row',     ['num'=>11, 'label'=>'Availability of PMAM Kiosk',            'name'=>'pmam_kiosk_available'])
        @include('dmo.audits.infrastructure.infra-select-row', ['num'=>12, 'label'=>'Location of PMAM Kiosk',                'name'=>'pmam_kiosk_location', 'options'=>['Easily Visible','Far Inside','Not Available'], 'remark'=>'pmam_kiosk_remarks'])
        @include('dmo.audits.infrastructure.infra-yn-row',     ['num'=>13, 'label'=>'Promotional boards prominently displayed','name'=>'promo_boards_displayed', 'remark'=>'promo_boards_remarks'])

        {{-- Beds --}}
        <div class="section-badge"><span>Beds &amp; Wards</span></div>
        @include('dmo.audits.infrastructure.infra-num-row', ['num'=>14, 'label'=>'Total Number of Beds',                    'name'=>'total_beds'])
        @include('dmo.audits.infrastructure.infra-num-row', ['num'=>15, 'label'=>'Number of Beds in General Ward',          'name'=>'general_ward_beds'])
        @include('dmo.audits.infrastructure.infra-yn-row',  ['num'=>16, 'label'=>'Adequate distance (4 feet) between beds', 'name'=>'bed_distance_adequate', 'remark'=>'bed_distance_remarks'])

        {{-- HDU --}}
        <div class="section-badge"><span>HDU</span></div>
        @include('dmo.audits.infrastructure.infra-yn-row',  ['num'=>17, 'label'=>'Is HDU Available?',      'name'=>'hdu_available'])
        @include('dmo.audits.infrastructure.infra-num-row', ['num'=>18, 'label'=>'Number of Beds in HDU',  'name'=>'hdu_beds'])

        {{-- ICU --}}
        <div class="section-badge"><span>ICU</span></div>
        @include('dmo.audits.infrastructure.infra-yn-row',  ['num'=>19, 'label'=>'Is ICU Available?',      'name'=>'icu_available'])
        @include('dmo.audits.infrastructure.infra-num-row', ['num'=>20, 'label'=>'Number of Beds in ICU',  'name'=>'icu_beds'])

        <div class="obs-card">
            <div class="obs-top">
                <div><span class="obs-num">21</span> &nbsp;<span class="obs-label">Is the ICU well equipped?</span></div>
                <x-infra-radio name="icu_well_equipped" />
            </div>
            <div class="sub-items">
                @foreach(['A'=>'Standard ICU bed','B'=>'Equipment/monitor for constant monitoring of vitals','C'=>'Emergency crash cart','D'=>'Defibrillator','E'=>'Ventilators','F'=>'Suction pumps','G'=>'Bedside oxygen facility','H'=>'Air conditioning'] as $key => $lbl)
                <div class="sub-item">
                    <div class="sub-alpha">{{ $key }}</div>
                    <div class="sub-label">{{ $lbl }}</div>
                    <div class="radio-group">
                        <input type="radio" name="icu_equipment[{{ $key }}]" id="icu-{{ $key }}-y" value="Yes" {{ old("icu_equipment.{$key}") === 'Yes' ? 'checked' : '' }}>
                        <label for="icu-{{ $key }}-y">Yes</label>
                        <input type="radio" name="icu_equipment[{{ $key }}]" id="icu-{{ $key }}-n" value="No"  {{ old("icu_equipment.{$key}") === 'No'  ? 'checked' : '' }}>
                        <label for="icu-{{ $key }}-n">No</label>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="obs-remark">
                <input type="text" name="icu_equipment_remarks" value="{{ old('icu_equipment_remarks') }}" placeholder="Remarks (optional)" />
            </div>
        </div>

        {{-- OT --}}
        <div class="section-badge"><span>Operation Theatre (OT)</span></div>
        @include('dmo.audits.infrastructure.infra-yn-row',  ['num'=>22, 'label'=>'Is OT Available?',                        'name'=>'ot_available'])
        @include('dmo.audits.infrastructure.infra-num-row', ['num'=>23, 'label'=>'Number of OTs',                           'name'=>'ot_count'])
        @include('dmo.audits.infrastructure.infra-num-row', ['num'=>24, 'label'=>'Number of OT Tables',                     'name'=>'ot_tables'])
        @include('dmo.audits.infrastructure.infra-yn-row',  ['num'=>25, 'label'=>'OT sterilization facility functional',    'name'=>'ot_sterilization', 'remark'=>'ot_sterilization_remarks'])
        @include('dmo.audits.infrastructure.infra-yn-row',  ['num'=>26, 'label'=>'Adequate illumination in each OT',        'name'=>'ot_lighting'])
        @include('dmo.audits.infrastructure.infra-yn-row',  ['num'=>27, 'label'=>'Air conditioning provided in each OT',   'name'=>'ot_ac'])

        <div class="obs-card">
            <div class="obs-top">
                <div><span class="obs-num">28</span> &nbsp;<span class="obs-label">Is the OT well equipped?</span></div>
                <x-infra-radio name="ot_well_equipped" />
            </div>
            <div class="sub-items">
                @foreach(['A'=>'Anesthetic machine','B'=>'Ventilator','C'=>'Laryngoscopes (Adult / Pediatric)','D'=>'Endotracheal tubes/laryngeal masks','E'=>'Airways / Nasal tubes','F'=>'Suction apparatus and connectors','G'=>'Oxygen','H'=>'Drugs for emergency situations','I'=>'Monitoring equipment incl. ECG, ETCO2','J'=>'Pulse oximeter and blood pressure','K'=>'Cardiac monitor','L'=>'Defibrillator'] as $key => $lbl)
                <div class="sub-item">
                    <div class="sub-alpha">{{ $key }}</div>
                    <div class="sub-label">{{ $lbl }}</div>
                    <div class="radio-group">
                        <input type="radio" name="ot_equipment[{{ $key }}]" id="ot-{{ $key }}-y" value="Yes" {{ old("ot_equipment.{$key}") === 'Yes' ? 'checked' : '' }}>
                        <label for="ot-{{ $key }}-y">Yes</label>
                        <input type="radio" name="ot_equipment[{{ $key }}]" id="ot-{{ $key }}-n" value="No"  {{ old("ot_equipment.{$key}") === 'No'  ? 'checked' : '' }}>
                        <label for="ot-{{ $key }}-n">No</label>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="obs-remark">
                <input type="text" name="ot_equipment_remarks" value="{{ old('ot_equipment_remarks') }}" placeholder="Remarks (optional)" />
            </div>
        </div>

        {{-- Diagnostics & Hygiene --}}
        <div class="section-badge"><span>Diagnostics &amp; Hygiene</span></div>
        @include('dmo.audits.infrastructure.infra-select-row', ['num'=>29, 'label'=>'Pathology / Diagnostics',              'name'=>'pathology_diagnostics', 'options'=>['Inhouse','Out sourced','Not Available'], 'remark'=>'pathology_remarks'])
        @include('dmo.audits.infrastructure.infra-yn-row',     ['num'=>30, 'label'=>'Availability of Biomedical Waste Management', 'name'=>'biomedical_waste', 'remark'=>'biomedical_waste_remarks'])
        @include('dmo.audits.infrastructure.infra-select-row', ['num'=>31, 'label'=>'Overall Hygiene Maintained in Hospital','name'=>'overall_hygiene', 'options'=>['Good','Average','Poor'], 'remark'=>'overall_hygiene_remarks'])

        <div class="obs-card">
            <div class="obs-top" style="flex-direction:column;gap:.5rem;">
                <div><span class="obs-num">32</span> &nbsp;<span class="obs-label">Any other remark or observation</span></div>
                <textarea name="infra_other_remarks" rows="3" placeholder="Free text observations…"
                          class="field-input" style="font-size:.82rem;">{{ old('infra_other_remarks') }}</textarea>
            </div>
        </div>

    </div>

    {{-- ════════════════════════════════════════
         C. Human Resource
         ════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">

        <div class="flex items-center gap-2 mb-5">
            <div class="w-8 h-8 rounded-lg bg-violet-100 text-violet-700 flex items-center justify-center text-sm flex-shrink-0">
                <i class="fas fa-user-doctor"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-sm">C. Human Resource</p>
                <p class="text-xs text-slate-500">Staff availability at the time of visit</p>
            </div>
        </div>

        @include('dmo.audits.infrastructure.infra-yn-row', ['num'=>33, 'label'=>'Availability of PMAM at the time of visit', 'name'=>'pmam_available', 'remark'=>'pmam_available_remarks'])

        <div class="obs-card">
            <div class="obs-top">
                <div><span class="obs-num">34</span> &nbsp;<span class="obs-label">Availability of on-duty doctors at time of visit</span></div>
                <x-infra-radio name="onduty_doctors" />
            </div>
            <div class="sub-items">
                @foreach(['A'=>'RMO','B'=>'Emergency doctor','C'=>'ICU doctor'] as $key => $lbl)
                <div class="sub-item">
                    <div class="sub-alpha">{{ $key }}</div>
                    <div class="sub-label">{{ $lbl }}</div>
                    <div class="radio-group">
                        <input type="radio" name="onduty_doctor_types[{{ $key }}]" id="doc-{{ $key }}-y" value="Yes" {{ old("onduty_doctor_types.{$key}") === 'Yes' ? 'checked' : '' }}>
                        <label for="doc-{{ $key }}-y">Yes</label>
                        <input type="radio" name="onduty_doctor_types[{{ $key }}]" id="doc-{{ $key }}-n" value="No"  {{ old("onduty_doctor_types.{$key}") === 'No'  ? 'checked' : '' }}>
                        <label for="doc-{{ $key }}-n">No</label>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="obs-remark">
                <input type="text" name="onduty_doctors_remarks" value="{{ old('onduty_doctors_remarks') }}" placeholder="Remarks (optional)" />
            </div>
        </div>

        @include('dmo.audits.infrastructure.infra-yn-row', ['num'=>35, 'label'=>'Adequate number of nurses at time of visit',             'name'=>'adequate_nurses',       'remark'=>'adequate_nurses_remarks'])
        @include('dmo.audits.infrastructure.infra-yn-row', ['num'=>36, 'label'=>'Are the nurses appropriately qualified?',               'name'=>'nurses_qualified',      'remark'=>'nurses_qualified_remarks'])
        @include('dmo.audits.infrastructure.infra-yn-row', ['num'=>37, 'label'=>'Availability of technicians (if applicable)',           'name'=>'technicians_available'])
        @include('dmo.audits.infrastructure.infra-yn-row', ['num'=>38, 'label'=>'Availability of pharmacists (if applicable)',           'name'=>'pharmacists_available'])
        @include('dmo.audits.infrastructure.infra-yn-row', ['num'=>39, 'label'=>'Availability of specialists for which claims are booked','name'=>'specialists_available', 'remark'=>'specialists_remarks'])

        <div class="obs-card">
            <div class="obs-top" style="flex-direction:column;gap:.5rem;">
                <div><span class="obs-num">40</span> &nbsp;<span class="obs-label">Any other remark or observation</span></div>
                <textarea name="hr_other_remarks" rows="3" placeholder="Free text observations…"
                          class="field-input" style="font-size:.82rem;">{{ old('hr_other_remarks') }}</textarea>
            </div>
        </div>

    </div>

    {{-- ── Submit Bar ── --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-2">
        <button type="submit"
                class="flex-1 sm:flex-none px-8 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition-all">
            <i class="fas fa-paper-plane"></i> Submit Audit
        </button>
        <button type="reset"
                class="flex-1 sm:flex-none px-6 py-3.5 rounded-xl border-2 border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold text-sm flex items-center justify-center gap-2 transition">
            <i class="fas fa-rotate-left"></i> Reset
        </button>
    </div>

</form>

<div id="toast-container"></div>

<script>
(function () {
    'use strict';

    /* ── AI Banner Upload ─────────────────────────────────────── */
    const fileInput   = document.getElementById('banner-file-input');
    const dropZone    = document.getElementById('banner-drop');
    const preview     = document.getElementById('banner-preview');
    const resultBox   = document.getElementById('ai-result-box');
    const resultTitle = document.getElementById('ai-result-title');
    const resultBody  = document.getElementById('ai-result-body');

    const fPass     = document.getElementById('f-ai-pass');
    const fBranding = document.getElementById('f-ai-branding');
    const fVisible  = document.getElementById('f-ai-visible');
    const fSummary  = document.getElementById('f-ai-summary');
    const fDetails  = document.getElementById('f-ai-details');

    dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
    dropZone.addEventListener('dragleave', ()  => dropZone.classList.remove('drag-over'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault(); dropZone.classList.remove('drag-over');
        const f = e.dataTransfer.files[0];
        if (f && f.type.startsWith('image/')) setFile(f);
    });

    fileInput.addEventListener('change', function () {
        if (this.files[0]) setFile(this.files[0]);
    });

    function setFile(file) {
        // Read as data-URI: show preview immediately then send to Vision API.
        // Matches VisionController::validateBedPhoto() — controller strips the prefix.
        const reader = new FileReader();
        reader.onload = e => {
            const dataUri = e.target.result;
            preview.src = dataUri;
            preview.style.display = 'block';
            runVisionCheck(dataUri);
        };
        reader.readAsDataURL(file);
    }

    /**
     * POST { image: "data:image/jpeg;base64,..." } to the server-side Vision endpoint.
     * Transport identical to VisionController::validateBedPhoto().
     */
    async function runVisionCheck(dataUri) {
        showLoading();
        try {
            const res = await fetch('{{ route("dmo.audits.infra-audit.verify-banner") }}', {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept':       'application/json',
                },
                body: JSON.stringify({ image: dataUri }),
            });

            const data = await res.json();

            if (!res.ok || !data.ok) {
                data.skipped ? showSkipped(data.message ?? 'Vision check skipped.')
                             : showError(data.message   ?? 'Vision check failed.');
                return;
            }

            // Populate hidden fields that travel with the main form POST
            fPass.value     = data.pass           ? '1' : '0';
            fBranding.value = data.pmjay_branding ? '1' : '0';
            fVisible.value  = data.visible        ? '1' : '0';
            fSummary.value  = data.summary        ?? '';
            fDetails.value  = data.details        ?? '';

            showResult(data);
            toast(
                data.pass ? 'success' : 'error',
                data.pass ? 'Banner Verified' : 'Verification Failed',
                data.summary ?? ''
            );
        } catch (err) {
            showError('Network error: ' + err.message);
        }
    }

    function showLoading() {
        resultBox.className = 'ai-result-box ai-loading';
        resultBox.style.display = 'block';
        resultTitle.innerHTML = '<span class="ai-spinner"></span> <span style="color:#475569">Analysing banner .…</span>';
        resultBody.textContent  = 'Please wait while the image is being verified.';
    }

    function showResult(data) {
        resultBox.className = 'ai-result-box ' + (data.pass ? 'ai-pass' : 'ai-fail');
        resultTitle.innerHTML = data.pass
            ? '<i class="fas fa-circle-check" style="color:#059669"></i> <span style="color:#166534">Verification Passed</span>'
            : '<i class="fas fa-circle-xmark" style="color:#e11d48"></i> <span style="color:#9f1239">Verification Failed</span>';
        resultBody.innerHTML = `<strong>${data.summary}</strong>
            <span style="display:block;margin-top:.3rem">${data.details}</span>
            <div style="margin-top:.5rem;display:flex;gap:.4rem;flex-wrap:wrap;font-size:.7rem;font-weight:700;">
                
                <span style="background:${data.visible?'#dcfce7':'#fee2e2'};color:${data.visible?'#166534':'#9f1239'};padding:.2rem .55rem;border-radius:9999px;">
                    Visible: ${data.visible ? '✓ Yes' : '✗ No'}</span>
            </div>`;
    }

    function showError(msg) {
        resultBox.className = 'ai-result-box ai-fail';
        resultBox.style.display = 'block';
        resultTitle.innerHTML = '<i class="fas fa-triangle-exclamation" style="color:#d97706"></i> <span style="color:#78350f">Check Error</span>';
        resultBody.textContent  = msg;
        toast('warning', 'Vision Check Error', msg);
    }

    function showSkipped(msg) {
        resultBox.className = 'ai-result-box ai-loading';
        resultBox.style.display = 'block';
        resultTitle.innerHTML = '<i class="fas fa-circle-info" style="color:#64748b"></i> <span style="color:#475569">Vision Check Skipped</span>';
        resultBody.textContent  = msg + ' The banner photo has been saved but could not be verified automatically.';
        toast('warning', 'Vision Skipped', msg);
    }

})();

/* ── Toast ─────────────────────────────────────────────────── */
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
    const dismiss = () => {
        el.classList.replace('toast-in', 'toast-out');
        el.addEventListener('transitionend', () => el.remove(), { once: true });
    };
    const timer = setTimeout(dismiss, duration);
    el.querySelector('.toast-close').addEventListener('click', () => { clearTimeout(timer); dismiss(); });
}

@if($errors->any())
toast('error', 'Validation Errors', '{{ $errors->first() }}');
@endif
</script>

@endsection

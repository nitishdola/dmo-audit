@extends('admin.layout.layout')

@section('page_title', 'Generate Audits')

@section('main_title')
<div class="breadcrumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
    <span class="breadcrumb-current">Generate Audits</span>
</div>
@endsection

@section('pageCss')
<style>
    .action-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 1.25rem;
        overflow: hidden;
        max-width: 560px;
    }
    .action-card-header {
        padding: 1.5rem 1.75rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .action-card-icon {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: .875rem;
        background: linear-gradient(135deg, #06b6d4, #3b82f6);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .action-card-icon.done {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    .action-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
    }
    .action-card-subtitle {
        font-size: .75rem;
        color: #94a3b8;
        margin-top: .15rem;
    }
    .action-card-body {
        padding: 1.75rem;
    }

    /* Alerts */
    .alert {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding: .875rem 1rem;
        border-radius: .875rem;
        margin-bottom: 1.25rem;
        font-size: .8125rem;
        font-weight: 500;
        line-height: 1.5;
    }
    .alert i { flex-shrink: 0; margin-top: .1rem; }
    .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
    .alert-success i { color: #16a34a; }
    .alert-error   { background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; }
    .alert-error i { color: #e11d48; }

    /* Already-generated state */
    .status-block {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 1rem;
    }
    .status-block-icon {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: .875rem;
        background: #dcfce7;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #16a34a;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .status-block-title {
        font-size: .9rem;
        font-weight: 700;
        color: #15803d;
    }
    .status-block-sub {
        font-size: .75rem;
        color: #4ade80;
        margin-top: .2rem;
    }

    /* Warning box */
    .warning-box {
        display: flex;
        gap: .75rem;
        padding: .875rem 1rem;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: .875rem;
        margin-bottom: 1.25rem;
    }
    .warning-box i { color: #d97706; font-size: .875rem; flex-shrink: 0; margin-top: .1rem; }
    .warning-box-text { font-size: .75rem; color: #92400e; line-height: 1.6; }
    .warning-box-text strong { color: #78350f; }

    /* Actions */
    .form-actions {
        display: flex;
        align-items: center;
        gap: .875rem;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid #f1f5f9;
    }
    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .625rem 1.25rem;
        background: linear-gradient(135deg, #0ea5e9, #3b82f6);
        color: #fff;
        border: none;
        border-radius: .75rem;
        font-size: .8125rem;
        font-weight: 600;
        cursor: pointer;
        transition: opacity .15s, transform .15s, box-shadow .15s;
        box-shadow: 0 2px 8px rgba(14,165,233,.3);
        text-decoration: none;
    }
    .btn-primary:hover {
        opacity: .9;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(14,165,233,.4);
    }
    .btn-primary:active { transform: translateY(0); }
    .btn-primary:disabled { opacity: .5; cursor: not-allowed; transform: none; box-shadow: none; }

    /* Loading state */
    .btn-primary.loading .btn-text    { display: none; }
    .btn-primary .btn-spinner         { display: none; }
    .btn-primary.loading .btn-spinner { display: inline-flex; align-items: center; gap: .5rem; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .spinner-ring {
        width: .875rem; height: .875rem;
        border: 2px solid rgba(255,255,255,.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .6s linear infinite;
    }

    /* Confirm modal */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,.45);
        backdrop-filter: blur(4px);
        z-index: 200;
        align-items: center;
        justify-content: center;
    }
    .modal-backdrop.open { display: flex; }
    .modal {
        background: #fff;
        border-radius: 1.25rem;
        padding: 2rem;
        max-width: 400px;
        width: calc(100% - 2rem);
        box-shadow: 0 24px 64px rgba(0,0,0,.18);
        animation: pop .2s cubic-bezier(.34,1.56,.64,1);
    }
    @keyframes pop { from { transform: scale(.92); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .modal-icon {
        width: 3rem; height: 3rem;
        border-radius: 1rem;
        background: #fff7ed;
        display: flex; align-items: center; justify-content: center;
        color: #f97316;
        font-size: 1.125rem;
        margin-bottom: 1rem;
    }
    .modal-title { font-size: 1rem; font-weight: 800; color: #0f172a; font-family: 'Syne', sans-serif; margin-bottom: .5rem; }
    .modal-body  { font-size: .8125rem; color: #64748b; line-height: 1.6; margin-bottom: 1.5rem; }
    .modal-actions { display: flex; gap: .75rem; }
    .btn-danger {
        display: inline-flex; align-items: center; gap: .5rem;
        padding: .625rem 1.25rem;
        background: linear-gradient(135deg, #f43f5e, #e11d48);
        color: #fff; border: none; border-radius: .75rem;
        font-size: .8125rem; font-weight: 600; cursor: pointer;
        box-shadow: 0 2px 8px rgba(244,63,94,.3);
        transition: opacity .15s, transform .15s;
    }
    .btn-danger:hover { opacity: .9; transform: translateY(-1px); }
    .btn-ghost {
        display: inline-flex; align-items: center; gap: .5rem;
        padding: .625rem 1.25rem;
        background: transparent; color: #64748b;
        border: 1.5px solid #e2e8f0; border-radius: .75rem;
        font-size: .8125rem; font-weight: 600; cursor: pointer;
        transition: background .15s, color .15s;
    }
    .btn-ghost:hover { background: #f8fafc; color: #334155; }
</style>
@endsection

@section('main_content')

<div style="margin-bottom:1.5rem;">
    <h1 style="font-size:1.375rem; font-weight:800; color:#0f172a; font-family:'Syne',sans-serif;">Generate Audits</h1>
    <p style="font-size:.8125rem; color:#94a3b8; margin-top:.25rem;">Create PMJAY audit cases from imported treatment records.</p>
</div>

<div class="action-card">
    <div class="action-card-header">
        <div class="action-card-icon {{ $alreadyGenerated ? 'done' : '' }}">
            <i class="fas {{ $alreadyGenerated ? 'fa-check' : 'fa-wand-magic-sparkles' }}"></i>
        </div>
        <div>
            <div class="action-card-title">PMJAY Audit Generation</div>
            <div class="action-card-subtitle">
                {{ $alreadyGenerated ? 'Audit cases are already active.' : 'No audit cases have been generated yet.' }}
            </div>
        </div>
    </div>

    <div class="action-card-body">

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
        @endif

        @if($alreadyGenerated)

            {{-- Already done state --}}
            <div class="status-block">
                <div class="status-block-icon">
                    <i class="fas fa-circle-check"></i>
                </div>
                <div>
                    <div class="status-block-title">Audits Already Generated</div>
                    <div class="status-block-sub">All audit cases are live and assigned to DMO officers.</div>
                </div>
            </div>

            <div style="margin-top:1.25rem; display:flex; gap:.875rem; flex-wrap:wrap;">
                <a href="{{ route('admin.audits.telephonic.index') }}" class="btn-primary">
                    <i class="fas fa-phone-alt"></i> View Telephonic Audits
                </a>
                <a href="{{ route('admin.dashboard') }}" class="btn-ghost">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
            </div>

        @else

            {{-- Generate form --}}
            <div class="warning-box">
                <i class="fas fa-triangle-exclamation"></i>
                <div class="warning-box-text">
                    <strong>This action is irreversible.</strong> Running generation will create audit cases for all imported PMJAY treatment records and assign them to DMO officers. Ensure all data has been imported before proceeding.
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-primary" onclick="openConfirm()">
                    <i class="fas fa-wand-magic-sparkles"></i> Generate Audits
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn-ghost">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

        @endif

    </div>
</div>

{{-- Confirm modal --}}
@if(!$alreadyGenerated)
<div class="modal-backdrop" id="confirm-modal">
    <div class="modal">
        <div class="modal-icon"><i class="fas fa-triangle-exclamation"></i></div>
        <div class="modal-title">Confirm Audit Generation</div>
        <div class="modal-body">
            This will generate audit cases for <strong>all imported PMJAY records</strong> and cannot be undone. Make sure all data is imported before continuing.
        </div>
        <div class="modal-actions">
            <form method="POST" action="/admin/generate-audits" id="generate-form">
                @csrf
                <button type="submit" class="btn-danger" id="confirm-btn">
                    <span class="btn-text"><i class="fas fa-bolt"></i> Yes, Generate</span>
                    <span class="btn-spinner"><span class="spinner-ring"></span> Generating…</span>
                </button>
            </form>
            <button type="button" class="btn-ghost" onclick="closeConfirm()">Cancel</button>
        </div>
    </div>
</div>
@endif

@endsection

@section('pageJs')
<script>
function openConfirm()  { document.getElementById('confirm-modal').classList.add('open'); }
function closeConfirm() { document.getElementById('confirm-modal').classList.remove('open'); }

// Close on backdrop click
document.getElementById('confirm-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeConfirm();
});

// Loading state on confirm
document.getElementById('generate-form')?.addEventListener('submit', function() {
    const btn = document.getElementById('confirm-btn');
    btn.classList.add('loading');
    btn.disabled = true;
});
</script>
@endsection

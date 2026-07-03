@extends('admin.layout.layout')

@section('page_title', 'Import PMJAY Data')

@section('main_title')
<div class="breadcrumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
    <a href="{{ route('admin.pmjay.index') }}">PMJAY Records</a>
    <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
    <span class="breadcrumb-current">Import Data</span>
</div>
@endsection

@section('pageCss')
<style>
    .upload-card {
        background: #fff;
        border-radius: 1.25rem;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        max-width: 640px;
    }
    .upload-card-header {
        padding: 1.5rem 1.75rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .upload-card-icon {
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
    .upload-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }
    .upload-card-subtitle {
        font-size: .75rem;
        color: #94a3b8;
        margin-top: .15rem;
    }
    .upload-card-body {
        padding: 1.75rem;
    }

    /* Drop zone */
    .drop-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 1rem;
        padding: 2.5rem 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: border-color .2s, background .2s;
        background: #f8fafc;
        position: relative;
    }
    .drop-zone:hover,
    .drop-zone.dragover {
        border-color: #06b6d4;
        background: #ecfeff;
    }
    .drop-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    .drop-zone-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 1rem;
        background: #e0f2fe;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto .875rem;
        color: #0ea5e9;
        font-size: 1.125rem;
        transition: transform .2s;
    }
    .drop-zone:hover .drop-zone-icon {
        transform: translateY(-2px);
    }
    .drop-zone-title {
        font-size: .875rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: .375rem;
    }
    .drop-zone-hint {
        font-size: .75rem;
        color: #94a3b8;
    }
    .drop-zone-hint span {
        color: #0ea5e9;
        font-weight: 600;
        cursor: pointer;
    }

    /* File preview pill */
    #file-preview {
        display: none;
        align-items: center;
        gap: .75rem;
        padding: .75rem 1rem;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: .875rem;
        margin-top: 1rem;
    }
    #file-preview .file-icon {
        width: 2rem;
        height: 2rem;
        border-radius: .5rem;
        background: #dcfce7;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #16a34a;
        font-size: .75rem;
        flex-shrink: 0;
    }
    #file-preview .file-name {
        font-size: .8125rem;
        font-weight: 600;
        color: #15803d;
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    #file-preview .file-remove {
        color: #94a3b8;
        cursor: pointer;
        font-size: .75rem;
        padding: .25rem;
        border-radius: .375rem;
        transition: color .15s, background .15s;
        background: none;
        border: none;
    }
    #file-preview .file-remove:hover {
        color: #e11d48;
        background: #fff1f2;
    }

    /* Info box */
    .info-box {
        display: flex;
        gap: .75rem;
        padding: .875rem 1rem;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: .875rem;
        margin-top: 1.25rem;
    }
    .info-box i {
        color: #3b82f6;
        font-size: .875rem;
        flex-shrink: 0;
        margin-top: .1rem;
    }
    .info-box-text {
        font-size: .75rem;
        color: #1d4ed8;
        line-height: 1.6;
    }

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
    .btn-primary:disabled {
        opacity: .5;
        cursor: not-allowed;
        transform: none;
    }
    .btn-ghost {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .625rem 1.25rem;
        background: transparent;
        color: #64748b;
        border: 1.5px solid #e2e8f0;
        border-radius: .75rem;
        font-size: .8125rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s, color .15s;
        text-decoration: none;
    }
    .btn-ghost:hover { background: #f8fafc; color: #334155; }

    /* Loading state */
    .btn-primary.loading .btn-text { display: none; }
    .btn-primary .btn-spinner { display: none; }
    .btn-primary.loading .btn-spinner { display: inline-flex; align-items: center; gap: .5rem; }

    @keyframes spin { to { transform: rotate(360deg); } }
    .spinner-ring {
        width: .875rem;
        height: .875rem;
        border: 2px solid rgba(255,255,255,.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .6s linear infinite;
    }

    /* Alert */
    .alert-success {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .875rem 1rem;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: .875rem;
        margin-bottom: 1.25rem;
        font-size: .8125rem;
        color: #15803d;
        font-weight: 500;
    }
    .alert-success i { color: #16a34a; }

    .alert-error {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding: .875rem 1rem;
        background: #fff1f2;
        border: 1px solid #fecdd3;
        border-radius: .875rem;
        margin-bottom: 1.25rem;
        font-size: .8125rem;
        color: #be123c;
        font-weight: 500;
    }
    .alert-error i { color: #e11d48; margin-top: .1rem; }
</style>
@endsection

@section('main_content')

<div style="margin-bottom:1.5rem;">
    <h1 style="font-size:1.375rem; font-weight:800; color:#0f172a; font-family:'Syne',sans-serif;">Import PMJAY Data</h1>
    <p style="font-size:.8125rem; color:#94a3b8; margin-top:.25rem;">Upload a JSON file to bulk-import PMJAY treatment records.</p>
</div>

<div class="upload-card">
    <div class="upload-card-header">
        <div class="upload-card-icon"><i class="fas fa-file-import"></i></div>
        <div>
            <div class="upload-card-title">Upload JSON File</div>
            <div class="upload-card-subtitle">Supported format: .json — PMJAY treatment export</div>
        </div>
    </div>

    <div class="upload-card-body">

        {{-- Success alert --}}
        @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
        @endif

        {{-- Validation errors --}}
        @if($errors->any())
        <div class="alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
        @endif

        <form method="POST"
              action="{{ route('admin.pmjay.import') }}"
              enctype="multipart/form-data"
              id="upload-form">
            @csrf

            {{-- Drop zone --}}
            <div class="drop-zone" id="drop-zone">
                <input type="file"
                       name="file"
                       id="file-input"
                       accept=".json,application/json"
                       required>
                <div class="drop-zone-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <div class="drop-zone-title">Drop your JSON file here</div>
                <div class="drop-zone-hint">or <span>browse to upload</span></div>
            </div>

            {{-- File name preview --}}
            <div id="file-preview">
                <div class="file-icon"><i class="fas fa-file-code"></i></div>
                <span class="file-name" id="file-name-text">—</span>
                <button type="button" class="file-remove" id="file-remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Info --}}
            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <div class="info-box-text">
                    The file must be in the PMJAY JSON export format. Large files may take a moment to process. Do not close the tab during import.
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary" id="submit-btn">
                    <span class="btn-text"><i class="fas fa-upload"></i> Import Data</span>
                    <span class="btn-spinner">
                        <span class="spinner-ring"></span> Importing…
                    </span>
                </button>
                <a href="{{ route('admin.pmjay.index') }}" class="btn-ghost">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
            </div>

        </form>
    </div>
</div>

@endsection

@section('pageJs')
<script>
const input     = document.getElementById('file-input');
const dropZone  = document.getElementById('drop-zone');
const preview   = document.getElementById('file-preview');
const nameText  = document.getElementById('file-name-text');
const removeBtn = document.getElementById('file-remove');
const form      = document.getElementById('upload-form');
const submitBtn = document.getElementById('submit-btn');

function showFile(file) {
    if (!file) return;
    nameText.textContent = file.name;
    preview.style.display = 'flex';
}

function clearFile() {
    input.value = '';
    preview.style.display = 'none';
    nameText.textContent = '—';
}

input.addEventListener('change', () => showFile(input.files[0]));
removeBtn.addEventListener('click', clearFile);

// Drag & drop
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file) {
        // Transfer to the real input via DataTransfer
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        showFile(file);
    }
});

// Loading state on submit
form.addEventListener('submit', () => {
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
});
</script>
@endsection

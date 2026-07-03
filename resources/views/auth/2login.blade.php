<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'DMO Audit Portal') }} — Log In</title>

    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    @vite('resources/css/app.css')

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:       #0f2558;
            --navy-deep:  #091840;
            --navy-mid:   #1a3a78;
            --green-ok:   #22c55e;
            --red-err:    #ef4444;
            --text-navy:  #e8edf8;
            --text-muted: rgba(232,237,248,0.5);
            --border-navy:rgba(255,255,255,0.1);
            --radius-md:  10px;
            --radius-lg:  16px;
            --font:       'Figtree', sans-serif;
        }

        html, body { height: 100%; font-family: var(--font); background: var(--navy-deep); -webkit-font-smoothing: antialiased; }

        .shell { min-height: 100vh; display: flex; flex-direction: column; }

        /* Topbar */
        .topbar { height: 60px; background: var(--navy-deep); border-bottom: 0.5px solid var(--border-navy); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; flex-shrink: 0; }
        .topbar-brand { display: flex; align-items: center; gap: 11px; }
        .topbar-icon { width: 36px; height: 36px; background: var(--navy-mid); border: 0.5px solid var(--border-navy); border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        .topbar-icon i { color: rgba(255,255,255,0.75); font-size: 16px; }
        .topbar-name { font-size: 14px; font-weight: 600; color: #fff; }
        .topbar-sub  { font-size: 11px; color: var(--text-muted); margin-top: 1px; }
        .topbar-badge { display: flex; align-items: center; gap: 6px; background: rgba(34,197,94,0.1); border: 0.5px solid rgba(34,197,94,0.25); border-radius: 20px; padding: 4px 12px; font-size: 11px; color: rgba(34,197,94,0.9); }
        .topbar-badge .pulse { width: 6px; height: 6px; background: var(--green-ok); border-radius: 50%; animation: pulse 2s ease-in-out infinite; }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(.8)} }

        /* Main grid */
        .main { flex: 1; display: grid; grid-template-columns: 1fr 460px; }
        @media(max-width:768px){ .main{grid-template-columns:1fr} .left-panel{display:none} }

        /* Left panel */
        .left-panel { background: var(--navy); padding: 48px 44px; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden; }
        .left-panel::before { content:''; position:absolute; inset:0; background-image: linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px); background-size:40px 40px; pointer-events:none; }
        .left-panel::after  { content:''; position:absolute; bottom:-80px; right:-80px; width:360px; height:360px; background:radial-gradient(circle,rgba(59,130,246,0.12) 0%,transparent 70%); pointer-events:none; }
        .panel-content { position:relative; z-index:1; }
        .seal { width:60px; height:60px; background:rgba(255,255,255,0.07); border:0.5px solid var(--border-navy); border-radius:14px; display:flex; align-items:center; justify-content:center; margin-bottom:28px; }
        .seal i { font-size:26px; color:rgba(255,255,255,0.7); }
        .panel-title { font-size:26px; font-weight:600; color:#fff; line-height:1.3; margin-bottom:10px; }
        .panel-desc  { font-size:13px; color:var(--text-muted); line-height:1.7; margin-bottom:36px; max-width:380px; }
        .stat-grid   { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:36px; }
        .stat-card   { background:rgba(255,255,255,0.05); border:0.5px solid var(--border-navy); border-radius:var(--radius-md); padding:16px 18px; }
        .stat-num    { font-size:24px; font-weight:600; color:#fff; }
        .stat-lbl    { font-size:11px; color:var(--text-muted); margin-top:2px; }
        .divider     { border:none; border-top:0.5px solid var(--border-navy); margin:0 0 24px; }
        .notice-list { display:flex; flex-direction:column; gap:12px; }
        .notice-item { display:flex; gap:10px; align-items:flex-start; }
        .notice-item i { font-size:13px; margin-top:3px; flex-shrink:0; color:rgba(255,255,255,0.3); }
        .notice-item span { font-size:12px; color:var(--text-muted); line-height:1.6; }

        /* Right panel */
        .right-panel { background:#f8fafc; padding:44px 40px; display:flex; flex-direction:column; justify-content:center; }
        .eyebrow     { font-size:10px; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:#64748b; margin-bottom:6px; }
        .form-title  { font-size:22px; font-weight:600; color:#0f172a; margin-bottom:4px; }
        .form-sub    { font-size:13px; color:#64748b; margin-bottom:28px; line-height:1.55; }

        .alert { border-radius:var(--radius-md); padding:12px 14px; font-size:13px; margin-bottom:20px; display:flex; gap:9px; align-items:flex-start; }
        .alert-success { background:#f0fdf4; border:0.5px solid #86efac; color:#166534; }
        .alert-error   { background:#fef2f2; border:0.5px solid #fca5a5; color:#991b1b; }
        .alert i { flex-shrink:0; margin-top:1px; }

        .field       { margin-bottom:20px; }
        .field label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:6px; }
        .input-wrap  { position:relative; }
        .input-wrap .ico { position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:15px; color:#94a3b8; pointer-events:none; }
        .input-wrap input { width:100%; height:42px; border:1px solid #e2e8f0; border-radius:var(--radius-md); padding:0 14px 0 38px; font-size:14px; font-family:var(--font); color:#0f172a; background:#fff; outline:none; transition:border-color .15s, box-shadow .15s; }
        .input-wrap input::placeholder { color:#cbd5e1; }
        .input-wrap input:focus { border-color:var(--navy); box-shadow:0 0 0 3px rgba(15,37,88,.1); }
        .input-wrap input.error { border-color:var(--red-err); box-shadow:0 0 0 3px rgba(239,68,68,.1); }
        .field-error { font-size:11px; color:var(--red-err); margin-top:5px; display:none; }
        .field-error.show { display:block; }

        .btn-primary { width:100%; height:42px; background:var(--navy); color:#fff; border:none; border-radius:var(--radius-md); font-size:14px; font-weight:600; font-family:var(--font); cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:background .15s, transform .1s; }
        .btn-primary:hover  { background:var(--navy-mid); }
        .btn-primary:active { transform:scale(.98); }
        .btn-primary i { font-size:15px; }

        .spinner { width:16px; height:16px; border:2px solid rgba(255,255,255,.3); border-top-color:#fff; border-radius:50%; animation:spin .7s linear infinite; display:none; }
        @keyframes spin { to{transform:rotate(360deg)} }

        .security-note { display:flex; gap:9px; align-items:flex-start; background:#f8fafc; border:0.5px solid #e2e8f0; border-radius:var(--radius-md); padding:11px 14px; margin-top:20px; }
        .security-note i { color:#94a3b8; font-size:13px; margin-top:2px; flex-shrink:0; }
        .security-note span { font-size:11px; color:#94a3b8; line-height:1.55; }

        /* Footer */
        .footer { background:var(--navy-deep); border-top:0.5px solid var(--border-navy); padding:14px 28px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
        .footer-left  { font-size:11px; color:var(--text-muted); }
        .footer-right { display:flex; align-items:center; gap:6px; font-size:11px; color:var(--text-muted); }
        .footer-right .dot { width:5px; height:5px; background:var(--green-ok); border-radius:50%; }
    </style>
</head>
<body>
<div class="shell">

    {{-- Topbar --}}
    <header class="topbar">
        <div class="topbar-brand">
            <div class="topbar-icon"><i class="fas fa-hospital-symbol"></i></div>
            <div>
                <div class="topbar-name">{{ config('app.name', 'DMO Audit Portal') }}</div>
                <div class="topbar-sub">Atal Amrit Abhiyan Society, Assam</div>
            </div>
        </div>
        <div class="topbar-badge"><span class="pulse"></span> Secure Portal</div>
    </header>

    <main class="main">

        {{-- Left panel --}}
        <div class="left-panel">
            <div class="panel-content">
                <div class="seal"><i class="fas fa-shield-halved"></i></div>
                <h1 class="panel-title">District Medical Officer<br>Management System</h1>
                <p class="panel-desc">Centralised audit and reporting platform for health scheme monitoring across all districts of Assam under Atal Amrit Abhiyan Society.</p>
                <div class="stat-grid">
                    <div class="stat-card"><div class="stat-num">35</div><div class="stat-lbl">Districts covered</div></div>
                    <div class="stat-card"><div class="stat-num">AB-PMJAY | AA-MMJAY</div><div class="stat-lbl">Scheme monitored</div></div>
                </div>
                <hr class="divider">
                <div class="notice-list">
                    <div class="notice-item"><i class="fas fa-location-dot"></i><span>Location access is required for accurate district-level data submission.</span></div>
                    <div class="notice-item"><i class="fas fa-robot"></i><span>AI-assisted insights are for decision support only — verify before acting.</span></div>
                    <div class="notice-item"><i class="fas fa-user-shield"></i><span>Authorised District Medical Officers only.</span></div>
                </div>
            </div>
        </div>

        {{-- Right panel --}}
        <div class="right-panel">

            @if (session('status'))
                <div class="alert alert-success"><i class="fas fa-circle-check"></i> {{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-circle-exclamation"></i>
                    <div>
                        <div style="font-weight:600;margin-bottom:4px;">Something went wrong</div>
                        <ul style="padding-left:16px;margin:0;">
                            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="eyebrow">Secure login — Step 1 of 2</div>
            <h2 class="form-title">Enter your mobile</h2>
            <p class="form-sub">We'll send a one-time password to your registered number.</p>

            <form id="mobileForm" method="POST" action="{{ route('auth.send.otp') }}" novalidate>
                @csrf
                <div class="field">
                    <label for="mobile">Registered mobile number</label>
                    <div class="input-wrap">
                        <i class="fas fa-mobile-screen ico"></i>
                        <input type="tel" id="mobile" name="mobile" value="{{ old('mobile') }}"
                               placeholder="10-digit mobile number" maxlength="10"
                               inputmode="numeric" pattern="[6-9][0-9]{9}" autocomplete="tel" required />
                    </div>
                    <div class="field-error" id="mobileError">Please enter a valid 10-digit mobile number.</div>
                </div>

                <button type="submit" class="btn-primary" id="sendBtn">
                    <span class="spinner" id="spinner"></span>
                    <i class="fas fa-paper-plane" id="sendIcon"></i>
                    <span id="sendLabel">Send OTP</span>
                </button>
            </form>

            <div class="security-note">
                <i class="fas fa-lock"></i>
                <span>Your session is secured end-to-end. This portal is for authorised District Medical Officer under Atal Amrit Abhiyan Society personnel only.</span>
            </div>
        </div>
    </main>

    <footer class="footer">
        <span class="footer-left">&copy; {{ date('Y') }} Atal Amrit Abhiyan Society &middot; Govt. of Assam</span>
        <div class="footer-right"><span class="dot"></span> Powered by IT Cell, SHA Assam</div>
    </footer>

</div>
<script>
(function(){
    const form     = document.getElementById('mobileForm');
    const input    = document.getElementById('mobile');
    const errEl    = document.getElementById('mobileError');
    const spinner  = document.getElementById('spinner');
    const sendIcon = document.getElementById('sendIcon');
    const sendLabel= document.getElementById('sendLabel');
    const sendBtn  = document.getElementById('sendBtn');

    /* Only allow numeric input */
    input.addEventListener('input', function(){
        this.value = this.value.replace(/\D/g,'').slice(0,10);
        this.classList.remove('error');
        errEl.classList.remove('show');
    });

    form.addEventListener('submit', function(e){
        errEl.classList.remove('show');
        input.classList.remove('error');
        if(!/^[6-9][0-9]{9}$/.test(input.value.trim())){
            e.preventDefault();
            input.classList.add('error');
            errEl.classList.add('show');
            input.focus();
            return;
        }
        spinner.style.display = 'block';
        sendIcon.style.display = 'none';
        sendLabel.textContent = 'Sending…';
        sendBtn.disabled = true;
    });
})();
</script>
</body>
</html>

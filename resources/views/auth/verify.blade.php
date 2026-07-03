<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'DMO Audit Portal') }} — Verify OTP</title>

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

        /* ── Topbar ── */
        .topbar { height: 60px; background: var(--navy-deep); border-bottom: 0.5px solid var(--border-navy); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; flex-shrink: 0; }
        .topbar-brand { display: flex; align-items: center; gap: 11px; }
        .topbar-icon { width: 36px; height: 36px; background: var(--navy-mid); border: 0.5px solid var(--border-navy); border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        .topbar-icon i { color: rgba(255,255,255,0.75); font-size: 16px; }
        .topbar-name { font-size: 14px; font-weight: 600; color: #fff; }
        .topbar-sub  { font-size: 11px; color: var(--text-muted); margin-top: 1px; }
        .topbar-badge { display: flex; align-items: center; gap: 6px; background: rgba(34,197,94,0.1); border: 0.5px solid rgba(34,197,94,0.25); border-radius: 20px; padding: 4px 12px; font-size: 11px; color: rgba(34,197,94,0.9); }
        .topbar-badge .pulse { width: 6px; height: 6px; background: var(--green-ok); border-radius: 50%; animation: pulse 2s ease-in-out infinite; }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(.8)} }

        /* ── Main grid ── */
        .main { flex: 1; display: grid; grid-template-columns: 1fr 460px; }

        /* ── Left panel ── */
        .left-panel { background: var(--navy); padding: 48px 44px; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden; }
        .left-panel::before { content:''; position:absolute; inset:0; background-image: linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px); background-size:40px 40px; pointer-events:none; }
        .left-panel::after  { content:''; position:absolute; bottom:-80px; right:-80px; width:360px; height:360px; background:radial-gradient(circle,rgba(59,130,246,0.12) 0%,transparent 70%); pointer-events:none; }
        .panel-content { position:relative; z-index:1; }

        /* Step tracker */
        .step-track { display:flex; flex-direction:column; gap:0; margin-bottom:40px; }
        .step-item  { display:flex; align-items:flex-start; gap:16px; position:relative; }
        .step-item:not(:last-child)::after { content:''; position:absolute; left:15px; top:32px; width:1px; height:32px; background:var(--border-navy); }
        .step-circle { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:13px; font-weight:600; border:0.5px solid var(--border-navy); }
        .step-circle.done    { background:rgba(34,197,94,0.15); border-color:rgba(34,197,94,0.4); color:var(--green-ok); }
        .step-circle.active  { background:rgba(255,255,255,0.12); border-color:rgba(255,255,255,0.3); color:#fff; }
        .step-circle.pending { background:transparent; color:var(--text-muted); }
        .step-text  { padding-top:6px; padding-bottom:24px; }
        .step-label { font-size:13px; font-weight:600; color:#fff; }
        .step-label.muted { color:var(--text-muted); font-weight:400; }
        .step-hint  { font-size:11px; color:var(--text-muted); margin-top:2px; }

        .seal { width:60px; height:60px; background:rgba(255,255,255,0.07); border:0.5px solid var(--border-navy); border-radius:14px; display:flex; align-items:center; justify-content:center; margin-bottom:28px; }
        .seal i { font-size:26px; color:rgba(255,255,255,0.7); }
        .panel-title { font-size:22px; font-weight:600; color:#fff; line-height:1.3; margin-bottom:10px; }
        .panel-desc  { font-size:13px; color:var(--text-muted); line-height:1.7; margin-bottom:36px; }
        .divider     { border:none; border-top:0.5px solid var(--border-navy); margin:0 0 24px; }
        .notice-list { display:flex; flex-direction:column; gap:12px; }
        .notice-item { display:flex; gap:10px; align-items:flex-start; }
        .notice-item i { font-size:13px; margin-top:3px; flex-shrink:0; color:rgba(255,255,255,0.3); }
        .notice-item span { font-size:12px; color:var(--text-muted); line-height:1.6; }

        /* ── Right panel ── */
        .right-panel { background:#f8fafc; padding:44px 40px; display:flex; flex-direction:column; justify-content:center; }
        .eyebrow     { font-size:10px; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:#64748b; margin-bottom:6px; }
        .form-title  { font-size:22px; font-weight:600; color:#0f172a; margin-bottom:4px; }
        .form-sub    { font-size:13px; color:#64748b; margin-bottom:20px; line-height:1.55; }

        /* Mobile pill */
        .mobile-pill { display:inline-flex; align-items:center; gap:7px; background:#eff6ff; border:0.5px solid #bfdbfe; border-radius:20px; padding:5px 12px; margin-bottom:20px; }
        .mobile-pill i { font-size:13px; color:#3b82f6; }
        .mobile-pill span { font-size:13px; font-weight:600; color:#1e40af; }
        .mobile-pill a { font-size:11px; color:#64748b; margin-left:6px; text-decoration:none; }
        .mobile-pill a:hover { color:var(--navy); text-decoration:underline; }

        /* ── Mobile context strip (replaces left panel on small screens) ── */
        .mobile-context { display:none; background:var(--navy); padding:14px 20px; border-bottom:0.5px solid var(--border-navy); }
        .mobile-context-inner { display:flex; align-items:center; justify-content:space-between; }
        .mobile-step-badge { display:flex; align-items:center; gap:8px; }
        .mobile-step-done { width:22px; height:22px; background:rgba(34,197,94,0.2); border:0.5px solid rgba(34,197,94,0.4); border-radius:50%; display:flex; align-items:center; justify-content:center; }
        .mobile-step-done i { font-size:10px; color:var(--green-ok); }
        .mobile-step-active { width:22px; height:22px; background:rgba(255,255,255,0.15); border:0.5px solid rgba(255,255,255,0.3); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:600; color:#fff; }
        .mobile-step-line { width:20px; height:1px; background:var(--border-navy); }
        .mobile-step-pending { width:22px; height:22px; background:transparent; border:0.5px solid var(--border-navy); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; color:var(--text-muted); }
        .mobile-step-label { font-size:12px; color:rgba(255,255,255,0.7); }
        .mobile-num { font-size:12px; color:var(--text-muted); }
        .mobile-num a { color:rgba(255,255,255,0.45); font-size:11px; text-decoration:none; margin-left:6px; }

        .alert { border-radius:var(--radius-md); padding:12px 14px; font-size:13px; margin-bottom:16px; display:flex; gap:9px; align-items:flex-start; }
        .alert-success { background:#f0fdf4; border:0.5px solid #86efac; color:#166534; }
        .alert-error   { background:#fef2f2; border:0.5px solid #fca5a5; color:#991b1b; }
        .alert i { flex-shrink:0; margin-top:1px; }

        /* ── OTP input ── */
        .otp-label { font-size:12px; font-weight:600; color:#374151; margin-bottom:10px; display:block; }
        .otp-input { width:100%; height:52px; border:1px solid #e2e8f0; border-radius:var(--radius-md); padding:0 14px; font-size:18px; font-weight:600; font-family:var(--font); color:#0f172a; background:#fff; outline:none; transition:border-color .15s, box-shadow .15s; caret-color:var(--navy); margin-bottom:10px; letter-spacing:4px; }
        .otp-input:focus { border-color:var(--navy); box-shadow:0 0 0 3px rgba(15,37,88,.1); }
        .otp-input.error { border-color:var(--red-err); box-shadow:0 0 0 3px rgba(239,68,68,.1); }

        .field-error { font-size:11px; color:var(--red-err); margin-bottom:12px; display:none; }
        .field-error.show { display:block; }

        /* Resend row */
        .resend-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
        .resend-label { font-size:12px; color:#64748b; }
        #resendBtn { font-size:12px; font-family:var(--font); color:var(--navy); background:none; border:none; cursor:pointer; padding:0; font-weight:600; }
        #resendBtn:disabled { opacity:.35; cursor:default; }

        /* Buttons */
        .btn-primary { width:100%; height:42px; background:var(--navy); color:#fff; border:none; border-radius:var(--radius-md); font-size:14px; font-weight:600; font-family:var(--font); cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:background .15s, transform .1s; }
        .btn-primary:hover  { background:var(--navy-mid); }
        .btn-primary:active { transform:scale(.98); }
        .btn-primary i { font-size:15px; }
        .btn-ghost { width:100%; height:38px; background:transparent; color:#64748b; border:1px solid #e2e8f0; border-radius:var(--radius-md); font-size:13px; font-family:var(--font); cursor:pointer; margin-top:10px; transition:background .15s; }
        .btn-ghost:hover { background:#f1f5f9; }

        .spinner { width:16px; height:16px; border:2px solid rgba(255,255,255,.3); border-top-color:#fff; border-radius:50%; animation:spin .7s linear infinite; display:none; }
        @keyframes spin { to{transform:rotate(360deg)} }

        .security-note { display:flex; gap:9px; align-items:flex-start; background:#f8fafc; border:0.5px solid #e2e8f0; border-radius:var(--radius-md); padding:11px 14px; margin-top:16px; }
        .security-note i { color:#94a3b8; font-size:13px; margin-top:2px; flex-shrink:0; }
        .security-note span { font-size:11px; color:#94a3b8; line-height:1.55; }

        /* ── Footer ── */
        .footer { background:var(--navy-deep); border-top:0.5px solid var(--border-navy); padding:14px 28px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
        .footer-left  { font-size:11px; color:var(--text-muted); }
        .footer-right { display:flex; align-items:center; gap:6px; font-size:11px; color:var(--text-muted); }
        .footer-right .dot { width:5px; height:5px; background:var(--green-ok); border-radius:50%; }

        /* ══════════════════════════════════════════
           MOBILE OVERRIDES  ≤ 768px
           Goal: entire form visible without scrolling
           ══════════════════════════════════════════ */
        @media (max-width: 768px) {
            /* Hide desktop left panel, show context strip */
            .left-panel { display: none; }
            .mobile-context { display: block; }

            /* Single-column layout */
            .main { grid-template-columns: 1fr; }

            /* Topbar: hide subtitle text to save height */
            .topbar { height: 52px; padding: 0 16px; }
            .topbar-sub { display: none; }
            .topbar-name { font-size: 13px; }
            .topbar-badge { padding: 3px 10px; font-size: 10px; }

            /* Right panel: tighter padding, no centering — let content sit at top */
            .right-panel {
                padding: 20px 20px 16px;
                justify-content: flex-start;
            }

            /* Compress text chrome */
            .eyebrow { margin-bottom: 3px; }
            .form-title { font-size: 18px; margin-bottom: 3px; }
            .form-sub { font-size: 12px; margin-bottom: 14px; line-height: 1.45; }

            /* Mobile pill tighter */
            .mobile-pill { margin-bottom: 14px; }

            /* OTP input: slightly shorter on small screens */
            .otp-input { height: 46px; font-size: 16px; }

            /* Resend row tighter */
            .resend-row { margin-bottom: 14px; }

            /* Hide desktop-style security note on mobile — info is in context strip */
            .security-note { display: none; }

            /* Back button: link style instead of full-width ghost */
            .btn-ghost {
                height: auto;
                border: none;
                padding: 0;
                margin-top: 14px;
                font-size: 12px;
                color: #64748b;
                background: none;
                text-align: left;
                width: auto;
            }

            /* Footer: single line, minimal */
            .footer { padding: 10px 16px; }
            .footer-right { display: none; }
        }

        /* Extra small phones (iPhone SE etc.) */
        @media (max-width: 390px) {
            .otp-input { height: 44px; font-size: 15px; }
            .right-panel { padding: 16px 16px 12px; }
            .form-title { font-size: 17px; }
        }
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


    {{-- Mobile-only context strip: step progress + masked number --}}
    <div class="mobile-context">
        <div class="mobile-context-inner">
            <div class="mobile-step-badge">
                <div class="mobile-step-done"><i class="fas fa-check"></i></div>
                <div class="mobile-step-line"></div>
                <div class="mobile-step-active">2</div>
                <div class="mobile-step-line"></div>
                <div class="mobile-step-pending">3</div>
                <span class="mobile-step-label" style="margin-left:10px;">Verify OTP</span>
            </div>
            @php
                $mob = request('mobile', '');
                $masked = strlen($mob) === 10 ? substr($mob,0,2).'XXXXXX'.substr($mob,-2) : '';
            @endphp
            <div class="mobile-num">{{ $masked }}<a href="{{ route('auth.login') }}">Change</a></div>
        </div>
    </div>

    <main class="main">

        {{-- Left panel --}}
        <div class="left-panel">
            <div class="panel-content">
                <div class="seal"><i class="fas fa-shield-halved"></i></div>
                <h1 class="panel-title">Verifying your<br>identity</h1>
                <p class="panel-desc">You're one step away from accessing the DMO Audit Portal. Enter the OTP sent to your registered number to continue.</p>

                {{-- Step tracker --}}
                <div class="step-track">
                    <div class="step-item">
                        <div class="step-circle done"><i class="fas fa-check" style="font-size:12px;"></i></div>
                        <div class="step-text">
                            <div class="step-label">Mobile verified</div>
                            {{-- Show masked number from request --}}
                            <div class="step-hint">
                                @php
                                    $mob = request('mobile', '');
                                    $masked = strlen($mob) === 10
                                        ? '+91 ' . substr($mob,0,2) . 'XXXXXX' . substr($mob,-2)
                                        : 'your number';
                                @endphp
                                {{ $masked }}
                            </div>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-circle active">2</div>
                        <div class="step-text">
                            <div class="step-label">Enter OTP</div>
                            <div class="step-hint">Check your SMS inbox</div>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-circle pending">3</div>
                        <div class="step-text">
                            <div class="step-label muted">Access portal</div>
                            <div class="step-hint">You'll be logged in</div>
                        </div>
                    </div>
                </div>

                <hr class="divider">
                <div class="notice-list">
                    <div class="notice-item"><i class="fas fa-triangle-exclamation"></i><span>Never share your OTP with anyone, including Govt. officials.</span></div>
                    <div class="notice-item"><i class="fas fa-clock"></i><span>The OTP is valid for 10 minutes. Request a new one if it expires.</span></div>
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
                        <div style="font-weight:600;margin-bottom:4px;">Verification failed</div>
                        <ul style="padding-left:16px;margin:0;">
                            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="eyebrow">Secure login — Step 2 of 2</div>
            <h2 class="form-title">Enter your OTP</h2>
            <p class="form-sub">A 6-digit code was sent via SMS to your registered mobile number.</p>

            {{-- Mobile pill --}}
            <div class="mobile-pill">
                <i class="fas fa-mobile-screen"></i>
                <span>{{ request('mobile') }}</span>
                <a href="{{ route('auth.login') }}">Change</a>
            </div>

            <form id="otpForm" method="POST" action="{{ route('auth.otp.verify') }}" novalidate>
                @csrf
                {{-- Pass mobile through to controller --}}
                <input type="hidden" name="mobile" value="{{ request('mobile') }}" />
                <label class="otp-label" for="otpInput">One-time password</label>
                <input class="otp-input" type="number" id="otpInput" name="otp" maxlength="6" inputmode="numeric" placeholder="Enter 6-digit OTP" autofocus autocomplete="one-time-code" />
                <div class="field-error" id="otpError">Please enter all 6 digits of the OTP.</div>

                <div class="resend-row">
                    <span class="resend-label" id="timerLabel">Resend in <strong id="timerCount">30</strong>s</span>
                    <button type="button" id="resendBtn" disabled>Resend OTP</button>
                </div>

                <button type="submit" class="btn-primary" id="verifyBtn">
                    <span class="spinner" id="spinner"></span>
                    <i class="fas fa-lock-open" id="verifyIcon"></i>
                    <span id="verifyLabel">Verify &amp; Log In</span>
                </button>
            </form>

            <a href="{{ route('auth.login') }}">
                <button type="button" class="btn-ghost">← Back to login</button>
            </a>

            <div class="security-note">
                <i class="fas fa-lock"></i>
                <span>Your session is secured end-to-end. Do not share this OTP with anyone.</span>
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
    const otpInput  = document.getElementById('otpInput');
    const otpError  = document.getElementById('otpError');
    const form      = document.getElementById('otpForm');
    const verifyBtn = document.getElementById('verifyBtn');
    const spinner   = document.getElementById('spinner');
    const verifyIcon= document.getElementById('verifyIcon');
    const verifyLabel=document.getElementById('verifyLabel');
    const timerLabel= document.getElementById('timerLabel');
    const timerCount= document.getElementById('timerCount');
    const resendBtn = document.getElementById('resendBtn');

    /* ── Timer ── */
    let timerInterval;
    function startTimer(seconds) {
        clearInterval(timerInterval);
        let t = seconds;
        timerCount.textContent = t;
        timerLabel.style.display = '';
        resendBtn.disabled = true;
        timerInterval = setInterval(function(){
            t--;
            timerCount.textContent = t;
            if(t <= 0){
                clearInterval(timerInterval);
                timerLabel.style.display = 'none';
                resendBtn.disabled = false;
            }
        }, 1000);
    }
    startTimer(30);

    /* ── OTP input: restrict to 6 digits ── */
    otpInput.addEventListener('input', function(){
        otpInput.value = otpInput.value.replace(/\D/g,'').slice(0,6);
        otpError.classList.remove('show');
        otpInput.classList.remove('error');
    });

    /* ── Form submit ── */
    form.addEventListener('submit', function(e){
        var otp = otpInput.value.replace(/\D/g,'');
        otpError.classList.remove('show');
        otpInput.classList.remove('error');
        if(otp.length !== 6){
            e.preventDefault();
            otpInput.classList.add('error');
            otpError.classList.add('show');
            otpInput.focus();
            return;
        }
        otpInput.value = otp;
        spinner.style.display  = 'block';
        verifyIcon.style.display = 'none';
        verifyLabel.textContent = 'Verifying…';
        verifyBtn.disabled = true;
    });

    /* ── Resend ── */
    resendBtn.addEventListener('click', function(){
        var f = document.createElement('form');
        f.method = 'POST';
        f.action = '{{ route("auth.send.otp") }}';
        var csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token'; csrf.value='{{ csrf_token() }}';
        var mob  = document.createElement('input'); mob.type='hidden';  mob.name='mobile';  mob.value='{{ request("mobile") }}';
        f.appendChild(csrf); f.appendChild(mob);
        document.body.appendChild(f);
        f.submit();
    });
})();
</script>
</body>
</html>

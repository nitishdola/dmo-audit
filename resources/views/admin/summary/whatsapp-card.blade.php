<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=1080"/>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

  *  { margin:0; padding:0; box-sizing:border-box; }
  body {
    width:1080px; min-height:1920px;
    background:#0b1120;
    font-family:'Inter',sans-serif;
    color:#f1f5f9;
    padding:56px 60px 64px;
  }

  /* ── Header ── */
  .header {
    display:flex; align-items:flex-start; justify-content:space-between;
    padding-bottom:36px;
    border-bottom:2px solid #1e2d4a;
    margin-bottom:40px;
  }
  .logo-row { display:flex; align-items:center; gap:16px; }
  .logo-box {
    width:52px; height:52px; border-radius:14px;
    background:#10b981;
    display:flex; align-items:center; justify-content:center;
    font-size:24px; font-weight:900; color:#0b1120;
  }
  .brand-name   { font-size:13px; font-weight:600; color:#64748b; letter-spacing:2px; text-transform:uppercase; }
  .brand-title  { font-size:28px; font-weight:800; color:#f1f5f9; line-height:1.15; margin-top:3px; }
  .period-pill  {
    background:#1e2d4a; border:1px solid #2d3f5e;
    border-radius:10px; padding:10px 20px; text-align:right;
  }
  .period-label { font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:1.5px; }
  .period-value { font-size:16px; font-weight:700; color:#94a3b8; margin-top:4px; }

  /* ── Grand total strip ── */
  .total-strip {
    background:linear-gradient(135deg,#1b2e1f 0%,#0b1a10 100%);
    border:1.5px solid #1e4d2c;
    border-radius:20px;
    padding:36px 44px;
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:36px;
  }
  .total-left .label { font-size:13px; font-weight:600; color:#6ee7b7; letter-spacing:1.5px; text-transform:uppercase; }
  .total-num { font-size:72px; font-weight:900; color:#ffffff; line-height:1; margin:8px 0 4px; letter-spacing:-3px; }
  .total-sub { font-size:14px; color:#4ade80; font-weight:500; }
  .total-right { text-align:right; }
  .comp-label  { font-size:13px; color:#6ee7b7; font-weight:600; text-transform:uppercase; letter-spacing:1.5px; }
  .comp-pct    { font-size:56px; font-weight:900; color:#10b981; line-height:1; margin-top:6px; }
  .comp-sub    { font-size:14px; color:#4ade80; margin-top:4px; }

  /* ── Section header ── */
  .section-head {
    font-size:11px; font-weight:700; color:#64748b;
    letter-spacing:2px; text-transform:uppercase;
    margin:40px 0 16px;
    display:flex; align-items:center; gap:14px;
  }
  .section-head::after { content:''; flex:1; height:1px; background:#1e2d4a; }

  /* ── Audit type cards row ── */
  .audit-cards { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:4px; }
  .audit-card {
    border-radius:18px;
    padding:30px 28px;
    position:relative; overflow:hidden;
  }
  .audit-card::before {
    content:''; position:absolute;
    top:0; left:0; right:0; height:4px;
    border-radius:18px 18px 0 0;
  }
  .card-tele   { background:#0c1e2e; border:1.5px solid #0e3a5a; }
  .card-tele::before   { background:linear-gradient(90deg,#0ea5e9,#38bdf8); }
  .card-field  { background:#1a160a; border:1.5px solid #3d2c0a; }
  .card-field::before  { background:linear-gradient(90deg,#f59e0b,#fbbf24); }
  .card-live   { background:#160d2a; border:1.5px solid #3b1d6e; }
  .card-live::before   { background:linear-gradient(90deg,#8b5cf6,#a78bfa); }
  .card-infra  { background:#200d14; border:1.5px solid #5b1532; }
  .card-infra::before  { background:linear-gradient(90deg,#f43f5e,#fb7185); }

  .card-label { font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase; margin-bottom:10px; }
  .tele-label  { color:#38bdf8; }
  .field-label { color:#fbbf24; }
  .live-label  { color:#a78bfa; }
  .infra-label { color:#fb7185; }

  .card-count { font-size:52px; font-weight:900; color:#ffffff; line-height:1; letter-spacing:-2px; }
  .card-sub   { font-size:13px; color:#64748b; margin-top:6px; }
  .card-sub strong { font-weight:700; }

  /* progress bar */
  .prog-wrap { margin-top:20px; }
  .prog-bar  { height:6px; border-radius:3px; background:#1e2d4a; overflow:hidden; }
  .prog-fill { height:100%; border-radius:3px; }
  .fill-tele  { background:#0ea5e9; }
  .fill-field { background:#f59e0b; }
  .prog-label { display:flex; justify-content:space-between; align-items:center; margin-top:8px; }
  .prog-pct   { font-size:22px; font-weight:800; }
  .tele-pct  { color:#38bdf8; }
  .field-pct { color:#fbbf24; }
  .prog-done  { font-size:12px; color:#64748b; }

  /* chip row for live / infra stats */
  .chip-row { display:flex; flex-wrap:wrap; gap:10px; margin-top:18px; }
  .chip {
    padding:6px 14px; border-radius:20px;
    font-size:12px; font-weight:600;
  }
  .chip-money  { background:#4c0519; color:#fb7185; border:1px solid #881337; }
  .chip-pub    { background:#0c1e2e; color:#38bdf8; border:1px solid #0e3a5a; }
  .chip-priv   { background:#1c102e; color:#a78bfa; border:1px solid #3b1d6e; }
  .chip-icu    { background:#1b2e1f; color:#4ade80; border:1px solid #1e4d2c; }
  .chip-ot     { background:#1a160a; color:#fbbf24; border:1px solid #3d2c0a; }
  .chip-banner-ok   { background:#1b2e1f; color:#4ade80; border:1px solid #1e4d2c; }
  .chip-banner-fail { background:#200d14; color:#fb7185; border:1px solid #5b1532; }
  .chip-good   { background:#1b2e1f; color:#4ade80; border:1px solid #1e4d2c; }
  .chip-avg    { background:#1a160a; color:#fbbf24; border:1px solid #3d2c0a; }
  .chip-poor   { background:#200d14; color:#fb7185; border:1px solid #5b1532; }

  /* ── ALERT: money charged ── */
  .money-alert {
    background:#200d14;
    border:1.5px solid #881337;
    border-radius:16px;
    padding:24px 28px;
    display:flex; align-items:center; gap:20px;
    margin-top:36px;
  }
  .money-icon {
    width:52px; height:52px; border-radius:50%;
    background:#5b1532;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0;
    font-size:22px;
  }
  .money-body .money-head { font-size:12px; font-weight:700; color:#fb7185; letter-spacing:1.5px; text-transform:uppercase; }
  .money-body .money-num  { font-size:42px; font-weight:900; color:#f43f5e; line-height:1; margin:4px 0 2px; }
  .money-body .money-sub  { font-size:13px; color:#94a3b8; }
  .money-badge { margin-left:auto; background:#f43f5e; color:#fff; border-radius:10px; padding:6px 16px; font-size:12px; font-weight:700; }

  /* ── DMO Leaderboard ── */
  .dmo-list { display:flex; flex-direction:column; gap:10px; }
  .dmo-row {
    background:#111c2e; border:1px solid #1e2d4a;
    border-radius:14px; padding:18px 22px;
    display:flex; align-items:center; gap:16px;
  }
  .rank-badge {
    width:34px; height:34px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:14px; font-weight:800; flex-shrink:0;
  }
  .rank-1 { background:#fbbf24; color:#1a0f00; }
  .rank-2 { background:#94a3b8; color:#0b1120; }
  .rank-3 { background:#b45309; color:#fff8f0; }
  .rank-n { background:#1e2d4a; color:#64748b; }
  .avatar {
    width:40px; height:40px; border-radius:12px;
    display:flex; align-items:center; justify-content:center;
    font-size:14px; font-weight:700; flex-shrink:0;
  }
  .dmo-info { flex:1; min-width:0; }
  .dmo-name { font-size:15px; font-weight:700; color:#f1f5f9; }
  .dmo-dist { font-size:12px; color:#64748b; margin-top:2px; }
  .dmo-chips { display:flex; gap:8px; flex-shrink:0; }
  .dc { padding:4px 10px; border-radius:8px; font-size:11px; font-weight:700; }
  .dc-t { background:#0c1e2e; color:#38bdf8; }
  .dc-f { background:#1a160a; color:#fbbf24; }
  .dc-l { background:#160d2a; color:#a78bfa; }
  .dc-i { background:#200d14; color:#fb7185; }
  .dc-m { background:#200d14; color:#f43f5e; border:1px solid #881337; } /* money alert chip */
  .dmo-total { font-size:22px; font-weight:900; color:#f1f5f9; flex-shrink:0; min-width:52px; text-align:right; }

  /* ── District breakdown ── */
  .district-list { display:flex; flex-direction:column; gap:10px; }
  .dist-row {
    background:#111c2e; border:1px solid #1e2d4a;
    border-radius:14px; padding:16px 22px;
    display:flex; align-items:center; gap:16px;
  }
  .dist-name { font-size:14px; font-weight:600; color:#f1f5f9; flex:1; min-width:0; }
  .dist-bar-wrap { flex:1; }
  .dist-bar-outer { height:8px; background:#1e2d4a; border-radius:4px; overflow:hidden; }
  .dist-bar-fill  { height:100%; border-radius:4px; background:linear-gradient(90deg,#0ea5e9,#8b5cf6); }
  .dist-count { font-size:16px; font-weight:800; color:#f1f5f9; min-width:44px; text-align:right; }

  /* ── Footer ── */
  .footer {
    margin-top:44px; padding-top:28px;
    border-top:1px solid #1e2d4a;
    display:flex; align-items:center; justify-content:space-between;
  }
  .footer-brand { font-size:12px; color:#475569; }
  .footer-ts    { font-size:11px; color:#334155; }
</style>
</head>
<body>

{{-- ══ HEADER ══ --}}
<div class="header">
  <div class="logo-row">
    <div class="logo-box">P</div>
    <div>
      <div class="brand-name">AB PMJAY Assam · Audit Division</div>
      <div class="brand-title">Monthly Audit Summary</div>
    </div>
  </div>
  <div class="period-pill">
    <div class="period-label">Period</div>
    <div class="period-value">{{ $from }} – {{ $to }}</div>
  </div>
</div>

{{-- ══ GRAND TOTAL STRIP ══ --}}
<div class="total-strip">
  <div class="total-left">
    <div class="label">Total Audits Conducted</div>
    <div class="total-num">{{ number_format($grand_total) }}</div>
    <div class="total-sub">All types combined — last 30 days</div>
  </div>
  <div class="total-right">
    <div class="comp-label">Completion</div>
    <div class="comp-pct">{{ $comp_rate }}%</div>
    <div class="comp-sub">{{ number_format($grand_completed) }} of {{ number_format($grand_total) }}</div>
  </div>
</div>

{{-- ══ AUDIT TYPE BREAKDOWN ══ --}}
<div class="section-head">Audit type breakdown</div>

<div class="audit-cards">

  {{-- Telephonic --}}
  <div class="audit-card card-tele">
    <div class="card-label tele-label">Telephonic Audit</div>
    <div class="card-count">{{ number_format($tele_total) }}</div>
    <div class="card-sub">Completed: <strong style="color:#38bdf8">{{ number_format($tele_completed) }}</strong></div>
    <div class="prog-wrap">
      <div class="prog-bar"><div class="prog-fill fill-tele" style="width:{{ $tele_rate }}%"></div></div>
      <div class="prog-label">
        <div class="prog-pct tele-pct">{{ $tele_rate }}%</div>
        <div class="prog-done">{{ number_format($tele_total - $tele_completed) }} pending</div>
      </div>
    </div>
  </div>

  {{-- Field Visits --}}
  <div class="audit-card card-field">
    <div class="card-label field-label">Field Visits</div>
    <div class="card-count">{{ number_format($field_total) }}</div>
    <div class="card-sub">Completed: <strong style="color:#fbbf24">{{ number_format($field_completed) }}</strong></div>
    <div class="prog-wrap">
      <div class="prog-bar"><div class="prog-fill fill-field" style="width:{{ $field_rate }}%"></div></div>
      <div class="prog-label">
        <div class="prog-pct field-pct">{{ $field_rate }}%</div>
        <div class="prog-done">{{ number_format($field_total - $field_completed) }} pending</div>
      </div>
    </div>
  </div>

  {{-- Live Audits --}}
  <div class="audit-card card-live">
    <div class="card-label live-label">Live Audit</div>
    <div class="card-count">{{ number_format($live_total) }}</div>
    <div class="card-sub">On-site beneficiary verification</div>
    <div class="chip-row" style="margin-top:16px;">
      <div class="chip chip-money">
        ⚠ {{ number_format($money_charged) }} money charged
      </div>
    </div>
  </div>

  {{-- Infrastructure Audits --}}
  <div class="audit-card card-infra">
    <div class="card-label infra-label">Infrastructure Audit</div>
    <div class="card-count">{{ number_format($infra_total) }}</div>
    <div class="card-sub">Hospital facility inspections</div>
    <div class="chip-row">
      <div class="chip chip-pub">Public: {{ $infra_public }}</div>
      <div class="chip chip-priv">Private: {{ $infra_private }}</div>
    </div>
    <div class="chip-row" style="margin-top:6px;">
      <div class="chip chip-icu">ICU: {{ $infra_icu }}</div>
      <div class="chip chip-ot">OT: {{ $infra_ot }}</div>
    </div>
    <div class="chip-row" style="margin-top:6px;">
      <div class="chip chip-good">Hygiene Good: {{ $infra_hygiene_good }}</div>
      <div class="chip chip-poor">Poor: {{ $infra_hygiene_poor }}</div>
    </div>
    <div class="chip-row" style="margin-top:6px;">
      <div class="chip chip-banner-ok">Banner ✓ {{ $infra_banner_passed }}</div>
      <div class="chip chip-banner-fail">✗ {{ $infra_banner_failed }}</div>
    </div>
  </div>

</div>

{{-- ══ MONEY CHARGED ALERT ══ --}}
@if($money_charged > 0)
<div class="money-alert">
  <div class="money-icon">⚠</div>
  <div class="money-body">
    <div class="money-head">Money Charged from Beneficiaries</div>
    <div class="money-num">{{ number_format($money_charged) }}</div>
    <div class="money-sub">Live audit cases where payment was collected</div>
  </div>
  <div class="money-badge">Action Required</div>
</div>
@endif

{{-- ══ DMO LEADERBOARD ══ --}}
<div class="section-head">Top performing DMOs</div>

<div class="dmo-list">
  @php
    $avatarColors = [
      ['bg'=>'#1e3a2f','color'=>'#4ade80'],
      ['bg'=>'#0c1e2e','color'=>'#38bdf8'],
      ['bg'=>'#1c102e','color'=>'#a78bfa'],
      ['bg'=>'#1a160a','color'=>'#fbbf24'],
      ['bg'=>'#200d14','color'=>'#fb7185'],
    ];
  @endphp
  @foreach($dmo_leaderboard as $i => $dmo)
  @php
    $ac  = $avatarColors[$i % count($avatarColors)];
    $initials = collect(explode(' ', $dmo['name']))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->implode('');
    $rankClass = match($i) { 0=>'rank-1', 1=>'rank-2', 2=>'rank-3', default=>'rank-n' };
  @endphp
  <div class="dmo-row">
    <div class="rank-badge {{ $rankClass }}">{{ $i + 1 }}</div>
    <div class="avatar" style="background:{{ $ac['bg'] }};color:{{ $ac['color'] }}">{{ $initials }}</div>
    <div class="dmo-info">
      <div class="dmo-name">{{ $dmo['name'] }}</div>
      <div class="dmo-dist">{{ $dmo['district'] }}</div>
    </div>
    <div class="dmo-chips">
      @if($dmo['tele']  > 0)<div class="dc dc-t">T: {{ $dmo['tele'] }}</div>@endif
      @if($dmo['field'] > 0)<div class="dc dc-f">F: {{ $dmo['field'] }}</div>@endif
      @if($dmo['live']  > 0)<div class="dc dc-l">L: {{ $dmo['live'] }}</div>@endif
      @if($dmo['infra'] > 0)<div class="dc dc-i">I: {{ $dmo['infra'] }}</div>@endif
      @if($dmo['money'] > 0)<div class="dc dc-m">₹ {{ $dmo['money'] }}</div>@endif
    </div>
    <div class="dmo-total">{{ number_format($dmo['total']) }}</div>
  </div>
  @endforeach

  @if($dmo_leaderboard->isEmpty())
  <div style="text-align:center;padding:24px;color:#475569;font-size:14px;">No DMO activity in this period</div>
  @endif
</div>

{{-- ══ DISTRICT BREAKDOWN ══ --}}
<div class="section-head">District breakdown (assigned audits)</div>

@php $distMax = $district_stats->max('total') ?: 1; @endphp

<div class="district-list">
  @foreach($district_stats as $dist)
  <div class="dist-row">
    <div class="dist-name">{{ $dist->district }}</div>
    <div class="dist-bar-wrap">
      <div class="dist-bar-outer">
        <div class="dist-bar-fill" style="width:{{ round($dist->total / $distMax * 100) }}%"></div>
      </div>
      <div style="display:flex;gap:10px;margin-top:6px;">
        <span style="font-size:11px;color:#38bdf8;">T: {{ $dist->tele }}</span>
        <span style="font-size:11px;color:#fbbf24;">F: {{ $dist->field_cnt }}</span>
      </div>
    </div>
    <div class="dist-count">{{ number_format($dist->total) }}</div>
  </div>
  @endforeach

  @if($district_stats->isEmpty())
  <div style="text-align:center;padding:24px;color:#475569;font-size:14px;">No district data in this period</div>
  @endif
</div>

{{-- ══ FOOTER ══ --}}
<div class="footer">
  <div class="footer-brand">PMJAY Audit Intelligence Platform · Assam State Health Agency</div>
  <div class="footer-ts">Generated {{ $generated_at }}</div>
</div>

</body>
</html>

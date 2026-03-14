{{--
    resources/views/admin/audits/_shared_css.blade.php
    Include with: @include('admin.audits._shared_css')
--}}
<style>
    .filter-bar { display:flex; flex-wrap:wrap; align-items:center; gap:.625rem; margin-bottom:1.5rem; }
    .filter-input { padding:.5rem .875rem; border:1.5px solid #e2e8f0; border-radius:.75rem; font-size:.8125rem; background:#fff; color:#334155; outline:none; transition:border-color .15s; }
    .filter-input:focus { border-color:#0ea5e9; }
    select.filter-input { cursor:pointer; }
    .filter-btn { display:inline-flex; align-items:center; gap:.35rem; padding:.5rem 1.125rem; border-radius:.75rem; font-size:.8125rem; font-weight:600; cursor:pointer; border:1.5px solid transparent; transition:all .15s; text-decoration:none; }
    .filter-btn-primary { background:#0f172a; color:#fff; border-color:#0f172a; }
    .filter-btn-primary:hover { background:#1e293b; }
    .filter-btn-ghost { background:#f1f5f9; color:#475569; border-color:#e2e8f0; }
    .filter-btn-ghost:hover { background:#e2e8f0; }

    .summary-chip { display:inline-flex; align-items:center; gap:.4rem; padding:.4rem .9rem; border-radius:9999px; font-size:.75rem; font-weight:700; }
    .chip-total   { background:#f1f5f9; color:#334155; }
    .chip-done    { background:#d1fae5; color:#065f46; }
    .chip-pending { background:#fef3c7; color:#92400e; }
    .chip-ai-pass { background:#d1fae5; color:#065f46; }
    .chip-ai-fail { background:#fee2e2; color:#991b1b; }
    .chip-ai-skip { background:#f1f5f9; color:#64748b; }

    .page-card { background:#fff; border:1px solid #e2e8f0; border-radius:1.25rem; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.04); }
    .audit-table { width:100%; border-collapse:collapse; }
    .audit-table thead th { padding:.75rem 1.25rem; text-align:left; font-size:.7rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:#94a3b8; background:#f8fafc; border-bottom:1px solid #e2e8f0; white-space:nowrap; }
    .audit-table tbody tr:hover { background:#f8fafc; }
    .audit-table tbody tr + tr { border-top:1px solid #f1f5f9; }
    .audit-table td { padding:.875rem 1.25rem; font-size:.8125rem; color:#334155; vertical-align:middle; }

    .status-badge { display:inline-flex; align-items:center; gap:.35rem; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:9999px; white-space:nowrap; }
    .status-completed { background:#d1fae5; color:#065f46; }
    .status-pending   { background:#fef3c7; color:#92400e; }

    .ai-badge { display:inline-flex; align-items:center; gap:.35rem; font-size:.7rem; font-weight:700; padding:.25rem .75rem; border-radius:9999px; }
    .ai-pass { background:#d1fae5; color:#065f46; }
    .ai-fail { background:#fee2e2; color:#991b1b; }
    .ai-skip { background:#f1f5f9; color:#64748b; }

    .action-btn { display:inline-flex; align-items:center; justify-content:center; width:1.875rem; height:1.875rem; border-radius:.625rem; color:#64748b; transition:background .12s, color .12s; text-decoration:none; }
    .action-btn:hover { background:#e0f2fe; color:#0369a1; }

    .empty-state { padding:3.5rem 1.5rem; text-align:center; }
    .empty-state i { font-size:2.5rem; color:#e2e8f0; margin-bottom:.875rem; display:block; }
    .empty-state p { color:#94a3b8; font-size:.875rem; }

    .table-footer { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:.75rem; padding:.875rem 1.25rem; background:#f8fafc; border-top:1px solid #e2e8f0; font-size:.8rem; color:#64748b; }
</style>

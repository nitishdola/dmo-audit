@extends('admin.layout.layout')

@section('page_title', 'PMJAY Records')

@section('main_title')
<div class="breadcrumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
    <span class="breadcrumb-current">PMJAY Records</span>
</div>
@endsection

@section('pageCss')
<style>
    /* ── Page header row ── */
    .page-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .page-header-left h1 {
        font-size: 1.375rem;
        font-weight: 800;
        color: #0f172a;
        font-family: 'Syne', sans-serif;
    }
    .page-header-left p {
        font-size: .8125rem;
        color: #94a3b8;
        margin-top: .25rem;
    }

    /* ── Stats strip ── */
    .stats-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stat-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: .875rem;
    }
    .stat-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: .75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .875rem;
        flex-shrink: 0;
    }
    .stat-icon.blue  { background: #eff6ff; color: #3b82f6; }
    .stat-icon.cyan  { background: #ecfeff; color: #06b6d4; }
    .stat-icon.green { background: #f0fdf4; color: #16a34a; }
    .stat-val {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0f172a;
        font-family: 'Syne', sans-serif;
        line-height: 1;
    }
    .stat-label {
        font-size: .7rem;
        color: #94a3b8;
        font-weight: 500;
        margin-top: .2rem;
    }

    /* ── Table card ── */
    .table-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 1.25rem;
        overflow: hidden;
    }
    .table-card-header {
        padding: 1.125rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .table-card-title {
        font-size: .9375rem;
        font-weight: 700;
        color: #0f172a;
    }
    .table-card-count {
        font-size: .75rem;
        color: #94a3b8;
        margin-top: .1rem;
    }

    /* Search */
    .search-wrap {
        position: relative;
    }
    .search-wrap i {
        position: absolute;
        left: .75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: .75rem;
        pointer-events: none;
    }
    .search-wrap input {
        padding: .5rem .875rem .5rem 2rem;
        border: 1.5px solid #e2e8f0;
        border-radius: .75rem;
        font-size: .8rem;
        color: #334155;
        background: #f8fafc;
        outline: none;
        transition: border-color .15s, background .15s;
        width: 220px;
    }
    .search-wrap input:focus {
        border-color: #06b6d4;
        background: #fff;
    }

    /* Table */
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table thead tr {
        background: #f8fafc;
    }
    .data-table th {
        padding: .75rem 1.25rem;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #64748b;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .data-table td {
        padding: .875rem 1.25rem;
        font-size: .8125rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td { background: #f8fafc; }
    .data-table tbody tr { transition: background .1s; }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: .2rem .6rem;
        border-radius: 9999px;
        font-size: .7rem;
        font-weight: 600;
    }
    .badge-blue  { background: #eff6ff; color: #2563eb; }
    .badge-green { background: #f0fdf4; color: #16a34a; }
    .badge-gray  { background: #f1f5f9; color: #64748b; }

    /* Hospital cell */
    .hospital-cell {
        display: flex;
        align-items: center;
        gap: .625rem;
    }
    .hospital-avatar {
        width: 1.875rem;
        height: 1.875rem;
        border-radius: .5rem;
        background: linear-gradient(135deg, #06b6d4, #3b82f6);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .625rem;
        font-weight: 800;
        color: #fff;
        flex-shrink: 0;
        font-family: 'Syne', sans-serif;
    }
    .hospital-name { font-weight: 600; color: #1e293b; }
    .hospital-district { font-size: .7rem; color: #94a3b8; margin-top: .05rem; }

    /* Empty state */
    .empty-state {
        padding: 4rem 1.5rem;
        text-align: center;
    }
    .empty-state-icon {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 1.25rem;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
        color: #94a3b8;
        margin: 0 auto 1rem;
    }
    .empty-state-title { font-size: .9375rem; font-weight: 700; color: #334155; }
    .empty-state-sub   { font-size: .8rem; color: #94a3b8; margin-top: .375rem; }

    /* Pagination */
    .pagination-wrap {
        padding: 1rem 1.5rem;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .pagination-info {
        font-size: .75rem;
        color: #94a3b8;
    }
    .pagination-links {
        display: flex;
        gap: .375rem;
    }
    .pagination-links a,
    .pagination-links span {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: .625rem;
        border: 1.5px solid #e2e8f0;
        font-size: .75rem;
        font-weight: 600;
        color: #64748b;
        text-decoration: none;
        transition: background .15s, color .15s, border-color .15s;
    }
    .pagination-links a:hover { background: #f8fafc; color: #334155; }
    .pagination-links span[aria-current="page"] {
        background: linear-gradient(135deg, #0ea5e9, #3b82f6);
        color: #fff;
        border-color: transparent;
    }

    /* Buttons */
    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .575rem 1.125rem;
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

    /* Responsive */
    @media (max-width: 768px) {
        .data-table th:nth-child(3),
        .data-table td:nth-child(3),
        .data-table th:nth-child(4),
        .data-table td:nth-child(4) { display: none; }
        .search-wrap input { width: 160px; }
    }
</style>
@endsection

@section('main_content')

{{-- Page header --}}
<div class="page-header">
    <div class="page-header-left">
        <h1>PMJAY Records</h1>
        <p>Browse and manage imported PMJAY treatment data.</p>
    </div>
    <a href="{{ route('admin.pmjay.upload') }}" class="btn-primary">
        <i class="fas fa-file-import"></i> Import Data
    </a>
</div>

{{-- Success alert --}}
@if(session('success'))
<div class="alert-success">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

{{-- Stats strip --}}
<div class="stats-strip">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-database"></i></div>
        <div>
            <div class="stat-val">{{ number_format($records->total()) }}</div>
            <div class="stat-label">Total Records</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cyan"><i class="fas fa-hospital"></i></div>
        <div>
            <div class="stat-val">{{ $records->pluck('hospital_id')->unique()->count() }}</div>
            <div class="stat-label">Hospitals</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-map-marker-alt"></i></div>
        <div>
            <div class="stat-val">{{ $records->pluck('hospital.district_id')->unique()->filter()->count() }}</div>
            <div class="stat-label">Districts</div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="table-card">
    <div class="table-card-header">
        <div>
            <div class="table-card-title">Treatment Records</div>
            <div class="table-card-count">Showing {{ $records->firstItem() }}–{{ $records->lastItem() }} of {{ number_format($records->total()) }} entries</div>
        </div>
        <div class="search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" id="table-search" placeholder="Search records…">
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="data-table" id="main-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Hospital</th>
                    <th>District</th>
                    <th>Treatment ID</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr>
                    <td style="color:#94a3b8; font-size:.75rem;">{{ $record->id }}</td>
                    <td>
                        <div class="hospital-cell">
                            <div class="hospital-avatar">
                                {{ strtoupper(substr($record->hospital->name ?? 'H', 0, 2)) }}
                            </div>
                            <div>
                                <div class="hospital-name">{{ $record->hospital->name ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($record->hospital?->district)
                            <span class="badge badge-blue">{{ $record->hospital->district->name }}</span>
                        @else
                            <span style="color:#cbd5e1; font-size:.75rem;">—</span>
                        @endif
                    </td>
                    <td>
                        <code style="font-size:.75rem; background:#f1f5f9; padding:.2rem .5rem; border-radius:.375rem; color:#475569;">
                            {{ $record->treatment_id ?? $record->id }}
                        </code>
                    </td>
                    <td>
                        <span class="badge badge-green">Active</span>
                    </td>
                    <td style="color:#64748b; font-size:.75rem; white-space:nowrap;">
                        {{ $record->created_at?->format('d M Y') ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fas fa-inbox"></i></div>
                            <div class="empty-state-title">No records found</div>
                            <div class="empty-state-sub">Import a PMJAY JSON file to get started.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($records->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Page {{ $records->currentPage() }} of {{ $records->lastPage() }}
        </div>
        <div class="pagination-links">
            {{ $records->links('pagination::simple-tailwind') }}
        </div>
    </div>
    @endif
</div>

@endsection

@section('pageJs')
<script>
// Client-side row search (supplements server pagination)
document.getElementById('table-search').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#main-table tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endsection
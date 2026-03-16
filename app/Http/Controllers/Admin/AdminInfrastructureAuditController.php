<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audits\InfrastructureAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminInfrastructureAuditController extends Controller
{
    // ── Index ──────────────────────────────────────────────────────────────

    /**
     * GET /admin/infra-audit
     * Paginated listing with filters + summary stats for the header KPI row.
     */
    public function index(Request $request): View
    {
        // ── Filters ───────────────────────────────────────────────────────
        $search       = $request->input('search');
        $type         = $request->input('type');          // Public | Private
        $bannerStatus = $request->input('banner');        // passed | failed | unchecked
        $hygiene      = $request->input('hygiene');       // Good | Average | Poor
        $from         = $request->input('from')
                            ? Carbon::parse($request->input('from'))->startOfDay()
                            : now()->subDays(30)->startOfDay();
        $to           = $request->input('to')
                            ? Carbon::parse($request->input('to'))->endOfDay()
                            : now()->endOfDay();

        // ── Base query ────────────────────────────────────────────────────
        $query = InfrastructureAudit::with('submittedBy')
            ->whereBetween('investigation_date', [$from->toDateString(), $to->toDateString()])
            ->when($search, fn ($q) =>
                $q->where(fn ($q2) =>
                    $q2->where('hospital_name',    'like', "%{$search}%")
                       ->orWhere('hospital_address','like', "%{$search}%")
                       ->orWhere('hospital_id',     'like', "%{$search}%")
                )
            )
            ->when($type,  fn ($q) => $q->where('hospital_type', $type))
            ->when($hygiene, fn ($q) => $q->where('overall_hygiene', $hygiene))
            ->when($bannerStatus === 'passed',    fn ($q) => $q->where('ai_banner_pass', true))
            ->when($bannerStatus === 'failed',    fn ($q) => $q->where('ai_banner_pass', false))
            ->when($bannerStatus === 'unchecked', fn ($q) => $q->whereNull('ai_banner_pass'))
            ->latest('investigation_date');

        $audits = $query->paginate(20)->withQueryString();

        // ── Summary KPIs for the period (single aggregation) ──────────────
        // Re-run on an unfiltered query within the date range so the KPI row
        // always reflects the full period, not just the current filter page.
        $stats = InfrastructureAudit::whereBetween('investigation_date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw("
                COUNT(*)                                                              AS total,
                SUM(CASE WHEN hospital_type = 'Public'  THEN 1 ELSE 0 END)           AS public_count,
                SUM(CASE WHEN hospital_type = 'Private' THEN 1 ELSE 0 END)           AS private_count,
                SUM(CASE WHEN ai_banner_pass = 1        THEN 1 ELSE 0 END)           AS banner_passed,
                SUM(CASE WHEN ai_banner_pass = 0        THEN 1 ELSE 0 END)           AS banner_failed,
                SUM(CASE WHEN ai_banner_pass IS NULL    THEN 1 ELSE 0 END)           AS banner_unchecked,
                SUM(CASE WHEN overall_hygiene = 'Good'  THEN 1 ELSE 0 END)           AS hygiene_good,
                SUM(CASE WHEN overall_hygiene = 'Average' THEN 1 ELSE 0 END)         AS hygiene_average,
                SUM(CASE WHEN overall_hygiene = 'Poor'  THEN 1 ELSE 0 END)           AS hygiene_poor,
                SUM(CASE WHEN icu_available   = 'Yes'   THEN 1 ELSE 0 END)           AS icu_count,
                SUM(CASE WHEN ot_available    = 'Yes'   THEN 1 ELSE 0 END)           AS ot_count,
                SUM(CASE WHEN hdu_available   = 'Yes'   THEN 1 ELSE 0 END)           AS hdu_count,
                SUM(CASE WHEN dghs_registered = 'Yes'   THEN 1 ELSE 0 END)           AS dghs_count,
                SUM(CASE WHEN overall_hygiene = 'Poor'
                           OR ai_banner_pass  = 0       THEN 1 ELSE 0 END)           AS flag_count
            ")
            ->first();

        $total    = (int) ($stats->total        ?? 0);
        $bannerRate = $total > 0
            ? round((int) ($stats->banner_passed ?? 0) / $total * 100)
            : 0;

        $kpis = [
            'total'           => $total,
            'public_count'    => (int) ($stats->public_count    ?? 0),
            'private_count'   => (int) ($stats->private_count   ?? 0),
            'banner_passed'   => (int) ($stats->banner_passed   ?? 0),
            'banner_failed'   => (int) ($stats->banner_failed   ?? 0),
            'banner_unchecked'=> (int) ($stats->banner_unchecked ?? 0),
            'banner_rate'     => $bannerRate,
            'hygiene_good'    => (int) ($stats->hygiene_good    ?? 0),
            'hygiene_average' => (int) ($stats->hygiene_average ?? 0),
            'hygiene_poor'    => (int) ($stats->hygiene_poor    ?? 0),
            'icu_count'       => (int) ($stats->icu_count       ?? 0),
            'ot_count'        => (int) ($stats->ot_count        ?? 0),
            'hdu_count'       => (int) ($stats->hdu_count       ?? 0),
            'dghs_count'      => (int) ($stats->dghs_count      ?? 0),
            'flag_count'      => (int) ($stats->flag_count      ?? 0),
        ];

        // ── Trend sparkline: daily counts for mini-chart ──────────────────
        $dailyCounts = InfrastructureAudit::whereBetween('investigation_date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw("DATE(investigation_date) AS d, COUNT(*) AS n")
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('n', 'd');

        $sparkDates  = collect(range(29, 0))->map(fn ($i) => now()->subDays($i)->format('Y-m-d'));
        $sparkData   = $sparkDates->map(fn ($d) => (int) ($dailyCounts[$d] ?? 0));

        return view('admin.audits.infrastructure.index', compact(
            'audits', 'kpis', 'sparkData',
            'search', 'type', 'bannerStatus', 'hygiene', 'from', 'to'
        ));
    }

    // ── Show ───────────────────────────────────────────────────────────────

    /**
     * GET /admin/infra-audit/{id}
     * Full read-only detail view of a single infrastructure audit record.
     * Uses plain integer ID (not route model binding) as per the route definition.
     */
    public function show(int $id): View
    {
        $audit = InfrastructureAudit::with('submittedBy')->findOrFail($id);

        // ── Flags / concerns for the admin alert strip ────────────────────
        $flags = [];

        if ($audit->ai_banner_pass === false) {
            $flags[] = ['level' => 'error', 'icon' => 'fa-exclamation-triangle',
                        'text'  => 'AI Banner Verification Failed — PMJAY signage not confirmed.'];
        }
        if (is_null($audit->ai_banner_pass)) {
            $flags[] = ['level' => 'warning', 'icon' => 'fa-question-circle',
                        'text'  => 'Banner photo was not AI-verified for this submission.'];
        }
        if ($audit->overall_hygiene === 'Poor') {
            $flags[] = ['level' => 'error', 'icon' => 'fa-biohazard',
                        'text'  => 'Overall hygiene rated Poor — requires follow-up.'];
        }
        if ($audit->hospital_existence === 'No') {
            $flags[] = ['level' => 'error', 'icon' => 'fa-building-circle-xmark',
                        'text'  => 'Hospital marked as non-existent at time of investigation.'];
        }
        if ($audit->dghs_registered === 'No') {
            $flags[] = ['level' => 'warning', 'icon' => 'fa-id-card-clip',
                        'text'  => 'Hospital is not registered with DGHS.'];
        }
        if ($audit->icu_available === 'Yes' && $audit->icu_well_equipped === 'No') {
            $flags[] = ['level' => 'warning', 'icon' => 'fa-bed-pulse',
                        'text'  => 'ICU present but reported as not well equipped.'];
        }
        if ($audit->ot_available === 'Yes' && $audit->ot_well_equipped === 'No') {
            $flags[] = ['level' => 'warning', 'icon' => 'fa-scalpel',
                        'text'  => 'OT present but reported as not well equipped.'];
        }
        if ($audit->onduty_doctors === 'No') {
            $flags[] = ['level' => 'error', 'icon' => 'fa-user-doctor',
                        'text'  => 'No on-duty doctors found at the time of visit.'];
        }
        if ($audit->adequate_nurses === 'No') {
            $flags[] = ['level' => 'warning', 'icon' => 'fa-user-nurse',
                        'text'  => 'Inadequate number of nurses reported.'];
        }
        if ($audit->biomedical_waste === 'No') {
            $flags[] = ['level' => 'warning', 'icon' => 'fa-trash-can-arrow-up',
                        'text'  => 'Biomedical waste management not available.'];
        }

        // ── Nearby audits for the same hospital (last 5, excluding this one) ─
        $previousAudits = InfrastructureAudit::where('hospital_name', $audit->hospital_name)
            ->where('id', '!=', $audit->id)
            ->latest('investigation_date')
            ->limit(5)
            ->get(['id', 'investigation_date', 'overall_hygiene', 'ai_banner_pass', 'submitted_by']);

        return view('admin.audits.infrastructure.show', compact('audit', 'flags', 'previousAudits'));
    }
}

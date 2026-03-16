<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveAudit;
use App\Models\PmjayAudit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Models\Audits\InfrastructureAudit;
class AdminDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $days = (int) $request->input('period', 30);
        $from = now()->subDays($days)->startOfDay();
        $to   = now()->endOfDay();

        // ── 1. KPI totals ─────────────────────────────────────────────────────
        $assigned = PmjayAudit::whereBetween('created_at', [$from, $to])
            ->selectRaw("
                COUNT(*)                                                              AS grand_total,
                SUM(CASE WHEN status = 'completed'      THEN 1 ELSE 0 END)           AS completed,
                SUM(CASE WHEN status != 'completed'     THEN 1 ELSE 0 END)           AS pending,
                SUM(CASE WHEN audit_type = 'telephonic' THEN 1 ELSE 0 END)           AS tele_total,
                SUM(CASE WHEN audit_type = 'telephonic'
                          AND status = 'completed'      THEN 1 ELSE 0 END)           AS tele_completed,
                SUM(CASE WHEN audit_type = 'field'      THEN 1 ELSE 0 END)           AS field_total,
                SUM(CASE WHEN audit_type = 'field'
                          AND status = 'completed'      THEN 1 ELSE 0 END)           AS field_completed
            ")
            ->first();

        $liveStats = LiveAudit::whereBetween('created_at', [$from, $to])
            ->selectRaw("
                COUNT(*)                                                                  AS live_total,
                SUM(CASE WHEN ai_bed_detected = 1
                          AND ai_patient_detected = 1                  THEN 1 ELSE 0 END) AS ai_passed,
                SUM(CASE WHEN (ai_bed_detected = 0 OR ai_patient_detected = 0)
                          AND ai_validation_message NOT LIKE '%skipped%' THEN 1 ELSE 0 END) AS ai_failed,
                SUM(CASE WHEN ai_validation_message LIKE '%skipped%'   THEN 1 ELSE 0 END) AS ai_skipped
            ")
            ->first();

        // ── 1a. Infrastructure Audit stats ────────────────────────────────────
        // Mirrors liveStats structure: single aggregated query, same date range.
        $infraStats = InfrastructureAudit::whereBetween('created_at', [$from, $to])
            ->selectRaw("
                COUNT(*)                                                              AS infra_total,
                SUM(CASE WHEN hospital_type = 'Public'  THEN 1 ELSE 0 END)           AS public_count,
                SUM(CASE WHEN hospital_type = 'Private' THEN 1 ELSE 0 END)           AS private_count,
                SUM(CASE WHEN ai_banner_pass = 1        THEN 1 ELSE 0 END)           AS banner_passed,
                SUM(CASE WHEN ai_banner_pass = 0        THEN 1 ELSE 0 END)           AS banner_failed,
                SUM(CASE WHEN ai_banner_pass IS NULL    THEN 1 ELSE 0 END)           AS banner_unchecked,
                SUM(CASE WHEN overall_hygiene = 'Good'  THEN 1 ELSE 0 END)           AS hygiene_good,
                SUM(CASE WHEN overall_hygiene = 'Poor'  THEN 1 ELSE 0 END)           AS hygiene_poor,
                SUM(CASE WHEN icu_available = 'Yes'     THEN 1 ELSE 0 END)           AS icu_count,
                SUM(CASE WHEN ot_available  = 'Yes'     THEN 1 ELSE 0 END)           AS ot_count
            ")
            ->first();

        $infraTotal        = (int) ($infraStats->infra_total       ?? 0);
        $infraBannerPassed = (int) ($infraStats->banner_passed     ?? 0);
        $infraBannerFailed = (int) ($infraStats->banner_failed     ?? 0);
        $infraUnchecked    = (int) ($infraStats->banner_unchecked  ?? 0);
        $infraBannerRate   = $infraTotal > 0
            ? round($infraBannerPassed / $infraTotal * 100)
            : 0;

        // Growth vs previous period
        $infraPrevTotal = InfrastructureAudit::whereBetween('created_at', [
            now()->subDays($days * 2)->startOfDay(), $from,
        ])->count();
        $infraGrowthPct = $infraPrevTotal > 0
            ? round((($infraTotal - $infraPrevTotal) / $infraPrevTotal) * 100)
            : 0;

        // Daily series for the chart (merged into existing timeline later)
        $infraByDay = InfrastructureAudit::whereBetween('created_at', [$from, $to])
            ->selectRaw("DATE(created_at) AS d, COUNT(*) AS n")
            ->groupBy('d')
            ->pluck('n', 'd');

        // Recent infra audit activity (for the activity feed)
        $recentInfra = InfrastructureAudit::with('submittedBy')
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($a) => [
                'type'          => 'infra',
                'dmo_name'      => $a->submittedBy?->name ?? '—',
                'patient_name'  => null,
                'hospital_name' => $a->hospital_name,
                'created_at'    => $a->created_at,
            ]);

        // Infra audits grouped by district (for district bar chart)
        

        // Per-DMO infra counts (merged into dmoStats below)
        $infraCounts = InfrastructureAudit::whereBetween('created_at', [$from, $to])
            ->selectRaw("submitted_by AS user_id, COUNT(*) AS n")
            ->groupBy('submitted_by')
            ->pluck('n', 'user_id');

        // Pack into array for view (mirrors $live / $tele / $field structure)
        $infra = [
            'total'          => $infraTotal,
            'public_count'   => (int) ($infraStats->public_count  ?? 0),
            'private_count'  => (int) ($infraStats->private_count ?? 0),
            'banner_passed'  => $infraBannerPassed,
            'banner_failed'  => $infraBannerFailed,
            'banner_unchecked'=> $infraUnchecked,
            'banner_pass_rate'=> $infraBannerRate,
            'hygiene_good'   => (int) ($infraStats->hygiene_good  ?? 0),
            'hygiene_poor'   => (int) ($infraStats->hygiene_poor  ?? 0),
            'icu_count'      => (int) ($infraStats->icu_count     ?? 0),
            'ot_count'       => (int) ($infraStats->ot_count      ?? 0),
            'growth_pct'     => abs($infraGrowthPct),
            'growth_up'      => $infraGrowthPct >= 0,
            'avg_per_dmo_day'=> $days > 0 ? round($infraTotal / max(1, $days), 1) : 0,
        ];

        // ── 2. Active DMOs ────────────────────────────────────────────────────
        $activeDmos = User::role('dmo')
            ->whereHas('liveAudits', fn ($q) => $q->whereBetween('created_at', [$from, $to]))
            ->count();

        // Growth rate vs previous period
        $prevTotal    = PmjayAudit::whereBetween('created_at', [now()->subDays($days * 2)->startOfDay(), $from])->count();
        $currTotal    = (int) ($assigned->grand_total ?? 0) + (int) ($liveStats->live_total ?? 0) + $infraTotal;
        $growthPct    = $prevTotal > 0 ? round((($currTotal - $prevTotal) / $prevTotal) * 100) : 0;
        $completedAll = (int) ($assigned->completed ?? 0) + (int) ($liveStats->live_total ?? 0) + $infraTotal;

        $totals = [
            'grand_total'     => $currTotal,
            'completed'       => $completedAll,
            'pending'         => (int) ($assigned->pending ?? 0),
            'active_dmos'     => $activeDmos,
            'growth_pct'      => abs($growthPct),
            'growth_up'       => $growthPct >= 0,
            'completion_rate' => $currTotal > 0 ? round($completedAll / $currTotal * 100) : 0,
        ];

        // ── 3. Per-type stats ─────────────────────────────────────────────────
        $teleTotal  = (int) ($assigned->tele_total  ?? 0);
        $fieldTotal = (int) ($assigned->field_total ?? 0);
        $liveTotal  = (int) ($liveStats->live_total ?? 0);
        $dmoCount   = max($activeDmos, 1);

        $tele = [
            'total'           => $teleTotal,
            'completed'       => (int) ($assigned->tele_completed  ?? 0),
            'completion_rate' => $teleTotal  > 0 ? round(($assigned->tele_completed  / $teleTotal)  * 100) : 0,
            'avg_per_dmo_day' => round($teleTotal  / $dmoCount / $days, 1),
        ];
        $field = [
            'total'           => $fieldTotal,
            'completed'       => (int) ($assigned->field_completed ?? 0),
            'completion_rate' => $fieldTotal > 0 ? round(($assigned->field_completed / $fieldTotal) * 100) : 0,
            'avg_per_dmo_day' => round($fieldTotal / $dmoCount / $days, 1),
        ];
        $live = [
            'total'           => $liveTotal,
            'ai_passed'       => (int) ($liveStats->ai_passed  ?? 0),
            'ai_failed'       => (int) ($liveStats->ai_failed  ?? 0),
            'ai_skipped'      => (int) ($liveStats->ai_skipped ?? 0),
            'ai_pass_rate'    => $liveTotal > 0 ? round(($liveStats->ai_passed / $liveTotal) * 100) : 0,
            'avg_per_dmo_day' => round($liveTotal / $dmoCount / $days, 1),
        ];

        // ── 4. Chart: daily counts ────────────────────────────────────────────
        $teleByDay  = PmjayAudit::whereBetween('created_at', [$from, $to])->where('audit_type', 'telephonic')
            ->selectRaw("DATE(created_at) AS d, COUNT(*) AS n")->groupBy('d')->pluck('n', 'd');
        $fieldByDay = PmjayAudit::whereBetween('created_at', [$from, $to])->where('audit_type', 'field')
            ->selectRaw("DATE(created_at) AS d, COUNT(*) AS n")->groupBy('d')->pluck('n', 'd');
        $liveByDay  = LiveAudit::whereBetween('created_at', [$from, $to])
            ->selectRaw("DATE(created_at) AS d, COUNT(*) AS n")->groupBy('d')->pluck('n', 'd');

        $chartDates  = collect(range($days - 1, 0))->map(fn ($i) => now()->subDays($i)->format('Y-m-d'));
        $chartLabels = $chartDates->map(fn ($d) => Carbon::parse($d)->format('d M'));
        $chartTele   = $chartDates->map(fn ($d) => (int) ($teleByDay[$d]  ?? 0));
        $chartField  = $chartDates->map(fn ($d) => (int) ($fieldByDay[$d] ?? 0));
        $chartLive   = $chartDates->map(fn ($d) => (int) ($liveByDay[$d]  ?? 0));
        $chartInfra  = $chartDates->map(fn ($d) => (int) ($infraByDay[$d] ?? 0)); // ← new series

        // ── 5. District breakdown ─────────────────────────────────────────────
        $districtData = PmjayAudit::whereBetween('pmjay_audits.created_at', [$from, $to])
            ->join('districts', 'districts.id', '=', 'pmjay_audits.district_id')
            ->selectRaw("
                districts.name AS district,
                SUM(CASE WHEN audit_type='telephonic' THEN 1 ELSE 0 END) AS tele,
                SUM(CASE WHEN audit_type='field'      THEN 1 ELSE 0 END) AS field_cnt,
                COUNT(*)                                                  AS total
            ")
            ->groupBy('districts.id', 'districts.name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $liveByDistrict = LiveAudit::whereBetween('live_audits.created_at', [$from, $to])
            ->join('users',         'users.id',              '=', 'live_audits.submitted_by')
            ->join('dmo_districts', 'dmo_districts.user_id', '=', 'users.id')
            ->join('districts',     'districts.id',          '=', 'dmo_districts.district_id')
            ->selectRaw("districts.name AS district, COUNT(*) AS n")
            ->groupBy('districts.id', 'districts.name')
            ->pluck('n', 'district');

        $districtLabels = $districtData->pluck('district');
        $districtTele   = $districtData->pluck('tele');
        $districtField  = $districtData->pluck('field_cnt');
        $districtLive   = $districtLabels->map(fn ($d) => (int) ($liveByDistrict[$d]    ?? 0));
        

        // ── 6. Heatmap: day × 3-hour bucket ──────────────────────────────────
        $heatmapRaw = DB::table(function ($q) use ($from, $to) {
            $q->from('pmjay_audits')
              ->whereBetween('created_at', [$from, $to])
              ->selectRaw("created_at AS ts")
              ->union(
                  DB::table('live_audits')
                    ->whereBetween('created_at', [$from, $to])
                    ->selectRaw("created_at AS ts")
              )
              ->union(
                  // Include infra audits in the activity heatmap
                  DB::table('infrastructure_audits')
                    ->whereBetween('created_at', [$from, $to])
                    ->whereNull('deleted_at')
                    ->selectRaw("created_at AS ts")
              );
        }, 'combined')
        ->selectRaw("
            MOD(DAYOFWEEK(ts) + 5, 7) AS dow,
            FLOOR((HOUR(ts) - 6) / 3) AS bucket,
            COUNT(*) AS n
        ")
        ->where(DB::raw('HOUR(ts)'), '>=', 6)
        ->where(DB::raw('HOUR(ts)'), '<',  24)
        ->groupBy('dow', 'bucket')
        ->get();

        $heatmap = array_fill(0, 7, array_fill(0, 6, 0));
        $heatmapMax = 1;
        foreach ($heatmapRaw as $row) {
            $d = max(0, min(6, (int) $row->dow));
            $b = max(0, min(5, (int) $row->bucket));
            $heatmap[$d][$b] = (int) $row->n;
            $heatmapMax      = max($heatmapMax, (int) $row->n);
        }

        // ── 7. Per-DMO stats ──────────────────────────────────────────────────
        $avatarPalette = [
            ['bg' => '#dbeafe', 'color' => '#1d4ed8'],
            ['bg' => '#d1fae5', 'color' => '#065f46'],
            ['bg' => '#ede9fe', 'color' => '#5b21b6'],
            ['bg' => '#fef3c7', 'color' => '#92400e'],
            ['bg' => '#fee2e2', 'color' => '#991b1b'],
            ['bg' => '#e0f2fe', 'color' => '#0369a1'],
            ['bg' => '#fce7f3', 'color' => '#9d174d'],
            ['bg' => '#ecfdf5', 'color' => '#14532d'],
        ];

        $teleCounts = DB::table('telephonic_audits')
            ->join('pmjay_audits', 'pmjay_audits.id', '=', 'telephonic_audits.pmjay_audit_id')
            ->whereBetween('telephonic_audits.created_at', [$from, $to])
            ->selectRaw("telephonic_audits.submitted_by AS user_id, COUNT(*) AS n")
            ->groupBy('telephonic_audits.submitted_by')
            ->pluck('n', 'user_id');

        $teleDone = DB::table('telephonic_audits')
            ->join('pmjay_audits', 'pmjay_audits.id', '=', 'telephonic_audits.pmjay_audit_id')
            ->whereBetween('telephonic_audits.created_at', [$from, $to])
            ->where('pmjay_audits.status', 'completed')
            ->selectRaw("telephonic_audits.submitted_by AS user_id, COUNT(*) AS n")
            ->groupBy('telephonic_audits.submitted_by')
            ->pluck('n', 'user_id');

        $fieldCounts = DB::table('field_visits')
            ->join('pmjay_audits', 'pmjay_audits.id', '=', 'field_visits.audit_id')
            ->whereBetween('field_visits.created_at', [$from, $to])
            ->selectRaw("field_visits.submitted_by AS user_id, COUNT(*) AS n")
            ->groupBy('field_visits.submitted_by')
            ->pluck('n', 'user_id');

        $fieldDone = DB::table('field_visits')
            ->join('pmjay_audits', 'pmjay_audits.id', '=', 'field_visits.audit_id')
            ->whereBetween('field_visits.created_at', [$from, $to])
            ->where('pmjay_audits.status', 'completed')
            ->selectRaw("field_visits.submitted_by AS user_id, COUNT(*) AS n")
            ->groupBy('field_visits.submitted_by')
            ->pluck('n', 'user_id');

        $liveCounts = LiveAudit::whereBetween('created_at', [$from, $to])
            ->selectRaw("submitted_by AS user_id, COUNT(*) AS n")
            ->groupBy('submitted_by')
            ->pluck('n', 'user_id');

        $dmoStats = User::role('dmo')
            ->with('districts')
            ->get()
            ->map(function ($u, $i) use (
                $teleCounts, $teleDone, $fieldCounts, $fieldDone,
                $liveCounts, $infraCounts, $avatarPalette
            ) {
                $tt    = (int) ($teleCounts[$u->id]  ?? 0);
                $td    = (int) ($teleDone[$u->id]    ?? 0);
                $ft    = (int) ($fieldCounts[$u->id] ?? 0);
                $fd    = (int) ($fieldDone[$u->id]   ?? 0);
                $live  = (int) ($liveCounts[$u->id]  ?? 0);
                $infra = (int) ($infraCounts[$u->id] ?? 0); // ← new column

                return [
                    'name'            => $u->name,
                    'district'        => $u->districts->first()?->name ?? '—',
                    'tele_total'      => $tt,
                    'tele_completed'  => $td,
                    'field_total'     => $ft,
                    'field_completed' => $fd,
                    'live'            => $live,
                    'infra'           => $infra,                          // ← new
                    'total'           => $tt + $ft + $live + $infra,      // ← included
                    'completed'       => $td + $fd + $live + $infra,      // ← included
                    'avatar_bg'       => $avatarPalette[$i % count($avatarPalette)]['bg'],
                    'avatar_color'    => $avatarPalette[$i % count($avatarPalette)]['color'],
                ];
            })
            ->sortByDesc('total')
            ->values();

        // ── 8. Recent activity feed ───────────────────────────────────────────
        $recentTele = DB::table('telephonic_audits')
            ->join('pmjay_audits',    'pmjay_audits.id',    '=', 'telephonic_audits.pmjay_audit_id')
            ->join('pmjay_treatments','pmjay_treatments.id','=', 'pmjay_audits.pmjay_treatment_id')
            ->leftJoin('users',       'users.id',           '=', 'telephonic_audits.submitted_by')
            ->whereBetween('telephonic_audits.created_at', [$from, $to])
            ->select('users.name AS dmo_name', 'pmjay_treatments.patient_name', 'telephonic_audits.created_at')
            ->orderByDesc('telephonic_audits.created_at')->limit(5)->get()
            ->map(fn ($r) => [
                'type'          => 'telephonic',
                'dmo_name'      => $r->dmo_name     ?? '—',
                'patient_name'  => $r->patient_name ?? null,
                'hospital_name' => null,
                'created_at'    => Carbon::parse($r->created_at),
            ]);

        $recentField = DB::table('field_visits')
            ->join('pmjay_audits',    'pmjay_audits.id',    '=', 'field_visits.audit_id')
            ->join('pmjay_treatments','pmjay_treatments.id','=', 'pmjay_audits.pmjay_treatment_id')
            ->leftJoin('users',       'users.id',           '=', 'field_visits.submitted_by')
            ->whereBetween('field_visits.created_at', [$from, $to])
            ->where('pmjay_audits.status', 'completed')
            ->select('users.name AS dmo_name', 'pmjay_treatments.patient_name', 'field_visits.created_at')
            ->orderByDesc('field_visits.created_at')->limit(5)->get()
            ->map(fn ($r) => [
                'type'          => 'field',
                'dmo_name'      => $r->dmo_name     ?? '—',
                'patient_name'  => $r->patient_name ?? null,
                'hospital_name' => null,
                'created_at'    => Carbon::parse($r->created_at),
            ]);

        $recentLive = LiveAudit::with('submittedBy', 'hospital')
            ->whereBetween('created_at', [$from, $to])
            ->latest()->limit(5)->get()
            ->map(fn ($l) => [
                'type'          => 'live',
                'dmo_name'      => $l->submittedBy?->name ?? '—',
                'patient_name'  => $l->patient_name,
                'hospital_name' => $l->hospital->name,
                'created_at'    => $l->created_at,
            ]);

        // Infra feed already built above as $recentInfra

        $recentActivity = $recentTele->concat($recentField)->concat($recentLive)->concat($recentInfra)
            ->sortByDesc('created_at')->take(12)->values();

        // ── 9. Top hospitals ──────────────────────────────────────────────────
        $topHospitalsField = PmjayAudit::whereBetween('pmjay_audits.created_at', [$from, $to])
            ->join('pmjay_treatments', 'pmjay_treatments.id', '=', 'pmjay_audits.pmjay_treatment_id')
            ->join('hospitals',        'hospitals.id',        '=', 'pmjay_treatments.hospital_id')
            ->selectRaw("hospitals.name AS name, COUNT(*) AS cnt")
            ->groupBy('hospitals.id', 'hospitals.name')
            ->orderByDesc('cnt')->limit(7)->pluck('cnt', 'name');

        $topHospitalsLive = LiveAudit::whereBetween('live_audits.created_at', [$from, $to])
            ->join('hospitals', 'hospitals.id', '=', 'live_audits.hospital_id')
            ->selectRaw('hospitals.name AS name, COUNT(*) AS cnt')
            ->groupBy('hospitals.id', 'hospitals.name')
            ->orderByDesc('cnt')->limit(7)->pluck('cnt', 'name');

        // Infra audits store the hospital name directly (no FK to hospitals table)
        // Group by the stored hospital_name string
        $topHospitalsInfra = InfrastructureAudit::whereBetween('created_at', [$from, $to])
            ->selectRaw("hospital_name AS name, COUNT(*) AS cnt")
            ->groupBy('hospital_name')
            ->orderByDesc('cnt')->limit(7)->pluck('cnt', 'name');

        $topHospitals = $topHospitalsField
            ->mergeRecursive($topHospitalsLive)
            ->mergeRecursive($topHospitalsInfra)
            ->map(fn ($v) => is_array($v) ? array_sum($v) : $v)
            ->sortDesc()->take(7)
            ->map(fn ($count, $name) => ['name' => $name, 'count' => $count])
            ->values();

        return view('admin.dashboard', compact(
            'totals', 'tele', 'field', 'live', 'infra',
            'chartLabels', 'chartTele', 'chartField', 'chartLive', 'chartInfra',
            'districtLabels', 'districtTele', 'districtField', 'districtLive',
            'heatmap', 'heatmapMax',
            'dmoStats',
            'recentActivity',
            'topHospitals',
            'chartDates'
        ));
    }
}

<?php

namespace App\Http\Controllers\DMO;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\PmjayAudit;
use App\Models\LiveAudit;
use App\Models\Audits\InfrastructureAudit;
use Illuminate\View\View;
class DmoDashboardController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        // ── Single query: all assignment/completion counts for this DMO ──────
        $counts = PmjayAudit::query()
            ->where('assigned_to', $userId)
            ->selectRaw("
                COUNT(*)                                             AS total_assigned,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS total_completed,

                SUM(CASE WHEN audit_type = 'telephonic'             THEN 1 ELSE 0 END) AS total_tele_assigned,
                SUM(CASE WHEN audit_type = 'telephonic'
                          AND status    = 'completed'               THEN 1 ELSE 0 END) AS total_tele_completed,

                SUM(CASE WHEN audit_type = 'field'                  THEN 1 ELSE 0 END) AS total_field_assigned,
                SUM(CASE WHEN audit_type = 'field'
                          AND status    = 'completed'               THEN 1 ELSE 0 END) AS total_field_completed
            ")
            ->first();

        // ── Live audit counts (independent — no assigned case) ───────────────
        $liveStats = LiveAudit::query()
            ->where('submitted_by', $userId)
            ->selectRaw("
                COUNT(*)                                                          AS total_live_audits,
                SUM(CASE WHEN ai_bed_detected = 1
                          AND ai_patient_detected = 1                THEN 1 ELSE 0 END) AS total_live_ai_passed
            ")
            ->first();

        $infrastructureAudits = InfrastructureAudit::where('submitted_by', $userId)->count();

        return view('dmo.dashboard', [
            'total_assigned'       => (int) ($counts->total_assigned       ?? 0),
            'total_completed'      => (int) ($counts->total_completed      ?? 0),

            'total_tele_assigned'  => (int) ($counts->total_tele_assigned  ?? 0),
            'total_tele_completed' => (int) ($counts->total_tele_completed ?? 0),

            'total_field_assigned' => (int) ($counts->total_field_assigned ?? 0),
            'total_field_completed'=> (int) ($counts->total_field_completed?? 0),

            'total_live_audits'    => (int) ($liveStats->total_live_audits    ?? 0),
            'total_live_ai_passed' => (int) ($liveStats->total_live_ai_passed ?? 0),

            'infrastructureAudits' => (int) ($infrastructureAudits ?? 0),
        ]);
    }
}
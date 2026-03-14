<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveAudit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLiveAuditController extends Controller
{
    public function index(Request $request): View
    {
        $query = LiveAudit::with(['submittedBy', 'attachments'])
            ->latest();

        if ($request->filled('ai_result')) {
            match ($request->ai_result) {
                'passed'  => $query->where('ai_bed_detected', true)->where('ai_patient_detected', true),
                'failed'  => $query->where(fn($q) => $q->where('ai_bed_detected', false)->orWhere('ai_patient_detected', false))
                                   ->where('ai_validation_message', 'not like', '%skipped%'),
                'skipped' => $query->where('ai_validation_message', 'like', '%skipped%'),
                default   => null,
            };
        }
        if ($request->filled('treatment_type')) {
            $query->where('treatment_type', $request->treatment_type);
        }
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(fn($q) => $q
                ->where('patient_name',  'like', $term)
                ->orWhere('hospital_name', 'like', $term)
                ->orWhere('pmjay_id',    'like', $term)
            );
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('dmo_id')) {
            $query->where('submitted_by', $request->dmo_id);
        }

        $audits = $query->paginate(20)->withQueryString();
        $dmos   = \App\Models\User::role('dmo')->orderBy('name')->get();

        $summary = LiveAudit::selectRaw("
            COUNT(*) AS total,
            SUM(CASE WHEN ai_bed_detected = 1 AND ai_patient_detected = 1 THEN 1 ELSE 0 END) AS ai_passed,
            SUM(CASE WHEN ai_validation_message LIKE '%skipped%' THEN 1 ELSE 0 END) AS ai_skipped,
            SUM(CASE WHEN (ai_bed_detected = 0 OR ai_patient_detected = 0)
                     AND ai_validation_message NOT LIKE '%skipped%' THEN 1 ELSE 0 END) AS ai_failed
        ")->first();

        return view('admin.audits.live.index', compact('audits', 'dmos', 'summary'));
    }

    public function show(int $id): View
    {
        $audit = LiveAudit::with(['submittedBy', 'attachments'])->findOrFail($id);
        return view('admin.audits.live.show', compact('audit'));
    }
}

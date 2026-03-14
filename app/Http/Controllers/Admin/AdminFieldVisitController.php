<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PmjayAudit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminFieldVisitController extends Controller
{
    public function index(Request $request): View
    {
        $query = PmjayAudit::with(['treatment.hospital', 'district', 'fieldVisit.submittedBy'])
            ->where('audit_type', 'field')
            ->latest('pmjay_audits.created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->filled('dmo_id')) {
            $query->where('assigned_to', $request->dmo_id);
        }

        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->whereHas('treatment', fn($q) => $q
                ->where('patient_name', 'like', $term)
                ->orWhere('member_id',  'like', $term)
            );
        }
        if ($request->filled('date_from')) {
            $query->whereDate('pmjay_audits.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('pmjay_audits.created_at', '<=', $request->date_to);
        }


        $audits    = $query->paginate(100)->withQueryString();
        $districts = \App\Models\District::orderBy('name')->get();
        $dmos      = \App\Models\User::role('dmo')->orderBy('name')->get();


        $summary_query = PmjayAudit::where('audit_type', 'field');
        if ($request->filled('district_id')) {
            $summary_query->where('district_id', $request->district_id);
        }
        if ($request->filled('dmo_id')) {
            $summary_query->where('assigned_to', $request->dmo_id);
        }
        $summary = $summary_query->selectRaw("
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
                SUM(CASE WHEN status = 'pending'   THEN 1 ELSE 0 END) AS pending
            ")->first();

        return view('admin.audits.field.index', compact('audits', 'districts', 'dmos', 'summary'));
    }

    public function show(int $id): View
    {
        $audit = PmjayAudit::with([
            'treatment.hospital',
            'district',
            'fieldVisit.submittedBy',
            'fieldVisit.attachments',
        ])->where('audit_type', 'field')->findOrFail($id);

        return view('admin.audits.field.show', compact('audit'));
    }
}

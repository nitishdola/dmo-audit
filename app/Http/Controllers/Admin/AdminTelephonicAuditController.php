<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PmjayAudit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTelephonicAuditController extends Controller
{
    public function index(Request $request): View
    {
        $query = PmjayAudit::with(['treatment.hospital', 'district', 'telephonicAudit.submittedBy'])
            ->where('audit_type', 'telephonic')
            ->latest('pmjay_audits.created_at');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
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

        $audits    = $query->paginate(20)->withQueryString();
        $districts = \App\Models\District::orderBy('name')->get();

        // Summary chips
        $summary = PmjayAudit::where('audit_type', 'telephonic')
            ->selectRaw("
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
                SUM(CASE WHEN status = 'pending'   THEN 1 ELSE 0 END) AS pending
            ")->first();

        return view('admin.audits.telephonic.index', compact('audits', 'districts', 'summary'));
    }

    public function show(int $id): View
    {
        $audit = PmjayAudit::with([
            'treatment.hospital',
            'treatment.hospital.district',
            'district',
            'telephonicAudit.submittedBy',
        ])->where('audit_type', 'telephonic')->findOrFail($id);

        return view('admin.audits.telephonic.show', compact('audit'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PmjayAudit;
use App\Services\PmjayAuditService;
use App\Http\Controllers\Controller;
class AdminController extends Controller
{
    public function generateAuditsPage()
    {
        $alreadyGenerated = PmjayAudit::count() > 0;

        return view('admin.generate-audits', compact('alreadyGenerated'));
    }

    public function generateAudits(PmjayAuditService $auditService)
    {
        if (PmjayAudit::count() > 0) {
            return redirect()->back()->with('error', 'Audits already generated.');
        }

        DB::beginTransaction();

        try {

            $auditService->generateAudits();

            DB::commit();

            return redirect()->back()->with('success', 'Audits generated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
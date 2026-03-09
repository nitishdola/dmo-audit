<?php

namespace App\Http\Controllers\DMO;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DmoDashboardController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $userId = auth()->id();

        $stats = DB::table('pmjay_audits')
            ->selectRaw("
                COUNT(*) as total_assigned,
                SUM(status = 'completed') as total_completed,
                SUM(audit_type = 'telephonic') as total_tele_assigned,
                SUM(audit_type = 'telephonic' AND status = 'completed') as total_tele_completed,
                SUM(audit_type = 'field') as total_field_assigned,
                SUM(audit_type = 'field' AND status = 'completed') as total_field_completed
            ")
            ->where('assigned_to', $userId)
            ->first();

        return view('dmo.dashboard', [
            'total_assigned'       => $stats->total_assigned ?? 0,
            'total_completed'      => $stats->total_completed ?? 0,
            'total_tele_assigned'  => $stats->total_tele_assigned ?? 0,
            'total_tele_completed' => $stats->total_tele_completed ?? 0,
            'total_field_assigned' => $stats->total_field_assigned ?? 0,
            'total_field_completed'=> $stats->total_field_completed ?? 0,
        ]);
    }
}
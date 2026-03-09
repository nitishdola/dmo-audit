<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PmjayAuditService
{
    public function generateAudits()
    {

        $districts = DB::table('dmo_districts')
            ->select('district_id')
            ->distinct()
            ->pluck('district_id');

        foreach ($districts as $districtId) {

            // Get eligible treatments not already audited
            $cases = DB::table('pmjay_treatments')
                ->where('patient_district_id', $districtId)
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                      ->from('pmjay_audits')
                      ->whereColumn('pmjay_audits.pmjay_treatment_id', 'pmjay_treatments.id');
                });

            $total = $cases->count();

            if ($total == 0) {
                continue;
            }

            $telephonicCount = ceil($total * 0.10);
            $fieldCount = ceil($total * 0.02);

            $selectedCases = $cases
                ->inRandomOrder()
                ->limit($telephonicCount + $fieldCount)
                ->pluck('id');

            if ($selectedCases->isEmpty()) {
                continue;
            }

            // Get DMOs for this district
            $dmos = DB::table('dmo_districts')
                ->where('district_id', $districtId)
                ->pluck('user_id');

            if ($dmos->isEmpty()) {
                continue;
            }

            $dmoCount = $dmos->count();
            $dmoIndex = 0;

            $insertData = [];

            foreach ($selectedCases as $index => $caseId) {

                $auditType = $index < $telephonicCount
                    ? 'telephonic'
                    : 'field';

                $assignedTo = $dmos[$dmoIndex];

                $insertData[] = [
                    'pmjay_treatment_id' => $caseId,
                    'district_id' => $districtId,
                    'assigned_to' => $assignedTo,
                    'audit_type' => $auditType,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // rotate DMOs
                $dmoIndex++;
                if ($dmoIndex >= $dmoCount) {
                    $dmoIndex = 0;
                }
            }

            DB::table('pmjay_audits')->insert($insertData);
        }
    }
}
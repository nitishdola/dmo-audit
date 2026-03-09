<?php

namespace App\Services;

use App\Models\Hospital;
use App\Models\District;
use App\Models\PmjayTreatment;
use Illuminate\Support\Facades\DB;

class PmjayImportService
{
    public function import($path)
    {
        $json = file_get_contents($path);
        $records = json_decode($json, true);

        if (!$records) {
            throw new \Exception("Invalid JSON file");
        }

        $hospitals = Hospital::pluck('id', 'hospital_code');
        $districts = District::pluck('id', 'lgd_code');

        $data = [];

        foreach ($records as $row) {
            
            if (empty($row['ben_mobile_no'])) {
                continue;
            }

            if (empty($row['case_id'])) {
                continue;
            }

            $hospitalCode = $row['hospital_code'] ?? null;

            if (!$hospitalCode || !isset($hospitals[$hospitalCode])) {
                continue;
            }

            $districtId = null;

            $districtCode = $row['patient_district_code'] ?? null;

            if ($districtCode && isset($districts[$districtCode])) {
                $districtId = $districts[$districtCode];
            }

            $data[] = [

                'registration_id' => $row['registration_id'] ?? null,
                'case_id' => $row['case_id'],

                'patient_name' => $row['patient_name'] ?? null,
                'member_id' => $row['member_id'] ?? null,

                'policy_code' => $row['policy_code'] ?? null,
                'preauth_init_date' => date('Y-m-d', strtotime($row['preauth_init_date'])) ?? null,
                'ben_mobile_no' => $row['ben_mobile_no'] ?? null,

                'hospital_id' => $hospitals[$hospitalCode],

                'patient_district_id' => $districtId,

                'procedure_code' => $row['procedure_code'] ?? null,
                'procedure_details' => $row['procedure_details'] ?? null,
                'category_details' => $row['category_details'] ?? null,

                'amount_preauth_approved' => $row['amount_preauth_approved'] ?? null,
                'amount_claim_paid' => $row['amount_claim_paid'] ?? null,

                'case_status' => $row['case_status'] ?? null,

                'admission_dt' => $row['admission_dt'] ?? null,
                'discharge_dt' => $row['discharge_dt'] ?? null,

                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::transaction(function () use ($data) {

            foreach (array_chunk($data, 1000) as $chunk) {

                PmjayTreatment::upsert(
                    $chunk,
                    ['case_id'],
                    [
                        'registration_id',
                        'patient_name',
                        'member_id',
                        'hospital_id',
                        'patient_district_id',
                        'procedure_code',
                        'procedure_details',
                        'category_details',
                        'amount_preauth_approved',
                        'amount_claim_paid',
                        'case_status',
                        'admission_dt',
                        'discharge_dt',
                        'updated_at'
                    ]
                );
            }

        });
    }
}
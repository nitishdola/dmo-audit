<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmjayTreatment extends Model
{

    protected $fillable = [

        'registration_id',
        'case_id',
        'patient_name',
        'patient_district_id',
        'member_id',
        'address',

        'policy_code',
        'preauth_init_date',
        'ben_mobile_no',

        'hospital_id',

        'procedure_code',
        'procedure_details',
        'category_details',

        'amount_preauth_approved',
        'amount_claim_paid',

        'case_status',

        'admission_dt',
        'discharge_dt'
    ];


    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function patientDistrict()
    {
        return $this->belongsTo(District::class);
    }

    public function audit()
    {
        return $this->hasOne(PmjayAudit::class, 'pmjay_treatment_id');
    }

    public function telephonicAudit()
    {
        return $this->hasOne(
            \App\Models\Audits\TelephonicAudit::class,
            'pmjay_treatment_id'
        );
    }

}
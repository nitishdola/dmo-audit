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

    public function audit()
    {
        return $this->hasOne(PmjayAudit::class);
    }

}
<?php

namespace App\Models\Audits;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryAudit extends Model
{
    protected $fillable = [
        'audit_id', 'pmjay_family_id', 'name', 'guardian_name', 'address',
        'district_id', 'state', 'pin_code', 'contact_no',
        'ecard_made_at', 'ecard_charged', 'ecard_charge_amount',
        'availed_services', 'hospital_id', 'symptoms',
        'admission_date', 'discharge_date', 'days_hospitalized',
        'free_food', 'treatment_given', 'surgery_scar', 'surgery_scar_remarks',
        'photo_match', 'other_remarks', 'recommendation', 'submitted_by',
    ];

    public function district() { return $this->belongsTo(District::class); }
    public function hospital() { return $this->belongsTo(Hospital::class); }
    public function members() { return $this->hasMany(BeneficiaryAuditMember::class); }
    public function submittedBy() { return $this->belongsTo(User::class, 'submitted_by'); }
}

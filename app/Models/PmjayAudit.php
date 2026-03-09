<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Audits\TelephonicAudit;
use App\Models\Audits\FieldVisit;
class PmjayAudit extends Model
{
    protected $fillable = [
        'pmjay_treatment_id',
        'district_id',
        'audit_type',
        'status'
    ];

    public function treatment()
    {
        return $this->belongsTo(PmjayTreatment::class, 'pmjay_treatment_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }
    public function telephonicAudit()
    {
        return $this->hasOne(TelephonicAudit::class, 'pmjay_audit_id');
    }

    public function fieldVisit()
    {
        return $this->hasOne(FieldVisit::class,'audit_id');
    }
}
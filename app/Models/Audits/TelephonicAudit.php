<?php 
namespace App\Models\Audits;

use Illuminate\Database\Eloquent\Model;
use App\Models\PmjayTreatment;
use App\Models\AuditConclusion;
use App\Models\User;

class TelephonicAudit extends Model
{
    protected $fillable = [
        'submitted_by',
        'pmjay_treatment_id',
        'observation',
        'audit_conclusion_id'
    ];

    public function pmjay_treatment()
    {
        return $this->belongsTo(PmjayTreatment::class, 'pmjay_treatment_id');
    }

    public function audit_conclusion()
    {
        return $this->belongsTo(AuditConclusion::class,'audit_conclusion_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class,'submitted_by');
    }
    
}
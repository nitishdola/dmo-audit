<?php 
namespace App\Models\Audits;

use Illuminate\Database\Eloquent\Model;
use App\Models\PmjayAudit;
use App\Models\AuditConclusion;

class TelephonicAudit extends Model
{
    protected $fillable = [
        'pmjay_audit_id',
        'observation',
        'audit_conclusion_id'
    ];

    public function pmjay_audit()
    {
        return $this->belongsTo(PmjayAudit::class, 'pmjay_audit_id');
    }

    public function audit_conclusion()
    {
        return $this->belongsTo(AuditConclusion::class,'audit_conclusion_id');
    }
}
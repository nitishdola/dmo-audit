<?php

namespace App\Models\Audits;

use Illuminate\Database\Eloquent\Model;

class MedicalAuditAttachment extends Model
{
    protected $fillable = [
        'medical_audit_id',
        'name',
        'file_path',
        'sort_order',
    ];

    public function medicalAudit()
    {
        return $this->belongsTo(MedicalAudit::class);
    }
}
<?php

namespace App\Models\Audits;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryAuditMember extends Model
{
    protected $fillable = ['beneficiary_audit_id', 'name', 'pmjay_id_number', 'gender', 'age', 'relationship', 'sort_order'];
}

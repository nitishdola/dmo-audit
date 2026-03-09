<?php

namespace App\Models\Audits;

use Illuminate\Database\Eloquent\Model;

class FieldVisitAttachment extends Model
{
    protected $fillable = [

        'field_visit_id',
        'name',
        'file_path'

    ];

    public function fieldVisit()
    {
        return $this->belongsTo(FieldVisit::class);
    }
}

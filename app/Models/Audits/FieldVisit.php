<?php

namespace App\Models\Audits;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class FieldVisit extends Model
{
    protected $fillable = [
        'audit_id',
        'patient_name',
        'package_booked',
        'treating_doctor',
        'doctor_specialization',
        'admission_datetime',
        'discharge_datetime',
        'treatment_type',
        'diagnosis',

        'lama',
        'lama_remarks',

        'outdoor_register',
        'outdoor_register_remarks',

        'indoor_register',
        'indoor_register_remarks',

        'ot_register',
        'ot_register_remarks',

        'lab_register',
        'lab_register_remarks',

        'ipd_complete',
        'ipd_complete_remarks',

        'ipd_aligns',
        'ipd_aligns_remarks',

        'ot_notes_available',
        'ot_notes_available_remarks',

        'ot_notes_complete',
        'ot_notes_complete_remarks',

        'ot_notes_align',
        'ot_notes_align_remarks',

        'pre_anaesthesia',
        'pre_anaesthesia_remarks',

        'nursing_notes_available',
        'nursing_notes_available_remarks',

        'nursing_notes_complete',
        'nursing_notes_complete_remarks',

        'doctor_notes_available',
        'doctor_notes_available_remarks',

        'doctor_notes_complete',
        'doctor_notes_complete_remarks',

        'progress_chart_available',
        'progress_chart_available_remarks',

        'progress_chart_complete',
        'progress_chart_complete_remarks',

        'treatment_chart_available',
        'treatment_chart_available_remarks',

        'treatment_chart_complete',
        'treatment_chart_complete_remarks',

        'monitoring_available',
        'monitoring_available_remarks',

        'discharge_summary',
        'discharge_summary_remarks',

        'overall_remarks',

        'photo_path',
        'photo_latitude',
        'photo_longitude',
        'photo_address',
        'photo_taken_at',

        'submitted_by'
    ];

    protected $casts = [
        'admission_datetime' => 'datetime',
        'discharge_datetime' => 'datetime',
        'photo_taken_at' => 'datetime'
    ];

    public function attachments()
    {
        return $this->hasMany(FieldVisitAttachment::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class,'submitted_by');
    }
}

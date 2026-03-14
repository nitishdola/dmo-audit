<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveAudit extends Model
{
    protected $table = 'live_audits';

    protected $fillable = [
        'submitted_by',

        // Patient & Case Details
        'patient_name',
        'contact_number',
        'hospital_id',        
        'district_id',        
        'pmjay_id',
        'registration_number',
        'package_booked',
        'treating_doctor',
        'doctor_specialization',
        'admission_datetime',
        'discharge_datetime',
        'treatment_type',

        // On-bed photo
        'bed_photo_path',
        'bed_photo_latitude',
        'bed_photo_longitude',
        'bed_photo_address',
        'bed_photo_taken_at',

        // AI results
        'ai_bed_detected',
        'ai_patient_detected',
        'ai_pmjay_card_detected',
        'ai_face_count',
        'ai_labels',
        'ai_objects',
        'ai_validation_message',

        // Patient ID proof
        'patient_id_collected',
        'patient_id_remarks',

        // Clinical interview
        'presenting_complaints',
        'symptoms_duration',
        'referred_from_other',
        'referred_from_name',
        'patient_admitted_when',
        'patient_still_admitted',
        'patient_still_admitted_remarks',
        'diagnostic_tests_done',
        'surgery_conducted',
        'surgery_scar_present',
        'surgery_scar_remarks',

        // Money charged
        'money_charged',
        'money_charged_amount',
        'receipt_available',
        'receipt_path',

        // Previous hospitalisation
        'previous_hospitalisation',
        'previous_hospitalisation_remarks',

        // Other
        'other_remarks',
    ];

    protected $casts = [
        'admission_datetime'     => 'datetime',
        'discharge_datetime'     => 'datetime',
        'patient_admitted_when'  => 'datetime',
        'bed_photo_taken_at'     => 'datetime',
        'ai_labels'              => 'array',
        'ai_objects'             => 'array',
        'ai_bed_detected'        => 'boolean',
        'ai_patient_detected'    => 'boolean',
        'ai_pmjay_card_detected' => 'boolean',
        'money_charged_amount'   => 'decimal:2',
    ];

    /* ── Relationships ── */

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(LiveAuditAttachment::class);
    }

    /* ── Helpers ── */

    public function aiPassed(): bool
    {
        return (bool) $this->ai_bed_detected && (bool) $this->ai_patient_detected;
    }
}

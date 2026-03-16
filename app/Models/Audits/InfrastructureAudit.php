<?php

namespace App\Models\Audits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfrastructureAudit extends Model
{
    use SoftDeletes;

    protected $table = 'infrastructure_audits';

    protected $fillable = [
        // Meta
        'submitted_by',
        'investigation_date',

        // A. Hospital Details
        'hospital_name',
        'hospital_address',
        'hospital_id',
        'hospital_type',
        'pmjay_beneficiaries_tms',
        'pmjay_beneficiaries_actual',

        // B. Infrastructure – existence & registration
        'hospital_existence',        'hospital_existence_remarks',
        'hospital_response',         'hospital_response_remarks',
        'dghs_registered',           'dghs_registered_remarks',

        // AI banner
        'banner_photo_path',
        'ai_banner_pass',            'ai_pmjay_branding',
        'ai_banner_visible',         'ai_banner_summary',
        'ai_banner_details',         'banner_remarks',

        // PMAM kiosk
        'pmam_kiosk_available',      'pmam_kiosk_location',       'pmam_kiosk_remarks',
        'promo_boards_displayed',    'promo_boards_remarks',

        // Beds
        'total_beds',                'general_ward_beds',
        'bed_distance_adequate',     'bed_distance_remarks',

        // HDU
        'hdu_available',             'hdu_beds',

        // ICU
        'icu_available',             'icu_beds',
        'icu_well_equipped',         'icu_equipment',             'icu_equipment_remarks',

        // OT
        'ot_available',              'ot_count',                  'ot_tables',
        'ot_sterilization',          'ot_sterilization_remarks',
        'ot_lighting',               'ot_ac',
        'ot_well_equipped',          'ot_equipment',              'ot_equipment_remarks',

        // Diagnostics & hygiene
        'pathology_diagnostics',     'pathology_remarks',
        'biomedical_waste',          'biomedical_waste_remarks',
        'overall_hygiene',           'overall_hygiene_remarks',
        'infra_other_remarks',

        // C. Human Resource
        'pmam_available',            'pmam_available_remarks',
        'onduty_doctors',            'onduty_doctor_types',       'onduty_doctors_remarks',
        'adequate_nurses',           'adequate_nurses_remarks',
        'nurses_qualified',          'nurses_qualified_remarks',
        'technicians_available',
        'pharmacists_available',
        'specialists_available',     'specialists_remarks',
        'hr_other_remarks',
    ];

    protected $casts = [
        'investigation_date'  => 'date',
        'ai_banner_pass'      => 'boolean',
        'ai_pmjay_branding'   => 'boolean',
        'ai_banner_visible'   => 'boolean',
        'icu_equipment'       => 'array',   // {A:'Yes', B:'No', ...}
        'ot_equipment'        => 'array',   // {A:'Yes', B:'No', ...}
        'onduty_doctor_types' => 'array',   // {A:'Yes', B:'No', C:'NA'}
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(\App\models\User::class, 'submitted_by');
    }

    // ── Accessors ──────────────────────────────────────────────────────────

    /** Human-friendly AI banner result label. */
    public function getAiBannerStatusAttribute(): string
    {
        if (is_null($this->ai_banner_pass)) {
            return 'Not checked';
        }
        return $this->ai_banner_pass ? 'Passed' : 'Failed';
    }

    /** Public storage URL for the uploaded banner photo. */
    public function getBannerPhotoUrlAttribute(): ?string
    {
        return $this->banner_photo_path
            ? asset('storage/' . $this->banner_photo_path)
            : null;
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeByDate($query, string $date)
    {
        return $query->whereDate('investigation_date', $date);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('hospital_type', $type);
    }

    public function scopeAiBannerFailed($query)
    {
        return $query->where('ai_banner_pass', false);
    }
}

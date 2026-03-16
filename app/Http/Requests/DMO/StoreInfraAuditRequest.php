<?php

namespace App\Http\Requests\DMO;

use Illuminate\Foundation\Http\FormRequest;

class StoreInfraAuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Adjust to your gate / policy as needed
        return auth()->check();
    }

    public function rules(): array
    {
        $yn  = 'nullable|in:Yes,No,NA';
        $str = 'nullable|string|max:500';

        return [
            // ── A. Hospital Details ─────────────────────────────────────
            'investigation_date'           => 'required|date|before_or_equal:today',
            'hospital_name'                => 'required|string|max:255',
            'hospital_address'             => 'required|string|max:1000',
            'hospital_id'                  => 'nullable|string|max:100',
            'hospital_type'                => 'required|in:Public,Private',
            'pmjay_beneficiaries_tms'      => 'nullable|integer|min:0',
            'pmjay_beneficiaries_actual'   => 'nullable|integer|min:0',

            // ── B. Infrastructure ───────────────────────────────────────
            'hospital_existence'           => $yn,
            'hospital_existence_remarks'   => $str,

            'hospital_response'            => 'nullable|in:Co-operative,Non Co-operative,Indifferent',
            'hospital_response_remarks'    => $str,

            'dghs_registered'              => $yn,
            'dghs_registered_remarks'      => $str,

            // AI banner
            'banner_photo'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'ai_banner_pass'               => 'nullable|boolean',
            'ai_pmjay_branding'            => 'nullable|boolean',
            'ai_banner_visible'            => 'nullable|boolean',
            'ai_banner_summary'            => 'nullable|string|max:500',
            'ai_banner_details'            => 'nullable|string|max:2000',
            'banner_remarks'               => $str,

            // PMAM kiosk
            'pmam_kiosk_available'         => $yn,
            'pmam_kiosk_location'          => 'nullable|in:Easily Visible,Far Inside,Not Available',
            'pmam_kiosk_remarks'           => $str,

            'promo_boards_displayed'       => $yn,
            'promo_boards_remarks'         => $str,

            // Beds
            'total_beds'                   => 'nullable|integer|min:0',
            'general_ward_beds'            => 'nullable|integer|min:0',
            'bed_distance_adequate'        => $yn,
            'bed_distance_remarks'         => $str,

            // HDU
            'hdu_available'                => $yn,
            'hdu_beds'                     => 'nullable|integer|min:0',

            // ICU
            'icu_available'                => $yn,
            'icu_beds'                     => 'nullable|integer|min:0',
            'icu_well_equipped'            => $yn,
            'icu_equipment'                => 'nullable|array',
            'icu_equipment.*'              => 'in:Yes,No',
            'icu_equipment_remarks'        => $str,

            // OT
            'ot_available'                 => $yn,
            'ot_count'                     => 'nullable|integer|min:0',
            'ot_tables'                    => 'nullable|integer|min:0',
            'ot_sterilization'             => $yn,
            'ot_sterilization_remarks'     => $str,
            'ot_lighting'                  => $yn,
            'ot_ac'                        => $yn,
            'ot_well_equipped'             => $yn,
            'ot_equipment'                 => 'nullable|array',
            'ot_equipment.*'               => 'in:Yes,No',
            'ot_equipment_remarks'         => $str,

            // Diagnostics & hygiene
            'pathology_diagnostics'        => 'nullable|in:Inhouse,Out sourced,Not Available',
            'pathology_remarks'            => $str,
            'biomedical_waste'             => $yn,
            'biomedical_waste_remarks'     => $str,
            'overall_hygiene'              => 'nullable|in:Good,Average,Poor',
            'overall_hygiene_remarks'      => $str,
            'infra_other_remarks'          => 'nullable|string|max:2000',

            // ── C. Human Resource ───────────────────────────────────────
            'pmam_available'               => $yn,
            'pmam_available_remarks'       => $str,

            'onduty_doctors'               => $yn,
            'onduty_doctor_types'          => 'nullable|array',
            'onduty_doctor_types.*'        => 'in:Yes,No',
            'onduty_doctors_remarks'       => $str,

            'adequate_nurses'              => $yn,
            'adequate_nurses_remarks'      => $str,
            'nurses_qualified'             => $yn,
            'nurses_qualified_remarks'     => $str,
            'technicians_available'        => $yn,
            'pharmacists_available'        => $yn,
            'specialists_available'        => $yn,
            'specialists_remarks'          => $str,
            'hr_other_remarks'             => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'investigation_date.required'   => 'Please enter the date of investigation.',
            'investigation_date.before_or_equal' => 'Investigation date cannot be in the future.',
            'hospital_name.required'        => 'Hospital name is required.',
            'hospital_address.required'     => 'Hospital address is required.',
            'hospital_type.required'        => 'Please select the hospital type (Public / Private).',
            'banner_photo.image'            => 'Banner photo must be an image file.',
            'banner_photo.max'              => 'Banner photo must not exceed 10 MB.',
        ];
    }
}

<?php

namespace App\Http\Requests\DMO;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLiveAuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // ── Patient & Case Details ────────────────────────────────────────
            'patient_name'          => ['required', 'string', 'max:255'],
            'contact_number'        => ['nullable', 'string', 'max:20'],

            // District & Hospital — dropdown driven but hospital_name is what gets stored.

            'district_id'           => ['integer', 'exists:districts,id'],
            'hospital_id'           => ['integer', 'exists:hospitals,id'],

            'pmjay_id'              => ['nullable', 'string', 'max:100'],
            'registration_number'   => ['nullable', 'string', 'max:100'],
            'package_booked'        => ['nullable', 'string', 'max:255'],
            'treating_doctor'       => ['nullable', 'string', 'max:255'],
            'doctor_specialization' => ['nullable', 'string', 'max:255'],
            'admission_datetime'    => ['nullable', 'date'],
            'discharge_datetime'    => ['nullable', 'date', 'after_or_equal:admission_datetime'],
            'treatment_type'        => ['required', Rule::in(['Surgical', 'Medical'])],

            // ── On-bed photograph (mandatory, AI-validated before submit) ─────
            'bed_photo'             => ['required', 'image', 'max:10240'],
            'bed_photo_latitude'    => ['required', 'numeric', 'between:-90,90'],
            'bed_photo_longitude'   => ['required', 'numeric', 'between:-180,180'],
            'bed_photo_address'     => ['nullable', 'string', 'max:500'],

            // ── AI result fields (populated by JS, sent as hidden inputs) ─────
            // Using 'in:0,1' instead of 'boolean' because the form sends '0'/'1' strings.
            'ai_bed_detected'        => ['required', Rule::in(['0', '1'])],
            'ai_patient_detected'    => ['required', Rule::in(['0', '1'])],
            'ai_pmjay_card_detected' => ['nullable', Rule::in(['0', '1'])],
            'ai_face_count'          => ['required', 'integer', 'min:0'],
            'ai_labels'              => ['nullable', 'string'],   // JSON string
            'ai_objects'             => ['nullable', 'string'],   // JSON string
            'ai_validation_message'  => ['nullable', 'string', 'max:500'],

            // ── Patient ID proof ──────────────────────────────────────────────
            'patient_id_collected'  => ['nullable', Rule::in(['Yes', 'No', 'NA'])],
            'patient_id_remarks'    => ['nullable', 'string', 'max:500'],

            // ── Clinical interview ────────────────────────────────────────────
            'presenting_complaints'          => ['nullable', 'string', 'max:2000'],
            'symptoms_duration'              => ['nullable', 'string', 'max:255'],
            'referred_from_other'            => ['nullable', Rule::in(['Yes', 'No', 'NA'])],
            'referred_from_name'             => ['nullable', 'string', 'max:255',
                                                  Rule::requiredIf(fn() => $this->referred_from_other === 'Yes')],
            'patient_admitted_when'          => ['nullable', 'date'],
            'patient_still_admitted'         => ['nullable', Rule::in(['Yes', 'No', 'NA'])],
            'patient_still_admitted_remarks' => ['nullable', 'string', 'max:500'],
            'diagnostic_tests_done'          => ['nullable', 'string', 'max:2000'],
            'surgery_conducted'              => ['nullable', Rule::in(['Yes', 'No', 'NA'])],
            'surgery_scar_present'           => ['nullable', Rule::in(['Yes', 'No', 'NA'])],
            'surgery_scar_remarks'           => ['nullable', 'string', 'max:500'],

            // ── Money charged ─────────────────────────────────────────────────
            'money_charged'        => ['nullable', Rule::in(['Yes', 'No', 'NA'])],
            'money_charged_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.99',
                Rule::requiredIf(fn() => $this->money_charged === 'Yes'),
            ],
            'receipt_available'    => ['nullable', Rule::in(['Yes', 'No', 'NA'])],
            'receipt_file'         => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:10240',
                Rule::requiredIf(fn() => $this->receipt_available === 'Yes'),
            ],

            // ── Previous hospitalisation ──────────────────────────────────────
            'previous_hospitalisation'         => ['nullable', Rule::in(['Yes', 'No', 'NA'])],
            'previous_hospitalisation_remarks' => ['nullable', 'string', 'max:500'],

            // ── Other ─────────────────────────────────────────────────────────
            'other_remarks' => ['nullable', 'string', 'max:3000'],

            // ── Supporting attachments ────────────────────────────────────────
            'attachments'         => ['nullable', 'array', 'max:10'],
            'attachments.*.name'  => ['required_with:attachments.*.file', 'string', 'max:255'],
            'attachments.*.file'  => [
                'required_with:attachments.*.name',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:10240',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // Patient
            'patient_name.required'         => 'Patient name is required.',

            // Hospital
           
            'hospital_id.exists'            => 'The selected hospital is not valid.',
            'district_id.exists'            => 'The selected district is not valid.',

            // Treatment
            'treatment_type.required'       => 'Please select the type of treatment.',
            'treatment_type.in'             => 'Treatment type must be Surgical or Medical.',

            // Photo
            'bed_photo.required'            => 'An on-bed patient photograph is mandatory.',
            'bed_photo.image'               => 'The bed photo must be an image file.',
            'bed_photo.max'                 => 'The bed photo must not exceed 10 MB.',
            'bed_photo_latitude.required'   => 'GPS coordinates are required. Please enable location access.',
            'bed_photo_latitude.between'    => 'Invalid GPS latitude.',
            'bed_photo_longitude.required'  => 'GPS coordinates are required. Please enable location access.',
            'bed_photo_longitude.between'   => 'Invalid GPS longitude.',

            // AI fields
            'ai_bed_detected.required'      => 'AI validation result is missing. Please retake the photo.',
            'ai_patient_detected.required'  => 'AI validation result is missing. Please retake the photo.',

            // Conditional
            'discharge_datetime.after_or_equal'  => 'Discharge date cannot be before admission date.',
            'referred_from_name.required_if'     => 'Please name the referral source.',
            'money_charged_amount.required_if'   => 'Please enter the amount charged.',
            'receipt_file.required_if'           => 'Please upload the receipt.',

            // Attachments
            'attachments.max'               => 'You may upload a maximum of 10 attachments.',
            'attachments.*.name.required_with'  => 'Please enter a name for each attachment.',
            'attachments.*.file.required_with'  => 'Please select a file for each named attachment.',
            'attachments.*.file.mimes'          => 'Attachments must be JPG, PNG or PDF.',
            'attachments.*.file.max'            => 'Each attachment must not exceed 10 MB.',
        ];
    }

    /**
     * Prepare / normalise data before validation runs.
     * Casts the '0'/'1' strings from hidden AI inputs to integers so
     * filter_var(FILTER_VALIDATE_BOOLEAN) works correctly in the controller.
     */
    protected function prepareForValidation(): void
    {
        $merge = [];

        // Normalise boolean-like strings from hidden inputs
        foreach (['ai_bed_detected', 'ai_patient_detected', 'ai_pmjay_card_detected'] as $field) {
            if ($this->has($field)) {
                $merge[$field] = in_array($this->input($field), ['1', 'true', true, 1], strict: false) ? '1' : '0';
            }
        }
        
        if (!empty($merge)) {
            $this->merge($merge);
        }
    }
}

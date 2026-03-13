<?php

namespace App\Http\Requests\DMO;

use Illuminate\Foundation\Http\FormRequest;

class StoreLiveAuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // Patient & Case Details
            'patient_name'          => ['required', 'string', 'max:255'],
            'contact_number'        => ['nullable', 'string', 'max:20'],
            'hospital_name'         => ['required', 'string', 'max:255'],
            'pmjay_id'              => ['nullable', 'string', 'max:100'],
            'registration_number'   => ['nullable', 'string', 'max:100'],
            'package_booked'        => ['nullable', 'string', 'max:255'],
            'treating_doctor'       => ['nullable', 'string', 'max:255'],
            'doctor_specialization' => ['nullable', 'string', 'max:255'],
            'admission_datetime'    => ['nullable', 'date'],
            'discharge_datetime'    => ['nullable', 'date', 'after_or_equal:admission_datetime'],
            'treatment_type'        => ['required', 'in:Surgical,Medical'],

            // On-bed photo (mandatory)
            'bed_photo'             => ['required', 'image', 'max:10240'],
            'bed_photo_latitude'    => ['required', 'numeric'],
            'bed_photo_longitude'   => ['required', 'numeric'],
            'bed_photo_address'     => ['nullable', 'string', 'max:500'],

            // AI result hidden fields
            'ai_bed_detected'        => ['required', 'boolean'],
            'ai_patient_detected'    => ['required', 'boolean'],
            'ai_pmjay_card_detected' => ['nullable', 'boolean'],
            'ai_face_count'          => ['required', 'integer', 'min:0'],
            'ai_labels'              => ['nullable', 'string'],
            'ai_objects'             => ['nullable', 'string'],
            'ai_validation_message'  => ['nullable', 'string', 'max:500'],

            // Patient ID
            'patient_id_collected'  => ['nullable', 'in:Yes,No,NA'],
            'patient_id_remarks'    => ['nullable', 'string', 'max:500'],

            // Clinical interview
            'presenting_complaints'          => ['nullable', 'string'],
            'symptoms_duration'              => ['nullable', 'string', 'max:255'],
            'referred_from_other'            => ['nullable', 'in:Yes,No,NA'],
            'referred_from_name'             => ['nullable', 'string', 'max:255'],
            'patient_admitted_when'          => ['nullable', 'date'],
            'patient_still_admitted'         => ['nullable', 'in:Yes,No,NA'],
            'patient_still_admitted_remarks' => ['nullable', 'string', 'max:500'],
            'diagnostic_tests_done'          => ['nullable', 'string'],
            'surgery_conducted'              => ['nullable', 'in:Yes,No,NA'],
            'surgery_scar_present'           => ['nullable', 'in:Yes,No,NA'],
            'surgery_scar_remarks'           => ['nullable', 'string', 'max:500'],

            // Money
            'money_charged'        => ['nullable', 'in:Yes,No,NA'],
            'money_charged_amount' => ['nullable', 'numeric', 'min:0'],
            'receipt_available'    => ['nullable', 'in:Yes,No,NA'],
            'receipt_file'         => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],

            // Previous hospitalisation
            'previous_hospitalisation'         => ['nullable', 'in:Yes,No,NA'],
            'previous_hospitalisation_remarks' => ['nullable', 'string', 'max:500'],

            // Other
            'other_remarks' => ['nullable', 'string'],

            // Attachments
            'attachments'         => ['nullable', 'array'],
            'attachments.*.name'  => ['required_with:attachments', 'string', 'max:255'],
            'attachments.*.file'  => ['required_with:attachments', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_name.required'        => 'Patient name is required.',
            'hospital_name.required'       => 'Please enter the hospital name.',
            'treatment_type.required'      => 'Please select the type of treatment.',
            'bed_photo.required'           => 'An on-bed patient photograph is mandatory.',
            'bed_photo_latitude.required'  => 'GPS coordinates are required. Please enable location access.',
            'ai_bed_detected.required'     => 'AI validation result is missing. Please retake the photo.',
            'ai_patient_detected.required' => 'AI validation result is missing. Please retake the photo.',
            'discharge_datetime.after_or_equal' => 'Discharge date cannot be before admission date.',
        ];
    }
}

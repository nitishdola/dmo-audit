<?php

namespace App\Http\Controllers\DMO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PmjayTreatment;
use App\Models\Audits\MedicalAudit;
use App\Models\Audits\MedicalAuditAttachment;
use Illuminate\Support\Facades\DB;
class MedicalAuditController extends Controller
{
    public function medicalAuditForm(Request $request, $id)
    {
        $audit = PmjayTreatment::with([
                'hospital',
                'audit',
                'telephonicAudit.audit_conclusion'
            ])->findOrFail($id); 
        return view('dmo.audits.medical.form', compact('audit'));
    }

    public function storeMedicalAudit(Request $request, $auditId)
    {
        $audit = PmjayTreatment::findOrFail($auditId);

        $validated = $request->validate([

            'patient_name' => 'required|string|max:255',
            'package_booked' => 'required|string|max:255',
            'treating_doctor' => 'required|string|max:255',
            'doctor_specialization' => 'required|string|max:255',

            'admission_datetime' => 'required|date',
            'discharge_datetime' => 'required|date|after_or_equal:admission_datetime',

            'treatment_type' => 'required|in:Surgical,Medical',
            'diagnosis' => 'required|string|max:500',

            'lama' => 'required|in:Yes,No,NA',
            'lama_remarks' => 'nullable|string|max:500',
            'lama_reason' => 'required_if:lama,Yes|nullable|string|max:500',

            'outdoor_register' => 'required|in:Yes,No,NA',
            'outdoor_register_remarks' => 'nullable|string|max:500',

            'indoor_register' => 'required|in:Yes,No,NA',
            'indoor_register_remarks' => 'nullable|string|max:500',

            'ot_register' => 'required|in:Yes,No,NA',
            'ot_register_remarks' => 'nullable|string|max:500',

            'lab_register' => 'required|in:Yes,No,NA',
            'lab_register_remarks' => 'nullable|string|max:500',

            'ipd_complete' => 'required|in:Yes,No,NA',
            'ipd_complete_remarks' => 'nullable|string|max:500',

            'ipd_aligns' => 'required|in:Yes,No,NA',
            'ipd_aligns_remarks' => 'nullable|string|max:500',

            'ot_notes_available' => 'required|in:Yes,No,NA',
            'ot_notes_available_remarks' => 'nullable|string|max:500',

            'ot_notes_complete' => 'required|in:Yes,No,NA',
            'ot_notes_complete_remarks' => 'nullable|string|max:500',

            'ot_notes_align' => 'required|in:Yes,No,NA',
            'ot_notes_align_remarks' => 'nullable|string|max:500',

            'pre_anaesthesia' => 'required|in:Yes,No,NA',
            'pre_anaesthesia_remarks' => 'nullable|string|max:500',

            'nursing_notes_available' => 'required|in:Yes,No,NA',
            'nursing_notes_available_remarks' => 'nullable|string|max:500',

            'nursing_notes_complete' => 'required|in:Yes,No,NA',
            'nursing_notes_complete_remarks' => 'nullable|string|max:500',

            'doctor_notes_available' => 'required|in:Yes,No,NA',
            'doctor_notes_available_remarks' => 'nullable|string|max:500',

            'doctor_notes_complete' => 'required|in:Yes,No,NA',
            'doctor_notes_complete_remarks' => 'nullable|string|max:500',

            'progress_chart_available' => 'required|in:Yes,No,NA',
            'progress_chart_available_remarks' => 'nullable|string|max:500',

            'progress_chart_complete' => 'required|in:Yes,No,NA',
            'progress_chart_complete_remarks' => 'nullable|string|max:500',

            'treatment_chart_available' => 'required|in:Yes,No,NA',
            'treatment_chart_available_remarks' => 'nullable|string|max:500',

            'treatment_chart_complete' => 'required|in:Yes,No,NA',
            'treatment_chart_complete_remarks' => 'nullable|string|max:500',

            'monitoring_available' => 'required|in:Yes,No,NA',
            'monitoring_available_remarks' => 'nullable|string|max:500',

            'discharge_summary' => 'required|in:Yes,No,NA',
            'discharge_summary_remarks' => 'nullable|string|max:500',

            'overall_remarks' => 'required|string',

            'attachments' => 'required|array|min:1',
            'attachments.*.name' => 'required|string|max:255',
            'attachments.*.file' => 'required|file|mimes:jpeg,jpg,png,pdf|max:10240',
        ]);

        DB::transaction(function () use ($validated, $request, $auditId) {

            $visit = MedicalAudit::create(array_merge(
                $validated,
                [
                    'audit_id' => $auditId,
                    'submitted_by' => auth()->id(),
                ]
            ));

            foreach ($request->file('attachments', []) as $index => $attachment) {

                $path = $attachment['file']->store(
                    "medical_audits/{$auditId}/attachments",
                    'public'
                );

                MedicalAuditAttachment::create([
                    'medical_audit_id' => $visit->id,
                    'name' => $validated['attachments'][$index]['name'],
                    'file_path' => $path,
                    'sort_order' => $index + 1,
                ]);
            }
        });


        return redirect()
            ->route('dmo.audits.medical.all')
            ->with('success', 'Medical Audit submitted successfully.');
    }
}

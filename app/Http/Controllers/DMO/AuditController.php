<?php

namespace App\Http\Controllers\DMO;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\PmjayAudit;
use App\Models\AuditConclusion;
use App\Models\Audits\TelephonicAudit;
use App\Models\Audits\FieldVisit;
use App\Models\Audits\FieldVisitAttachment;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    public function telephonicAudits(Request $request)
    {
        $status = $request->status;
        $query = PmjayAudit::with('treatment', 'treatment.hospital')
                ->where('assigned_to', auth()->id())
                ->where('audit_type', 'telephonic');

        if ($status) {
            $query->where('status', $status);
        }

        $audits = $query->latest()->paginate(1000); 

        return view('dmo.audits.telephonic.telephonic', compact('audits', 'status'));
    }

    public function telephonicAuditForm(Request $request, $id)
    {
        $conclusions = AuditConclusion::where('active',1)->get();
        $audit = PmjayAudit::with('treatment', 'district', 'treatment.hospital', 'telephonicAudit', 'telephonicAudit.audit_conclusion')
                ->where('assigned_to', auth()->id())
                ->where('audit_type', 'telephonic')->where('id', $id)->firstOrFail(); 
        return view('dmo.audits.telephonic.view', compact('audit', 'conclusions'));
    }

    public function storeTelephonicObservation(Request $request, $auditId)
    {
        

        $request->validate([
            'observation' => 'required|string',
            'audit_conclusion_id' => 'required|exists:audit_conclusions,id'
        ]);

        $audit = PmjayAudit::findOrFail($auditId);

        if ($audit->assigned_to != auth()->id()) {
            abort(403);
        }

        if (TelephonicAudit::where('pmjay_audit_id', $auditId)->exists()) {
            return back()->with('error','Observation already submitted.');
        }

        TelephonicAudit::create([
            'pmjay_audit_id' => $audit->id,
            'observation' => $request->observation,
            'audit_conclusion_id' => $request->audit_conclusion_id,
        ]);

        $audit->status = 'completed';
        $audit->save();
        

        return redirect()->route('audits.telephonic')
                ->with('success','Observation saved successfully');
    }

    public function fieldAudits(Request $request)
    {
        $status = $request->status;
        $query = PmjayAudit::with('treatment', 'treatment.hospital')
                ->where('assigned_to', auth()->id())
                ->where('audit_type', 'field');

        if ($status) {
            $query->where('status', $status);
        }

        $audits = $query->latest()->paginate(1000); 

        return view('dmo.audits.field.field', compact('audits', 'status'));
    }

    public function fieldAuditForm(Request $request, $id)
    {
        $audit = PmjayAudit::with('treatment', 'district', 'treatment.hospital', 'fieldVisit', 'fieldVisit.attachments')
                ->where('assigned_to', auth()->id())
                ->where('audit_type', 'field')->where('id', $id)->firstOrFail(); 
        return view('dmo.audits.field.view', compact('audit'));
    }

    public function storeFieldVisit(Request $request, $auditId)
    {
        $audit = \App\Models\PmjayAudit::findOrFail($auditId);

        $validated = $request->validate([

            'patient_name' => 'nullable|string|max:255',
            'package_booked' => 'nullable|string|max:255',
            'treating_doctor' => 'nullable|string|max:255',
            'doctor_specialization' => 'nullable|string|max:255',

            'admission_datetime' => 'nullable|date',
            'discharge_datetime' => 'nullable|date',

            'treatment_type' => 'nullable|in:Surgical,Medical',
            'diagnosis' => 'nullable|string|max:500',

            'lama' => 'nullable|in:Yes,No,NA',
            'lama_remarks' => 'nullable|string|max:500',

            'outdoor_register' => 'nullable|in:Yes,No,NA',
            'outdoor_register_remarks' => 'nullable|string|max:500',

            'indoor_register' => 'nullable|in:Yes,No,NA',
            'indoor_register_remarks' => 'nullable|string|max:500',

            'ot_register' => 'nullable|in:Yes,No,NA',
            'ot_register_remarks' => 'nullable|string|max:500',

            'lab_register' => 'nullable|in:Yes,No,NA',
            'lab_register_remarks' => 'nullable|string|max:500',

            'ipd_complete' => 'nullable|in:Yes,No,NA',
            'ipd_complete_remarks' => 'nullable|string|max:500',

            'ipd_aligns' => 'nullable|in:Yes,No,NA',
            'ipd_aligns_remarks' => 'nullable|string|max:500',

            'ot_notes_available' => 'nullable|in:Yes,No,NA',
            'ot_notes_available_remarks' => 'nullable|string|max:500',

            'ot_notes_complete' => 'nullable|in:Yes,No,NA',
            'ot_notes_complete_remarks' => 'nullable|string|max:500',

            'ot_notes_align' => 'nullable|in:Yes,No,NA',
            'ot_notes_align_remarks' => 'nullable|string|max:500',

            'pre_anaesthesia' => 'nullable|in:Yes,No,NA',
            'pre_anaesthesia_remarks' => 'nullable|string|max:500',

            'nursing_notes_available' => 'nullable|in:Yes,No,NA',
            'nursing_notes_available_remarks' => 'nullable|string|max:500',

            'nursing_notes_complete' => 'nullable|in:Yes,No,NA',
            'nursing_notes_complete_remarks' => 'nullable|string|max:500',

            'doctor_notes_available' => 'nullable|in:Yes,No,NA',
            'doctor_notes_available_remarks' => 'nullable|string|max:500',

            'doctor_notes_complete' => 'nullable|in:Yes,No,NA',
            'doctor_notes_complete_remarks' => 'nullable|string|max:500',

            'progress_chart_available' => 'nullable|in:Yes,No,NA',
            'progress_chart_available_remarks' => 'nullable|string|max:500',

            'progress_chart_complete' => 'nullable|in:Yes,No,NA',
            'progress_chart_complete_remarks' => 'nullable|string|max:500',

            'treatment_chart_available' => 'nullable|in:Yes,No,NA',
            'treatment_chart_available_remarks' => 'nullable|string|max:500',

            'treatment_chart_complete' => 'nullable|in:Yes,No,NA',
            'treatment_chart_complete_remarks' => 'nullable|string|max:500',

            'monitoring_available' => 'nullable|in:Yes,No,NA',
            'monitoring_available_remarks' => 'nullable|string|max:500',

            'discharge_summary' => 'nullable|in:Yes,No,NA',
            'discharge_summary_remarks' => 'nullable|string|max:500',

            'overall_remarks' => 'nullable|string',

            'photo_latitude' => 'required|numeric',
            'photo_longitude' => 'required|numeric',
            'photo_address' => 'nullable|string|max:1000',

            'visit_photo' => 'required|image|max:10240',

            'attachments' => 'required|array|min:1',
            'attachments.*.name' => 'required|string|max:255',
            'attachments.*.file' => 'required|file|mimes:jpeg,jpg,png,pdf|max:10240',

        ]);

        DB::transaction(function () use ($request, $validated, $auditId) {

            $photoPath = $request->file('visit_photo')
                ->store('field_visits/'.$auditId.'/photos', 'public');

            $visit = FieldVisit::create(array_merge(
                $validated,
                [
                    'audit_id' => $auditId,
                    'photo_path' => $photoPath,
                    'photo_taken_at' => now(),
                    'created_by' => auth()->id(),
                ]
            ));

            foreach ($request->file('attachments', []) as $idx => $item) {

                $filePath = $item['file']
                    ->store('field_visits/'.$auditId.'/attachments', 'public');

                FieldVisitAttachment::create([
                    'field_visit_id' => $visit->id,
                    'name' => $validated['attachments'][$idx]['name'],
                    'file_path' => $filePath,
                ]);
            }
        });

        $audit->status = 'completed';
        $audit->save();

        return redirect()
            ->route('dmo.audits.field.all')
            ->with('success','Field visit report submitted successfully.');
    }
}
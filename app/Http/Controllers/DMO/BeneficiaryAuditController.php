<?php

namespace App\Http\Controllers\DMO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Models\PmjayTreatment;
use App\Models\Audits\BeneficiaryAudit;
use App\Models\Audits\BeneficiaryAuditMember;
class BeneficiaryAuditController extends Controller
{

    public function beneficiaryyAuditForm(Request $request, $id)
    {
        $hospitals   = \App\Models\Hospital::orderBy('name')->get();
        $districts   = \App\Models\District::orderBy('name')->get();
        $audit = PmjayTreatment::with([
                'hospital',
                'audit',
                'telephonicAudit.audit_conclusion'
            ])->findOrFail($id); 
        return view('dmo.audits.beneficiary_audit.form', compact('audit', 'hospitals', 'districts'));
    }

    public function storeBeneficiaryAudit(Request $request, $auditId)
    {
        $audit = PmjayTreatment::findOrFail($auditId);

        $validated = $request->validate([

            'pmjay_family_id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'guardian_name' => 'required|string|max:255',
            'address' => 'required|string|max:1000',
            'district_id' => 'required|exists:districts,id',
            'state' => 'required|string|max:255',
            'pin_code' => 'required|digits:6',
            'contact_no' => 'required|digits:10',

            'members' => 'nullable|array',
            'members.*.name' => 'nullable|string|max:255',
            'members.*.pmjay_id_number' => 'nullable|string|max:255',
            'members.*.gender' => 'nullable|in:Male,Female,Other',
            'members.*.age' => 'nullable|integer|min:0|max:120',
            'members.*.relationship' => 'nullable|string|max:255',

            'ecard_made_at' => 'required|string|max:255',
            'ecard_charged' => 'required|in:Yes,No',
            'ecard_charge_amount' => 'required_if:ecard_charged,Yes|nullable|numeric|min:0',

            'availed_services' => 'required|in:Yes,No',
            'hospital_id' => 'required_if:availed_services,Yes|nullable|exists:hospitals,id',
            'symptoms' => 'required_if:availed_services,Yes|nullable|string|max:1000',
            'admission_date' => 'required_if:availed_services,Yes|nullable|date',
            'discharge_date' => 'required_if:availed_services,Yes|nullable|date|after_or_equal:admission_date',
            'days_hospitalized' => 'required_if:availed_services,Yes|nullable|integer|min:1',
            'free_food' => 'required_if:availed_services,Yes|nullable|in:Yes,No',
            'treatment_given' => 'required_if:availed_services,Yes|nullable|string|max:1000',
            'surgery_scar' => 'required_if:availed_services,Yes|nullable|in:Yes,No,NA',
            'surgery_scar_remarks' => 'nullable|string|max:500',

            'photo_match' => 'required|in:Yes,No,NA',

            'other_remarks' => 'nullable|string|max:1000',
            'recommendation' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($validated, $auditId) {

            $beneficiaryAudit = BeneficiaryAudit::create(array_merge(
                Arr::except($validated, ['members']),
                [
                    'audit_id' => $auditId,
                    'submitted_by' => auth()->id(),
                ]
            ));

            foreach ($validated['members'] ?? [] as $index => $member) {
                // Skip fully-empty rows (JS shouldn't send them, but guard anyway)
                if (empty(array_filter($member))) continue;

                BeneficiaryAuditMember::create(array_merge($member, [
                    'beneficiary_audit_id' => $beneficiaryAudit->id,
                    'sort_order' => $index + 1,
                ]));
            }
        });

        return redirect()
            ->route('dmo.audits.beneficiary.all')
            ->with('success', 'Beneficiary Audit submitted successfully.');
    }
}

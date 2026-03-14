<?php

namespace App\Http\Controllers\DMO;

use App\Http\Controllers\Controller;
use App\Http\Requests\DMO\StoreLiveAuditRequest;
use App\Models\LiveAudit;
use App\Models\LiveAuditAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\Request;

class LiveAuditController extends Controller
{
    /**
     * Show the blank independent audit form.
     */
    public function create(): View
    {
        $hospitals   = \App\Models\Hospital::orderBy('name')->get();
        $districts   = \App\Models\District::orderBy('name')->get();
        return view('dmo.audits.live.live', compact('hospitals', 'districts'));
    }

    /**
     * Show a previously submitted audit in read-only mode.
     */
    public function show(int $id): View
    {
        $liveAudit = LiveAudit::with(['attachments', 'submittedBy', 'hospital', 'district'])
            ->where('id', $id)
            ->where('submitted_by', auth()->id())
            ->firstOrFail();

        return view('dmo.audits.live.show', compact('liveAudit'));
    }

    /**
     * Store a new independent live audit — no assigned case, no pmjay_audits reference.
     */
    public function store(StoreLiveAuditRequest $request): RedirectResponse
    {
        //dd($request->all());
        $liveAudit = DB::transaction(function () use ($request) {

            $bedPhotoPath = $request->file('bed_photo')
                ->store('live-audits/photos', 'public');

            $receiptPath = null;
            if ($request->hasFile('receipt_file')) {
                $receiptPath = $request->file('receipt_file')
                    ->store('live-audits/receipts', 'public');
            }

            $liveAudit = LiveAudit::create([
                'submitted_by'          => auth()->id(),

                'patient_name'          => $request->patient_name,
                'contact_number'        => $request->contact_number,
                'hospital_name'         => $request->hospital_name,
                'pmjay_id'              => $request->pmjay_id,
                'hospital_id'           => $request->hospital_id,
                'district_id'           => $request->district_id,
                'registration_number'   => $request->registration_number,
                'package_booked'        => $request->package_booked,
                'treating_doctor'       => $request->treating_doctor,
                'doctor_specialization' => $request->doctor_specialization,
                'admission_datetime'    => $request->admission_datetime,
                'discharge_datetime'    => $request->discharge_datetime,
                'treatment_type'        => $request->treatment_type,

                'bed_photo_path'        => $bedPhotoPath,
                'bed_photo_latitude'    => $request->bed_photo_latitude,
                'bed_photo_longitude'   => $request->bed_photo_longitude,
                'bed_photo_address'     => $request->bed_photo_address,
                'bed_photo_taken_at'    => now(),

                'ai_bed_detected'        => filter_var($request->ai_bed_detected,        FILTER_VALIDATE_BOOLEAN),
                'ai_patient_detected'    => filter_var($request->ai_patient_detected,    FILTER_VALIDATE_BOOLEAN),
                'ai_pmjay_card_detected' => filter_var($request->ai_pmjay_card_detected, FILTER_VALIDATE_BOOLEAN),
                'ai_face_count'          => (int) $request->ai_face_count,
                'ai_labels'              => $request->ai_labels  ? json_decode($request->ai_labels,  true) : [],
                'ai_objects'             => $request->ai_objects ? json_decode($request->ai_objects, true) : [],
                'ai_validation_message'  => $request->ai_validation_message,

                'patient_id_collected'  => $request->patient_id_collected,
                'patient_id_remarks'    => $request->patient_id_remarks,

                'presenting_complaints'          => $request->presenting_complaints,
                'symptoms_duration'              => $request->symptoms_duration,
                'referred_from_other'            => $request->referred_from_other,
                'referred_from_name'             => $request->referred_from_name,
                'patient_admitted_when'          => $request->patient_admitted_when,
                'patient_still_admitted'         => $request->patient_still_admitted,
                'patient_still_admitted_remarks' => $request->patient_still_admitted_remarks,
                'diagnostic_tests_done'          => $request->diagnostic_tests_done,
                'surgery_conducted'              => $request->surgery_conducted,
                'surgery_scar_present'           => $request->surgery_scar_present,
                'surgery_scar_remarks'           => $request->surgery_scar_remarks,

                'money_charged'        => $request->money_charged,
                'money_charged_amount' => $request->money_charged_amount,
                'receipt_available'    => $request->receipt_available,
                'receipt_path'         => $receiptPath,

                'previous_hospitalisation'         => $request->previous_hospitalisation,
                'previous_hospitalisation_remarks' => $request->previous_hospitalisation_remarks,

                'other_remarks' => $request->other_remarks,
            ]);

            if ($request->has('attachments')) {
                foreach ($request->attachments as $index => $attachment) {
                    if (isset($attachment['file'])) {
                        $path = $attachment['file']->store('live-audits/attachments', 'public');
                        LiveAuditAttachment::create([
                            'live_audit_id' => $liveAudit->id,
                            'name'          => $attachment['name'],
                            'file_path'     => $path,
                            'sort_order'    => $index,
                        ]);
                    }
                }
            }

            return $liveAudit;
        });

        return redirect()
            ->route('dmo.audits.live-audit.show', $liveAudit->id)
            ->with('success', 'Live audit submitted successfully.');
    }

    /**
     * List all live audits submitted by this DMO, newest first.
     *
     * GET /dmo/live-audit
     */
    public function viewAll(Request $request): View
    {
        $query = LiveAudit::with('attachments')
            ->where('submitted_by', auth()->id())
            ->latest();

        // Optional status filter: ai_passed | ai_skipped | all (default)
        if ($request->filled('filter')) {
            match ($request->filter) {
                'ai_passed'  => $query->where('ai_bed_detected', true)
                                      ->where('ai_patient_detected', true),
                'ai_skipped' => $query->where('ai_validation_message', 'like', '%skipped%'),
                default      => null,
            };
        }

        // Optional search: patient name or hospital
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('patient_name',  'like', $term)
                  ->orWhere('hospital_name', 'like', $term)
                  ->orWhere('pmjay_id',      'like', $term);
            });
        }

        $liveAudits = $query->paginate(15)->withQueryString();

        // Summary counts for filter chips
        $stats = LiveAudit::where('submitted_by', auth()->id())
            ->selectRaw("
                COUNT(*)                                                              AS total,
                SUM(CASE WHEN ai_bed_detected = 1
                          AND ai_patient_detected = 1              THEN 1 ELSE 0 END) AS ai_passed,
                SUM(CASE WHEN ai_validation_message LIKE '%skipped%' THEN 1 ELSE 0 END) AS ai_skipped
            ")
            ->first();

        return view('dmo.audits.live.view_all', compact('liveAudits', 'stats'));
    }
}

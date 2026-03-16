<?php

namespace App\Http\Controllers\DMO;

use App\Http\Controllers\Controller;
use App\Http\Requests\DMO\StoreInfraAuditRequest;
use App\Http\Requests\DMO\UpdateInfraAuditRequest;
use App\Services\InfraAuditService;
use App\Models\Audits\InfrastructureAudit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InfrastructureAuditController extends Controller
{
    public function __construct(
        private readonly InfraAuditService $service,
    ) {}

    // ── Routes ─────────────────────────────────────────────────────────────

    /**
     * GET /dmo/infra-audit
     * Paginated listing with optional date/type filters.
     */
    public function index(Request $request): View
    {
        $audits = InfrastructureAudit::with('submittedBy')
            ->when($request->date, fn ($q) => $q->byDate($request->date))
            ->when($request->type, fn ($q) => $q->byType($request->type))
            ->latest('investigation_date')
            ->paginate(20)
            ->withQueryString();

        return view('dmo.audits.infrastructure.index', compact('audits'));
    }

    /**
     * GET /dmo/infra-audit/create
     * Show the blank audit form.
     */
    public function create(): View
    {
        return view('dmo.audits.infrastructure.infrastructure', [
            'infraAudit' => null,
        ]);
    }

    /**
     * POST /dmo/infra-audit
     * Validate and persist a new audit, then redirect to the read-only view.
     */
    public function store(StoreInfraAuditRequest $request): RedirectResponse
    {
        $infraAudit = $this->service->store(
            $request->validated(),
            $request->file('banner_photo'),
        );

        return redirect()
            ->route('dmo.audits.infra-audit.show', $infraAudit)
            ->with('success', 'Infrastructure audit submitted successfully.');
    }

    /**
     * GET /dmo/infra-audit/{infraAudit}
     * Read-only view of a submitted audit.
     */
    public function show($id): View
    {
        //$this->authorize('view', $infraAudit);
        //$infraAudit->load('submittedBy');

        $infraAudit = InfrastructureAudit::whereId($id)->with('submittedBy')->first();

        return view('dmo.audits.infrastructure.show', [
            'infraAudit' => $infraAudit,
        ]);
    }

    /**
     * GET /dmo/infra-audit/{infraAudit}/edit
     * Pre-populated edit form (supervisors / admins only).
     */
    public function edit(InfrastructureAudit $infraAudit): View
    {
        $this->authorize('update', $infraAudit);

        return view('dmo.audit.infra-edit', [
            'infraAudit' => $infraAudit,
        ]);
    }

    /**
     * PUT /dmo/infra-audit/{infraAudit}
     * Apply corrections to a submitted audit.
     */
    public function update(UpdateInfraAuditRequest $request, InfrastructureAudit $infraAudit): RedirectResponse
    {
        $this->service->update(
            $infraAudit,
            $request->validated(),
            $request->file('banner_photo'),
        );

        return redirect()
            ->route('dmo.infra-audit.show', $infraAudit)
            ->with('success', 'Audit record updated successfully.');
    }

    /**
     * DELETE /dmo/infra-audit/{infraAudit}
     * Soft-delete (admins / supervisors only).
     */
    public function destroy(InfrastructureAudit $infraAudit): RedirectResponse
    {
        $this->authorize('delete', $infraAudit);

        $infraAudit->delete();

        return redirect()
            ->route('dmo.infra-audit.index')
            ->with('success', 'Audit record deleted.');
    }

    /**
     * POST /dmo/infra-audit/verify-banner   (AJAX — no page reload)
     *
     * Accepts a base64-encoded image string (matching VisionController pattern),
     * strips the data-URI prefix, and passes raw bytes to Google Cloud Vision
     * via InfraAuditService::verifyBannerWithVision().
     *
     * Request body:
     *   { "image": "data:image/jpeg;base64,/9j/4AAQ..." }
     *
     * Response JSON:
     *   { "ok": true, "pass": true, "pmjay_branding": true,
     *     "visible": true, "summary": "...", "details": "...",
     *     "labels": [...], "objects": [...] }
     */
    public function verifyBanner(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|string',   // base64 data-URI from the upload widget
        ]);

        // Strip the data-URI prefix (e.g. "data:image/jpeg;base64,")
        // Mirrors the exact line in VisionController::validateBedPhoto()
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $request->input('image'));

        $result = $this->service->verifyBannerWithVision($base64);

        // Return a 500 status on hard Vision API errors so the front-end
        // can distinguish network/auth failures from a clean "failed" result.
        $status = (! $result['ok'] && ! ($result['skipped'] ?? false)) ? 500 : 200;

        return response()->json($result, $status);
    }
}

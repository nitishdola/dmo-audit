<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditSummaryImageService;
use Illuminate\Http\Response;

class AuditSummaryImageController extends Controller
{
    public function __construct(
        private readonly AuditSummaryImageService $service,
    ) {}

    // ── Routes ─────────────────────────────────────────────────────────────

    /**
     * GET /admin/audit-summary/image
     *
     * Generates the monthly summary PNG and streams it directly to the browser.
     * Use this URL as the image source when sending via WhatsApp Business API.
     *
     * Response headers:
     *   Content-Type:        image/png
     *   Content-Disposition: inline; filename="audit-summary-{date}.png"
     */
    public function image(): Response
    {
        $png      = $this->service->generatePng();
        $filename = 'audit-summary-' . now()->format('Y-m-d') . '.png';

        return response($png, 200, [
            'Content-Type'        => 'image/png',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control'       => 'no-store',
        ]);
    }

    /**
     * GET /admin/audit-summary/download
     *
     * Same PNG but forces a file download.
     * Useful for manually downloading and uploading to WhatsApp.
     */
    public function download(): Response
    {
        $png      = $this->service->generatePng();
        $filename = 'audit-summary-' . now()->format('Y-m-d') . '.png';

        return response($png, 200, [
            'Content-Type'        => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * GET /admin/audit-summary/preview
     *
     * Returns the raw HTML blade render (no Browsershot).
     * Use this in the browser to check layout before generating the PNG.
     */
    public function preview(): \Illuminate\View\View
    {
        $data = $this->service->collectStats();

        return view('admin.summary.whatsapp-card', $data);
    }

    
}

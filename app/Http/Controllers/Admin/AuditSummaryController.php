<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditSummaryImageService;
use App\Services\AuditSummaryWhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuditSummaryController extends Controller
{
    public function __construct(
        private readonly AuditSummaryImageService    $imageService,
        private readonly AuditSummaryWhatsAppService $whatsappService,
    ) {}

    /**
     * GET /admin/audit-summary/image
     * Stream PNG to browser for preview.
     */
    public function image(): Response
    {
        $png = $this->imageService->generatePng();

        return response($png, 200, [
            'Content-Type'        => 'image/png',
            'Content-Disposition' => 'inline; filename="audit-summary-' . now()->format('Y-m-d') . '.png"',
            'Cache-Control'       => 'no-store',
        ]);
    }

    /**
     * GET /admin/audit-summary/download
     * Force-download the PNG.
     */
    public function download(): Response
    {
        $png = $this->imageService->generatePng();

        return response($png, 200, [
            'Content-Type'        => 'image/png',
            'Content-Disposition' => 'attachment; filename="audit-summary-' . now()->format('Y-m-d') . '.png"',
        ]);
    }

    /**
     * POST /admin/audit-summary/send-whatsapp
     *
     * Collects stats → sends Twilio template message to all recipients.
     * No image is attached — text summary only via approved template.
     */
    public function sendWhatsApp(): JsonResponse
    {
        $stats   = $this->imageService->collectStats();
        $results = $this->whatsappService->broadcast($stats);
        
        $sentCount   = count($results['sent']);
        $failedCount = count($results['failed']);

        return response()->json([
            'success' => $failedCount === 0,
            'message' => "Sent to {$sentCount} recipient(s)."
                       . ($failedCount > 0 ? " {$failedCount} failed." : ''),
            'sent'    => $results['sent'],
            'failed'  => $results['failed'],
            'period'  => $stats['from'] . ' – ' . $stats['to'],
        ], $failedCount > 0 ? 207 : 200);
    }
}

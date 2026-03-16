<?php

namespace App\Services;

use App\Models\Audits\InfrastructureAudit;
use Google\Cloud\Vision\V1\Client\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\BatchAnnotateImagesRequest;
use Google\Cloud\Vision\V1\AnnotateImageRequest;
use Google\Cloud\Vision\V1\Image;
use Google\Cloud\Vision\V1\Feature;
use Google\Cloud\Vision\V1\Feature\Type;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InfraAuditService
{
    // ── Public API ─────────────────────────────────────────────────────────

    /**
     * Persist a new InfraAudit record from validated request data.
     */
    public function store(array $data, ?UploadedFile $bannerPhoto): InfrastructureAudit
    {
        $bannerPath = $bannerPhoto ? $this->storeBannerPhoto($bannerPhoto) : null;
        $payload    = $this->buildPayload($data, $bannerPath);

        return InfrastructureAudit::create($payload);
    }

    /**
     * Update an existing InfraAudit record.
     * Only replaces the banner photo if a new file is provided.
     */
    public function update(InfraAudit $infraAudit, array $data, ?UploadedFile $bannerPhoto): InfraAudit
    {
        $bannerPath = $infraAudit->banner_photo_path; // keep existing by default

        if ($bannerPhoto) {
            // Remove the old file from storage before saving the new one
            if ($infraAudit->banner_photo_path) {
                Storage::disk('public')->delete($infraAudit->banner_photo_path);
            }
            $bannerPath = $this->storeBannerPhoto($bannerPhoto);
        }

        $payload = $this->buildPayload($data, $bannerPath);

        // Never overwrite the original submitter on an edit
        unset($payload['submitted_by']);

        $infraAudit->update(array_filter($payload, fn ($v) => !is_null($v)));

        return $infraAudit->fresh();
    }

    /**
     * Verify a hospital banner image using Google Cloud Vision API.
     *
     * Mirrors the pattern in VisionController::validateBedPhoto() exactly:
     *   - Accepts raw base64 string (data-URI prefix stripped by caller)
     *   - Runs LABEL_DETECTION + OBJECT_LOCALIZATION in a single API call
     *   - Checks for PMJAY / Ayushman Bharat signage keywords in results
     *   - Returns a structured array that the controller JSON-encodes
     *
     * @param  string  $base64  Pure base64 image bytes (no data-URI prefix)
     * @return array{ok:bool, pass:bool, pmjay_branding:bool, visible:bool,
     *               summary:string, details:string,
     *               labels:array, objects:array}
     */
    public function verifyBannerWithVision_test(string $base64): array
    {
        $credentialsPath = storage_path(config('services.google_cloud.key_file'));

        if (! file_exists($credentialsPath)) {
            Log::warning('InfraAuditService: Google Cloud credentials file not found – skipping Vision check.');
            return $this->aiSkipResponse('Google Cloud credentials not configured.');
        }

        $imageBytes = base64_decode($base64);

        try {
            $client = new ImageAnnotatorClient([
                'credentials' => $credentialsPath,
                'transport'   => 'rest',
            ]);

            $image = new Image();
            $image->setContent($imageBytes);

            // ── Feature 1: Label Detection (scene / text context) ──────────────
            // Picks up printed text categories, signage, and broad scene labels.
            $labelFeature = new Feature();
            $labelFeature->setType(Type::LABEL_DETECTION);
            $labelFeature->setMaxResults(30);

            // ── Feature 2: Object Localisation (physical items in frame) ──────
            // More precise than labels for detecting physical signboards/banners.
            $objectFeature = new Feature();
            $objectFeature->setType(Type::OBJECT_LOCALIZATION);
            $objectFeature->setMaxResults(20);

            // ── Feature 3: Text Detection (OCR – reads actual text on banner) ──
            // Critical: lets us find "Ayushman", "PMJAY", "AB-PMJAY" printed
            // directly on the signage even when label scores are low.
            $textFeature = new Feature();
            $textFeature->setType(Type::TEXT_DETECTION);

            $annotateRequest = new AnnotateImageRequest();
            $annotateRequest->setImage($image);
            $annotateRequest->setFeatures([$labelFeature, $objectFeature, $textFeature]);

            $batchRequest = new BatchAnnotateImagesRequest();
            $batchRequest->setRequests([$annotateRequest]);

            $response = $client->batchAnnotateImages($batchRequest);
            $result   = $response->getResponses()[0];
            $client->close();

            // ── Collect labels (≥ 50 % confidence) ────────────────────────────
            $labels = [];
            foreach ($result->getLabelAnnotations() as $label) {
                if ($label->getScore() >= 0.50) {
                    $labels[] = strtolower($label->getDescription());
                }
            }

            // ── Collect objects (≥ 50 % confidence) ───────────────────────────
            $objects = [];
            foreach ($result->getLocalizedObjectAnnotations() as $obj) {
                if ($obj->getScore() >= 0.50) {
                    $objects[] = strtolower($obj->getName());
                }
            }

            // ── Collect OCR text (full concatenated string, lower-cased) ──────
            $ocrText = '';
            $textAnnotations = $result->getTextAnnotations();
            if (count($textAnnotations) > 0) {
                // Index 0 is the full-page text block
                $ocrText = strtolower($textAnnotations[0]->getDescription());
            }

            

            // ── Check 1: Is this actually a banner / signage? ─────────────────
            // We look for visual objects that indicate a flat printed surface,
            // OR scene labels that suggest an information board / poster.
            $bannerObjects = [
                'banner', 'sign', 'signage', 'poster', 'billboard', 'board',
                'advertising', 'placard', 'notice board', 'flex', 'hoarding',
            ];
            $bannerLabels = [
                'banner', 'signage', 'sign', 'poster', 'billboard', 'advertising',
                'brand', 'logo', 'text', 'font', 'graphic design', 'display',
                'information', 'notice', 'placard', 'board', 'wall',
            ];

            $isBanner = ! empty(array_intersect($objects, $bannerObjects))
                     || ! empty(array_intersect($labels,  $bannerLabels))
                     || str_contains($ocrText, 'hospital')
                     || str_contains($ocrText, 'health')
                     || str_contains($ocrText, 'scheme')
                     || str_contains($ocrText, 'care')
                     || str_contains($ocrText, 'government')
                     || str_contains($ocrText, 'govt');

            // ── Check 2: PMJAY / Ayushman Bharat branding detected ────────────
            // Primary: OCR is the most reliable signal — reads what's literally
            // printed. Secondary: label scores sometimes flag government health
            // campaigns by name.
            $pmjayOcrKeywords = [
                'ayushman', 'pmjay', 'ab-pmjay', 'ab pmjay',
                'pradhan mantri', 'jan arogya',
                'ayushman bharat', 'health protection',
            ];
            $pmjayLabelKeywords = [
                'ayushman bharat', 'pmjay', 'ab pmjay',
                'national health', 'health scheme',
            ];

            $pmjayBranding = false;
            foreach ($pmjayOcrKeywords as $kw) {
                if (str_contains($ocrText, $kw)) {
                    $pmjayBranding = true;
                    break;
                }
            }
            if (! $pmjayBranding) {
                $pmjayBranding = ! empty(array_intersect($labels, $pmjayLabelKeywords));
            }

            // ── Check 3: Is the banner visibly readable / prominent? ──────────
            // We infer readability from the presence of OCR text (Vision found
            // actual printed text) and the scene being well-lit / clear.
            $poorVisibilityLabels = ['blur', 'blurred', 'dark', 'night', 'shadow', 'dim'];
            $isReadable = strlen($ocrText) > 10  // some text was detected
                       && empty(array_intersect($labels, $poorVisibilityLabels));

            // ── Build verdict ─────────────────────────────────────────────────
            // Pass = looks like a banner AND PMJAY branding found AND readable.
            $pass = $isBanner && $pmjayBranding && $isReadable;

            // ── Human-readable summary & details ──────────────────────────────
            [$summary, $details] = $this->buildBannerNarrative(
                $pass, $isBanner, $pmjayBranding, $isReadable,
                $labels, $objects, $ocrText
            );

            return [
                'ok'             => true,
                'pass'           => $pass,
                'pmjay_branding' => $pmjayBranding,
                'visible'        => $isReadable,
                'summary'        => $summary,
                'details'        => $details,
                'labels'         => $labels,
                'objects'        => $objects,
            ];

        } catch (\Exception $e) {
            Log::error('InfraAuditService Vision API error: ' . $e->getMessage());
            return $this->aiErrorResponse('Vision API error: ' . $e->getMessage());
        }
    }


    public function verifyBannerWithVision(string $base64): array
    {
        $credentialsPath = storage_path(config('services.google_cloud.key_file'));

        if (! file_exists($credentialsPath)) {
            Log::warning('InfraAuditService: Google Cloud credentials file not found – skipping Vision check.');
            return $this->aiSkipResponse('Google Cloud credentials not configured.');
        }

        $imageBytes = base64_decode($base64);

        try {
            $client = new ImageAnnotatorClient([
                'credentials' => $credentialsPath,
                'transport'   => 'rest',
            ]);

            $image = new Image();
            $image->setContent($imageBytes);

            // ── Feature 1: Label Detection (scene / text context) ──────────────
            // Picks up printed text categories, signage, and broad scene labels.
            $labelFeature = new Feature();
            $labelFeature->setType(Type::LABEL_DETECTION);
            $labelFeature->setMaxResults(30);

            // ── Feature 2: Object Localisation (physical items in frame) ──────
            // More precise than labels for detecting physical signboards/banners.
            $objectFeature = new Feature();
            $objectFeature->setType(Type::OBJECT_LOCALIZATION);
            $objectFeature->setMaxResults(20);

            // ── Feature 3: Text Detection (OCR – reads actual text on banner) ──
            // Critical: lets us find "Ayushman", "PMJAY", "AB-PMJAY" printed
            // directly on the signage even when label scores are low.
            $textFeature = new Feature();
            $textFeature->setType(Type::TEXT_DETECTION);

            $annotateRequest = new AnnotateImageRequest();
            $annotateRequest->setImage($image);
            $annotateRequest->setFeatures([$labelFeature, $objectFeature, $textFeature]);

            $batchRequest = new BatchAnnotateImagesRequest();
            $batchRequest->setRequests([$annotateRequest]);

            $response = $client->batchAnnotateImages($batchRequest);
            $result   = $response->getResponses()[0];
            $client->close();

            // ── Collect labels (≥ 50 % confidence) ────────────────────────────
            $labels = [];
            foreach ($result->getLabelAnnotations() as $label) {
                if ($label->getScore() >= 0.50) {
                    $labels[] = strtolower($label->getDescription());
                }
            }

            // ── Collect objects (≥ 50 % confidence) ───────────────────────────
            $objects = [];
            foreach ($result->getLocalizedObjectAnnotations() as $obj) {
                if ($obj->getScore() >= 0.50) {
                    $objects[] = strtolower($obj->getName());
                }
            }

            // ── Collect OCR text (full concatenated string, lower-cased) ──────
            $ocrText = '';
            $textAnnotations = $result->getTextAnnotations();
            if (count($textAnnotations) > 0) {
                // Index 0 is the full-page text block
                $ocrText = strtolower($textAnnotations[0]->getDescription());
            }
            // ── Check 1: Is this actually a banner / signage? ─────────────────
            // We look for visual objects that indicate a flat printed surface,
            // OR scene labels that suggest an information board / poster.
            $bannerObjects = [
                'banner', 'sign', 'signage', 'poster', 'billboard', 'board',
                'advertising', 'placard', 'notice board', 'flex', 'hoarding',
            ];
            $bannerLabels = [
                'banner', 'signage', 'sign', 'poster', 'billboard', 'advertising',
                'brand', 'logo', 'text', 'font', 'graphic design', 'display',
                'information', 'notice', 'placard', 'board', 'wall',
            ];

            $isBanner = ! empty(array_intersect($objects, $bannerObjects))
                     || ! empty(array_intersect($labels,  $bannerLabels))
                     || str_contains($ocrText, 'hospital')
                     || str_contains($ocrText, 'polyclinic')
                     || str_contains($ocrText, 'clinic')
                     || str_contains($ocrText, 'health')
                     || str_contains($ocrText, 'scheme')
                     || str_contains($ocrText, 'government')
                     || str_contains($ocrText, 'govt')
                     || str_contains($ocrText, 'care')
                     || str_contains($ocrText, 'child');

            // ── Check 3: Is the banner visibly readable / prominent? ──────────
            // We infer readability from the presence of OCR text (Vision found
            // actual printed text) and the scene being well-lit / clear.
            $poorVisibilityLabels = ['blur', 'blurred', 'dark', 'night', 'shadow', 'dim'];
            $isReadable = strlen($ocrText) > 10  // some text was detected
                       && empty(array_intersect($labels, $poorVisibilityLabels));
            
            // ── Build verdict ─────────────────────────────────────────────────
            // Pass = looks like a banner AND PMJAY branding found AND readable.
            $pass = $isBanner && $isReadable;
    
            // ── Human-readable summary & details ──────────────────────────────
            [$summary, $details] = $this->buildBannerNarrative(
                $pass, $isBanner, $isReadable,
                $labels, $objects, $ocrText
            );
            

            return [
                'ok'             => true,
                'pass'           => $pass,
                'visible'        => $isReadable,
                'summary'        => $summary,
                'details'        => $details,
                'labels'         => $labels,
                'objects'        => $objects,
            ];

        } catch (\Exception $e) {
            Log::error('InfraAuditService Vision API error: ' . $e->getMessage());
            return $this->aiErrorResponse('Vision API error: ' . $e->getMessage());
        }
    }

    // ── Private helpers ────────────────────────────────────────────────────

    private function storeBannerPhoto(UploadedFile $file): string
    {
        return $file->store('infra-audits/banners', 'public');
    }

    /**
     * Build auditor-friendly summary + details strings from Vision results.
     */
    private function buildBannerNarrative(
        bool   $pass,
        bool   $isBanner,
        bool   $isReadable,
        array  $labels,
        array  $objects,
        string $ocrText,
    ): array {
        if ($pass) {
            // $summary = 'PMJAY banner detected and verified successfully.';
            // $details = 'The image shows a hospital promotional signage with Ayushman Bharat / PMJAY branding. '
            //          . 'The banner is clearly visible and readable. '
            //          . 'OCR confirmed relevant scheme text on the board.';
            // return [$summary, $details];


            $summary = '.';
            $details = 'The image shows a hospital promotional signage. '
                     . 'The banner is clearly visible and readable. ';
            return [$summary, $details];
        }

        $issues  = [];
        $details = [];

        if (! $isBanner) {
            $issues[]  = 'no promotional banner/signage detected';
            $details[] = 'The image does not appear to show a hospital banner or promotional signage — ';
        }

        

        if (! $isReadable) {
            $issues[]  = 'banner may not be clearly readable';
            $details[] = 'The image quality or lighting appears insufficient for text recognition. '
                       . 'Retake the photo in better light with the banner fully in frame.';
        }

        $summary = 'Verification failed: ' . implode('; ', $issues) . '.';
        return [$summary, implode(' ', $details)];
    }

    private function buildPayload(array $data, ?string $bannerPath): array
    {
        return array_merge(
            [
                'submitted_by'       => Auth::id(),
                'investigation_date' => $data['investigation_date'] ?? null,
            ],
            $this->pick($data, [
                // A. Hospital Details
                'hospital_name', 'hospital_address', 'hospital_id',
                'hospital_type', 'pmjay_beneficiaries_tms', 'pmjay_beneficiaries_actual',

                // B. Infrastructure
                'hospital_existence',       'hospital_existence_remarks',
                'hospital_response',        'hospital_response_remarks',
                'dghs_registered',          'dghs_registered_remarks',
                'ai_banner_pass',           'ai_pmjay_branding',
                'ai_banner_visible',        'ai_banner_summary',
                'ai_banner_details',        'banner_remarks',
                'pmam_kiosk_available',     'pmam_kiosk_location',      'pmam_kiosk_remarks',
                'promo_boards_displayed',   'promo_boards_remarks',
                'total_beds',               'general_ward_beds',
                'bed_distance_adequate',    'bed_distance_remarks',
                'hdu_available',            'hdu_beds',
                'icu_available',            'icu_beds',
                'icu_well_equipped',        'icu_equipment',            'icu_equipment_remarks',
                'ot_available',             'ot_count',                 'ot_tables',
                'ot_sterilization',         'ot_sterilization_remarks',
                'ot_lighting',              'ot_ac',
                'ot_well_equipped',         'ot_equipment',             'ot_equipment_remarks',
                'pathology_diagnostics',    'pathology_remarks',
                'biomedical_waste',         'biomedical_waste_remarks',
                'overall_hygiene',          'overall_hygiene_remarks',
                'infra_other_remarks',

                // C. Human Resource
                'pmam_available',           'pmam_available_remarks',
                'onduty_doctors',           'onduty_doctor_types',      'onduty_doctors_remarks',
                'adequate_nurses',          'adequate_nurses_remarks',
                'nurses_qualified',         'nurses_qualified_remarks',
                'technicians_available',
                'pharmacists_available',
                'specialists_available',    'specialists_remarks',
                'hr_other_remarks',
            ]),
            ['banner_photo_path' => $bannerPath],
        );
    }

    private function pick(array $data, array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $data[$key] ?? null;
        }
        return $result;
    }

    private function aiSkipResponse(string $reason = 'Vision API not configured.'): array
    {
        return [
            'ok'      => false,
            'skipped' => true,
            'message' => $reason,
        ];
    }

    private function aiErrorResponse(string $message): array
    {
        return [
            'ok'      => false,
            'skipped' => false,
            'message' => $message,
        ];
    }
}

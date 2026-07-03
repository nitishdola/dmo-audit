<?php

namespace App\Http\Controllers\DMO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VisionController extends Controller
{
    // ── Shared helper ──────────────────────────────────────────────────────

    /**
     * Send a base64 image + prompt to Claude and return the parsed JSON array.
     * Throws on network/API failure so callers can catch and return 500.
     */
    private function askClaude(string $base64, string $prompt): array
    {
        $response = Http::withHeaders([
            'x-api-key'         => config('services.anthropic.api_key'),
            'anthropic-version' => '2023-06-01',
            'Content-Type'      => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model'      => 'claude-haiku-4-5',
            'max_tokens' => 512,
            'messages'   => [[
                'role'    => 'user',
                'content' => [
                    [
                        'type'   => 'image',
                        'source' => [
                            'type'       => 'base64',
                            'media_type' => 'image/jpeg',
                            'data'       => $base64,
                        ],
                    ],
                    ['type' => 'text', 'text' => $prompt],
                ],
            ]],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Anthropic API error: HTTP ' . $response->status());
        }

        $text = $response->json('content.0.text', '');

        // Strip accidental markdown fences
        $text = preg_replace('/^```json\s*/i', '', trim($text));
        $text = preg_replace('/```$/m', '', $text);

        $parsed = json_decode(trim($text), true);

        if (! is_array($parsed)) {
            throw new \RuntimeException('Non-JSON response from Anthropic: ' . $text);
        }

        return $parsed;
    }

    // ── Routes ─────────────────────────────────────────────────────────────

    /**
     * POST /dmo/vision/validate-bed-photo
     *
     * Checks that the image contains:
     *   1. At least 2 visible people (beneficiary + DMO/staff)
     *   2. A hospital bed with the beneficiary lying on it
     */
    public function validateBedPhoto(Request $request): JsonResponse
    {
        $request->validate(['image' => 'required|string']);

        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $request->input('image'));

        $prompt = <<<PROMPT
        Analyse this hospital image and respond ONLY with a valid JSON object — no markdown, no explanation.

        Return exactly this structure:
        {
          "person_count": <integer — total visible people including partially visible>,
          "bed_detected": <true/false — is there a hospital bed, stretcher, or gurney in frame?>,
          "patient_on_bed": <true/false — is at least one person lying/sitting on the bed?>,
          "labels": ["list","of","relevant","scene","labels"],
          "objects": ["list","of","physical","objects","detected"]
        }

        Definitions:
        - Count every visible human figure (face, silhouette, partial body) as a person.
        - bed_detected: true for hospital bed, stretcher, gurney, mattress on a frame in a clinical setting.
        - patient_on_bed: true if a person appears to be a patient resting on the bed.
        PROMPT;

        try {
            $data = $this->askClaude($base64, $prompt);

            $personCount  = (int)  ($data['person_count']  ?? 0);
            $bedDetected  = (bool) ($data['bed_detected']  ?? false);
            $twoPersons   = $personCount >= 2;
            $labels       = $data['labels']  ?? [];
            $objects      = $data['objects'] ?? [];

            $errors = [];
            if (! $twoPersons) {
                $errors[] = $personCount === 0
                    ? 'No people detected. At least 2 must be visible (beneficiary + DMO).'
                    : 'Only 1 person detected. The DMO and the beneficiary must both be visible.';
            }
            if (! $bedDetected) {
                $errors[] = 'No hospital bed detected. Photo must show the beneficiary lying on a hospital bed.';
            }

            $valid   = $twoPersons && $bedDetected;
            $message = $valid
                ? "✓ {$personCount} people detected · Beneficiary on bed confirmed"
                : implode(' ', $errors);

            return response()->json([
                'valid'                 => $valid,
                'face_count'            => $personCount,
                'ai_bed_detected'       => $bedDetected,
                'ai_patient_detected'   => $twoPersons,
                'ai_labels'             => $labels,
                'ai_objects'            => $objects,
                'ai_validation_message' => $message,
                'message'               => $message,
            ]);

        } catch (\Exception $e) {
            Log::error('VisionController::validateBedPhoto — ' . $e->getMessage());
            $msg = 'Vision API error: ' . $e->getMessage();
            return response()->json([
                'valid'                 => false,
                'face_count'            => 0,
                'ai_bed_detected'       => false,
                'ai_patient_detected'   => false,
                'ai_labels'             => [],
                'ai_objects'            => [],
                'ai_validation_message' => $msg,
                'message'               => $msg,
            ], 500);
        }
    }

    /**
     * POST /dmo/vision/validate-photo
     *
     * Simpler check: at least 2 people visible (used for non-bed photos).
     */
    public function validatePhoto(Request $request): JsonResponse
    {
        $request->validate(['image' => 'required|string']);

        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $request->input('image'));

        $prompt = <<<PROMPT
        Analyse this image and respond ONLY with a valid JSON object — no markdown, no explanation.

        Return exactly this structure:
        {
          "person_count": <integer — total number of visible people, including partial figures>
        }
        PROMPT;

        try {
            $data  = $this->askClaude($base64, $prompt);
            $count = (int) ($data['person_count'] ?? 0);
            $valid = $count >= 2;

            $message = $valid
                ? "✓ {$count} people detected"
                : ($count === 0
                    ? 'No faces detected. Please ensure people are visible in the photo.'
                    : 'Only 1 person detected. The photo must include at least 2 people.');

            return response()->json([
                'valid'      => $valid,
                'face_count' => $count,
                'message'    => $message,
            ]);

        } catch (\Exception $e) {
            Log::error('VisionController::validatePhoto — ' . $e->getMessage());
            return response()->json([
                'valid'   => false,
                'message' => 'Vision API error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
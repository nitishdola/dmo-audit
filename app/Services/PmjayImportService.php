<?php

namespace App\Services;

use App\Models\Hospital;
use App\Models\District;
use App\Models\PmjayTreatment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PmjayImportService
{
    private const CHUNK_SIZE = 500;

    public function import(string $path): array
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        // Normalize lgd_code keys to trimmed strings to avoid type/whitespace mismatches
        $hospitals = Hospital::pluck('id', 'hospital_code')
            ->mapWithKeys(fn($id, $code) => [trim((string) $code) => $id]);

        $districts = District::pluck('id', 'lgd_code')
            ->mapWithKeys(fn($id, $code) => [trim((string) $code) => $id]);

        $stats = ['processed' => 0, 'skipped' => 0, 'errors' => 0];

        match ($extension) {
            'json'  => $this->importJson($path, $hospitals, $districts, $stats),
            'csv'   => $this->importCsv($path, $hospitals, $districts, $stats),
            default => throw new \Exception("Unsupported file type: {$extension}"),
        };

        return $stats;
    }

    // -------------------------------------------------------------------------
    // JSON import — streams the array without loading whole file into memory
    // Requires:  composer require halaxa/json-machine
    // -------------------------------------------------------------------------
    private function importJson(string $path, $hospitals, $districts, array &$stats): void
    {
        $records = \JsonMachine\Items::fromFile($path, ['pointer' => '']);

        $batch = [];

        foreach ($records as $row) {
            $row    = (array) $row;
            $result = $this->buildRow($row, $hospitals, $districts, $stats);

            if ($result === null) {
                continue;
            }

            $batch[] = $result;
            $stats['processed']++;

            if (count($batch) >= self::CHUNK_SIZE) {
                $this->upsertChunk($batch, $stats);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            $this->upsertChunk($batch, $stats);
        }
    }

    // -------------------------------------------------------------------------
    // CSV import — reads line-by-line using fgetcsv (constant memory)
    // -------------------------------------------------------------------------
    private function importCsv(string $path, $hospitals, $districts, array &$stats): void
    {
        $handle = fopen($path, 'r');

        if (!$handle) {
            throw new \Exception("Cannot open file: {$path}");
        }

        $headers = fgetcsv($handle);

        if (!$headers) {
            fclose($handle);
            throw new \Exception("CSV file is empty or has no header row");
        }

        $headers = array_map('trim', $headers);
        $batch   = [];

        while (($values = fgetcsv($handle)) !== false) {

            if (count($values) !== count($headers)) {
                $stats['errors']++;
                continue;
            }

            $row    = array_combine($headers, $values);
            $result = $this->buildRow($row, $hospitals, $districts, $stats);

            if ($result === null) {
                continue;
            }

            $batch[] = $result;
            $stats['processed']++;

            if (count($batch) >= self::CHUNK_SIZE) {
                $this->upsertChunk($batch, $stats);
                $batch = [];
            }
        }

        fclose($handle);

        if (!empty($batch)) {
            $this->upsertChunk($batch, $stats);
        }
    }

    // -------------------------------------------------------------------------
    // Shared row builder — returns null when the row should be skipped
    // -------------------------------------------------------------------------
    private function buildRow(array $row, $hospitals, $districts, array &$stats): ?array
    {
        // --- Required fields ---
        if (empty($row['ben_mobile_no']) || empty($row['case_id'])) {
            $stats['skipped']++;
            Log::debug('PmjayImport: skipped — missing ben_mobile_no or case_id', [
                'case_id'       => $row['case_id']       ?? null,
                'ben_mobile_no' => $row['ben_mobile_no'] ?? null,
            ]);
            return null;
        }

        // --- Hospital lookup ---
        $hospitalCode = isset($row['hospital_code'])
            ? trim((string) $row['hospital_code'])
            : null;

        if (!$hospitalCode || !isset($hospitals[$hospitalCode])) {
            $stats['skipped']++;
            Log::debug('PmjayImport: skipped — hospital_code not found in DB', [
                'case_id'       => $row['case_id'],
                'hospital_code' => $hospitalCode,
            ]);
            return null;
        }

        // --- District lookup (with trim + string cast to avoid type mismatches) ---
        $districtId   = null;
        $districtCode = isset($row['patient_district_code'])
            ? trim((string) $row['patient_district_code'])
            : null;

        if ($districtCode !== null && $districtCode !== '') {
            if (isset($districts[$districtCode])) {
                $districtId = $districts[$districtCode];
            } else {
                // District code present in file but missing from districts table.
                // Log for investigation but do NOT skip — insert with null if column allows,
                // or change the line below to: $stats['skipped']++; return null;
                Log::warning('PmjayImport: unmatched patient_district_code', [
                    'case_id'               => $row['case_id'],
                    'patient_district_code' => $districtCode,
                ]);
                // If patient_district_id is NOT NULL in your DB schema, uncomment these two lines:
                // $stats['skipped']++;
                // return null;
            }
        }

        // --- Date parsing ---
        $preauthDate = null;
        if (!empty($row['preauth_init_date'])) {
            $ts          = strtotime($row['preauth_init_date']);
            $preauthDate = $ts !== false ? date('Y-m-d', $ts) : null;
        }

        return [
            'registration_id'         => $row['registration_id']         ?? null,
            'case_id'                 => $row['case_id'],
            'patient_name'            => $row['patient_name']            ?? null,
            'member_id'               => $row['member_id']               ?? null,
            'policy_code'             => $row['policy_code']             ?? null,
            'preauth_init_date'       => $preauthDate,
            'ben_mobile_no'           => $row['ben_mobile_no'],
            'hospital_id'             => $hospitals[$hospitalCode],
            'patient_district_id'     => $districtId,
            'procedure_code'          => $row['procedure_code']          ?? null,
            'procedure_details'       => $row['procedure_details']       ?? null,
            'category_details'        => $row['category_details']        ?? null,
            'amount_preauth_approved' => $row['amount_preauth_approved'] ?? null,
            'amount_claim_paid'       => $row['amount_claim_paid']       ?? null,
            'case_status'             => $row['case_status']             ?? null,
            'admission_dt'            => $row['admission_dt']            ?? null,
            'discharge_dt'            => $row['discharge_dt']            ?? null,
            'created_at'              => now(),
            'updated_at'              => now(),
        ];
    }

    // -------------------------------------------------------------------------
    // Upsert one chunk — each chunk gets its own transaction so a single
    // bad chunk doesn't roll back the entire import
    // -------------------------------------------------------------------------
    private function upsertChunk(array $chunk, array &$stats): void
    {
        try {
            DB::transaction(function () use ($chunk) {
                PmjayTreatment::upsert(
                    $chunk,
                    ['case_id'],
                    [
                        'registration_id',
                        'patient_name',
                        'member_id',
                        'hospital_id',
                        'patient_district_id',
                        'procedure_code',
                        'procedure_details',
                        'category_details',
                        'amount_preauth_approved',
                        'amount_claim_paid',
                        'case_status',
                        'admission_dt',
                        'discharge_dt',
                        'updated_at',
                    ]
                );
            });
        } catch (\Throwable $e) {
            // The chunk failed — count all rows as errors and keep going
            $stats['errors']  += count($chunk);
            $stats['processed'] -= count($chunk);
            Log::error('PmjayImport: chunk upsert failed', [
                'error'      => $e->getMessage(),
                'chunk_size' => count($chunk),
                'first_case' => $chunk[0]['case_id'] ?? null,
                'last_case'  => end($chunk)['case_id'] ?? null,
            ]);
        }
    }
}
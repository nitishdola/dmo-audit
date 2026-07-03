<?php

namespace App\Console\Commands;

use App\Services\PmjayImportService;
use Illuminate\Console\Command;

class ImportPmjay extends Command
{
    protected $signature   = 'pmjay:import {path : Full path to JSON or CSV file}';
    protected $description = 'Import PMJAY treatment records from a JSON or CSV file';

    public function handle(PmjayImportService $service): int
    {
        $path = $this->argument('path');

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return 1;
        }

        $this->info("Starting import from: {$path}");
        $bar = $this->output->createProgressBar();
        $bar->start();

        $stats = $service->import($path);

        $bar->finish();
        $this->newLine();
        $this->table(
            ['Processed', 'Skipped', 'Errors'],
            [[$stats['processed'], $stats['skipped'], $stats['errors']]]
        );

        return 0;
    }
}
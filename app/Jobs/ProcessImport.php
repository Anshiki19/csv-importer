<?php

namespace App\Jobs;

use App\Models\Import;
use App\Services\CsvImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessImport implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Import $import,
        public string $path
    ) {
    }

    public function handle(CsvImportService $csvImportService): void
    {
        $this->import->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        try {
            $totalRows = $this->countDataRows();

            $this->import->update([
                'total_rows' => $totalRows,
            ]);

            $csvImportService->process(
                $this->import,
                $this->path
            );

            $this->import->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $this->import->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            Log::error('Customer import failed.', [
                'import_id' => $this->import->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function countDataRows(): int
    {
        $file = fopen(storage_path('app/private/' . $this->path), 'r');

        if ($file === false) {
            throw new \RuntimeException('Unable to open the CSV file.');
        }

        $count = 0;

        fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            if (count(array_filter(
                $row,
                fn ($value) => trim((string) $value) !== ''
            )) > 0) {
                $count++;
            }
        }

        fclose($file);

        return $count;
    }
}
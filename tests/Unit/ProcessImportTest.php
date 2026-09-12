<?php

namespace Tests\Unit;

use App\Jobs\ProcessImport;
use App\Models\CustomerDetail;
use App\Models\Import;
use App\Services\CsvImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessImportTest extends TestCase
{
    use RefreshDatabase;

    private function createCsv(string $filename, array $rows): string
    {
        $directory = storage_path('app/private/imports');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $path = $directory . '/' . $filename;

        file_put_contents($path, implode("\n", $rows));

        return 'imports/' . $filename;
    }

    public function test_import_job_processes_valid_csv_successfully(): void
    {
        $path = $this->createCsv('job-valid.csv', [
            'name,email,phone',
            'John Doe,john@example.com,9876543210',
        ]);

        $import = Import::create([
            'filename' => 'job-valid.csv',
            'status' => 'pending',
        ]);

        $job = new ProcessImport($import, $path);

        $job->handle(new CsvImportService());

        $import->refresh();

        $this->assertEquals('completed', $import->status);

        $this->assertNotNull($import->started_at);
        $this->assertNotNull($import->completed_at);

        $this->assertDatabaseHas('customer_details', [
            'import_id' => $import->id,
            'email' => 'john@example.com',
        ]);

        $this->assertEquals(1, $import->imported_rows);
        $this->assertEquals(1, $import->total_rows);
    }

    public function test_import_job_marks_import_as_failed_for_invalid_file(): void
    {
        $path = $this->createCsv('job-invalid-header.csv', [
            'name,email,phone,age',
            'John Doe,john@example.com,9876543210,30',
        ]);

        $import = Import::create([
            'filename' => 'job-invalid-header.csv',
            'status' => 'pending',
        ]);

        $job = new ProcessImport($import, $path);

        try {
            $job->handle(new CsvImportService());

            $this->fail('The import job should have failed.');
        } catch (\Throwable $e) {
            // Expected exception.
        }

        $import->refresh();

        $this->assertEquals('failed', $import->status);

        $this->assertNotNull($import->started_at);
        $this->assertNotNull($import->completed_at);

        $this->assertEquals(
            'The CSV file must contain exactly these columns in this order: name, email, phone.',
            $import->failure_reason
        );
    }

    public function test_import_job_does_not_import_rows_when_file_validation_fails(): void
    {
        $path = $this->createCsv('job-invalid-columns.csv', [
            'name,email,phone,age',
            'John Doe,john@example.com,9876543210,30',
        ]);

        $import = Import::create([
            'filename' => 'job-invalid-columns.csv',
            'status' => 'pending',
        ]);

        $job = new ProcessImport($import, $path);

        try {
            $job->handle(new CsvImportService());
        } catch (\Throwable $e) {
            // Expected exception.
        }

        $this->assertDatabaseMissing('customer_details', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_import_job_counts_multiple_data_rows(): void
    {
        $path = $this->createCsv('job-multiple.csv', [
            'name,email,phone',
            'John Doe,john@example.com,9876543210',
            'Jane Doe,jane@example.com,9876543211',
            'Mike Doe,mike@example.com,9876543212',
        ]);

        $import = Import::create([
            'filename' => 'job-multiple.csv',
            'status' => 'pending',
        ]);

        $job = new ProcessImport($import, $path);

        $job->handle(new CsvImportService());

        $import->refresh();

        $this->assertEquals('completed', $import->status);
        $this->assertEquals(3, $import->total_rows);
        $this->assertEquals(3, $import->imported_rows);

        $this->assertDatabaseCount('customer_details', 3);
    }
}
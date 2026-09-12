<?php

namespace Tests\Unit;

use App\Models\CustomerDetail;
use App\Models\Import;
use App\Services\CsvImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CsvImportServiceTest extends TestCase
{
    use RefreshDatabase;

    private CsvImportService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new CsvImportService();
    }

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

    public function test_valid_csv_row_is_imported(): void
    {
        $path = $this->createCsv('valid.csv', [
            'name,email,phone',
            'John Doe,john@example.com,9876543210',
        ]);

        $import = Import::create([
            'filename' => 'valid.csv',
            'status' => 'processing',
        ]);

        $this->service->process($import, $path);

        $this->assertDatabaseHas('customer_details', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '9876543210',
        ]);

        $this->assertDatabaseHas('imports', [
            'id' => $import->id,
            'imported_rows' => 1,
        ]);
    }

    public function test_invalid_email_is_rejected(): void
    {
        $path = $this->createCsv('invalid-email.csv', [
            'name,email,phone',
            'John Doe,invalid-email,9876543210',
        ]);

        $import = Import::create([
            'filename' => 'invalid-email.csv',
            'status' => 'processing',
        ]);

        $this->service->process($import, $path);

        $this->assertDatabaseMissing('customer_details', [
            'email' => 'invalid-email',
        ]);

        $this->assertDatabaseHas('import_errors', [
            'import_id' => $import->id,
            'error_type' => 'invalid',
        ]);

        $this->assertDatabaseHas('imports', [
            'id' => $import->id,
            'invalid_rows' => 1,
        ]);
    }

    public function test_invalid_phone_is_rejected(): void
    {
        $path = $this->createCsv('invalid-phone.csv', [
            'name,email,phone',
            'John Doe,john@example.com,98765',
        ]);

        $import = Import::create([
            'filename' => 'invalid-phone.csv',
            'status' => 'processing',
        ]);

        $this->service->process($import, $path);

        $this->assertDatabaseMissing('customer_details', [
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('imports', [
            'id' => $import->id,
            'invalid_rows' => 1,
        ]);

        $this->assertDatabaseHas('import_errors', [
            'import_id' => $import->id,
            'error_type' => 'invalid',
        ]);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        $existingImport = Import::create([
            'filename' => 'existing.csv',
            'status' => 'completed',
        ]);

        CustomerDetail::create([
            'import_id' => $existingImport->id,
            'name' => 'Existing User',
            'email' => 'john@example.com',
            'phone' => '9876543210',
        ]);

        $path = $this->createCsv('duplicate.csv', [
            'name,email,phone',
            'John Doe,john@example.com,9999999999',
        ]);

        $import = Import::create([
            'filename' => 'duplicate.csv',
            'status' => 'processing',
        ]);

        $this->service->process($import, $path);

        $this->assertDatabaseHas('imports', [
            'id' => $import->id,
            'duplicate_rows' => 1,
        ]);

        $this->assertDatabaseHas('import_errors', [
            'import_id' => $import->id,
            'error_type' => 'duplicate',
        ]);
    }

    public function test_missing_name_is_rejected(): void
    {
        $path = $this->createCsv('missing-name.csv', [
            'name,email,phone',
            ',john@example.com,9876543210',
        ]);

        $import = Import::create([
            'filename' => 'missing-name.csv',
            'status' => 'processing',
        ]);

        $this->service->process($import, $path);

        $this->assertDatabaseMissing('customer_details', [
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('import_errors', [
            'import_id' => $import->id,
            'error_type' => 'invalid',
        ]);

        $this->assertDatabaseHas('imports', [
            'id' => $import->id,
            'invalid_rows' => 1,
        ]);
    }

    public function test_missing_email_is_rejected(): void
    {
        $path = $this->createCsv('missing-email.csv', [
            'name,email,phone',
            'John Doe,,9876543210',
        ]);

        $import = Import::create([
            'filename' => 'missing-email.csv',
            'status' => 'processing',
        ]);

        $this->service->process($import, $path);

        $this->assertDatabaseMissing('customer_details', [
            'name' => 'John Doe',
        ]);

        $this->assertDatabaseHas('import_errors', [
            'import_id' => $import->id,
            'error_type' => 'invalid',
        ]);

        $this->assertDatabaseHas('imports', [
            'id' => $import->id,
            'invalid_rows' => 1,
        ]);
    }

    public function test_missing_phone_is_rejected(): void
    {
        $path = $this->createCsv('missing-phone.csv', [
            'name,email,phone',
            'John Doe,john@example.com,',
        ]);

        $import = Import::create([
            'filename' => 'missing-phone.csv',
            'status' => 'processing',
        ]);

        $this->service->process($import, $path);

        $this->assertDatabaseMissing('customer_details', [
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('import_errors', [
            'import_id' => $import->id,
            'error_type' => 'invalid',
        ]);

        $this->assertDatabaseHas('imports', [
            'id' => $import->id,
            'invalid_rows' => 1,
        ]);
    }

    public function test_csv_without_header_is_rejected(): void
    {
        $path = $this->createCsv('without-header.csv', [
            'John Doe,john@example.com,9876543210',
        ]);

        $import = Import::create([
            'filename' => 'without-header.csv',
            'status' => 'processing',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'The CSV file must contain exactly these columns in this order: name, email, phone.'
        );

        $this->service->process($import, $path);
    }

    public function test_csv_with_header_only_is_rejected(): void
    {
        $path = $this->createCsv('header-only.csv', [
            'name,email,phone',
        ]);

        $import = Import::create([
            'filename' => 'header-only.csv',
            'status' => 'processing',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'The CSV file must contain at least one customer row.'
        );

        $this->service->process($import, $path);
    }

    public function test_blank_csv_is_rejected(): void
    {
        $path = $this->createCsv('blank.csv', [
            '',
        ]);

        $import = Import::create([
            'filename' => 'blank.csv',
            'status' => 'processing',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'The CSV file cannot be empty.'
        );

        $this->service->process($import, $path);
    }

    public function test_csv_with_extra_column_is_rejected(): void
    {
        $path = $this->createCsv('extra-column.csv', [
            'name,email,phone,age',
            'John Doe,john@example.com,9876543210,30',
        ]);

        $import = Import::create([
            'filename' => 'extra-column.csv',
            'status' => 'processing',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'The CSV file must contain exactly these columns in this order: name, email, phone.'
        );

        $this->service->process($import, $path);
    }

    public function test_csv_with_wrong_column_order_is_rejected(): void
    {
        $path = $this->createCsv('wrong-order.csv', [
            'email,name,phone',
            'john@example.com,John Doe,9876543210',
        ]);

        $import = Import::create([
            'filename' => 'wrong-order.csv',
            'status' => 'processing',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'The CSV file must contain exactly these columns in this order: name, email, phone.'
        );

        $this->service->process($import, $path);
    }
}
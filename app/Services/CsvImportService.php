<?php

namespace App\Services;

use App\Models\Import;
use App\Models\ImportError;
use App\Models\CustomerDetail;
use RuntimeException;

class CsvImportService
{
    private const REQUIRED_HEADERS = [
        'name',
        'email',
        'phone',
    ];

    public function process(Import $import, string $path): void
    {
        $file = fopen(storage_path('app/private/' . $path), 'r');

        if ($file === false) {
            throw new RuntimeException('Unable to open the CSV file.');
        }

        try {
            $headers = fgetcsv($file);

            if ($headers === false || $this->isEmptyRow($headers)) {
                throw new RuntimeException('The CSV file cannot be empty.');
            }

            $headers = array_map(
                fn($header) => trim((string) $header),
                $headers
            );

            if ($headers !== self::REQUIRED_HEADERS) {
                throw new RuntimeException(
                    'The CSV file must contain exactly these columns in this order: name, email, phone.'
                );
            }

            $rowNumber = 1;
            $hasDataRows = false;

            while (($row = fgetcsv($file)) !== false) {
                $rowNumber++;

                if ($this->isEmptyRow($row)) {
                    continue;
                }

                $hasDataRows = true;

                $this->processRow($import, $rowNumber, $row);
            }

            if (!$hasDataRows) {
                throw new RuntimeException(
                    'The CSV file must contain at least one customer row.'
                );
            }
        } finally {
            fclose($file);
        }
    }

    private function processRow(
        Import $import,
        int $rowNumber,
        array $row
    ): void {
        $name = trim($row[0] ?? '');
        $email = trim($row[1] ?? '');
        $phone = trim($row[2] ?? '');

        if ($name === '' || $email === '' || $phone === '') {
            $this->recordError(
                $import,
                $rowNumber,
                'invalid',
                'Name, email and phone are required.',
                $row
            );

            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->recordError(
                $import,
                $rowNumber,
                'invalid',
                'The email address is invalid.',
                $row
            );

            return;
        }

        if (!preg_match('/^[0-9]{10}$/', $phone)) {
            $this->recordError(
                $import,
                $rowNumber,
                'invalid',
                'The phone number must contain exactly 10 digits.',
                $row
            );

            return;
        }

        if (CustomerDetail::where('email', $email)->exists()) {
            $this->recordError(
                $import,
                $rowNumber,
                'duplicate',
                'A customer with this email already exists.',
                $row
            );

            return;
        }

        CustomerDetail::create([
            'import_id' => $import->id,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ]);

        $import->increment('imported_rows');
    }

    private function recordError(
        Import $import,
        int $rowNumber,
        string $type,
        string $message,
        array $row
    ): void {
        ImportError::create([
            'import_id' => $import->id,
            'row_number' => $rowNumber,
            'error_type' => $type,
            'message' => $message,
            'row_data' => $row,
        ]);

        if ($type === 'invalid') {
            $import->increment('invalid_rows');
        }

        if ($type === 'duplicate') {
            $import->increment('duplicate_rows');
        }
    }

    private function isEmptyRow(array $row): bool
    {
        return count(array_filter(
            $row,
            fn($value) => trim((string) $value) !== ''
        )) === 0;
    }
}
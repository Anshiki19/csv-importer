# CSV Customer Importer

A Laravel-based feature for importing customer data from CSV files — built with a focus on validation, maintainability, error handling, and testability.


## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Setup](#setup)
- [CSV Format](#csv-format)
- [Validation Rules](#validation-rules)
- [Processing & Import States](#processing--import-states)
- [Testing](#testing)
- [Key Technical Decisions](#key-technical-decisions)
- [Assumptions](#assumptions)
- [Limitations & Possible Improvements](#limitations--possible-improvements)
- [AI Usage](#ai-usage)


## Features

- Upload and import customer data from CSV files
- Validates the CSV header and structure before processing
- Supports exactly three columns, in this order: `name`, `email`, `phone`
- Validates required fields, email format, and 10-digit phone numbers
- Detects duplicate customer emails
- Invalid rows are recorded as import errors while valid rows continue processing
- Blank rows are ignored
- Import status is tracked throughout processing
- Import processing is handled through a queued job
- Failed imports store a meaningful failure reason
- Displays imported customers and row-level import errors



## Requirements

| Requirement - Version 
| PHP - 8.2+ 
| Composer - Latest 
| Laravel - 12.1+


## Setup

1. Clone the repository and install dependencies

-------bash-------
git clone <repository-url>
cd csv-importer
composer install
-------------------

2. Create the environment file

-------bash-------
cp .env.example .env
------------------

3. Generate the application key

-------bash----------
php artisan key:generate
---------------------

4. Configure the MySQL database in `.env`:

-------env--------------
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csv_importer
DB_USERNAME=
DB_PASSWORD=
-------------------------

5. Create the database (via MySQL or phpMyAdmin), then run migrations:

-------bash--------------
php artisan migrate
--------------------------

6. Start the application

-------bash------------
php artisan serve
-----------------------

Open the app in your browser at [http://127.0.0.1:8000](http://127.0.0.1:8000)


## CSV Format

The importer expects the following exact header:

-------csv----------------
name,email,phone
--------------------------

Example:

-------csv----------------
name,email,phone
John Doe,john@example.com,9876543210
Jane Doe,jane@example.com,9876543211
-----------------------


## Validation Rules

- The CSV must contain a header
- The header must contain exactly `name,email,phone`, in that order
- At least one non-empty customer row is required
- `name`, `email`, and `phone` are all required fields
- `email` must be a valid email address
- `phone` must contain exactly 10 digits
- Customer `email` must not already exist in the database
- Blank rows are ignored
- Rows containing validation errors are recorded in the import errors table and do not prevent other valid rows from being imported


## Processing & Import States

Import processing is handled through a queued job, keeping the upload request fast and separate from CSV processing.

Standard flow:

-------------------------------
pending → processing → completed
-------------------------------

On an unrecoverable file-level error:

-------------------------------
pending → processing → failed
---------------------------------------

| Error type | Behavior |
| File-level error | Fails the entire import; failure reason is stored and displayed |
| Row-level error | Does not fail the import; the invalid row and reason are stored as an import error, and valid rows continue processing |



## Testing

The test suite runs against a separate MySQL database to avoid affecting application data.

1. Create a test database, e.g. `csv_importer_test`

2. Configure `.env.testing`:

-------env----------------
APP_ENV=testing
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csv_importer_test
DB_USERNAME=
DB_PASSWORD=
-----------------------

3. Run the tests:

-------bash------------
php artisan test
-----------------------

Coverage includes:

- Successful CSV import
- Invalid email
- Invalid phone number
- Missing name / email / phone
- Duplicate email
- Missing CSV header
- Empty CSV
- Header-only CSV
- Extra columns
- Incorrect column order
- Successful queued import processing
- Failed import handling
- Preventing data import when file-level validation fails
- Multiple-row import processing


## Key Technical Decisions

Service-based import processing
CSV parsing and row validation live in a dedicated `CsvImportService` rather than the controller, keeping the controller lightweight and the import logic easy to test and maintain.

Queued processing
The import runs as a Laravel queued job, decoupling the upload/request flow from potentially longer-running CSV processing — and laying groundwork for handling larger files.

Separate handling of file-level vs. row-level errors
Structural problems with the CSV (bad header, no rows) fail the entire import. Individual invalid rows are recorded as errors without blocking valid rows.

Import tracking
An `imports` record tracks status, row counts, timestamps, and failure reason, giving visibility into the outcome of each import.

Automated tests
Validation rules, edge cases, and job behavior are covered by automated tests using a dedicated MySQL test database.


## Assumptions

Where requirements were open-ended, the following assumptions were made:

1. CSV was chosen as the supported import format to keep the feature within the expected 2–3 hour scope.
2. Required customer columns are exactly `name`, `email`, and `phone`.
3. Column order is fixed to keep the input format predictable.
4. Phone numbers must contain exactly 10 digits.
5. Email addresses are treated as unique customer identifiers.
6. Blank rows are considered harmless and are skipped.
7. A row-level validation error should not prevent valid rows in the same file from being imported.
8. File-level structural errors (invalid header, no customer rows) fail the import, since the file can't be reliably processed.
9. Authentication and authorization are considered out of scope for this exercise.



## Limitations & Possible Improvements

With additional time, the following would be worth adding:

- Pagination for large customer and import-error lists
- CSV upload size limits and more explicit file-security validation
- Chunked/batched processing of very large CSV files to reduce memory and database usage
- More detailed import progress reporting
- Retry/failure handling and monitoring for queued jobs
- Authorization/authentication, if this were part of a larger production application
- Support for additional import formats (e.g. Excel)
- Feature tests covering the complete browser upload flow
- More granular validation/error reporting for malformed CSV structures



## AI Usage

AI coding tools were used as part of development, as permitted by the exercise instructions. All generated code and suggestions were reviewed, adapted to the application's requirements, manually validated, and covered with automated tests.
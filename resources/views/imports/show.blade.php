@extends('layouts.app')

@section('title', 'Import Details')

@section('content')

    <div>

        <div class="page-header">
            <h1>Import #{{ $import->id }}</h1>

            <p>
                {{ $import->filename }}
            </p>
        </div>

        @if (session('success'))
            <div class="info-box" style="margin-bottom: 24px;">
                <p class="info-box-text" style="margin: 0;">
                    {{ session('success') }}
                </p>
            </div>
        @endif

        <div style="margin-bottom: 24px;">
            <span class="status status-{{ $import->status }}">
                {{ ucfirst($import->status) }}
            </span>
        </div>

        <div class="summary-grid">

            <div class="summary-card">
                <div class="summary-label">
                    Total Rows
                </div>

                <div class="summary-value">
                    {{ $import->total_rows }}
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-label">
                    Imported
                </div>

                <div class="summary-value">
                    {{ $import->imported_rows }}
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-label">
                    Invalid
                </div>

                <div class="summary-value">
                    {{ $import->invalid_rows }}
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-label">
                    Duplicates
                </div>

                <div class="summary-value">
                    {{ $import->duplicate_rows }}
                </div>
            </div>

        </div>

        <div class="card section">

            <h2 class="section-title">
                Successfully Imported Customers
            </h2>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($import->customers as $customer)

                            <tr>
                                <td>
                                    {{ $customer->name }}
                                </td>

                                <td>
                                    {{ $customer->email }}
                                </td>

                                <td>
                                    {{ $customer->phone }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="3" style="text-align: center; padding: 30px;">
                                    No customers were imported successfully.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <div class="card section">

            <h2 class="section-title">
                {{ $import->invalid_rows  + $import->duplicate_rows }} rows need attention
            </h2>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>Row No</th>
                            <th>Type</th>
                            <th>Message</th>
                            <th>Data</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($import->errors as $error)

                            <tr>
                                <td>
                                    {{ $error->row_number }}
                                </td>

                                <td>
                                    {{ ucfirst($error->error_type) }}
                                </td>

                                <td>
                                    {{ $error->message }}
                                </td>

                                <td>
                                    <strong>Name:</strong> {{ $error->row_data[0] ?? '-' }}<br>
                                    <strong>Email:</strong> {{ $error->row_data[1] ?? '-' }}<br>
                                    <strong>Phone:</strong> {{ $error->row_data[2] ?? '-' }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" style="text-align: center; padding: 30px;">
                                    No errors found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <div style="margin-top: 24px;">
            <a href="{{ route('imports.index') }}" class="button">
                Back to Import History
            </a>
        </div>

    </div>



@endsection
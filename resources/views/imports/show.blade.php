@extends('layouts.app')

@section('title', 'Import Summary')

@section('content')

<div>

    <div class="page-header">
        <h1>Import #1</h1>

        <p>
            Customer import summary
        </p>
    </div>

    <div class="card" style="margin-bottom: 24px;">

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong>customers.csv</strong>

                <div class="help-text">
                    Import completed successfully.
                </div>
            </div>

            <span class="status">
                Completed
            </span>
        </div>

    </div>

    <div class="summary-grid">

        <div class="summary-card">
            <div class="summary-label">
                Total Rows
            </div>

            <div class="summary-value">
                100
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">
                Imported
            </div>

            <div class="summary-value">
                82
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">
                Invalid
            </div>

            <div class="summary-value">
                10
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">
                Duplicates
            </div>

            <div class="summary-value">
                8
            </div>
        </div>

    </div>

    <div class="section">

        <h2 class="section-title">
            Import Errors
        </h2>

        <div class="card">

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>Row</th>
                            <th>Type</th>
                            <th>Message</th>
                            <th>Data</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>5</td>
                            <td>Invalid</td>
                            <td>Invalid email address</td>
                            <td>Invalid email address</td>
                        </tr>

                        <tr>
                            <td>12</td>
                            <td>Duplicate</td>
                            <td>Email already exists</td>
                            <td>Invalid email address</td>
                        </tr>

                        <tr>
                            <td>18</td>
                            <td>Invalid</td>
                            <td>Phone number is required</td>
                            <td>Invalid email address</td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <div class="section">

        <h2 class="section-title">
            Webhook Delivery
        </h2>

        <div class="card">

            <div class="webhook-info">

                <div class="webhook-item">
                    <div class="webhook-label">
                        Event
                    </div>

                    <div class="webhook-value">
                        import.completed
                    </div>
                </div>

                <div class="webhook-item">
                    <div class="webhook-label">
                        Status
                    </div>

                    <div class="webhook-value">
                        Delivered
                    </div>
                </div>

                <div class="webhook-item">
                    <div class="webhook-label">
                        Attempts
                    </div>

                    <div class="webhook-value">
                        1
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection
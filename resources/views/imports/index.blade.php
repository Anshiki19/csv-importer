@extends('layouts.app')

@section('title', 'Import History')

@section('content')

    <div>


        <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Import History</h1>

                <p>
                    View all customer CSV imports and their processing results.
                </p>
            </div>

            <a href="{{ route('imports.create') }}" class="button">
                Upload CSV
            </a>
        </div>

        <div class="card">

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>File Name</th>
                            <th>Status</th>
                            <th>Failure Reason</th>
                            <th>Total</th>
                            <th>Imported</th>
                            <th>Invalid</th>
                            <th>Duplicates</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($imports as $import)

                            <tr>
                                <td>{{ $import->id }}</td>

                                <td>{{ $import->filename }}</td>

                                <td>
                                    <span class="status status-{{ $import->status }}">
                                        {{ ucfirst($import->status) }}
                                    </span>
                                </td>
                                <td>{{ $import->failure_reason ?? '-' }}</td>

                                <td>{{ $import->total_rows }}</td>

                                <td>{{ $import->imported_rows }}</td>

                                <td>{{ $import->invalid_rows }}</td>

                                <td>{{ $import->duplicate_rows }}</td>

                                <td>
                                    {{ $import->created_at->format('d M Y, h:i A') }}
                                </td>

                                <td>
                                @if ($import->status === 'completed')
                                   <a href="{{ route('imports.show', $import) }}">
                                        View
                                    </a>
                                @else
                                      <span style="color: #999; cursor: not-allowed;">
                                        View
                                    </span>
                                @endif
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="9" style="text-align: center; padding: 40px;">
                                    No imports found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection
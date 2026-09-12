@extends('layouts.app')

@section('title', 'Import History')

@section('content')

<div>


<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1>Import History</h1>

        <p>
            View and manage your customer CSV imports.
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
                    <th>Total</th>
                    <th>Imported</th>
                    <th>Invalid</th>
                    <th>Duplicates</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>3</td>
                    <td>customers3.csv</td>
                    <td>
                        <span class="status">
                            Completed
                        </span>
                    </td>
                    <td>100</td>
                    <td>82</td>
                    <td>10</td>
                    <td>8</td>
                    <td>12 Sep 2026</td>
                    <td>
                        <a href="{{ route('imports.show', 3) }}">
                            View
                        </a>
                    </td>
                </tr>

                <tr>
                    <td>2</td>
                    <td>customers2.csv</td>
                    <td>
                        <span class="status status-processing">
                            Processing
                        </span>
                    </td>
                    <td>500</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>12 Sep 2026</td>
                    <td>
                        <a href="{{ route('imports.show', 2) }}">
                            View
                        </a>
                    </td>
                </tr>

                <tr>
                    <td>1</td>
                    <td>customers.csv</td>
                    <td>
                        <span class="status">
                            Completed
                        </span>
                    </td>
                    <td>50</td>
                    <td>45</td>
                    <td>3</td>
                    <td>2</td>
                    <td>11 Sep 2026</td>
                    <td>
                        <a href="{{ route('imports.show', 1) }}">
                            View
                        </a>
                    </td>
                </tr>

            </tbody>

        </table>

    </div>

</div>


</div>

@endsection

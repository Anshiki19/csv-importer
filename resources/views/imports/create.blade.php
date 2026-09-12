@extends('layouts.app')

@section('title', 'Import Customers')

@section('content')

<div style="max-width: 700px; margin: 0 auto;">

    <div class="page-header">
        <h1>Import Customers</h1>

        <p>
            Upload a CSV to create customers in bulk. You'll get a summary once processing finishes.
        </p>
    </div>

    <div class="card">

        <form action="#" method="POST" enctype="multipart/form-data">

            @csrf

            <div class="form-group">
                <label for="file" class="form-label">
                    CSV File
                </label>

                <input
                    id="file"
                    name="file"
                    type="file"
                    accept=".csv,text/csv"
                    class="file-input"
                >

                <p class="help-text">
                    Maximum file size: 5 MB
                </p>
            </div>

            <div class="info-box">
                <p class="info-box-title">
                    Required columns : Name, Email, Phone No
                </p>

                <p class="info-box-text">
                    Rows missing these, or with an invalid email, will be skipped and listed as errors — the rest of the file still imports.
                </p>
            </div>

            <div style="margin-top: 24px;">
                <button type="submit" class="button">
                    Upload & Import
                </button>
            </div>

        </form>

    </div>

</div>

@endsection
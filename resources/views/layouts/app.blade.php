<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Customer Importer')</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>

    <header class="header">
        <div class="container" style="display: flex; justify-content: space-between;">
            <a href="{{ route('imports.create') }}" class="brand">
                Customer Importer
            </a>
            <a href="{{ route('imports.index') }}">Show Imports</a>
        </div>
    </header>

    <main class="container page-content">
        @yield('content')
    </main>

</body>
</html>
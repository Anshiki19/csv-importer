<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('imports.index');
});

Route::get('/imports', function () { return view('imports.index'); })->name('imports.index');
Route::get('/imports/create', function () { return view('imports.create'); })->name('imports.create');

Route::get('/imports/{import}', function ($import) {
    return view('imports.show', [
        'import' => $import,
    ]);  })->name('imports.show');
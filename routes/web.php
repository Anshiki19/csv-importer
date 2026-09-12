<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;

Route::get('/', function () {
    return redirect()->route('imports.index');
});

// Route::get('/imports', function () { return view('imports.index'); })->name('imports.index');
Route::get('/imports', [ImportController::class, 'index'])->name('imports.index');
Route::post('/store', [ImportController::class, 'store'])->name('imports.store');
Route::get('/imports/create', function () { return view('imports.create'); })->name('imports.create');
Route::get('/imports/{import}', [ImportController::class, 'show'])->name('imports.show');
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImportRequest;
use App\Models\Import;
use App\Jobs\ProcessImport;

class ImportController extends Controller
{
    public function index()
    {
        $imports = Import::latest()->get();

        return view('imports.index', compact('imports'));
    }

    public function store(StoreImportRequest $request)
    {
        $file = $request->file('file');

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        $filename = $originalName . '_' . now()->format('Y-m-d_H-i-s') . '.' . $extension;

        $path = $file->storeAs('imports', $filename);

        $import = Import::create([
            'filename' => $filename,
            'status' => 'pending',
        ]);

        ProcessImport::dispatch($import, $path);

        return redirect()
            ->route('imports.show', $import)
            ->with('success', 'CSV uploaded successfully.');
    }


    public function show(Import $import)
{
    $import->load('errors');

    return view('imports.show', compact('import'));
}
}


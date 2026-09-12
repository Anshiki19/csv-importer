<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'extensions:csv,xls,xlsx',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please select a file.',
            'file.file' => 'The uploaded file is invalid.',
            'file.extensions' => 'The file must be a CSV or Excel file.',
            'file.max' => 'The file must not be larger than 5 MB.',
        ];
    }

}
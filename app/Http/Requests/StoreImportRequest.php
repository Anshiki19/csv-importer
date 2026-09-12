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
                'extensions:csv',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please select a CSV file.',
            'file.file' => 'The uploaded file is invalid.',
            'file.extensions' => 'The file must be a CSV file.',
            'file.max' => 'The CSV file must not be larger than 5 MB.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $file = $this->file('file');

            if ($file && $file->getSize() === 0) {
                $validator->errors()->add(
                    'file',
                    'The CSV file cannot be empty.'
                );
            }
        });
    }

}
<?php

namespace App\Http\Requests\master_data;

use Illuminate\Foundation\Http\FormRequest;

class golonganRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'golongan' => 'required',
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'golongan.required' => 'Kolom golongan tidak boleh kosong.',
            'status.required' => 'Kolom status tidak boleh kosong.',
        ];
    }
}

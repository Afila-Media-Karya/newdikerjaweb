<?php

namespace App\Http\Requests\master_data;

use Illuminate\Foundation\Http\FormRequest;

class pendidikanRequest extends FormRequest
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
            'pendidikan' => 'required',
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'pendidikan.required' => 'Kolom pendidikan tidak boleh kosong.',
            'status.required' => 'Kolom status tidak boleh kosong.',
        ];
    }
}

<?php

namespace App\Http\Requests\master_data;

use Illuminate\Foundation\Http\FormRequest;

class agamaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'agama' => 'required',
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'agama.required' => 'Kolom agama tidak boleh kosong.',
            'status.required' => 'Kolom status tidak boleh kosong.',
        ];
    }
}

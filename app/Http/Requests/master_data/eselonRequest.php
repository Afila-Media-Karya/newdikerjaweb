<?php

namespace App\Http\Requests\master_data;

use Illuminate\Foundation\Http\FormRequest;

class eselonRequest extends FormRequest
{
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
            'eselon' => 'required',
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'eselon.required' => 'Kolom eselon tidak boleh kosong.',
            'status.required' => 'Kolom status tidak boleh kosong.',
        ];
    }
}

<?php

namespace App\Http\Requests\master_data;

use Illuminate\Foundation\Http\FormRequest;

class satuanRequest extends FormRequest
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
            'satuan' => 'required',
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'satuan.required' => 'Kolom satuan tidak boleh kosong.',
            'status.required' => 'Kolom status tidak boleh kosong.',
        ];
    }
}

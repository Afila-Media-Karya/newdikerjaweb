<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class perangkatdaerahRequest extends FormRequest
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
            'nama_satuan_kerja' => 'required',
            'inisial_satuan_kerja' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'nama_satuan_kerja.required' => 'kolom nama satuan kerja tidak boleh kosong.',
            'inisial_satuan_kerja.required' => 'kolom inisial satuan kerja tidak boleh kosong.',
        ]; 
    }
}

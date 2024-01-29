<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class pegawaipensiunRequest extends FormRequest
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
                'id_pegawai' => 'required',
                'id_satuan_kerja' => 'required',
                'tmt' => 'required',
            ];
    }

    public function messages()
    {
           return [
                'id_pegawai.required' => 'kolom pegawai tidak boleh kosong.',
                'id_satuan_kerja.required' => 'kolom satuan kerja tidak boleh kosong.',
                'tmt.required' => 'kolom tmt pensiun tidak boleh kosong.',
            ]; 
        
    }
}

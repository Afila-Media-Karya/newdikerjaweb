<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class pegawaikeluarRequest extends FormRequest
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
                'tujuan_daerah' => 'required',
                'id_satuan_kerja' => 'required',
                'id_pegawai' => 'required',
                'tmt' => 'required',
            ];
    }

    public function messages()
    {
           return [
                'id_satuan_kerja.required' => 'kolom satuab kerja tidak boleh kosong.',
                'id_pegawai.required' => 'kolom pegawai tidak boleh kosong.',
                'tmt.required' => 'kolom tmt mutasi keluar tidak boleh kosong.',
            ]; 
        
    }
}

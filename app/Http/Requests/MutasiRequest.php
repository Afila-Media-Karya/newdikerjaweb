<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MutasiRequest extends FormRequest
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
                'id_satuan_kerja' => 'required',
                'id_satuan_kerja_baru' => 'required',
                'id_pegawai' => 'required',
                'tmt' => 'required',
                'id_jabatan_baru' => 'required'
            ];
    }

    public function messages()
    {
           return [
                'id_satuan_kerja.required' => 'kolom satuan kerja tidak boleh kosong.',
                'id_satuan_kerja_baru.required' => 'kolom satuan kerja baru tidak boleh kosong.',
                'id_pegawai.required' => 'kolom pegawai tidak boleh kosong.',
                'id_jabatan_baru.required' => 'kolom jabatan baru tidak boleh kosong.',
                'tmt.required' => 'kolom tmt mutasi keluar tidak boleh kosong.',
            ]; 
        
    }
}

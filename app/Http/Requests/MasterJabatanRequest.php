<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MasterJabatanRequest extends FormRequest
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
                'nama_struktur' => 'required',
                'nama_jabatan' => 'required',
                'jenis_jabatan' => 'required',
                'pagu_tpp' => 'required',
                'id_satuan_kerja' => 'required',
                'id_kelompok_jabatan' => 'nullable',
            ];
    }

    public function messages()
    {
           return [
                'nama_struktur.required' => 'kolom nama struktur tidak boleh kosong.',
                'nama_jabatan.required' => 'kolom nama jabatan tidak boleh kosong.',
                'jenis_jabatan.required' => 'kolom jenis jabatan tidak boleh kosong.',
                'pagu_tpp.required' => 'kolom pagu tpp tidak boleh kosong.',
                'id_satuan_kerja.required' => 'kolom satuan kerja tidak boleh kosong.',
                'id_kelompok_jabatan.required' => 'kolom kelompok jabatan tidak boleh kosong.',
            ]; 
        
    }
}

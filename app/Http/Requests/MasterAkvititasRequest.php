<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MasterAkvititasRequest extends FormRequest
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
                'aktivitas' => 'required',
                'satuan' => 'required',
                'waktu' => 'required|numeric',
                'jenis' => 'required',
                'id_kelompok_jabatan' => 'required',
            ];
    }

    public function messages()
    {
           return [
                'aktivitas.required' => 'kolom aktivitas tidak boleh kosong.',
                'satuan.required' => 'kolom satuan tidak boleh kosong.',
                'waktu.required' => 'kolom waktu tidak boleh kosong.',
                'waktu.numeric' => 'kolom waktu harus berupa angka.',
                'jenis.required' => 'kolom jenis tidak boleh kosong.',
                'id_kelompok_jabatan.required' => 'kolom kelompok jabatan tidak boleh kosong.',
            ]; 
        
    }
}

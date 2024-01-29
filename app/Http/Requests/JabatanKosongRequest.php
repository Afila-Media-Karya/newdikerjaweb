<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JabatanKosongRequest extends FormRequest
{
     public function authorize(): bool
    {
        return true;
    }

   public function rules(): array
    {
            return [
                'id_pegawai' => 'required',
                'status_jabatan' => 'required',
            ];
    }

    public function messages()
    {
           return [
                'id_pegawai.required' => 'kolom pegawai tidak boleh kosong.',
                'status_jabatan.required' => 'kolom status tidak boleh kosong.',
            ]; 
        
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LokasiRequest extends FormRequest
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
                'id_unit_kerja' => 'required',
                'nama_lokasi' => 'required',
                'longitude' => 'required',
                'latitude' => 'required',
                'radius' => 'required',
            ];
    }

    public function messages()
    {
           return [
                'id_satuan_kerja.required' => 'kolom satuan kerja tidak boleh kosong.',
                'id_unit_kerja.required' => 'kolom unit kerja tidak boleh kosong.',
                'nama_lokasi.required' => 'kolom nama lokasi tidak boleh kosong.',
                'long.required' => 'kolom longitude tidak boleh kosong.',
                'lat.required' => 'kolom lattitude tidak boleh kosong.',
                'radius.required' => 'kolom radius tidak boleh kosong.',
            ]; 
        
    }
}

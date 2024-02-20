<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AktivitasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'tanggal' => 'required',
            'id_sasaran' => 'required',
            'aktivitas' => 'required',
            'keterangan' => 'required',
            'volume' => 'required',
        ];
    }

    public function messages()
    {
           return [
                'tanggal.required' => 'kolom tanggal tidak boleh kosong.',
                'id_sasaran.required' => 'kolom sasaran kinerja tidak boleh kosong.',
                'aktivitas.required' => 'kolom aktivitas tidak boleh kosong.',
                'keterangan.required' => 'kolom keterangan tidak boleh kosong.',
                'volume.required' => 'kolom hasil tidak boleh kosong.',
            ]; 
    }
}

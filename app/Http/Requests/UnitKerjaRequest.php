<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitKerjaRequest extends FormRequest
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
            'nama_unit_kerja' => 'required',
            'waktu_masuk' => 'required',
            'waktu_keluar' => 'required',
            'waktu_apel' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'id_satuan_kerja.required' => 'kolom satuan kerja tidak boleh kosong.',
            'nama_unit_kerja.required' => 'kolom nama unit kerja tidak boleh kosong.',
            'waktu_masuk.required' => 'kolom waktu masuk tidak boleh kosong.',
            'waktu_keluar.required' => 'kolom waktu keluar tidak boleh kosong.',
            'waktu_apel.required' => 'kolom waktu apel tidak boleh kosong.',

        ]; 
    }
}

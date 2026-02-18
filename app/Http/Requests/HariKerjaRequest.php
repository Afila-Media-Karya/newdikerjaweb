<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HariKerjaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipe_pegawai' => 'required|string',
            'hari' => 'required|integer|min:1|max:7',
            'is_hari_kerja' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'tipe_pegawai.required' => 'Tipe pegawai harus diisi',
            'hari.required' => 'Hari harus diisi',
            'is_hari_kerja.required' => 'Status hari kerja harus diisi',
        ];
    }
}

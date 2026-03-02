<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JamKerjaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipe_pegawai' => 'required|string',
            'kategori' => 'required|string|in:reguler,ramadan',
            'shift' => 'nullable|string|in:pagi,siang,malam',
            'jumlah_shift' => 'nullable|integer|in:2,3',
            'hari' => 'required|integer|min:1|max:7',
            'jam_masuk' => 'required',
            'jam_keluar' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'tipe_pegawai.required' => 'Tipe pegawai harus diisi',
            'kategori.required' => 'Kategori harus diisi',
            'hari.required' => 'Hari harus diisi',
            'jam_masuk.required' => 'Jam masuk harus diisi',
            'jam_keluar.required' => 'Jam keluar harus diisi',
        ];
    }
}

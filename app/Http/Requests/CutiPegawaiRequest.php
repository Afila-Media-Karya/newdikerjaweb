<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CutiPegawaiRequest extends FormRequest
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
            'jenis_layanan' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_akhir' => 'required',
            'alamat' => 'required',
            'alasan' => 'required',
            'dokumen' => 'required|file|max:500|mimes:pdf'
        ];
    }

    public function messages()
    {
           return [
                'jenis_layanan.required' => 'kolom jenis layanan tidak boleh kosong.',
                'tanggal_mulai.required' => 'kolom tanggal mulai tidak boleh kosong.',
                'tanggal_akhir.required' => 'kolom tanggal selesai tidak boleh kosong.',
                'alamat.required' => 'kolom alamat tidak boleh kosong.',
                'alasan.required' => 'kolom alasan tidak boleh kosong.',
                'dokumen.required' => 'kolom dokumen tidak boleh kosong.',
                'dokumen.file' => 'kolom dokumen harus berupa file.',
                'dokumen.max' => 'kolom dokumen harus maksimal 500 kb.',
                'dokumen.mimes' => 'kolom dokumen harus ekstensi pdf.',
            ]; 
    }
}

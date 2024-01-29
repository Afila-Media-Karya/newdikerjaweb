<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LayananGeneralRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $request = request();

        if (!isset($request->uuid)) {
            return [
                'dokumen' => 'required|mimes:pdf|max:2048',
                'dokumen_pendukung' => 'required|mimes:pdf|max:2048',
                'id_pegawai' => 'required',
                'id_satuan_kerja' => 'required',
                'jenis_layanan' => 'required',
                'keterangan' => 'required',
                'status_general' => 'required',
            ];
        }else{
             return [
                'dokumen' => 'nullable|mimes:pdf|max:2048',
                'dokumen_pendukung' => 'nullable|mimes:pdf|max:2048',
                'id_pegawai' => 'required',
                'id_satuan_kerja' => 'required',
                'jenis_layanan' => 'required',
                'keterangan' => 'required',
                'status_general' => 'required',
            ];
        }            
    }

    public function messages()
    {
           return [
                'dokumen.required' => 'kolom dokumen tidak boleh kosong.',
                'dokumen.mimes' => 'kolom dokumen berekstensi pdf',
                'dokumen_pendukung.required' => 'kolom dokumen pendukung tidak boleh kosong.',
                'dokumen_pendukung.mimes' => 'kolom dokumen pendukung berekstensi pdf',
                'id_pegawai.required' => 'kolom pegawai tidak boleh kosong.',
                'id_satuan_kerja.required' => 'kolom satuan kerja tidak boleh kosong.',
                'jenis_layanan.required' => 'kolom jenis layanan tidak boleh kosong.',
                'keterangan.required' => 'kolom keterangan tidak boleh kosong.',
                'status_general.required' => 'kolom status tidak boleh kosong.',
            ]; 
        
    }
}

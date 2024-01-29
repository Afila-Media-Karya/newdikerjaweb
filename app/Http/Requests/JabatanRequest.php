<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JabatanRequest extends FormRequest
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
        $request = request();

        // if ($request->type == 'administrator') {
           if (!isset($request->uuid)) {
                return [
                    'id_pegawai' => 'required',
                    'id_lokasi_kerja' => 'required',
                    'id_lokasi_apel' => 'required',
                    'id_master_jabatan' => 'required',
                    'id_satuan_kerja' => 'required',
                    'id_unit_kerja' => 'required',
                    'id_parent' => 'required',
                    'status' => 'required',
                    'pembayaran' => 'required',
                ];
            }else{
                return [
                    'id_pegawai' => 'required',
                    'id_parent' => 'nullable',
                    'status' => 'required',
                    'pembayaran' => 'required',
                ];
            }
        // }
            
        // if ($request->type == 'web') {
        //     return [
        //         'id_pegawai' => 'required',
        //         'id_parent' => 'required',
        //         'status' => 'required',
        //         'pembayaran' => 'required',
        //     ];
        // }
            
    }

    public function messages()
    {
           return [
                'id_lokasi_kerja.required' => 'kolom lokasi kerja tidak boleh kosong.',
                'id_lokasi_apel.required' => 'kolom lokasi apel tidak boleh kosong.',
                'id_pegawai.required' => 'kolom pegawai tidak boleh kosong.',
                'id_master_jabatan.required' => 'kolom jabatan tidak boleh kosong.',
                'id_satuan_kerja.required' => 'kolom satuan kerja tidak boleh kosong.',
                'id_unit_kerja.required' => 'kolom unit kerja tidak boleh kosong.',
                'id_parent.required' => 'kolom atasan langsung tidak boleh kosong.',
                'status.required' => 'kolom status tidak boleh kosong.',
                'pembayaran.required' => 'kolom pembayaran tidak boleh kosong.',
            ]; 
        
    }
}

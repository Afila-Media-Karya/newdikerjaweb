<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AbsenRequest extends FormRequest
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

        if ($request->tipe_pegawai == 'tenaga_kesehatan') {
            return [
                'id_satuan_kerja' => 'required',
                'tanggal_absen' => 'required',
                'waktu_masuk' => 'required|date_format:H:i',
                'waktu_keluar' => 'nullable|date_format:H:i',
                'status' => 'required',
                'validation' => 'nullable',
                'shift' => 'required'
            ];
        }else{
            return [
                'id_satuan_kerja' => 'required',
                'tanggal_absen' => 'required',
                'waktu_masuk' => 'required|date_format:H:i',
                'waktu_keluar' => 'nullable|date_format:H:i|after:waktu_masuk',
                'status' => 'required',
                'validation' => 'nullable'
            ];
        }
            
    }

    public function messages()
    {
           return [
                'id_satuan_kerja.required' => 'kolom satuan kerja tidak boleh kosong.',
                'tanggal_absen.required' => 'kolom tanggal absen tidak boleh kosong.',
                'waktu_masuk.required' => 'kolom waktu masuk tidak boleh kosong.',
                'waktu_keluar.after' => 'Waktu keluar harus lebih besar dari waktu masuk.',
                'status.required' => 'kolom status tidak boleh kosong.',
                'shift.required' => 'kolom shift tidak boleh kosong.',
            ]; 
        
    }
}

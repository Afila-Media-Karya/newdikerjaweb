<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HariLiburRequest extends FormRequest
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
                'nama_libur' => 'required',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            ];
    }

    public function messages()
    {
           return [
                'nama_libur.required' => 'kolom jenis libur tidak boleh kosong.',
                'tanggal_mulai.required' => 'kolom tanggal mulai tidak boleh kosong.',
                'tanggal_selesai.required' => 'kolom tanggal selesai tidak boleh kosong.',
                'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setidaknya sama dengan tanggal mulai.',
            ]; 
        
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AbsenUpdateRequest extends FormRequest
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
                'waktu_masuk' => 'required',
                'waktu_keluar' => 'nullable',
                'status' => 'required',
                'validation' => 'nullable'
            ];
    }

    public function messages()
    {
           return [
                'waktu_masuk.required' => 'kolom waktu masuk tidak boleh kosong.',
                'waktu_keluar.after' => 'Waktu keluar harus lebih besar dari waktu masuk.',
                'status.required' => 'kolom status tidak boleh kosong.',
            ]; 
        
    }
}

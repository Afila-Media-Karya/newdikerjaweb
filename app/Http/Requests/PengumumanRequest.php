<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengumumanRequest extends FormRequest
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
            'judul' => 'required',
            'deskripsi' => 'required',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tanggal' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'judul.required' => 'kolom judul tidak boleh kosong.',
            'tanggal.required' => 'kolom judul tidak boleh kosong.',
            'deskripsi.required' => 'kolom deskripsi tidak boleh kosong.',
            // 'gambar.required' => 'kolom gambar tidak boleh kosong.',
            'gambar.image' => 'Kolom gambar harus berupa file gambar.',
            'gambar.mimes' => 'Kolom gambar harus memiliki format JPEG, PNG, JPG, atau GIF.',
            'gambar.max' => 'Ukuran file gambar tidak boleh lebih dari 2MB.',
        ]; 
    }
}

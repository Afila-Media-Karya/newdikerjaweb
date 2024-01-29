<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LayananRequest extends FormRequest
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
                'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048|max_dimensions:32,32',
                'nama' => 'required',
                'url' => 'required',
                'status' => 'required',
            ];
    }

    public function messages()
    {
           return [
                'gambar.max_dimensions' => 'gambar harus berdimensi 32 * 32 pixels',
                'gambar.required' => 'kolom gambar tidak boleh kosong.',
                'gambar.image' => 'kolom gambar harus berupa file gambar.',
                'gambar.mimes' => 'kolom gambar berekstensi jpeg,png,jpg',
                'gambar.max' => 'kolom gambar maksimal 2048 kb',
                'nama.required' => 'kolom nama layanan tidak boleh kosong.',
                'url.required' => 'kolom url tidak boleh kosong.',
                'status.required' => 'kolom status tidak boleh kosong.',
            ]; 
        
    }
}

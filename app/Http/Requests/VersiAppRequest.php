<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VersiAppRequest extends FormRequest
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
                'version' => 'required',
                'status' => 'required'
            ];
    }

    public function messages()
    {
           return [
                'version.required' => 'kolom versi tidak boleh kosong.',
                'status.required' => 'kolom status tidak boleh kosong.'
            ]; 
        
    }

}

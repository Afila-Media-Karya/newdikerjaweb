<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AkunRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'password_lama' => 'required',
            'password_baru' => 'required|min:8',
            'password_baru_ulang' => 'required|same:password_baru',
        ];
    }

    public function messages()
    {
           return [
                'password_lama.required' => 'kolom password lama tidak boleh kosong.',
                'password_baru.required' => 'kolom password baru tidak boleh kosong.',
                'password_baru_ulang.required' => 'kolom password baru ulang tidak boleh kosong.',
                'password_baru_ulang.same' => 'kolom password baru ulang harus sama dengan password baru.',
            ]; 
    }
}

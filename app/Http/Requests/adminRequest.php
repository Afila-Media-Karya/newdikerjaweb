<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class adminRequest extends FormRequest
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
            $uuid = $request->uuid;
            return [
                'username' => 'required',
                'role' => 'required',
                'password' =>  !isset($uuid) ? 'required|min:8|confirmed' : 'nullable|min:8|confirmed',
                'status_user' => 'required',
            ];
    }

    public function messages()
    {
           return [
                'username.required' => 'kolom username tidak boleh kosong.',
                'password.required' => 'Kolom Password harus diisi.',
                'password.min' => 'Kolom Password harus terdiri dari minimal 8 karakter.',
                'password.confirmed' => 'Password dan Ulang Password harus sesuai.',
                'role.required' => 'kolom role tidak boleh kosong.',
                'status_user.required' => 'kolom status tidak boleh kosong.',
            ]; 
        
    }
}

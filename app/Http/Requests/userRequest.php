<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class userRequest extends FormRequest
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
                'username' => 'required|unique:users,username',
                'role' => 'required',
                'password' => 'required|min:8|confirmed',
                'id_pegawai' => 'required|unique:users,id_pegawai',
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
                'id_pegawai.required' => 'kolom pegawai tidak boleh kosong.',
                'id_pegawai.unique' => 'Pegawai sudah mempunyai akun.',
                'username.unique' => 'username sudah digunakan.',
                'status_user.required' => 'kolom status tidak boleh kosong.',
            ]; 
        
    }
}

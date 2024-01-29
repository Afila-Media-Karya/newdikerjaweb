<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use DB;
class userUpdateRequest extends FormRequest
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
       
        $rules = [
            'username' => 'required',
            'role' => 'required',
            'password' => 'nullable|min:8|confirmed',
            'status_user' => 'required',
        ];

        // Ambil nilai id_pegawai sebelumnya dari database
        $previousIdPegawai = DB::table('users')->where('uuid',$request->uuid)->first()->id_pegawai;

        // Cek apakah id_pegawai saat ini sama dengan yang sebelumnya
        if ($this->has('id_pegawai') && $this->input('id_pegawai') != $previousIdPegawai) {

            $rules['id_pegawai'] = 'required|unique:users,id_pegawai';
        } else {
            // Jika sama, hilangkan validasi unik
            $rules['id_pegawai'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
           return [
                'username.required' => 'kolom username tidak boleh kosong.',
                'password.min' => 'Kolom Password harus terdiri dari minimal 8 karakter.',
                'password.confirmed' => 'Password dan Ulang Password harus sesuai.',
                'role.required' => 'kolom role tidak boleh kosong.',
                'id_pegawai.required' => 'kolom pegawai tidak boleh kosong.',
                 'id_pegawai.unique' => 'Pegawai sudah mempunyai akun.',
                'status_user.required' => 'kolom status tidak boleh kosong.',
            ]; 
        
    }
}

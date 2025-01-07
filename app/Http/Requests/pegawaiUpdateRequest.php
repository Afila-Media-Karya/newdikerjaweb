<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class pegawaiUpdateRequest extends FormRequest
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
                'nama' => 'required',
                'nip' => 'required||numeric|digits:18',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required',
                'agama' => 'required',
                'status_perkawinan' => 'required',
                'tmt_pegawai' => 'required|date',
                'golongan' => 'required',
                'tmt_golongan' => 'required|date',
                'tmt_jabatan' => 'required|date',
                'pendidikan' => 'required',
                'pendidikan_lulus' => 'required|date',
                'pendidikan_struktural' => 'required',
                'pendidikan_struktural_lulus' => 'required|date',
                'status_kepegawaian' => 'nullable',
                'tipe_pegawai' => 'nullable',
            ];
    }

    public function messages()
    {
           return [
                'nama.required' => 'kolom nama tidak boleh kosong.',
                'nip.required' => 'kolom nip tidak boleh kosong.',
                'nip.digits' => 'NIP harus memiliki 18 karakter',
                'nip.numeric' => 'NIP hanya boleh berisi angka.',
                'tempat_lahir.required' => 'kolom tempat lahir tidak boleh kosong.',
                'tanggal_lahir.required' => 'kolom tanggal lahir tidak boleh kosong.',
                'tanggal_lahir.date' => 'Format tanggal harus valid.',
                'jenis_kelamin.required' => 'Pilihan jenis kelamin tidak boleh kosong.',
                'agama.required' => 'kolom agama tidak boleh kosong.',
                'status_perkawinan.required' => 'kolom status perkawinan tidak boleh kosong.',
                'tmt_pegawai.required' => 'kolom tmt pegawai tidak boleh kosong.',
                'tmt_jabatan.required' => 'kolom tmt jabatan tidak boleh kosong.',
                'golongan.required' => 'kolom golongan tidak boleh kosong.',
                'tmt_golongan.required' => 'kolom tmt golongan tidak boleh kosong.',
                'pendidikan.required' => 'kolom pendidikan tidak boleh kosong.',
                'pendidikan_lulus.required' => 'kolom pendidikan tidak boleh kosong.',
                'pendidikan_struktural.required' => 'kolom pendidikan tidak boleh kosong.',
                'pendidikan_struktural_lulus.required' => 'kolom pendidikan tidak boleh kosong.', 
                // 'status_kepegawaian.required' => 'kolom status kepegawaian tidak boleh kosong.', 
                // 'tipe_pegawai.required' => 'kolom tipe pegawai tidak boleh kosong.', 
            ]; 
        
    }
}

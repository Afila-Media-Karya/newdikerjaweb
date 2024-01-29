<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class pegawaimasukRequest extends FormRequest
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
                'asal_daerah' => 'required',
                'tmt' => 'required',
                'id_jabatan_masuk' => 'required',
                'id_satuan_kerja' => 'required',
                'nama' => 'required',
                'nip' => 'required||numeric|digits:18|unique:tb_pegawai,nip',
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
            ];
    }

    public function messages()
    {
           return [
                'id_jabatan_masuk.required' => 'kolom jabatan masuk tidak boleh kosong.',
                'asal_daerah.required' => 'kolom instansi asal tidak boleh kosong.',
                'tmt.required' => 'kolom tmt masuk tidak boleh kosong.',
                'id_satuan_kerja.required' => 'kolom satuan kerja masuk tidak boleh kosong.',
                'nama.required' => 'kolom nama tidak boleh kosong.',
                'nip.required' => 'kolom nip tidak boleh kosong.',
                'nip.digits' => 'NIP harus memiliki 18 karakter',
                'nip.numeric' => 'NIP hanya boleh berisi angka.',
                'nip.unique' => 'NIP sudah terdaftar.',
                'tempat_lahir.required' => 'kolom tempat lahir tidak boleh kosong.',
                'tanggal_lahir.required' => 'kolom tanggal lahir tidak boleh kosong.',
                'tanggal_lahir.date' => 'Format tanggal harus valid.',
                'jenis_kelamin.required' => 'Pilihan jenis kelamin tidak boleh kosong.',
                'agama.required' => 'kolom agama tidak boleh kosong.',
                'status_perkawinan.required' => 'kolom status perkawinan tidak boleh kosong.',
                'tmt_pegawai.required' => 'kolom tmt pegawai tidak boleh kosong.',
                'tmt_jabatan.required' => 'kolom tmt jabatan tidak boleh kosong.',
                'status.required' => 'kolom status tidak boleh kosong.',
                'golongan.required' => 'kolom golongan tidak boleh kosong.',
                'tmt_golongan.required' => 'kolom tmt golongan tidak boleh kosong.',
                'pendidikan.required' => 'kolom pendidikan tidak boleh kosong.',
                'pendidikan_lulus.required' => 'kolom pendidikan tidak boleh kosong.',
                'pendidikan_struktural.required' => 'kolom pendidikan tidak boleh kosong.',
                'pendidikan_struktural_lulus.required' => 'kolom pendidikan tidak boleh kosong.', 
            ]; 
        
    }
}

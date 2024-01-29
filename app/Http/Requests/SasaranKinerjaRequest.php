<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SasaranKinerjaRequest extends FormRequest
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
        $request = request();
        $level = intval($request->level);
        $jenis = $request->jenis;
        $rules = [];

        if ($level <= 2) {
            $rules = [
                'jenis' => 'required',
                'rencana' => 'required', 
            ]; 
        } else {
            $rules = [
                'jenis' => 'required',
                'rencana' => 'required', 
                'id_skp_atasan' => $jenis === 'utama' ? 'required' : 'nullable',
                'iki_iki_kuantitas' => 'required',
                'iki_satuan_kuantitas' => 'required',
                'iki_target_kuantitas' => 'required',
                'iki_iki_kualitas' => 'required',
                'iki_satuan_kualitas' => 'required',
                'iki_target_kualitas' => 'required',
                'iki_iki_waktu' => 'required',
                'iki_satuan_waktu' => 'required',
                'iki_target_waktu' => 'required',
            ];
        } 

        return $rules;
    }


    public function messages()
    {
        return [
            'jenis.required' => 'kolom jenis tidak boleh kosong.',
            'rencana.required' => 'kolom rencana tidak boleh kosong.',
            'id_skp_atasan.required' => 'Kolom sasaran kerja atasan langsung tidak boleh kosong.',
            'iki_iki_kuantitas.required' => 'Kolom iki kuantitas tidak boleh kosong.',
            'iki_satuan_kuantitas.required' => 'Kolom satuan kuantitas tidak boleh kosong.',
            'iki_target_kuantitas.required' => 'Kolom satuan kuantitas tidak boleh kosong.',
            'iki_iki_kualitas.required' => 'Kolom iki kualitas tidak boleh kosong.',
            'iki_satuan_kualitas.required' => 'Kolom satuan kualitas tidak boleh kosong.',
            'iki_target_kualitas.required' => 'Kolom target kualitas tidak boleh kosong.',
            'iki_iki_waktu.required' => 'Kolom iki waktu tidak boleh kosong.',
            'iki_satuan_waktu.required' => 'Kolom satuan waktu tidak boleh kosong.',
            'iki_target_waktu.required' => 'Kolom target waktu tidak boleh kosong.',
        ]; 
    }
}

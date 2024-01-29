<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KelompokAktivitasRequest extends FormRequest
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
                'kelompok' => 'required',
                'id_jenis_jabatan' => 'required',
                'repeater-aktivitas' => [
                'required',
                'array',
                 function($attribute, $value, $fail){
                     $result = false;
                    $commonValues = [];

                    $array = array_column($value, 'aktivitas');
                    $counts = array_count_values($array);

                    $duplicates = array_filter($counts, function ($count) {
                        return $count > 1;
                    });

                    $result = array_keys($duplicates);
                
                    if (count($result) > 0) {
                        $fail("Aktivitas tidak boleh sama");
                    }
                }
            ],
            'repeater-aktivitas.*.aktivitas' => 'required',
            ];
    }

    public function messages()
    {
           return [
                'kelompok.required' => 'kolom kelompok tidak boleh kosong.',
                'id_jenis_jabatan.required' => 'kolom jenis jabatan tidak boleh kosong.',
            ]; 
        
    }

}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GedungRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('gedung');

        $rules = [
            'kode'   => 'required|string|max:20|unique:gedung,kode,' . $id,
            'nama'   => 'required|string|max:100',
            'lokasi' => 'nullable|string|max:255',
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'kode.required'  => 'Kode gedung wajib diisi.',
            'kode.unique'    => 'Kode gedung sudah digunakan.',
            'kode.max'       => 'Kode gedung maksimal 20 karakter.',
            'nama.required'  => 'Nama gedung wajib diisi.',
            'nama.max'       => 'Nama gedung maksimal 100 karakter.',
            'lokasi.max'     => 'Lokasi gedung maksimal 255 karakter.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors(),
        ], 422));
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MahasiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('mahasiswa');

        return [
            'nama'      => 'required|string|max:100',
            'email'     => 'required|email|max:100|unique:users,email,' . ($this->route('mahasiswa') ? \App\Models\Mahasiswa::find($this->route('mahasiswa'))?->user_id : ''),
            'password'  => $id ? 'nullable|string|min:6' : 'required|string|min:6',
            'user_id'   => 'nullable|integer',
            'nim'       => 'required|string|max:20|unique:mahasiswa,nim,' . $id,
            'jurusan_id'=> 'required|exists:jurusan,id',
            'angkatan'  => 'nullable|integer|digits:4',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID wajib diisi.',
            'nim.required'     => 'NIM wajib diisi.',
            'nim.unique'       => 'NIM sudah terdaftar.',
            'jurusan_id.required' => 'Jurusan wajib diisi.',
            'jurusan_id.exists' => 'Jurusan tidak valid.',
            'angkatan.digits'  => 'Angkatan harus 4 digit (contoh: 2024).',
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

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DosenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('dosen');
        $dosen = $id ? \App\Models\Dosen::find($id) : null;
        $userId = $dosen?->user_id ?? '';

        return [
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:users,email,' . $userId,
            'password' => $id ? 'nullable|string|min:6' : 'required|string|min:6',
            'user_id'  => 'nullable|integer',
            'nip'      => 'required|string|max:30|unique:dosen,nip,' . ($dosen?->id ?? ''),
            'jabatan'  => 'nullable|string|max:100',
            'jurusan_id' => 'required|exists:jurusan,id',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID wajib diisi.',
            'nip.required'     => 'NIP wajib diisi.',
            'nip.unique'       => 'NIP sudah terdaftar.',
            'jurusan_id.required' => 'Jurusan wajib diisi.',
            'jurusan_id.exists'   => 'Jurusan tidak valid.',
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

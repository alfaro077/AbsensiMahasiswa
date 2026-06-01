<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('user');

        $rules = [
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:users,email,' . $id,
            'role'     => 'required|in:mahasiswa,dosen',
        ];

        // Password required on create, optional on update
        if ($this->isMethod('POST')) {
            $rules['password'] = 'required|string|min:6|max:255';
        } else {
            $rules['password'] = 'nullable|string|min:6|max:255';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nama.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
            'role.required'     => 'Role wajib diisi.',
            'role.in'           => 'Role harus mahasiswa atau dosen.',
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

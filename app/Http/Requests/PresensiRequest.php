<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PresensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sesi_id'      => 'required|integer|exists:sesi_kuliah,id',
            'mahasiswa_id' => 'required|integer|exists:mahasiswa,id',
            'waktu_absen'  => 'nullable|date',
            'metode'       => 'required|in:qr,kode_unik,manual',
            'status'       => 'required|in:hadir,izin,sakit,alpha,pending_izin,pending_sakit',
            'keterangan'   => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'sesi_id.required'      => 'Sesi kuliah wajib diisi.',
            'sesi_id.exists'        => 'Sesi kuliah tidak ditemukan.',
            'mahasiswa_id.required' => 'Mahasiswa wajib diisi.',
            'mahasiswa_id.exists'   => 'Mahasiswa tidak ditemukan.',
            'metode.required'       => 'Metode absensi wajib diisi.',
            'metode.in'             => 'Metode harus qr, kode_unik, atau manual.',
            'status.required'       => 'Status kehadiran wajib diisi.',
            'status.in'             => 'Status harus hadir, izin, sakit, alpha, pending_izin, atau pending_sakit.',
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

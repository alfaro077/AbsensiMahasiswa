<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;

class EnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mahasiswa_id'     => 'required|integer|exists:mahasiswa,id',
            'mata_kuliah_id'   => [
                'required',
                'integer',
                'exists:mata_kuliah,id',
                function ($attribute, $value, $fail) {
                    $mahasiswaId = $this->input('mahasiswa_id');
                    $mahasiswa = Mahasiswa::with('jurusan')->find($mahasiswaId);
                    $mataKuliah = MataKuliah::with('jurusan')->find($value);
                    
                    if ($mahasiswa && $mataKuliah) {
                        if ($mahasiswa->jurusan_id !== $mataKuliah->jurusan_id) {
                            $mahasiswaJurusan = $mahasiswa->jurusan?->nama ?? 'Tidak ditentukan';
                            $mataKuliahJurusan = $mataKuliah->jurusan?->nama ?? 'Tidak ditentukan';
                            $fail("Mahasiswa dari jurusan $mahasiswaJurusan tidak dapat didaftarkan ke mata kuliah dari jurusan $mataKuliahJurusan.");
                        }
                    }
                }
            ],
            'kelas_paralel_id' => 'nullable|integer|exists:kelas_paralel,id',
            'tahun_ajaran'     => 'nullable|string|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'mahasiswa_id.required'   => 'Mahasiswa wajib diisi.',
            'mahasiswa_id.exists'     => 'Mahasiswa tidak ditemukan.',
            'mata_kuliah_id.required' => 'Mata kuliah wajib diisi.',
            'mata_kuliah_id.exists'   => 'Mata kuliah tidak ditemukan.',
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

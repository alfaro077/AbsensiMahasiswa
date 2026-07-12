<?php

namespace App\Http\Requests;

use App\Models\Dosen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MataKuliahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('mata_kuliah');

        return [
            'kode'      => 'required|string|max:20|unique:mata_kuliah,kode,' . $id,
            'nama'      => 'required|string|max:150',
            'sks'       => 'required|integer|min:1|max:8',
            'semester'  => 'nullable|integer|min:1|max:14',
            'dosen_id'  => 'required|integer|exists:dosen,id',
            'jurusan_id'=> 'required|exists:jurusan,id',
        ];
    }

    /**
     * Validasi tambahan: jurusan dosen harus sama dengan jurusan mata kuliah.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return; // skip jika sudah ada error dasar
            }

            $dosenId = $this->input('dosen_id');
            $jurusanId = $this->input('jurusan_id');

            if ($dosenId && $jurusanId) {
                $dosen = Dosen::find($dosenId);
                if ($dosen && (int) $dosen->jurusan_id !== (int) $jurusanId) {
                    $dosenNama = $dosen->user?->nama ?? 'Dosen';
                    $dosenJurusan = $dosen->jurusan?->nama ?? 'jurusan lain';
                    $validator->errors()->add(
                        'dosen_id',
                        "Dosen {$dosenNama} berasal dari {$dosenJurusan}, tidak sesuai dengan jurusan mata kuliah yang dipilih."
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'kode.required'     => 'Kode mata kuliah wajib diisi.',
            'kode.unique'       => 'Kode mata kuliah sudah terdaftar.',
            'nama.required'     => 'Nama mata kuliah wajib diisi.',
            'sks.required'      => 'SKS wajib diisi.',
            'dosen_id.required' => 'Dosen pengampu wajib diisi.',
            'dosen_id.exists'   => 'Dosen tidak ditemukan.',
            'jurusan_id.required' => 'Jurusan wajib diisi.',
            'jurusan_id.exists' => 'Jurusan tidak valid.',
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


<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SesiKuliahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('jam_mulai') && !empty($this->jam_mulai)) {
            $this->merge([
                'jam_mulai' => date('H:i', strtotime($this->jam_mulai)),
            ]);
        }
        if ($this->has('jam_selesai') && !empty($this->jam_selesai)) {
            $this->merge([
                'jam_selesai' => date('H:i', strtotime($this->jam_selesai)),
            ]);
        }
    }

    public function rules(): array
    {
        $id = $this->route('sesi_kuliah');

        $rules = [
            'mata_kuliah_id'  => 'required|integer|exists:mata_kuliah,id',
            'tanggal'         => 'required|date',
            'jam_mulai'       => 'required|date_format:H:i',
            'jam_selesai'     => 'required|date_format:H:i|after:jam_mulai',
            'topik'           => 'nullable|string|max:200',
            'gedung'          => 'required|string|max:100',
            'lantai'          => 'required|string|max:50',
            'ruangan'         => 'required|string|max:100',
            'qr_code'         => 'nullable|string|max:100|unique:sesi_kuliah,qr_code,' . $id,
            'kode_unik'       => 'nullable|string|max:10|unique:sesi_kuliah,kode_unik,' . $id,
            'kode_expires_at' => 'nullable|date',
            'is_active'       => 'nullable|boolean',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['mata_kuliah_id'] = 'sometimes|required|integer|exists:mata_kuliah,id';
            $rules['tanggal']        = 'sometimes|required|date';
            $rules['jam_mulai']      = 'sometimes|required|date_format:H:i';
            $rules['jam_selesai']    = 'sometimes|required|date_format:H:i|after:jam_mulai';
            $rules['gedung']         = 'sometimes|required|string|max:100';
            $rules['lantai']         = 'sometimes|required|string|max:50';
            $rules['ruangan']        = 'sometimes|required|string|max:100';
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $id = $this->route('sesi_kuliah');
            
            $tanggal = $this->input('tanggal') ?? ($id ? \App\Models\SesiKuliah::find($id)?->tanggal?->toDateString() : null);
            $jamMulai = $this->input('jam_mulai') ?? ($id ? \App\Models\SesiKuliah::find($id)?->jam_mulai : null);
            $jamSelesai = $this->input('jam_selesai') ?? ($id ? \App\Models\SesiKuliah::find($id)?->jam_selesai : null);
            
            $gedung = $this->input('gedung') ?? ($id ? \App\Models\SesiKuliah::find($id)?->gedung : null);
            $lantai = $this->input('lantai') ?? ($id ? \App\Models\SesiKuliah::find($id)?->lantai : null);
            $ruangan = $this->input('ruangan') ?? ($id ? \App\Models\SesiKuliah::find($id)?->ruangan : null);
            
            $mataKuliahId = $this->input('mata_kuliah_id') ?? ($id ? \App\Models\SesiKuliah::find($id)?->mata_kuliah_id : null);
            
            if ($tanggal && $jamMulai && $jamSelesai) {
                $jamMulaiFormatted = date('H:i:s', strtotime($jamMulai));
                $jamSelesaiFormatted = date('H:i:s', strtotime($jamSelesai));
                
                // 1. Cek Bentrok Ruangan
                if ($gedung && $lantai && $ruangan) {
                    $bentrokRuangan = \App\Models\SesiKuliah::where('tanggal', $tanggal)
                        ->where('gedung', $gedung)
                        ->where('lantai', $lantai)
                        ->where('ruangan', $ruangan)
                        ->where(function ($q) use ($jamMulaiFormatted, $jamSelesaiFormatted) {
                            $q->where('jam_mulai', '<', $jamSelesaiFormatted)
                              ->where('jam_selesai', '>', $jamMulaiFormatted);
                        })
                        ->when($id, fn($q) => $q->where('id', '!=', $id))
                        ->exists();

                    if ($bentrokRuangan) {
                        $validator->errors()->add('ruangan', "Bentrokan Jadwal: Ruangan ini ({$gedung}, {$lantai}, {$ruangan}) sudah digunakan oleh sesi lain pada tanggal dan waktu tersebut.");
                    }
                }
                
                // 2. Cek Bentrok Dosen
                if ($mataKuliahId) {
                    $mataKuliah = \App\Models\MataKuliah::find($mataKuliahId);
                    $dosenId = $mataKuliah?->dosen_id;
                    
                    if ($dosenId) {
                        $bentrokDosen = \App\Models\SesiKuliah::where('tanggal', $tanggal)
                            ->whereHas('mataKuliah', fn($q) => $q->where('dosen_id', $dosenId))
                            ->where(function ($q) use ($jamMulaiFormatted, $jamSelesaiFormatted) {
                                $q->where('jam_mulai', '<', $jamSelesaiFormatted)
                                  ->where('jam_selesai', '>', $jamMulaiFormatted);
                            })
                            ->when($id, fn($q) => $q->where('id', '!=', $id))
                            ->exists();

                        if ($bentrokDosen) {
                            $dosenNama = $mataKuliah->dosen?->user?->nama ?? 'Dosen';
                            $validator->errors()->add('jam_mulai', "Bentrokan Jadwal: {$dosenNama} sudah memiliki jadwal mengajar lain pada tanggal dan waktu tersebut.");
                        }
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'mata_kuliah_id.required' => 'Mata kuliah wajib diisi.',
            'mata_kuliah_id.exists'   => 'Mata kuliah tidak ditemukan.',
            'tanggal.required'        => 'Tanggal wajib diisi.',
            'jam_mulai.required'      => 'Jam mulai wajib diisi.',
            'jam_selesai.required'    => 'Jam selesai wajib diisi.',
            'jam_selesai.after'       => 'Jam selesai harus setelah jam mulai.',
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

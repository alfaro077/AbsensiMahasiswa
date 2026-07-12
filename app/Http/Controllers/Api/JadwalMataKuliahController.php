<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalMataKuliah;
use App\Models\KelasParalel;
use App\Models\MataKuliah;
use App\Models\Ruangan;
use App\Traits\ApiResponse;
use App\Traits\Filterable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JadwalMataKuliahController extends Controller
{
    use ApiResponse, Filterable;

    /**
     * GET /api/jadwal-mata-kuliah
     */
    public function index(Request $request): JsonResponse
    {
        $query = JadwalMataKuliah::query();

        if ($request->filled('include')) {
            $allowed = ['mataKuliah', 'gedung', 'kelasParalel', 'ruangan', 'kelasParalel.dosen'];
            $includes = array_intersect(explode(',', $request->include), $allowed);
            $query->with($includes);
        }

        // Auto-filter berdasarkan role
        $user = $request->user();
        if ($user && $user->role === 'dosen') {
            $dosenId = $user->dosen?->id;
            $query->where(function ($q) use ($dosenId) {
                $q->whereHas('mataKuliah', function ($q2) use ($dosenId) {
                    $q2->where('dosen_id', $dosenId);
                })->orWhereHas('kelasParalel', function ($q2) use ($dosenId) {
                    $q2->where('dosen_id', $dosenId);
                });
            });
        } elseif ($user && $user->role === 'mahasiswa') {
            $mahasiswaId = $user->mahasiswa?->id;
            $query->whereHas('mataKuliah', function ($q) use ($mahasiswaId) {
                $q->whereHas('mahasiswa', function ($q2) use ($mahasiswaId) {
                    $q2->where('mahasiswa.id', $mahasiswaId);
                });
            });
        }

        $result = $this->applyFilters(
            query: $query,
            request: $request,
            filterableFields: ['mata_kuliah_id', 'kelas_paralel_id', 'hari', 'ruangan_id'],
            searchableFields: [],
            sortableFields: ['id', 'mata_kuliah_id', 'kelas_paralel_id', 'hari', 'jam_mulai', 'jam_selesai', 'ruangan_id'],
        );

        return $this->success($result, 'Data jadwal mata kuliah berhasil diambil');
    }

    /**
     * POST /api/jadwal-mata-kuliah
     */
    public function store(Request $request): JsonResponse
    {
        $this->normalizeTimeRequest($request);

        $validated = $request->validate([
            'kelas_paralel_id' => 'required|integer|exists:kelas_paralel,id',
            'hari'             => ['required', Rule::in(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'])],
            'jam_mulai'        => 'required|date_format:H:i',
            'jam_selesai'      => 'required|date_format:H:i|after:jam_mulai',
            'ruangan_id'       => 'required|integer|exists:ruangan,id',
        ]);

        // Derive mata_kuliah_id + gedung_id from relasi
        $kelas = KelasParalel::findOrFail($validated['kelas_paralel_id']);
        $validated['mata_kuliah_id'] = $kelas->mata_kuliah_id;

        $ruangan = Ruangan::findOrFail($validated['ruangan_id']);
        $validated['gedung_id'] = $ruangan->gedung_id;

        $bentrokMsg = $this->cekBentrok($validated, null);
        if ($bentrokMsg) {
            return response()->json([
                'success' => false,
                'message' => 'Bentrokan Jadwal',
                'errors'  => $bentrokMsg,
            ], 422);
        }

        $jadwal = JadwalMataKuliah::create($validated);
        $jadwal->load(['mataKuliah', 'gedung', 'kelasParalel', 'ruangan']);
        return $this->created($jadwal, 'Jadwal mata kuliah berhasil ditambahkan');
    }

    /**
     * GET /api/jadwal-mata-kuliah/{id}
     */
    public function show(int $id): JsonResponse
    {
        $jadwal = JadwalMataKuliah::with(['mataKuliah', 'gedung', 'kelasParalel', 'ruangan'])->find($id);
        if (!$jadwal) {
            return $this->notFound('Jadwal tidak ditemukan');
        }
        return $this->success($jadwal, 'Detail jadwal berhasil diambil');
    }

    /**
     * PUT /api/jadwal-mata-kuliah/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $jadwal = JadwalMataKuliah::find($id);
        if (!$jadwal) {
            return $this->notFound('Jadwal tidak ditemukan');
        }

        $this->normalizeTimeRequest($request);

        $validated = $request->validate([
            'kelas_paralel_id' => 'sometimes|required|integer|exists:kelas_paralel,id',
            'hari'             => ['sometimes', 'required', Rule::in(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'])],
            'jam_mulai'        => 'sometimes|required|date_format:H:i',
            'jam_selesai'      => 'sometimes|required|date_format:H:i|after:jam_mulai',
            'ruangan_id'       => 'sometimes|required|integer|exists:ruangan,id',
        ]);

        $data = [
            'hari'        => $validated['hari']        ?? $jadwal->hari,
            'jam_mulai'   => $validated['jam_mulai']   ?? $jadwal->jam_mulai,
            'jam_selesai' => $validated['jam_selesai'] ?? $jadwal->jam_selesai,
        ];

        // Derive kelas + ruangan info
        $kelasId = $validated['kelas_paralel_id'] ?? $jadwal->kelas_paralel_id;
        $ruanganId = $validated['ruangan_id'] ?? $jadwal->ruangan_id;

        $kelas = KelasParalel::find($kelasId);
        $data['kelas_paralel_id'] = $kelasId;
        $data['mata_kuliah_id'] = $kelas?->mata_kuliah_id ?? $jadwal->mata_kuliah_id;

        $ruangan = Ruangan::find($ruanganId);
        $data['ruangan_id'] = $ruanganId;
        $data['gedung_id'] = $ruangan?->gedung_id ?? $jadwal->gedung_id;

        $bentrokMsg = $this->cekBentrok($data, $id);
        if ($bentrokMsg) {
            return response()->json([
                'success' => false,
                'message' => 'Bentrokan Jadwal',
                'errors'  => $bentrokMsg,
            ], 422);
        }

        if (isset($validated['kelas_paralel_id'])) {
            $validated['mata_kuliah_id'] = $data['mata_kuliah_id'];
        }
        if (isset($validated['ruangan_id'])) {
            $validated['gedung_id'] = $data['gedung_id'];
        }

        $jadwal->update($validated);
        $jadwal->load(['mataKuliah', 'gedung', 'kelasParalel', 'ruangan']);
        return $this->success($jadwal, 'Jadwal berhasil diperbarui');
    }

    /**
     * DELETE /api/jadwal-mata-kuliah/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $jadwal = JadwalMataKuliah::find($id);
        if (!$jadwal) {
            return $this->notFound('Jadwal tidak ditemukan');
        }
        $jadwal->delete();
        return $this->success(null, 'Jadwal berhasil dihapus');
    }

    private function normalizeTimeRequest(Request $request): void
    {
        foreach (['jam_mulai', 'jam_selesai'] as $field) {
            if ($request->has($field) && is_string($request->$field)) {
                $parts = explode(':', $request->$field);
                if (count($parts) >= 2) {
                    $request->merge([
                        $field => str_pad($parts[0], 2, '0', STR_PAD_LEFT) . ':' . str_pad($parts[1], 2, '0', STR_PAD_LEFT)
                    ]);
                }
            }
        }
    }

    /**
     * Cek seluruh kemungkinan bentrok jadwal.
     */
    private function cekBentrok(array $data, ?int $ignoreId): ?array
    {
        $errors = [];

        // 1. Cek Bentrok Ruangan (global — semua kelas)
        $bentrokRuangan = JadwalMataKuliah::with(['mataKuliah', 'kelasParalel', 'ruangan', 'gedung'])
            ->where('hari', $data['hari'])
            ->where('ruangan_id', $data['ruangan_id'])
            ->where(function ($q) use ($data) {
                $q->where('jam_mulai', '<', $data['jam_selesai'])
                  ->where('jam_selesai', '>', $data['jam_mulai']);
            })
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->first();

        if ($bentrokRuangan) {
            $ruang = $bentrokRuangan->ruangan;
            $namaGedung = $bentrokRuangan->gedung?->nama ?? 'Unknown';
            $namaRuangan = $ruang?->nama ?? 'Unknown';
            $mkBentrok = $bentrokRuangan->mataKuliah?->nama ?? 'Mata Kuliah';
            $kelasLabel = $bentrokRuangan->kelasParalel ? ' (' . $bentrokRuangan->kelasParalel->nama_kelas . ')' : '';
            $errors['ruangan'] = "Ruangan {$namaGedung} Lantai {$ruang?->lantai} - {$namaRuangan} sudah digunakan oleh \"{$mkBentrok}{$kelasLabel}\" pada hari {$bentrokRuangan->hari} pukul {$bentrokRuangan->jam_mulai} - {$bentrokRuangan->jam_selesai}.";
        }

        // 2. Cek Bentrok Dosen (global — semua kelas)
        $dosenId = null;
        $mataKuliah = MataKuliah::find($data['mata_kuliah_id']);

        if (isset($data['kelas_paralel_id'])) {
            $kelasParalel = KelasParalel::find($data['kelas_paralel_id']);
            $dosenId = $kelasParalel?->dosen_id;
        }
        if (!$dosenId) {
            $dosenId = $mataKuliah?->dosen_id;
        }

        if ($dosenId) {
            $bentrokDosen = JadwalMataKuliah::with(['mataKuliah', 'kelasParalel'])
                ->where('hari', $data['hari'])
                ->where(function ($q) use ($dosenId) {
                    $q->whereHas('mataKuliah', fn($q2) => $q2->where('dosen_id', $dosenId))
                      ->orWhereHas('kelasParalel', fn($q2) => $q2->where('dosen_id', $dosenId));
                })
                ->where(function ($q) use ($data) {
                    $q->where('jam_mulai', '<', $data['jam_selesai'])
                      ->where('jam_selesai', '>', $data['jam_mulai']);
                })
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->first();

            if ($bentrokDosen) {
                $dosenNama = $bentrokDosen->kelasParalel?->dosen?->user?->nama
                    ?? $bentrokDosen->mataKuliah?->dosen?->user?->nama
                    ?? 'Dosen';
                $mkBentrok = $bentrokDosen->mataKuliah?->nama ?? 'Mata Kuliah';
                $kelasLabel = $bentrokDosen->kelasParalel ? ' (' . $bentrokDosen->kelasParalel->nama_kelas . ')' : '';
                $errors['jam_mulai'] = "Dosen {$dosenNama} sudah memiliki jadwal mengajar \"{$mkBentrok}{$kelasLabel}\" pada hari {$bentrokDosen->hari} pukul {$bentrokDosen->jam_mulai} - {$bentrokDosen->jam_selesai}.";
            }
        }

        // 3. Cek Bentrok dalam satu kelas paralel yang sama
        if (isset($data['kelas_paralel_id'])) {
            $bentrokKelas = JadwalMataKuliah::with(['gedung', 'ruangan'])
                ->where('hari', $data['hari'])
                ->where('kelas_paralel_id', $data['kelas_paralel_id'])
                ->where(function ($q) use ($data) {
                    $q->where('jam_mulai', '<', $data['jam_selesai'])
                      ->where('jam_selesai', '>', $data['jam_mulai']);
                })
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->first();

            if ($bentrokKelas) {
                $ruang = $bentrokKelas->ruangan;
                $gedungBentrok = $bentrokKelas->gedung?->nama ?? 'Gedung';
                $errors['kelas_paralel_id'] = "Kelas \"{$mataKuliah?->nama}\" sudah memiliki jadwal lain pada hari {$bentrokKelas->hari} pukul {$bentrokKelas->jam_mulai} - {$bentrokKelas->jam_selesai} di {$gedungBentrok} Lantai {$ruang?->lantai} - {$ruang?->nama}.";
            }
        }

        return empty($errors) ? null : $errors;
    }
}

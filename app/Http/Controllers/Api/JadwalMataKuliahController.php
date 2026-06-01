<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalMataKuliah;
use App\Models\MataKuliah;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JadwalMataKuliahController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/jadwal-mata-kuliah
     * List jadwal, filterable by mata_kuliah_id
     */
    public function index(Request $request): JsonResponse
    {
        $query = JadwalMataKuliah::with(['mataKuliah', 'gedung']);

        if ($request->filled('mata_kuliah_id')) {
            $query->where('mata_kuliah_id', $request->mata_kuliah_id);
        }

        $data = $query->orderBy('hari')->orderBy('jam_mulai')->get();
        return $this->success($data, 'Data jadwal mata kuliah berhasil diambil');
    }

    /**
     * POST /api/jadwal-mata-kuliah
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mata_kuliah_id' => 'required|integer|exists:mata_kuliah,id',
            'hari'           => ['required', Rule::in(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'])],
            'jam_mulai'      => 'required|date_format:H:i',
            'jam_selesai'    => 'required|date_format:H:i|after:jam_mulai',
            'gedung_id'      => 'required|integer|exists:gedung,id',
            'lantai'         => 'required|string|max:50',
            'ruangan'        => 'required|string|max:100',
        ]);

        $bentrokMsg = $this->cekBentrok($validated, null);
        if ($bentrokMsg) {
            return response()->json([
                'success' => false,
                'message' => 'Bentrokan Jadwal',
                'errors'  => $bentrokMsg,
            ], 422);
        }

        $jadwal = JadwalMataKuliah::create($validated);
        $jadwal->load(['mataKuliah', 'gedung']);
        return $this->created($jadwal, 'Jadwal mata kuliah berhasil ditambahkan');
    }

    /**
     * GET /api/jadwal-mata-kuliah/{id}
     */
    public function show(int $id): JsonResponse
    {
        $jadwal = JadwalMataKuliah::with(['mataKuliah', 'gedung'])->find($id);
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

        $validated = $request->validate([
            'mata_kuliah_id' => 'sometimes|required|integer|exists:mata_kuliah,id',
            'hari'           => ['sometimes', 'required', Rule::in(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'])],
            'jam_mulai'      => 'sometimes|required|date_format:H:i',
            'jam_selesai'    => 'sometimes|required|date_format:H:i|after:jam_mulai',
            'gedung_id'      => 'sometimes|required|integer|exists:gedung,id',
            'lantai'         => 'sometimes|required|string|max:50',
            'ruangan'        => 'sometimes|required|string|max:100',
        ]);

        $data = [
            'hari'        => $validated['hari']        ?? $jadwal->hari,
            'jam_mulai'   => $validated['jam_mulai']   ?? $jadwal->jam_mulai,
            'jam_selesai' => $validated['jam_selesai'] ?? $jadwal->jam_selesai,
            'gedung_id'   => $validated['gedung_id']   ?? $jadwal->gedung_id,
            'lantai'      => $validated['lantai']      ?? $jadwal->lantai,
            'ruangan'     => $validated['ruangan']     ?? $jadwal->ruangan,
            'mata_kuliah_id' => $validated['mata_kuliah_id'] ?? $jadwal->mata_kuliah_id,
        ];

        $bentrokMsg = $this->cekBentrok($data, $id);
        if ($bentrokMsg) {
            return response()->json([
                'success' => false,
                'message' => 'Bentrokan Jadwal',
                'errors'  => $bentrokMsg,
            ], 422);
        }

        $jadwal->update($validated);
        $jadwal->load(['mataKuliah', 'gedung']);
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

    /**
     * Cek seluruh kemungkinan bentrok jadwal.
     * Mengembalikan array errors atau null jika aman.
     */
    private function cekBentrok(array $data, ?int $ignoreId): ?array
    {
        $errors = [];

        // 1. Cek Bentrok Ruangan
        $bentrokRuangan = JadwalMataKuliah::where('hari', $data['hari'])
            ->where('gedung_id', $data['gedung_id'])
            ->where('lantai', $data['lantai'])
            ->where('ruangan', $data['ruangan'])
            ->where(function ($q) use ($data) {
                $q->where('jam_mulai', '<', $data['jam_selesai'])
                  ->where('jam_selesai', '>', $data['jam_mulai']);
            })
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists();

        if ($bentrokRuangan) {
            $gedung = \App\Models\Gedung::find($data['gedung_id']);
            $namaGedung = $gedung?->nama ?? 'Unknown';
            $errors['ruangan'] = "Ruangan {$namaGedung}, {$data['lantai']}, {$data['ruangan']} sudah digunakan di hari dan jam yang sama.";
        }

        // 2. Cek Bentrok Dosen
        $mataKuliah = MataKuliah::find($data['mata_kuliah_id']);
        $dosenId = $mataKuliah?->dosen_id;

        if ($dosenId) {
            $bentrokDosen = JadwalMataKuliah::where('hari', $data['hari'])
                ->whereHas('mataKuliah', fn($q) => $q->where('dosen_id', $dosenId))
                ->where(function ($q) use ($data) {
                    $q->where('jam_mulai', '<', $data['jam_selesai'])
                      ->where('jam_selesai', '>', $data['jam_mulai']);
                })
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists();

            if ($bentrokDosen) {
                $dosenNama = $mataKuliah->dosen?->user?->nama ?? 'Dosen';
                $errors['jam_mulai'] = "Bentrokan Jadwal: {$dosenNama} sudah memiliki jadwal mengajar di hari dan jam yang sama.";
            }
        }

        // 3. Cek Bentrok Mata Kuliah yang sama
        $bentrokMK = JadwalMataKuliah::where('hari', $data['hari'])
            ->where('mata_kuliah_id', $data['mata_kuliah_id'])
            ->where(function ($q) use ($data) {
                $q->where('jam_mulai', '<', $data['jam_selesai'])
                  ->where('jam_selesai', '>', $data['jam_mulai']);
            })
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists();

        if ($bentrokMK) {
            $errors['mata_kuliah_id'] = "Mata kuliah ini sudah memiliki jadwal lain di hari dan jam yang sama.";
        }

        return empty($errors) ? null : $errors;
    }
}

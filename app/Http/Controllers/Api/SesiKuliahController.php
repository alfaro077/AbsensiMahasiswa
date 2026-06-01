<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SesiKuliahRequest;
use App\Models\SesiKuliah;
use App\Traits\ApiResponse;
use App\Traits\Filterable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SesiKuliahController extends Controller
{
    use ApiResponse, Filterable;

    /**
     * GET /api/sesi-kuliah
     */
    public function index(Request $request): JsonResponse
    {
        $query = SesiKuliah::query()->withCount('presensi');

        if ($request->filled('include')) {
            $includes = explode(',', $request->include);
            $query->with($includes);
        }

        $filterable = ['mata_kuliah_id', 'tanggal', 'is_active'];

        $user = $request->user();
        if ($user && $user->role === 'dosen') {
            $query->whereHas('mataKuliah', function ($q) use ($user) {
                $q->where('dosen_id', $user->dosen?->id ?? 0);
            });
        } else if ($request->filled('dosen_id')) {
            $query->whereHas('mataKuliah', function ($q) use ($request) {
                $q->where('dosen_id', $request->dosen_id);
            });
        } else if ($request->filled('mahasiswa_id')) {
            $query->whereHas('mataKuliah.mahasiswa', function ($q) use ($request) {
                $q->where('mahasiswa.id', $request->mahasiswa_id);
            });
        }
        $result = $this->applyFilters(
            query: $query,
            request: $request,
            filterableFields: $filterable,
            searchableFields: ['topik', 'qr_code', 'kode_unik'],
            sortableFields: ['id', 'mata_kuliah_id', 'tanggal', 'jam_mulai', 'jam_selesai', 'is_active'],
        );

        return $this->success($result, 'Data sesi kuliah berhasil diambil');
    }

    /**
     * POST /api/sesi-kuliah
     */
    public function store(SesiKuliahRequest $request): JsonResponse
    {
        $sesiKuliah = SesiKuliah::create($request->validated());
        $sesiKuliah->load('mataKuliah');
        return $this->created($sesiKuliah, 'Sesi kuliah berhasil dibuat');
    }

    /**
     * GET /api/sesi-kuliah/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $query = SesiKuliah::query()->withCount('presensi');

        if ($request->filled('include')) {
            $includes = explode(',', $request->include);
            $query->with($includes);
        }

        $sesiKuliah = $query->find($id);

        if (!$sesiKuliah) {
            return $this->notFound('Sesi kuliah tidak ditemukan');
        }

        return $this->success($sesiKuliah, 'Detail sesi kuliah berhasil diambil');
    }

    /**
     * PUT /api/sesi-kuliah/{id}
     */
    public function update(SesiKuliahRequest $request, int $id): JsonResponse
    {
        $sesiKuliah = SesiKuliah::find($id);

        if (!$sesiKuliah) {
            return $this->notFound('Sesi kuliah tidak ditemukan');
        }

        $sesiKuliah->update($request->validated());
        $sesiKuliah->load('mataKuliah');
        return $this->success($sesiKuliah, 'Sesi kuliah berhasil diperbarui');
    }

    /**
     * DELETE /api/sesi-kuliah/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $sesiKuliah = SesiKuliah::find($id);

        if (!$sesiKuliah) {
            return $this->notFound('Sesi kuliah tidak ditemukan');
        }

        $sesiKuliah->delete();
        return $this->success(null, 'Sesi kuliah berhasil dihapus');
    }
}

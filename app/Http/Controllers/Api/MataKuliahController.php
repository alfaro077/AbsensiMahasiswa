<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MataKuliahRequest;
use App\Models\MataKuliah;
use App\Traits\ApiResponse;
use App\Traits\Filterable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    use ApiResponse, Filterable;

    /**
     * GET /api/mata-kuliah
     */
    public function index(Request $request): JsonResponse
    {
        $query = MataKuliah::query();

        if ($request->filled('include')) {
            $allowed = ['dosen', 'dosen.user', 'sesiKuliah', 'mahasiswa', 'jurusan'];
            $includes = array_intersect(explode(',', $request->include), $allowed);
            $query->with($includes);
        }

        $filterable = ['semester', 'sks', 'dosen_id', 'jurusan_id'];
        
        // Auto-filter based on Role (Secure)
        $user = $request->user();
        if ($user && $user->role === 'dosen') {
            $query->where('dosen_id', $user->dosen?->id);
            // Ignore any manually passed dosen_id to prevent seeing others' data
            $filterable = array_diff($filterable, ['dosen_id']);
        } 
        else if ($user && $user->role === 'mahasiswa') {
            $query->whereHas('mahasiswa', function ($q) use ($user) {
                $q->where('mahasiswa.id', $user->mahasiswa?->id);
            });
        }
        // Admin can filter by any dosen_id
        else if ($request->has('dosen_id')) {
            $query->where('dosen_id', $request->dosen_id);
            $filterable = array_diff($filterable, ['dosen_id']);
        }

        $result = $this->applyFilters(
            query: $query,
            request: $request,
            filterableFields: $filterable,
            searchableFields: ['kode', 'nama'],
            sortableFields: ['id', 'kode', 'nama', 'sks', 'semester'],
        );

        return $this->success($result, 'Data mata kuliah berhasil diambil');
    }

    /**
     * POST /api/mata-kuliah
     */
    public function store(MataKuliahRequest $request): JsonResponse
    {
        $mataKuliah = MataKuliah::create($request->validated());
        $mataKuliah->load('dosen');
        return $this->created($mataKuliah, 'Mata kuliah berhasil dibuat');
    }

    /**
     * GET /api/mata-kuliah/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $query = MataKuliah::query();

        if ($request->filled('include')) {
            $allowed = ['dosen', 'dosen.user', 'sesiKuliah', 'mahasiswa', 'jurusan'];
            $includes = array_intersect(explode(',', $request->include), $allowed);
            $query->with($includes);
        }

        $mataKuliah = $query->find($id);

        if (!$mataKuliah) {
            return $this->notFound('Mata kuliah tidak ditemukan');
        }

        return $this->success($mataKuliah, 'Detail mata kuliah berhasil diambil');
    }

    /**
     * PUT /api/mata-kuliah/{id}
     */
    public function update(MataKuliahRequest $request, int $id): JsonResponse
    {
        $mataKuliah = MataKuliah::find($id);

        if (!$mataKuliah) {
            return $this->notFound('Mata kuliah tidak ditemukan');
        }

        $mataKuliah->update($request->validated());
        $mataKuliah->load('dosen');
        return $this->success($mataKuliah, 'Mata kuliah berhasil diperbarui');
    }

    /**
     * DELETE /api/mata-kuliah/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $mataKuliah = MataKuliah::find($id);

        if (!$mataKuliah) {
            return $this->notFound('Mata kuliah tidak ditemukan');
        }

        $mataKuliah->delete();
        return $this->success(null, 'Mata kuliah berhasil dihapus');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GedungRequest;
use App\Models\Gedung;
use App\Traits\ApiResponse;
use App\Traits\Filterable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GedungController extends Controller
{
    use ApiResponse, Filterable;

    public function index(Request $request): JsonResponse
    {
        $query = Gedung::query()->withCount('jadwal');

        $result = $this->applyFilters(
            query: $query,
            request: $request,
            filterableFields: ['id'],
            searchableFields: ['kode', 'nama', 'lokasi'],
            sortableFields: ['id', 'kode', 'nama', 'lokasi', 'created_at'],
        );

        return $this->success($result, 'Data gedung berhasil diambil');
    }

    public function store(GedungRequest $request): JsonResponse
    {
        $gedung = Gedung::create($request->validated());
        return $this->created($gedung, 'Gedung berhasil ditambahkan');
    }

    public function show(int $id): JsonResponse
    {
        $gedung = Gedung::withCount('jadwal')->find($id);

        if (!$gedung) {
            return $this->notFound('Gedung tidak ditemukan');
        }

        return $this->success($gedung, 'Detail gedung berhasil diambil');
    }

    public function update(GedungRequest $request, int $id): JsonResponse
    {
        $gedung = Gedung::find($id);

        if (!$gedung) {
            return $this->notFound('Gedung tidak ditemukan');
        }

        $gedung->update($request->validated());
        return $this->success($gedung, 'Gedung berhasil diperbarui');
    }

    public function destroy(int $id): JsonResponse
    {
        $gedung = Gedung::find($id);

        if (!$gedung) {
            return $this->notFound('Gedung tidak ditemukan');
        }

        $gedung->delete();
        return $this->success(null, 'Gedung berhasil dihapus');
    }
}

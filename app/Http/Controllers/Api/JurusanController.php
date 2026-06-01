<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Traits\ApiResponse;
use App\Traits\Filterable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    use ApiResponse, Filterable;

    /**
     * GET /api/jurusan
     */
    public function index(Request $request): JsonResponse
    {
        $query = Jurusan::query();

        $result = $this->applyFilters(
            query: $query,
            request: $request,
            filterableFields: ['kode'],
            searchableFields: ['kode', 'nama'],
            sortableFields: ['id', 'kode', 'nama'],
        );

        return $this->success($result, 'Data jurusan berhasil diambil');
    }

    /**
     * GET /api/jurusan/{id}
     */
    public function show(int $id): JsonResponse
    {
        $jurusan = Jurusan::find($id);

        if (!$jurusan) {
            return $this->notFound('Jurusan tidak ditemukan');
        }

        return $this->success($jurusan, 'Detail jurusan berhasil diambil');
    }

    /**
     * POST /api/jurusan
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kode' => 'required|string|max:20|unique:jurusan,kode',
            'nama' => 'required|string|max:100',
        ]);

        $jurusan = Jurusan::create($data);
        return $this->created($jurusan, 'Jurusan berhasil dibuat');
    }

    /**
     * PUT /api/jurusan/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $jurusan = Jurusan::find($id);

        if (!$jurusan) {
            return $this->notFound('Jurusan tidak ditemukan');
        }

        $data = $request->validate([
            'kode' => 'required|string|max:20|unique:jurusan,kode,' . $id,
            'nama' => 'required|string|max:100',
        ]);

        $jurusan->update($data);
        return $this->success($jurusan, 'Jurusan berhasil diperbarui');
    }

    /**
     * DELETE /api/jurusan/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $jurusan = Jurusan::find($id);

        if (!$jurusan) {
            return $this->notFound('Jurusan tidak ditemukan');
        }

        $jurusan->delete();
        return $this->success(null, 'Jurusan berhasil dihapus');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RuanganRequest;
use App\Models\Ruangan;
use App\Traits\ApiResponse;
use App\Traits\Filterable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    use ApiResponse, Filterable;

    public function index(Request $request): JsonResponse
    {
        $query = Ruangan::query();

        if ($request->filled('include')) {
            $allowed = ['gedung'];
            $includes = array_intersect(explode(',', $request->include), $allowed);
            $query->with($includes);
        }

        $result = $this->applyFilters(
            query: $query,
            request: $request,
            filterableFields: ['gedung_id', 'lantai'],
            searchableFields: ['nama'],
            sortableFields: ['id', 'nama', 'lantai', 'gedung_id', 'created_at'],
        );

        return $this->success($result, 'Data ruangan berhasil diambil');
    }

    public function store(RuanganRequest $request): JsonResponse
    {
        $ruangan = Ruangan::create($request->validated());
        $ruangan->load('gedung');
        return $this->created($ruangan, 'Ruangan berhasil ditambahkan');
    }

    public function show(int $id): JsonResponse
    {
        $ruangan = Ruangan::with('gedung')->find($id);
        if (!$ruangan) {
            return $this->notFound('Ruangan tidak ditemukan');
        }
        return $this->success($ruangan, 'Detail ruangan berhasil diambil');
    }

    public function update(RuanganRequest $request, int $id): JsonResponse
    {
        $ruangan = Ruangan::find($id);
        if (!$ruangan) {
            return $this->notFound('Ruangan tidak ditemukan');
        }
        $ruangan->update($request->validated());
        $ruangan->load('gedung');
        return $this->success($ruangan, 'Ruangan berhasil diperbarui');
    }

    public function destroy(int $id): JsonResponse
    {
        $ruangan = Ruangan::find($id);
        if (!$ruangan) {
            return $this->notFound('Ruangan tidak ditemukan');
        }
        $ruangan->delete();
        return $this->success(null, 'Ruangan berhasil dihapus');
    }
}

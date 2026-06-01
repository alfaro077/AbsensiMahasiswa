<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollmentRequest;
use App\Models\Enrollment;
use App\Traits\ApiResponse;
use App\Traits\Filterable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    use ApiResponse, Filterable;

    /**
     * GET /api/enrollment
     */
    public function index(Request $request): JsonResponse
    {
        $query = Enrollment::query();

        if ($request->filled('include')) {
            $allowed = ['mahasiswa', 'mataKuliah', 'mahasiswa.user'];
            $includes = array_intersect(explode(',', $request->include), $allowed);
            $query->with($includes);
        }

        $result = $this->applyFilters(
            query: $query,
            request: $request,
            filterableFields: ['mahasiswa_id', 'mata_kuliah_id', 'tahun_ajaran'],
            searchableFields: ['tahun_ajaran'],
            sortableFields: ['id', 'mahasiswa_id', 'mata_kuliah_id', 'tahun_ajaran'],
        );

        return $this->success($result, 'Data enrollment berhasil diambil');
    }

    /**
     * POST /api/enrollment
     */
    public function store(EnrollmentRequest $request): JsonResponse
    {
        $enrollment = Enrollment::create($request->validated());
        $enrollment->load(['mahasiswa', 'mataKuliah']);
        return $this->created($enrollment, 'Enrollment berhasil dibuat');
    }

    /**
     * GET /api/enrollment/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $query = Enrollment::query();

        if ($request->filled('include')) {
            $allowed = ['mahasiswa', 'mataKuliah', 'mahasiswa.user'];
            $includes = array_intersect(explode(',', $request->include), $allowed);
            $query->with($includes);
        }

        $enrollment = $query->find($id);

        if (!$enrollment) {
            return $this->notFound('Enrollment tidak ditemukan');
        }

        return $this->success($enrollment, 'Detail enrollment berhasil diambil');
    }

    /**
     * PUT /api/enrollment/{id}
     */
    public function update(EnrollmentRequest $request, int $id): JsonResponse
    {
        $enrollment = Enrollment::find($id);

        if (!$enrollment) {
            return $this->notFound('Enrollment tidak ditemukan');
        }

        $enrollment->update($request->validated());
        $enrollment->load(['mahasiswa', 'mataKuliah']);
        return $this->success($enrollment, 'Enrollment berhasil diperbarui');
    }

    /**
     * DELETE /api/enrollment/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $enrollment = Enrollment::find($id);

        if (!$enrollment) {
            return $this->notFound('Enrollment tidak ditemukan');
        }

        $enrollment->delete();
        return $this->success(null, 'Enrollment berhasil dihapus');
    }
}

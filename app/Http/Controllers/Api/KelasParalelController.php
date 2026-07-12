<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KelasParalel;
use App\Traits\ApiResponse;
use App\Traits\Filterable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelasParalelController extends Controller
{
    use ApiResponse, Filterable;

    public function index(Request $request): JsonResponse
    {
        $query = KelasParalel::query();

        if ($request->filled('include')) {
            $allowed = ['mataKuliah', 'dosen', 'dosen.user'];
            $includes = array_intersect(explode(',', $request->include), $allowed);
            $query->with($includes);
        }

        $query->withCount('jadwal');

        $result = $this->applyFilters(
            query: $query,
            request: $request,
            filterableFields: ['mata_kuliah_id', 'dosen_id', 'tahun_ajaran'],
            searchableFields: ['nama_kelas'],
            sortableFields: ['id', 'mata_kuliah_id', 'nama_kelas', 'dosen_id', 'tahun_ajaran'],
        );

        return $this->success($result, 'Data kelas paralel berhasil diambil');
    }

    public function store(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return $this->error('Anda tidak memiliki izin untuk menambah kelas paralel', 403);
        }

        $validated = $request->validate([
            'mata_kuliah_id' => 'required|integer|exists:mata_kuliah,id',
            'nama_kelas'     => 'required|string|max:20',
            'dosen_id'       => 'nullable|integer|exists:dosen,id',
            'tahun_ajaran'   => 'nullable|string|max:20',
        ]);

        $exists = KelasParalel::where('mata_kuliah_id', $validated['mata_kuliah_id'])
            ->where('nama_kelas', $validated['nama_kelas'])
            ->exists();

        if ($exists) {
            return $this->validationError([
                'nama_kelas' => "Kelas {$validated['nama_kelas']} sudah ada untuk mata kuliah ini."
            ]);
        }

        $kelas = KelasParalel::create($validated);
        $kelas->load(['mataKuliah', 'dosen.user']);
        return $this->created($kelas, 'Kelas paralel berhasil ditambahkan');
    }

    public function show(int $id): JsonResponse
    {
        $kelas = KelasParalel::with(['mataKuliah', 'dosen.user'])->find($id);
        if (!$kelas) {
            return $this->notFound('Kelas paralel tidak ditemukan');
        }
        return $this->success($kelas, 'Detail kelas paralel berhasil diambil');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return $this->error('Anda tidak memiliki izin untuk mengubah kelas paralel', 403);
        }

        $kelas = KelasParalel::find($id);
        if (!$kelas) {
            return $this->notFound('Kelas paralel tidak ditemukan');
        }

        $validated = $request->validate([
            'mata_kuliah_id' => 'sometimes|required|integer|exists:mata_kuliah,id',
            'nama_kelas'     => 'sometimes|required|string|max:20',
            'dosen_id'       => 'nullable|integer|exists:dosen,id',
            'tahun_ajaran'   => 'nullable|string|max:20',
        ]);

        if (isset($validated['nama_kelas']) && $validated['nama_kelas'] !== $kelas->nama_kelas) {
            $exists = KelasParalel::where('mata_kuliah_id', $validated['mata_kuliah_id'] ?? $kelas->mata_kuliah_id)
                ->where('nama_kelas', $validated['nama_kelas'])
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return $this->validationError([
                    'nama_kelas' => "Kelas {$validated['nama_kelas']} sudah ada untuk mata kuliah ini."
                ]);
            }
        }

        $kelas->update($validated);
        $kelas->load(['mataKuliah', 'dosen.user']);
        return $this->success($kelas, 'Kelas paralel berhasil diperbarui');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return $this->error('Anda tidak memiliki izin untuk menghapus kelas paralel', 403);
        }

        $kelas = KelasParalel::find($id);
        if (!$kelas) {
            return $this->notFound('Kelas paralel tidak ditemukan');
        }
        $kelas->delete();
        return $this->success(null, 'Kelas paralel berhasil dihapus');
    }
}

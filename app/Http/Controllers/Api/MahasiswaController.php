<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MahasiswaRequest;
use App\Models\Mahasiswa;
use App\Traits\ApiResponse;
use App\Traits\Filterable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class MahasiswaController extends Controller
{
    use ApiResponse, Filterable;

    /**
     * GET /api/mahasiswa
     */
    public function index(Request $request): JsonResponse
    {
        $query = Mahasiswa::query();

        if ($request->filled('include')) {
            $allowed = ['user', 'enrollments', 'mataKuliah', 'presensi', 'jurusan'];
            $includes = array_intersect(explode(',', $request->include), $allowed);
            $query->with($includes);
        }

        $result = $this->applyFilters(
            query: $query,
            request: $request,
            filterableFields: ['user_id', 'jurusan_id', 'angkatan'],
            searchableFields: ['nim'],
            sortableFields: ['id', 'nim', 'angkatan'],
        );

        return $this->success($result, 'Data mahasiswa berhasil diambil');
    }

    /**
     * POST /api/mahasiswa
     */
    public function store(MahasiswaRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();
            
            // 1. Create User
            $user = User::create([
                'nama'     => $data['nama'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => 'mahasiswa',
            ]);

            // 2. Create Mahasiswa
            $mahasiswa = Mahasiswa::create([
                'user_id'    => $user->id,
                'nim'        => $data['nim'],
                'jurusan_id' => $data['jurusan_id'],
                'angkatan'   => $data['angkatan'] ?? null,
            ]);

            $mahasiswa->load('user');
            return $this->created($mahasiswa, 'Mahasiswa dan Akun User berhasil dibuat');
        });
    }

    /**
     * GET /api/mahasiswa/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $query = Mahasiswa::query();

        if ($request->filled('include')) {
            $allowed = ['user', 'enrollments', 'mataKuliah', 'presensi', 'jurusan'];
            $includes = array_intersect(explode(',', $request->include), $allowed);
            $query->with($includes);
        }

        $mahasiswa = $query->find($id);

        if (!$mahasiswa) {
            return $this->notFound('Mahasiswa tidak ditemukan');
        }

        return $this->success($mahasiswa, 'Detail mahasiswa berhasil diambil');
    }

    /**
     * PUT /api/mahasiswa/{id}
     */
    public function update(MahasiswaRequest $request, int $id): JsonResponse
    {
        $mahasiswa = Mahasiswa::with('user')->find($id);

        if (!$mahasiswa) {
            return $this->notFound('Mahasiswa tidak ditemukan');
        }

        return DB::transaction(function () use ($request, $mahasiswa) {
            $data = $request->validated();

            // 1. Update User
            $userData = [
                'nama'  => $data['nama'],
                'email' => $data['email'],
            ];
            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }
            $mahasiswa->user->update($userData);

            // 2. Update Mahasiswa
            $mahasiswa->update([
                'nim'        => $data['nim'],
                'jurusan_id' => $data['jurusan_id'],
                'angkatan'   => $data['angkatan'],
            ]);

            $mahasiswa->load('user');
            return $this->success($mahasiswa, 'Data Mahasiswa dan Akun berhasil diperbarui');
        });
    }

    /**
     * DELETE /api/mahasiswa/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $mahasiswa = Mahasiswa::find($id);

        if (!$mahasiswa) {
            return $this->notFound('Mahasiswa tidak ditemukan');
        }

        $mahasiswa->delete();
        return $this->success(null, 'Mahasiswa berhasil dihapus');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DosenRequest;
use App\Models\Dosen;
use App\Traits\ApiResponse;
use App\Traits\Filterable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DosenController extends Controller
{
    use ApiResponse, Filterable;

    /**
     * GET /api/dosen
     */
    public function index(Request $request): JsonResponse
    {
        $query = Dosen::query();

        if ($request->filled('include')) {
            $allowed = ['user', 'mataKuliah', 'jurusan'];
            $includes = array_intersect(explode(',', $request->include), $allowed);
            $query->with($includes);
        }

        $result = $this->applyFilters(
            query: $query,
            request: $request,
            filterableFields: ['user_id', 'jabatan', 'jurusan_id'],
            searchableFields: ['nip', 'jabatan'],
            sortableFields: ['id', 'nip', 'jabatan'],
        );

        return $this->success($result, 'Data dosen berhasil diambil');
    }

    /**
     * POST /api/dosen
     */
    public function store(DosenRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();
            
            // 1. Create User
            $user = User::create([
                'nama'     => $data['nama'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => 'dosen',
            ]);

            // 2. Create Dosen
            $dosen = Dosen::create([
                'user_id'    => $user->id,
                'jurusan_id' => $data['jurusan_id'],
                'nip'        => $data['nip'],
                'jabatan'    => $data['jabatan'] ?? null,
            ]);

            $dosen->load('user');
            return $this->created($dosen, 'Dosen dan Akun User berhasil dibuat');
        });
    }

    /**
     * GET /api/dosen/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $query = Dosen::query();

        if ($request->filled('include')) {
            $allowed = ['user', 'mataKuliah', 'jurusan'];
            $includes = array_intersect(explode(',', $request->include), $allowed);
            $query->with($includes);
        }

        $dosen = $query->find($id);

        if (!$dosen) {
            return $this->notFound('Dosen tidak ditemukan');
        }

        return $this->success($dosen, 'Detail dosen berhasil diambil');
    }

    /**
     * PUT /api/dosen/{id}
     */
    public function update(DosenRequest $request, int $id): JsonResponse
    {
        $dosen = Dosen::with('user')->find($id);

        if (!$dosen) {
            return $this->notFound('Dosen tidak ditemukan');
        }

        return DB::transaction(function () use ($request, $dosen) {
            $data = $request->validated();

            // 1. Update User
            $userData = [
                'nama'  => $data['nama'],
                'email' => $data['email'],
            ];
            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }
            $dosen->user->update($userData);

            // 2. Update Dosen
            $dosen->update([
                'nip'        => $data['nip'],
                'jabatan'    => $data['jabatan'],
                'jurusan_id' => $data['jurusan_id'],
            ]);

            $dosen->load('user');
            return $this->success($dosen, 'Data Dosen dan Akun berhasil diperbarui');
        });
    }

    /**
     * DELETE /api/dosen/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $dosen = Dosen::find($id);

        if (!$dosen) {
            return $this->notFound('Dosen tidak ditemukan');
        }

        $dosen->delete();
        return $this->success(null, 'Dosen berhasil dihapus');
    }
}

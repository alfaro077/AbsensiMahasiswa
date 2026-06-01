<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Traits\Filterable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse, Filterable;

    /**
     * GET /api/users
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        // Eager load relationships
        if ($request->filled('include')) {
            $allowed = ['dosen', 'mahasiswa'];
            $includes = array_intersect(explode(',', $request->include), $allowed);
            $query->with($includes);
        }

        $result = $this->applyFilters(
            query: $query,
            request: $request,
            filterableFields: ['role'],
            searchableFields: ['nama', 'email'],
            sortableFields: ['id', 'nama', 'email', 'role', 'created_at'],
        );

        return $this->success($result, 'Data user berhasil diambil');
    }

    /**
     * POST /api/users
     */
    public function store(UserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());
        return $this->created($user, 'User berhasil dibuat');
    }

    /**
     * GET /api/users/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $query = User::query();

        if ($request->filled('include')) {
            $allowed = ['dosen', 'mahasiswa'];
            $includes = array_intersect(explode(',', $request->include), $allowed);
            $query->with($includes);
        }

        $user = $query->find($id);

        if (!$user) {
            return $this->notFound('User tidak ditemukan');
        }

        return $this->success($user, 'Detail user berhasil diambil');
    }

    /**
     * PUT /api/users/{id}
     */
    public function update(UserRequest $request, int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return $this->notFound('User tidak ditemukan');
        }

        $data = $request->validated();

        // Only update password if provided
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        return $this->success($user, 'User berhasil diperbarui');
    }

    /**
     * DELETE /api/users/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return $this->notFound('User tidak ditemukan');
        }

        $user->delete();
        return $this->success(null, 'User berhasil dihapus');
    }
}

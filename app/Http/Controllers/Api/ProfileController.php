<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/profile
     * Mengambil detail profil user yang sedang login beserta data spesifik rolenya.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load(['mahasiswa.jurusan', 'dosen.jurusan']);
        return $this->success($user, 'Profil berhasil diambil');
    }

    /**
     * PUT /api/profile
     * Memperbarui nama, email, dan password milik user sendiri.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:100',
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $user->nama = $data['nama'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return $this->success(
            $user->load(['mahasiswa.jurusan', 'dosen.jurusan']), 
            'Profil berhasil diperbarui'
        );
    }
}

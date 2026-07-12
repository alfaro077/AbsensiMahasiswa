<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    use ApiResponse;
    /**
     * Endpoint Pendaftaran Pengguna (Register).
     *
     * Digunakan untuk mendaftar sebagai Mahasiswa, Dosen, atau Admin.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['dosen', 'mahasiswa'])],
            
            // Aturan spesifik untuk Mahasiswa
            'nim' => 'required_if:role,mahasiswa|unique:mahasiswa,nim|max:20',
            'jurusan_id' => 'required_if:role,mahasiswa|exists:jurusan,id',
            'angkatan' => 'required_if:role,mahasiswa|integer|digits:4',

            // Aturan spesifik untuk Dosen
            'nip' => 'required_if:role,dosen|unique:dosen,nip|max:50',
            'jabatan' => 'nullable|string|max:100',
            'jurusan_id' => 'required_if:role,dosen|exists:jurusan,id',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors(), 'Validasi gagal');
        }

        // Terapkan transaksi database untuk memastikan keutuhan data (User + Profil)
        \DB::beginTransaction();
        try {
            // Pembuatan User
            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            // Pembuatan Profil Spesifik Berdasarkan Role
            if ($request->role === 'mahasiswa') {
                Mahasiswa::create([
                    'user_id' => $user->id,
                    'nim' => $request->nim,
                    'jurusan_id' => $request->jurusan_id,
                    'angkatan' => $request->angkatan,
                ]);
            } elseif ($request->role === 'dosen') {
                Dosen::create([
                    'user_id' => $user->id,
                    'nip' => $request->nip,
                    'jabatan' => $request->jabatan,
                    'jurusan_id' => $request->jurusan_id,
                ]);
            }

            \DB::commit();

            // Opsional: Langsung buat token persis setelah register
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->error('Registration failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Endpoint Autentikasi Pengguna (Login).
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors(), 'Validasi gagal');
        }

        $user = User::with(['mahasiswa', 'dosen'])->where('email', $request->email)->first();

        // Validasi kredensial (email dan password)
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->error('Email atau password salah', 401);
        }

        // Generate Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 200);
    }
}

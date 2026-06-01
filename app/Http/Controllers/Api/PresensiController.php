<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PresensiRequest;
use App\Models\Presensi;
use App\Traits\ApiResponse;
use App\Traits\Filterable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    use ApiResponse, Filterable;

    /**
     * GET /api/presensi
     */
    public function index(Request $request): JsonResponse
    {
        $query = Presensi::query();

        if ($request->filled('include')) {
            $includes = explode(',', $request->include);
            $query->with($includes);
        }

        $result = $this->applyFilters(
            query: $query,
            request: $request,
            filterableFields: ['sesi_id', 'mahasiswa_id', 'metode', 'status'],
            searchableFields: ['keterangan'],
            sortableFields: ['id', 'sesi_id', 'mahasiswa_id', 'waktu_absen', 'metode', 'status'],
        );

        return $this->success($result, 'Data presensi berhasil diambil');
    }

    /**
     * POST /api/presensi
     */
    public function store(PresensiRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        // 1. Check if session is active
        $sesi = \App\Models\SesiKuliah::find($validated['sesi_id']);
        if (!$sesi) {
            return $this->notFound('Sesi kuliah tidak ditemukan');
        }
        
        if (!$sesi->is_active) {
            return $this->error('Sesi kuliah ini sudah tidak aktif / sudah ditutup.', 422);
        }

        // Validasi waktu untuk absensi mandiri mahasiswa (bukan input manual dosen)
        if (in_array($validated['metode'], ['qr', 'kode_unik'])) {
            $jakartaNow = now('Asia/Jakarta');
            $today = $jakartaNow->toDateString();
            $nowTime = $jakartaNow->format('H:i:s');
            
            // Cek Tanggal
            if ($sesi->tanggal->toDateString() !== $today) {
                return $this->error('Sesi kuliah ini bukan untuk hari ini.', 422);
            }
            
            // Cek Jam Mulai
            if ($nowTime < $sesi->jam_mulai) {
                return $this->error('Sesi absensi perkuliahan belum dimulai.', 422);
            }
            
            // Cek Jam Selesai
            if ($nowTime > $sesi->jam_selesai) {
                return $this->error('Batas waktu absensi mandiri telah berakhir. Silakan hubungi Dosen Anda.', 422);
            }
        }

        // 2. Check for duplicate attendance
        $exists = Presensi::where('sesi_id', $validated['sesi_id'])
            ->where('mahasiswa_id', $validated['mahasiswa_id'])
            ->exists();
            
        if ($exists) {
            return $this->error('Anda sudah melakukan absensi untuk sesi ini.', 422);
        }

        // 3. Set default values
        if (!isset($validated['waktu_absen'])) {
            $validated['waktu_absen'] = now();
        }

        $presensi = Presensi::create($validated);
        $presensi->load(['sesiKuliah', 'mahasiswa']);
        
        return $this->created($presensi, 'Presensi berhasil dicatat');
    }

    /**
     * GET /api/presensi/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $query = Presensi::query();

        if ($request->filled('include')) {
            $includes = explode(',', $request->include);
            $query->with($includes);
        }

        $presensi = $query->find($id);

        if (!$presensi) {
            return $this->notFound('Presensi tidak ditemukan');
        }

        return $this->success($presensi, 'Detail presensi berhasil diambil');
    }

    /**
     * PUT /api/presensi/{id}
     */
    public function update(PresensiRequest $request, int $id): JsonResponse
    {
        $presensi = Presensi::find($id);

        if (!$presensi) {
            return $this->notFound('Presensi tidak ditemukan');
        }

        $presensi->update($request->validated());
        $presensi->load(['sesiKuliah', 'mahasiswa']);
        return $this->success($presensi, 'Presensi berhasil diperbarui');
    }

    /**
     * DELETE /api/presensi/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $presensi = Presensi::find($id);

        if (!$presensi) {
            return $this->notFound('Presensi tidak ditemukan');
        }

        $presensi->delete();
        return $this->success(null, 'Presensi berhasil dihapus');
    }

    /**
     * GET /api/laporan/presensi
     */
    public function report(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $tahunAjaran = $request->input('tahun_ajaran');
        $mataKuliahId = $request->input('mata_kuliah_id');
        $jurusanId = $request->input('jurusan_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $query = \App\Models\Enrollment::query()
            ->with(['mahasiswa.user', 'mahasiswa.jurusan', 'mataKuliah.dosen.user']);
            
        if ($user && $user->role === 'dosen') {
            $dosenId = $user->dosen?->id ?? 0;
            $query->whereHas('mataKuliah', function ($q) use ($dosenId) {
                $q->where('dosen_id', $dosenId);
            });
        }
        
        if ($tahunAjaran) {
            $query->where('tahun_ajaran', $tahunAjaran);
        }
        if ($mataKuliahId) {
            $query->where('mata_kuliah_id', $mataKuliahId);
        }
        if ($jurusanId) {
            $query->whereHas('mahasiswa', function ($q) use ($jurusanId) {
                $q->where('jurusan_id', $jurusanId);
            });
        }
        
        $enrollments = $query->get();
        $reportData = [];
        
        foreach ($enrollments as $enrollment) {
            $mahasiswaId = $enrollment->mahasiswa_id;
            $mkId = $enrollment->mata_kuliah_id;
            
            $sessionsQuery = \App\Models\SesiKuliah::where('mata_kuliah_id', $mkId);
            if ($startDate) {
                $sessionsQuery->where('tanggal', '>=', $startDate);
            }
            if ($endDate) {
                $sessionsQuery->where('tanggal', '<=', $endDate);
            }
            
            $sessionIds = $sessionsQuery->pluck('id');
            $totalSessions = count($sessionIds);
            
            $presensiStats = \App\Models\Presensi::whereIn('sesi_id', $sessionIds)
                ->where('mahasiswa_id', $mahasiswaId)
                ->selectRaw("status, count(*) as count")
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
                
            $hadir = $presensiStats['hadir'] ?? 0;
            $izin = $presensiStats['izin'] ?? 0;
            $sakit = $presensiStats['sakit'] ?? 0;
            $pendingIzin = $presensiStats['pending_izin'] ?? 0;
            $pendingSakit = $presensiStats['pending_sakit'] ?? 0;
            $explicitAlpha = $presensiStats['alpha'] ?? 0;
            
            $totalIzin = $izin + $pendingIzin;
            $totalSakit = $sakit + $pendingSakit;
            
            $totalPresensiCount = \App\Models\Presensi::whereIn('sesi_id', $sessionIds)
                ->where('mahasiswa_id', $mahasiswaId)
                ->count();
                
            $alpha = max(0, $totalSessions - $totalPresensiCount) + $explicitAlpha;
            
            $presentCount = $hadir + $totalIzin + $totalSakit;
            $percentage = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100, 1) : 100;
            
            $reportData[] = [
                'mahasiswa_id' => $mahasiswaId,
                'nim' => $enrollment->mahasiswa?->nim ?? 'N/A',
                'nama_mahasiswa' => $enrollment->mahasiswa?->user?->nama ?? 'N/A',
                'jurusan' => $enrollment->mahasiswa?->jurusan?->nama ?? 'N/A',
                'mata_kuliah' => $enrollment->mataKuliah?->nama ?? 'N/A',
                'kode_mk' => $enrollment->mataKuliah?->kode ?? 'N/A',
                'tahun_ajaran' => $enrollment->tahun_ajaran,
                'total_sesi' => $totalSessions,
                'hadir' => $hadir,
                'izin' => $totalIzin,
                'sakit' => $totalSakit,
                'alpha' => $alpha,
                'persentase' => $percentage
            ];
        }
        
        $summary = [
            'total_students' => count($reportData),
            'avg_attendance' => count($reportData) > 0 ? round(array_sum(array_column($reportData, 'persentase')) / count($reportData), 1) : 0,
            'total_hadir' => array_sum(array_column($reportData, 'hadir')),
            'total_izin' => array_sum(array_column($reportData, 'izin')),
            'total_sakit' => array_sum(array_column($reportData, 'sakit')),
            'total_alpha' => array_sum(array_column($reportData, 'alpha')),
        ];
        
        return $this->success([
            'summary' => $summary,
            'details' => $reportData
        ], 'Laporan rekapitulasi presensi berhasil dibuat');
    }
}

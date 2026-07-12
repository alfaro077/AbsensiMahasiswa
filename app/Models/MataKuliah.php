<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliah';

    protected $fillable = [
        'jurusan_id',
        'kode',
        'nama',
        'sks',
        'semester',
        'dosen_id',
    ];

    protected $appends = ['sudah_terjadwal'];

    public $timestamps = false;

    public function getSudahTerjadwalAttribute(): bool
    {
        $total = $this->total_kelas ?? 0;
        $terjadwal = $this->terjadwal_kelas ?? 0;
        return $total > 0 && $total === $terjadwal;
    }

    // ─── Relationships ──────────────────────────────────

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function sesiKuliah(): HasMany
    {
        return $this->hasMany(SesiKuliah::class, 'mata_kuliah_id');
    }

    public function mahasiswa(): BelongsToMany
    {
        return $this->belongsToMany(Mahasiswa::class, 'enrollment', 'mata_kuliah_id', 'mahasiswa_id')
                    ->withPivot('tahun_ajaran');
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(JadwalMataKuliah::class, 'mata_kuliah_id');
    }

    public function kelasParalel(): HasMany
    {
        return $this->hasMany(KelasParalel::class, 'mata_kuliah_id');
    }
}

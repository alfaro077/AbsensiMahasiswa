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

    public $timestamps = false;

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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KelasParalel extends Model
{
    protected $table = 'kelas_paralel';

    protected $fillable = [
        'mata_kuliah_id',
        'nama_kelas',
        'dosen_id',
        'tahun_ajaran',
    ];

    protected $appends = ['sudah_terjadwal'];

    public function getSudahTerjadwalAttribute(): bool
    {
        return ($this->jadwal_count ?? 0) > 0;
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(JadwalMataKuliah::class, 'kelas_paralel_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'kelas_paralel_id');
    }

    public function mahasiswa()
    {
        return $this->belongsToMany(Mahasiswa::class, 'enrollment', 'kelas_paralel_id', 'mahasiswa_id')
                    ->withPivot('tahun_ajaran');
    }
}

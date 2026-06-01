<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';

    protected $fillable = [
        'user_id',
        'nim',
        'jurusan_id',
        'angkatan',
    ];

    public $timestamps = false;

    // ─── Relationships ──────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'mahasiswa_id');
    }

    public function mataKuliah(): BelongsToMany
    {
        return $this->belongsToMany(MataKuliah::class, 'enrollment', 'mahasiswa_id', 'mata_kuliah_id')
                    ->withPivot('tahun_ajaran');
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'mahasiswa_id');
    }
}

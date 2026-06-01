<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    protected $table = 'presensi';

    protected $fillable = [
        'sesi_id',
        'mahasiswa_id',
        'waktu_absen',
        'metode',
        'status',
        'keterangan',
    ];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'waktu_absen' => 'datetime',
        ];
    }

    // ─── Relationships ──────────────────────────────────

    public function sesiKuliah(): BelongsTo
    {
        return $this->belongsTo(SesiKuliah::class, 'sesi_id');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
}

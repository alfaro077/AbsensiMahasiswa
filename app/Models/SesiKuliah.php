<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiKuliah extends Model
{
    protected $table = 'sesi_kuliah';

    protected $fillable = [
        'mata_kuliah_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'topik',
        'gedung',
        'lantai',
        'ruangan',
        'qr_code',
        'kode_unik',
        'kode_expires_at',
        'is_active',
    ];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'tanggal'         => 'date',
            'kode_expires_at' => 'datetime',
            'is_active'       => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'sesi_id');
    }
}

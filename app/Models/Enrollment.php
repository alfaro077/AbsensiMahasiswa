<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    protected $table = 'enrollment';

    protected $fillable = [
        'mahasiswa_id',
        'mata_kuliah_id',
        'kelas_paralel_id',
        'tahun_ajaran',
    ];

    public $timestamps = false;

    // ─── Relationships ──────────────────────────────────

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function kelasParalel(): BelongsTo
    {
        return $this->belongsTo(KelasParalel::class, 'kelas_paralel_id');
    }
}

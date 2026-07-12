<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalMataKuliah extends Model
{
    protected $table = 'jadwal_mata_kuliah';

    protected $fillable = [
        'mata_kuliah_id',
        'kelas_paralel_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'gedung_id',
        'ruangan_id',
    ];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function kelasParalel(): BelongsTo
    {
        return $this->belongsTo(KelasParalel::class, 'kelas_paralel_id');
    }

    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Ruangan::class, 'ruangan_id');
    }
}

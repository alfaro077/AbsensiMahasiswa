<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalMataKuliah extends Model
{
    protected $table = 'jadwal_mata_kuliah';

    protected $fillable = [
        'mata_kuliah_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'gedung_id',
        'lantai',
        'ruangan',
    ];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }
}

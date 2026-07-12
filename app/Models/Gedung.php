<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gedung extends Model
{
    protected $table = 'gedung';

    protected $fillable = [
        'kode',
        'nama',
        'lokasi',
    ];

    public function jadwal(): HasMany
    {
        return $this->hasMany(JadwalMataKuliah::class, 'gedung_id');
    }

    public function ruangan(): HasMany
    {
        return $this->hasMany(Ruangan::class, 'gedung_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ruangan extends Model
{
    protected $table = 'ruangan';

    protected $fillable = [
        'gedung_id',
        'nama',
        'lantai',
        'kapasitas',
    ];

    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }
}

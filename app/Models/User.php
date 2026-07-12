<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'telegram_chat_id',
        'telegram_token',
    ];

    protected $hidden = [
        'password',
    ];

    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'password'   => 'hashed',
            'created_at' => 'datetime',
        ];
    }

    // ─── Relationships ──────────────────────────────────

    public function dosen(): HasOne
    {
        return $this->hasOne(Dosen::class, 'user_id');
    }

    public function mahasiswa(): HasOne
    {
        return $this->hasOne(Mahasiswa::class, 'user_id');
    }
}

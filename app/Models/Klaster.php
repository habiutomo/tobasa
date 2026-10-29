<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Klaster extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'warna', 'urutan', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    public function polis(): HasMany
    {
        return $this->hasMany(Poli::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poli extends Model
{
    use HasFactory;

    protected $fillable = ['klaster_id', 'nama', 'prefix', 'warna', 'deskripsi', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function klaster(): BelongsTo
    {
        return $this->belongsTo(Klaster::class);
    }

    public function antrians(): HasMany
    {
        return $this->hasMany(Antrian::class);
    }

    public function lokets(): HasMany
    {
        return $this->hasMany(Loket::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loket extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'poli_id', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    public function antrians(): HasMany
    {
        return $this->hasMany(Antrian::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
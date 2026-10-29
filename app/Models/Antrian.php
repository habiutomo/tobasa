<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Antrian extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'poli_id',
        'nomor',
        'no_antrian',
        'nama_pasien',
        'status',
        'loket_id',
        'called_at',
        'finished_at',
        'skipped_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'called_at' => 'datetime',
        'finished_at' => 'datetime',
        'skipped_at' => 'datetime',
    ];

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    public function loket(): BelongsTo
    {
        return $this->belongsTo(Loket::class);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('tanggal', today());
    }
}
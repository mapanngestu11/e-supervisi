<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstrumentAspek extends Model
{
    protected $fillable = [
        'instrument_id',
        'judul',
        'urutan',
    ];

    public function instrument(): BelongsTo
    {
        return $this->belongsTo(Instrument::class);
    }

    public function indikators(): HasMany
    {
        return $this->hasMany(InstrumentIndikator::class)->orderBy('urutan');
    }

    public function letterLabel(): string
    {
        $index = static::query()
            ->where('instrument_id', $this->instrument_id)
            ->where('urutan', '<', $this->urutan)
            ->count();

        return chr(65 + $index).'.';
    }
}

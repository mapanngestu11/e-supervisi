<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstrumentIndikator extends Model
{
    protected $fillable = [
        'instrument_aspek_id',
        'deskripsi',
        'urutan',
    ];

    public function aspek(): BelongsTo
    {
        return $this->belongsTo(InstrumentAspek::class, 'instrument_aspek_id');
    }
}

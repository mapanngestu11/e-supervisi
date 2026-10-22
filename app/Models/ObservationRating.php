<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservationRating extends Model
{
    protected $fillable = [
        'guru_id',
        'guru_uuid',
        'instrument_indikator_id',
        'nilai',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_uuid');
    }

    public function indikator(): BelongsTo
    {
        return $this->belongsTo(InstrumentIndikator::class, 'instrument_indikator_id');
    }
}

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

    protected static function booted(): void
    {
        static::saving(function (self $model) {
            if ($model->guru_id && ! $model->guru_uuid) {
                $model->guru_uuid = Guru::query()->whereKey($model->guru_id)->value('uuid');
            }
        });
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function indikator(): BelongsTo
    {
        return $this->belongsTo(InstrumentIndikator::class, 'instrument_indikator_id');
    }
}

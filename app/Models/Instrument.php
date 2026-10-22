<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instrument extends Model
{
    protected $fillable = [
        'judul',
        'urutan',
    ];

    public function aspeks(): HasMany
    {
        return $this->hasMany(InstrumentAspek::class)->orderBy('urutan');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanAnalysis extends Model
{
    protected $fillable = [
        'guru_id',
        'guru_uuid',
        'scores',
        'saran_instrumen',
        'saran_instrumen_1',
        'saran_instrumen_2',
        'saran_instrumen_3',
        'saran_instrumen_4',
        'kesimpulan_akhir',
    ];

    protected function casts(): array
    {
        return [
            'scores' => 'array',
            'saran_instrumen' => 'array',
        ];
    }

    public function getSaranForInstrument($instrumentId): ?string
    {
        // Try JSON column first
        if ($this->saran_instrumen && isset($this->saran_instrumen[$instrumentId])) {
            return $this->saran_instrumen[$instrumentId];
        }

        // Fallback to legacy columns
        return $this->{'saran_instrumen_' . $instrumentId} ?? null;
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_uuid');
    }
}

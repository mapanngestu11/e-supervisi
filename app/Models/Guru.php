<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Guru extends Model
{
    use HasUuids;

    protected $fillable = [
        'nama_lengkap',
        'nip',
        'jenis_kelamin',
        'tugas_utama',
        'status_sertifikasi',
        'mata_pelajaran',
        'atribut_tambahan',
    ];

    protected function casts(): array
    {
        return [
            'atribut_tambahan' => 'array',
        ];
    }

    public function sertifikasiLabel(): string
    {
        return str_contains(strtolower($this->status_sertifikasi), 'belum') ? 'BELUM' : 'SUDAH';
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstitusiProfile extends Model
{
    protected $fillable = [
        'judul_laporan',
        'nama_instansi',
        'nama_supervisor',
        'nip_supervisor',
        'nama_kepala_sekolah',
        'nip_kepala_sekolah',
        'alamat_instansi',
        'kontak_wa',
        'email_dinas',
    ];
}

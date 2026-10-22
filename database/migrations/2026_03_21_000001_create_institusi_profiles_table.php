<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institusi_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('judul_laporan')->nullable();
            $table->string('nama_instansi')->nullable();
            $table->string('nama_supervisor')->nullable();
            $table->string('nip_supervisor')->nullable();
            $table->string('nama_kepala_sekolah')->nullable();
            $table->string('nip_kepala_sekolah')->nullable();
            $table->text('alamat_instansi')->nullable();
            $table->string('kontak_wa')->nullable();
            $table->string('email_dinas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institusi_profiles');
    }
};

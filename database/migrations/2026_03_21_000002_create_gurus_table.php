<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('nip')->nullable();
            $table->string('jenis_kelamin')->default('Laki-laki');
            $table->string('tugas_utama')->nullable();
            $table->string('status_sertifikasi')->default('Belum Sertifikasi');
            $table->string('mata_pelajaran')->nullable();
            $table->json('atribut_tambahan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};

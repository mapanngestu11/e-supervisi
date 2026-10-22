<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->json('scores');
            $table->text('saran_instrumen_1')->nullable();
            $table->text('saran_instrumen_2')->nullable();
            $table->text('saran_instrumen_3')->nullable();
            $table->text('saran_instrumen_4')->nullable();
            $table->text('kesimpulan_akhir')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_analyses');
    }
};

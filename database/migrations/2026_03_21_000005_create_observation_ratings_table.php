<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observation_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('instrument_indikator_id')->constrained('instrument_indikators')->cascadeOnDelete();
            $table->unsignedTinyInteger('nilai');
            $table->timestamps();

            $table->unique(['guru_id', 'instrument_indikator_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observation_ratings');
    }
};

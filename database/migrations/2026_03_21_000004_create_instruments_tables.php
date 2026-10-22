<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instruments', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('instrument_aspeks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instrument_id')->constrained('instruments')->cascadeOnDelete();
            $table->string('judul');
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('instrument_indikators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instrument_aspek_id')->constrained('instrument_aspeks')->cascadeOnDelete();
            $table->string('deskripsi', 500);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instrument_indikators');
        Schema::dropIfExists('instrument_aspeks');
        Schema::dropIfExists('instruments');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update observation_ratings table - add guru_uuid column
        Schema::table('observation_ratings', function (Blueprint $table) {
            $table->uuid('guru_uuid')->nullable()->after('id');
        });

        // Migrate data from guru_id to guru_uuid
        \DB::table('observation_ratings')->whereNull('guru_uuid')->get()->each(function ($rating) {
            $guru = \DB::table('gurus')->where('id', $rating->guru_id)->first();
            if ($guru) {
                \DB::table('observation_ratings')->where('id', $rating->id)->update([
                    'guru_uuid' => $guru->uuid
                ]);
            }
        });

        Schema::table('observation_ratings', function (Blueprint $table) {
            $table->uuid('guru_uuid')->nullable(false)->change();
        });

        // Update laporan_analyses table - add guru_uuid column
        Schema::table('laporan_analyses', function (Blueprint $table) {
            $table->uuid('guru_uuid')->nullable()->after('id');
        });

        // Migrate data from guru_id to guru_uuid
        \DB::table('laporan_analyses')->whereNull('guru_uuid')->get()->each(function ($analysis) {
            $guru = \DB::table('gurus')->where('id', $analysis->guru_id)->first();
            if ($guru) {
                \DB::table('laporan_analyses')->where('id', $analysis->id)->update([
                    'guru_uuid' => $guru->uuid
                ]);
            }
        });

        Schema::table('laporan_analyses', function (Blueprint $table) {
            $table->uuid('guru_uuid')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('observation_ratings', function (Blueprint $table) {
            $table->dropColumn('guru_uuid');
        });

        Schema::table('laporan_analyses', function (Blueprint $table) {
            $table->dropColumn('guru_uuid');
        });
    }
};

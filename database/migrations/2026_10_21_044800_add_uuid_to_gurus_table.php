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
        Schema::table('gurus', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        // Generate UUIDs for existing records
        \DB::table('gurus')->whereNull('uuid')->get()->each(function ($guru) {
            \DB::table('gurus')->where('id', $guru->id)->update([
                'uuid' => \Illuminate\Support\Str::uuid()
            ]);
        });

        // Make it NOT NULL after populating
        Schema::table('gurus', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};

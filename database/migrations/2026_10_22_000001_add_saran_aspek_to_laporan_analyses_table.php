<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('laporan_analyses', 'saran_aspek')) {
            Schema::table('laporan_analyses', function (Blueprint $table) {
                $table->json('saran_aspek')->nullable()->after('saran_instrumen');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('laporan_analyses', 'saran_aspek')) {
            Schema::table('laporan_analyses', function (Blueprint $table) {
                $table->dropColumn('saran_aspek');
            });
        }
    }
};

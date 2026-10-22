<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var array<int, string>
     */
    private array $tables = ['observation_ratings', 'laporan_analyses'];

    /**
     * Idempotent: safe to re-run after a partially applied attempt.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasColumn($table, 'guru_uuid')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->uuid('guru_uuid')->nullable()->after('id');
                });
            }

            DB::table($table)->whereNull('guru_uuid')->get()->each(function ($row) use ($table) {
                $uuid = DB::table('gurus')->where('id', $row->guru_id)->value('uuid');
                if ($uuid) {
                    DB::table($table)->where('id', $row->id)->update(['guru_uuid' => $uuid]);
                }
            });

            if (! DB::table($table)->whereNull('guru_uuid')->exists()) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->uuid('guru_uuid')->nullable(false)->change();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'guru_uuid')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('guru_uuid');
                });
            }
        }
    }
};

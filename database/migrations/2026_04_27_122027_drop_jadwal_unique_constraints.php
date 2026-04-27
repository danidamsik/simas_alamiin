<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            if (! $this->indexExists('jadwal', 'jadwal_kelas_id_index')) {
                $table->index('kelas_id');
            }

            if (! $this->indexExists('jadwal', 'jadwal_guru_id_index')) {
                $table->index('guru_id');
            }
        });

        Schema::table('jadwal', function (Blueprint $table) {
            if ($this->indexExists('jadwal', 'jadwal_kelas_hari_session_periode_unique')) {
                $table->dropUnique('jadwal_kelas_hari_session_periode_unique');
            }

            if ($this->indexExists('jadwal', 'jadwal_guru_hari_session_periode_unique')) {
                $table->dropUnique('jadwal_guru_hari_session_periode_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }

    private function indexExists(string $table, string $index): bool
    {
        return collect(DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$index]))->isNotEmpty();
    }
};

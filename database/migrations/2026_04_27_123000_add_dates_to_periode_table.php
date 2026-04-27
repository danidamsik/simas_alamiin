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
        Schema::table('periode', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('semester');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
        });

        DB::table('periode')
            ->select('id', 'tahun_ajaran', 'semester')
            ->orderBy('id')
            ->get()
            ->each(function ($periode) {
                $dates = $this->dateRangeFor($periode->tahun_ajaran, $periode->semester);

                if ($dates) {
                    DB::table('periode')
                        ->where('id', $periode->id)
                        ->update($dates);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periode', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);
        });
    }

    /**
     * @return array{tanggal_mulai: string, tanggal_selesai: string}|null
     */
    private function dateRangeFor(string $tahunAjaran, string $semester): ?array
    {
        if (! preg_match('/^(\d{4})\D+(\d{4})$/', $tahunAjaran, $matches)) {
            return null;
        }

        if ($semester === 'ganjil') {
            return [
                'tanggal_mulai' => "{$matches[1]}-07-01",
                'tanggal_selesai' => "{$matches[1]}-12-31",
            ];
        }

        return [
            'tanggal_mulai' => "{$matches[2]}-01-01",
            'tanggal_selesai' => "{$matches[2]}-06-30",
        ];
    }
};

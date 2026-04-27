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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->index();
            $table->foreignId('kelas_id')->index()->constrained('kelas')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('guru_id')->constrained('guru')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('session_id')->constrained('sessions')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('periode_id')->index()->constrained('periode')->restrictOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            $table->unique(['tanggal', 'kelas_id', 'session_id', 'periode_id'], 'absensi_tanggal_kelas_session_periode_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};

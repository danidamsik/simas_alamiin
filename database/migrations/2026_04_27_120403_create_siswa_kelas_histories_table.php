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
        Schema::create('siswa_kelas_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('kelas_id')->constrained('kelas')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('periode_id')->constrained('periode')->restrictOnDelete()->cascadeOnUpdate();
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_kelas_history');
    }
};

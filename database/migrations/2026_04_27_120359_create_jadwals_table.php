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
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('guru_id')->constrained('guru')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('mata_pelajaran');
            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']);
            $table->foreignId('session_id')->constrained('sessions')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('periode_id')->constrained('periode')->restrictOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};

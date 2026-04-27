<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('audit_logs') && ! Schema::hasTable('audit_log')) {
            Schema::rename('audit_logs', 'audit_log');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('audit_log') && ! Schema::hasTable('audit_logs')) {
            Schema::rename('audit_log', 'audit_logs');
        }
    }
};

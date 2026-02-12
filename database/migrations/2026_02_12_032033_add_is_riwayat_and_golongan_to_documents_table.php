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
        Schema::table('documents', function (Blueprint $table) {
            $table->boolean('is_riwayat')
                  ->default(false)
                  ->after('type');

            $table->string('keterangan_riwayat')
                  ->nullable()
                  ->after('is_riwayat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['is_riwayat', 'keterangan_riwayat']);
        });
    }
};

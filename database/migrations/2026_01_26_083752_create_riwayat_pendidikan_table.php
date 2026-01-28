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
        Schema::create('riwayat_pendidikan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')
                ->constrained('pegawai')
                ->cascadeOnDelete();
            $table->string('tingkat_pendidikan', 50);
            $table->string('nama_sekolah', 150);
            $table->string('jurusan', 150)->nullable();
            $table->year('tahun_masuk')->nullable();
            $table->year('tahun_lulus');
            $table->string('nomor_ijazah', 100);
            $table->date('tanggal_ijazah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pendidikan');
    }
};

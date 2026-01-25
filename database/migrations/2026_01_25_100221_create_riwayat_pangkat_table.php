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
        Schema::create('riwayat_pangkat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')
                ->constrained('pegawai') // <- nama tabel pegawai di DB
                ->cascadeOnDelete();
            $table->foreignId('ref_id_pangkat')
                ->constrained('pangkats') // <- nama tabel pangkat di DB
                ->cascadeOnDelete();
            $table->date('tmt_pangkat');
            $table->string('nomor_sk');
            $table->date('tanggal_sk');
            $table->string('dokumen_sk')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pangkat');
    }
};

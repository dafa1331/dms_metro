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
        Schema::create('riwayat_jabatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')
                ->constrained('pegawai')
                ->cascadeOnDelete();
            $table->foreignId('jabatan_id')
                ->constrained('jabatans') // <- nama tabel pangkat di DB
                ->cascadeOnDelete();
            $table->foreignId('opd_id')
                ->constrained('opds') // <- nama tabel pangkat di DB
                ->cascadeOnDelete();
            $table->enum('jenis_jabatan', ['definitif', 'tambahan']);
            $table->foreignId('parent_jabatan_id')
                ->nullable()
                ->constrained('riwayat_jabatan')
                ->nullOnDelete();
            $table->date('tmt_mulai');
            $table->date('tmt_selesai')->nullable();
            $table->string('nomor_sk', 100)->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->index(['pegawai_id', 'jenis_jabatan']);
            $table->index(['pegawai_id', 'status_aktif']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_jabatan');
    }
};

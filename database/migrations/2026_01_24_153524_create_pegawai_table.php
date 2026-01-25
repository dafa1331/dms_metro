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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();

            $table->char('nip', 18)->unique();
            $table->string('nama_lengkap', 150);
            $table->string('gelar_depan', 30)->nullable();
            $table->string('gelar_belakang', 30)->nullable();

            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');

            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->tinyInteger('id_agama')->unsigned()->nullable();

            $table->string('email')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->text('alamat')->nullable();

            $table->enum('status_pegawai', ['PNS', 'PPPK', 'PPPKPW']);

            $table->timestamps();

            $table->foreign('id_agama')
                ->references('id_agama')
                ->on('ref_agama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};

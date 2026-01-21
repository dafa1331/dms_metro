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
        Schema::create('documents', function (Blueprint $table) {

            $table->increments('id_document');

            $table->unsignedInteger('opd_id')->index();

            $table->string('nip', 30)->nullable();

            $table->string('type', 20);

            $table->string('file_name', 255);

            $table->string('original_name', 255);

            $table->integer('size');

            $table->string('mime', 100);

            $table->dateTime('uploaded_at');

            $table->text('catatan')->nullable();

            $table->enum('status_dokumen', [
                'proses',
                'tolak',
                'terima',
            ])->default('proses');

            $table->dateTime('tanggal_verif')
                  ->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};

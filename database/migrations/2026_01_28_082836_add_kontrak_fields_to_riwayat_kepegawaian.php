<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riwayat_kepegawaian', function (Blueprint $table) {
            $table->enum('jenis_kepegawaian', ['PNS', 'PPPK', 'PPPKPW'])
                ->after('pegawai_id');

            $table->date('tmt_selesai')
                ->nullable()
                ->after('tmt_status');

            $table->unsignedTinyInteger('periode_kontrak')
                ->nullable()
                ->after('tmt_selesai')
                ->comment('Kontrak ke-1, ke-2, dst');
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_kepegawaian', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_kepegawaian',
                'tmt_selesai',
                'periode_kontrak',
            ]);
        });
    }
};

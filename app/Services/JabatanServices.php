<?php

namespace App\Services;

use App\Models\Jabatan;
use App\Models\RiwayatJabatan;
use Illuminate\Validation\ValidationException;

class JabatanService
{
    public static function assign(array $data): RiwayatJabatan
    {
        $jabatan = Jabatan::findOrFail($data['jabatan_id']);

        // 🔒 Kunci jabatan struktural
        if ($jabatan->jenis_jabatan === 'struktural') {
            $exists = RiwayatJabatan::where('jabatan_id', $jabatan->id)
                ->where('status_aktif', 1)
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'jabatan_id' => 'Jabatan struktural ini sudah diisi oleh pegawai lain.',
                ]);
            }
        }

        // 🔄 Nonaktifkan jabatan lama pegawai
        RiwayatJabatan::where('pegawai_id', $data['pegawai_id'])
            ->where('status_aktif', 1)
            ->update([
                'status_aktif' => 0,
                'tmt_selesai' => now(),
            ]);

        // ✅ Simpan jabatan baru
        return RiwayatJabatan::create([
            'pegawai_id'       => $data['pegawai_id'],
            'jabatan_id'       => $jabatan->id,
            'opd_id'           => $data['opd_id'] ?? null,
            'jenis_jabatan'    => $jabatan->jenis_jabatan,
            'parent_jabatan_id'=> $data['parent_jabatan_id'] ?? null,
            'tmt_mulai'        => $data['tmt_mulai'],
            'tmt_selesai'      => null,
            'nomor_sk'         => $data['nomor_sk'] ?? null,
            'tanggal_sk'       => $data['tanggal_sk'] ?? null,
            'status_aktif'     => 1,
            'is_struktural'    => $jabatan->jenis_jabatan === 'struktural',
        ]);
    }
}

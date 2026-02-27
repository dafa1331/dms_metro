<?php

namespace App\Imports;

use App\Models\RiwayatJabatan;
use App\Models\Pegawai;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class RiwayatJabatanImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            DB::transaction(function () use ($row) {

                $pegawai = Pegawai::where('nip', $row['nip'])->first();

                if (!$pegawai) {
                    return; // skip jika nip tidak ditemukan
                }

                // Validasi tanggal
                if (empty($row['tmt_mulai'])) {
                    return;
                }

                $tmtBaru = Carbon::parse($row['tmt_mulai']);

                $isStruktural = (int) $row['is_struktural'];
                $jenisJabatan = $row['jenis_jabatan'];

                /*
                 |--------------------------------------------------------------------------
                 | 1️⃣ NONAKTIFKAN JABATAN LAMA
                 |--------------------------------------------------------------------------
                 */

                if ($isStruktural === 1) {

                    // Jika jabatan baru struktural → tutup semua jabatan aktif
                    RiwayatJabatan::where('pegawai_id', $pegawai->id)
                        ->whereNull('tmt_selesai')
                        ->update([
                            'tmt_selesai' => $tmtBaru->copy()->subDay(),
                            'status_aktif' => 0,
                        ]);

                } else {

                    // Jika bukan struktural → tutup jabatan aktif dengan jenis sama
                    RiwayatJabatan::where('pegawai_id', $pegawai->id)
                        ->whereNull('tmt_selesai')
                        ->where('jenis_jabatan', $jenisJabatan)
                        ->update([
                            'tmt_selesai' => $tmtBaru->copy()->subDay(),
                            'status_aktif' => 0,
                        ]);
                }

                /*
                 |--------------------------------------------------------------------------
                 | 2️⃣ INSERT RIWAYAT BARU
                 |--------------------------------------------------------------------------
                 */

                RiwayatJabatan::create([
                    'pegawai_id'        => $pegawai->id,
                    'jabatan_id'        => $row['jabatan_id'],
                    'opd_id'            => $row['opd_id'],
                    'jenis_jabatan'     => $row['jenis_jabatan'],
                    'is_struktural'     => $row['jenis_jabatan'] === 'struktural' ? 1 : 0,

                    // 🔥 FIX DI SINI
                    'parent_jabatan_id' => !empty($row['parent_jabatan_id']) && $row['parent_jabatan_id'] !== 'NULL'
                                            ? (int) $row['parent_jabatan_id']
                                            : null,

                    'tmt_mulai'         => $tmtBaru,
                    'tmt_selesai'       => null,
                    'nomor_sk'          => $row['nomor_sk'] ?? null,
                    'tanggal_sk'        => !empty($row['tanggal_sk'])
                                            ? Carbon::parse($row['tanggal_sk'])
                                            : null,
                    'status_aktif'      => 1,
                ]);
            });
        }
    }
}
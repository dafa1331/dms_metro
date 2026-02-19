<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Opd;
use App\Exports\RekapFungsionalExport;
use Maatwebsite\Excel\Facades\Excel;

class RekapFungsional extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.rekap-fungsional';
    protected static ?string $navigationLabel = 'Rekap Fungsional per jenjang';
    protected static ?string $navigationGroup = 'Rekapitulasi';
    protected static ?string $title = 'Rekap Jabatan Fungsional';

    public $bulan;
    public $tahun;
    public $opd_id;
    public $data = [];
    public $jenjangList = [];

    public function mount()
    {
        $this->bulan = now()->month;
        $this->tahun = now()->year;

        $this->loadData();
    }

    public function loadData()
    {
        $awal = Carbon::create($this->tahun, $this->bulan, 1)->startOfMonth();
        $akhir = Carbon::create($this->tahun, $this->bulan, 1)->endOfMonth();

        $this->jenjangList = DB::table('jenjang_jabatans')->pluck('nama_jenjang');

        $query = DB::table('riwayat_jabatan as rk')
            ->join('jabatans as j', 'rk.jabatan_id', '=', 'j.id')
            ->join('jenjang_jabatans as jj', 'j.jenjang_jabatan_id', '=', 'jj.id')
            ->where('j.jenis_jabatan', 'fungsional')
            ->where('rk.status_aktif', 1)
            ->whereDate('rk.tmt_mulai', '<=', $akhir)
            ->where(function ($q) use ($awal) {
                $q->whereNull('rk.tmt_selesai')
                  ->orWhereDate('rk.tmt_selesai', '>=', $awal);
            });

        if ($this->opd_id) {
            $query->where('rk.opd_id', $this->opd_id);
        }

        $rows = $query->select(
                'j.nama_jabatan',
                'jj.nama_jenjang',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('j.nama_jabatan', 'jj.nama_jenjang')
            ->get();

        $this->data = $rows
            ->groupBy('nama_jabatan')
            ->map(function ($items) {
                $result = [];

                foreach ($this->jenjangList as $jenjang) {
                    $result[$jenjang] = 0;
                }

                foreach ($items as $item) {
                    $result[$item->nama_jenjang] = $item->total;
                }

                $result['total'] = array_sum($result);

                return $result;
            });
    }

    public function export()
    {
        return Excel::download(
            new RekapFungsionalExport(
                $this->data,
                $this->jenjangList,
                $this->bulan,
                $this->tahun
            ),
            'rekap-fungsional.xlsx'
        );
    }

    public function updated()
    {
        $this->loadData();
    }

    public function getOpdOptionsProperty()
    {
        return Opd::pluck('nama_opd', 'id');
    }
}

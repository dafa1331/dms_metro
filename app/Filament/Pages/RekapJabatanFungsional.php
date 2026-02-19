<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Action;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Pangkat;
use App\Models\Opd;
use App\Exports\RekapJabatanFungsionalExport;

class RekapJabatanFungsional extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static string $view = 'filament.pages.rekap-jabatan-fungsional';
    protected static ?string $navigationLabel = 'Rekap Jabatan Fungsional';
    protected static ?string $navigationGroup = 'Rekapitulasi';
    protected static ?string $title = 'Rekapitulasi Jabatan Fungsional';

    public $opd_id = null;
    public $bulan;
    public $tahun;

    public $data = [];
    public $pangkats = [];

    public function mount(): void
    {
        $this->bulan = now()->month;
        $this->tahun = now()->year;

        $this->form->fill([
            'opd_id' => $this->opd_id,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
        ]);

        $this->loadData();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('opd_id')
                    ->label('OPD')
                    ->options(Opd::pluck('nama_opd', 'id'))
                    ->searchable()
                    ->placeholder('Semua OPD'),

                Forms\Components\Select::make('bulan')
                    ->options([
                        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
                        5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
                        9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
                    ])
                    ->required(),

                Forms\Components\TextInput::make('tahun')
                    ->numeric()
                    ->required(),
            ])
            ->columns(3);
    }

    public function filter()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->pangkats = Pangkat::orderBy('golongan')->get();

        $tanggalLaporan = \Carbon\Carbon::createFromDate(
            $this->tahun,
            $this->bulan,
            1
        )->endOfMonth();

        $query = DB::table('riwayat_jabatan as rj')
            ->join('jabatans as j', 'j.id', '=', 'rj.jabatan_id')
            ->leftJoin('jenjang_jabatans as jj', 'jj.id', '=', 'j.jenjang_jabatan_id')
            ->join('riwayat_pangkat as rp', 'rp.pegawai_id', '=', 'rj.pegawai_id')
            ->join('pangkats as p', 'p.id', '=', 'rp.ref_id_pangkat')
            ->where('rj.status_aktif', 1)
            ->where('rp.is_aktif', 1)
            ->where('j.jenis_jabatan', 'fungsional')
            ->where('j.aktif', 1)
            ->where('rj.tmt_mulai', '<=', $tanggalLaporan)
            ->where(function ($q) use ($tanggalLaporan) {
                $q->whereNull('rj.tmt_selesai')
                ->orWhere('rj.tmt_selesai', '>=', $tanggalLaporan);
            });

        if ($this->opd_id) {
            $query->where('rj.opd_id', $this->opd_id);
        }

        $rawData = $query->select(
                DB::raw("
                    CONCAT(
                        j.nama_jabatan,
                        ' ',
                        COALESCE(jj.nama_jenjang, '')
                    ) as nama_jabatan_lengkap
                "),
                'p.golongan',
                DB::raw('COUNT(DISTINCT rj.pegawai_id) as total')
            )
            ->groupBy('j.nama_jabatan', 'jj.nama_jenjang', 'p.golongan')
            ->orderBy('j.nama_jabatan')
            ->orderBy('jj.nama_jenjang')
            ->get();

        $grouped = [];

        foreach ($rawData as $item) {
            $key = trim($item->nama_jabatan_lengkap);

            $grouped[$key]['nama_jabatan'] = $key;
            $grouped[$key]['counts'][$item->golongan] = $item->total;
        }

        $this->data = array_values($grouped);
}



    protected function getHeaderActions(): array
    {
        return [
            Action::make('Tampilkan')
                ->action('filter')
                ->color('primary'),

            Action::make('Export Excel')
                ->action(function () {
                    return Excel::download(
                        new RekapJabatanFungsionalExport($this->data, $this->pangkats),
                        'rekap-jabatan-fungsional.xlsx'
                    );
                })
                ->color('success'),
        ];
    }
}

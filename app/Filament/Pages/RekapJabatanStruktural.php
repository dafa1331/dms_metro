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
use App\Exports\RekapJabatanStrukturalExport;

class RekapJabatanStruktural extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static string $view = 'filament.pages.rekap-jabatan-struktural';
    protected static ?string $navigationLabel = 'Rekap Jabatan Struktural';
    protected static ?string $navigationGroup = 'Rekapitulasi';
    protected static ?string $title = 'Rekapitulasi Jabatan Struktural';

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

        // 🔹 Ambil SEMUA jabatan struktural dulu
        $allJabatan = DB::table('jabatans')
            ->where('jenis_jabatan', 'struktural')
            ->where('aktif', 1)
            ->orderBy('nama_jabatan')
            ->get();

        // 🔹 Query rekap yang ada isinya saja
        $query = DB::table('riwayat_jabatan as rj')
            ->join('jabatans as j', 'j.id', '=', 'rj.jabatan_id')
            ->join('riwayat_pangkat as rp', 'rp.pegawai_id', '=', 'rj.pegawai_id')
            ->join('pangkats as p', 'p.id', '=', 'rp.ref_id_pangkat')
            ->where('rj.status_aktif', 1)
            ->where('rj.is_struktural', 1)
            ->where('rp.is_aktif', 1)
            ->where('j.jenis_jabatan', 'struktural')
            ->whereYear('rj.tmt_mulai', '<=', $this->tahun)
            ->whereMonth('rj.tmt_mulai', '<=', $this->bulan);

        if ($this->opd_id) {
            $query->where('rj.opd_id', $this->opd_id);
        }

        $rawData = $query
            ->select(
                'j.nama_jabatan',
                'p.golongan',
                DB::raw('COUNT(DISTINCT rj.pegawai_id) as total')
            )
            ->groupBy('j.nama_jabatan', 'p.golongan')
            ->get();

        // 🔹 Ubah hasil query jadi array cepat
        $rekapMap = [];

        foreach ($rawData as $item) {
            $rekapMap[$item->nama_jabatan][$item->golongan] = $item->total;
        }

        // 🔹 Sekarang kita bentuk data FINAL berdasarkan semua jabatan
        $final = [];

        foreach ($allJabatan as $jabatan) {

            $row = [
                'nama_jabatan' => $jabatan->nama_jabatan,
                'counts' => [],
            ];

            foreach ($this->pangkats as $pangkat) {

                $row['counts'][$pangkat->golongan] =
                    $rekapMap[$jabatan->nama_jabatan][$pangkat->golongan] ?? 0;
            }

            $final[] = $row;
        }

        $this->data = $final;
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
                        new RekapJabatanStrukturalExport($this->data, $this->pangkats),
                        'rekap-jabatan-struktural.xlsx'
                    );
                })
                ->color('success'),
        ];
    }
}

<?php

namespace App\Filament\Pages;

use App\Models\Pegawai;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Filament\Forms;
use Filament\Notifications\Notification;
use App\Models\RiwayatKepegawaian;

class ProyeksiPensiun extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Proyeksi Pensiun';
    protected static ?string $navigationGroup = 'Data Kepegawaian';

    protected static string $view = 'filament.pages.proyeksi-pensiun';

    protected function getTableQuery(): Builder
{
    return Pegawai::query()
        ->select('pegawai.*')
        ->leftJoin('riwayat_jabatan', function ($join) {
            $join->on('pegawai.id', '=', 'riwayat_jabatan.pegawai_id')
                 ->where('riwayat_jabatan.status_aktif', 1);
        })
        ->leftJoin('jabatans', 'riwayat_jabatan.jabatan_id', '=', 'jabatans.id')
        ->whereNotNull('pegawai.tanggal_lahir');
}

    protected function getTableColumns(): array
{
    return [
        Tables\Columns\TextColumn::make('nip')
            ->label('NIP')
            ->searchable(),

        Tables\Columns\TextColumn::make('nama_lengkap')
            ->label('Nama')
            ->searchable(),

        Tables\Columns\TextColumn::make('tanggal_lahir')
            ->label('Tanggal Lahir')
            ->date('d-m-Y')
            ->sortable(),

        Tables\Columns\TextColumn::make('usia_bup')
            ->label('Usia BUP')
            ->sortable(),

        Tables\Columns\TextColumn::make('tmt_bup')
            ->label('TMT BUP')
            ->date('d-m-Y')
            ->sortable()
            ->badge()
            ->color(fn($state) => $state && \Carbon\Carbon::parse($state)->isPast() ? 'danger' : 'success'),

        Tables\Columns\TextColumn::make('tmt_pensiun')
            ->label('TMT Pensiun')
            ->date('d-m-Y')
            ->sortable()
            ->badge()
            ->color(fn($state) => $state && \Carbon\Carbon::parse($state)->isPast() ? 'danger' : 'success'),

        Tables\Columns\TextColumn::make('status_pensiun')
            ->label('Status')
            ->getStateUsing(fn($record) => 
                !$record->tmt_bup ? '-' :
                (\Carbon\Carbon::parse($record->tmt_bup)->isPast() ? 'Lewat BUP' : 'Akan Pensiun')
            )
            ->badge()
            ->color(fn($record) => 
                !$record->tmt_bup ? 'secondary' : 
                (\Carbon\Carbon::parse($record->tmt_bup)->isPast() ? 'danger' : 'warning')
            ),
    ];
}




    protected function getTableActions(): array
{
    return [
        Tables\Actions\Action::make('prosesPensiun')
            ->label('Proses Pensiun')
            ->icon('heroicon-o-check-circle')
            ->color('danger')
            ->visible(fn ($record) =>
                $record->tmt_bup !== '-' &&
                \Carbon\Carbon::parse($record->tmt_bup)->isPast()
            )
            ->form([
                
                    Forms\Components\Select::make('status_pegawai')
                        ->options([
                            'PNS' => 'PNS',
                            'PPPK' => 'PPPK',
                            'PPPKPW' => 'PPPKPW',
                        ])
                        ->required(),

                Forms\Components\TextInput::make('nomor_sk')
                    ->label('Nomor SK')
                    ->required(),

                Forms\Components\DatePicker::make('tanggal_sk')
                    ->label('Tanggal SK')
                    ->required(),

                Forms\Components\DatePicker::make('tmt_status')
                    ->label('TMT BUP')
                    ->default(fn ($record) => $record->tmt_bup)
                    ->required(),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3),
            ])
            ->requiresConfirmation()
            ->action(function ($record, array $data) {

                // Nonaktifkan riwayat lama
                RiwayatKepegawaian::where('pegawai_id', $record->id)
                    ->update(['is_aktif' => 0]);

                // Insert riwayat pensiun
                RiwayatKepegawaian::create([
                    'pegawai_id' => $record->id,
                    'jenis_kepegawaian' => $data['status_pegawai'],
                    'status' => 'PENSIUN',
                    'tmt_status' => $data['tmt_status'],
                    'nomor_sk' => $data['nomor_sk'],
                    'tanggal_sk' => $data['tanggal_sk'],
                    'keterangan' => $data['keterangan'],
                    'is_aktif' => 1,
                ]);

                Notification::make()
                    ->title('Berhasil diproses sebagai Pensiun')
                    ->success()
                    ->send();
            }),
    ];
}

    protected function getTableFilters(): array
    {
        return [

            // FILTER TAHUN
            Tables\Filters\SelectFilter::make('tahun')
    ->label('Filter Tahun')
    ->options([
        'all' => 'Semua',
        now()->year => now()->year,
        now()->year + 1 => now()->year + 1,
        now()->year + 2 => now()->year + 2,
    ])
    ->query(function (Builder $query, array $data) {

    $tahun = $data['value'] ?? null;

    if (!$tahun || $tahun === 'all') {
        return;
    }

    $query->whereRaw("
        YEAR(
            DATE_ADD(
                LAST_DAY(
                    DATE_ADD(
                        pegawai.tanggal_lahir,
                        INTERVAL
                        (
                            CASE
                                WHEN jabatans.jenis_jabatan = 'fungsional'
                                    AND LOWER(jabatans.jenis_fungsional) LIKE '%guru%'
                                THEN 60

                                WHEN jabatans.jenis_jabatan = 'fungsional'
                                    AND jabatans.jenjang_jabatan_id LIKE '%8%'
                                THEN 65

                                WHEN jabatans.jenis_jabatan = 'fungsional'
                                    AND jabatans.jenjang_jabatan_id LIKE '%7%'
                                THEN 60

                                WHEN jabatans.jenis_jabatan = 'fungsional'
                                THEN 58

                                WHEN jabatans.jenis_jabatan = 'struktural'
                                    AND (jabatans.eselon LIKE 'I%' OR jabatans.eselon LIKE 'II%')
                                THEN 60

                                ELSE 58
                            END
                        ) YEAR
                    )
                ),
                INTERVAL 1 MONTH
            )
        ) = ?
    ", [$tahun]);
}),



            // FILTER BULAN
            Tables\Filters\SelectFilter::make('bulan')
    ->label('Filter Bulan')
    ->options([
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ])
    ->query(function (Builder $query, array $data) {

        $bulan = $data['value'] ?? null;

        if (!$bulan) {
            return;
        }

        $query->whereRaw("
    MONTH(
        DATE_ADD(
            LAST_DAY(
                DATE_ADD(
                    pegawai.tanggal_lahir,
                    INTERVAL
                    (
                        CASE
                            WHEN jabatans.jenis_jabatan = 'fungsional'
                                AND LOWER(jabatans.jenis_fungsional) LIKE '%guru%'
                            THEN 60

                            WHEN jabatans.jenis_jabatan = 'fungsional'
                                AND jabatans.jenjang_jabatan_id LIKE '%8%'
                            THEN 65

                            WHEN jabatans.jenis_jabatan = 'fungsional'
                                AND jabatans.jenjang_jabatan_id LIKE '%7%'
                            THEN 60

                            WHEN jabatans.jenis_jabatan = 'fungsional'
                            THEN 58

                            WHEN jabatans.jenis_jabatan = 'struktural'
                                AND (jabatans.eselon LIKE 'I%' OR jabatans.eselon LIKE 'II%')
                            THEN 60

                            ELSE 58
                        END
                    ) YEAR
                )
            ),
            INTERVAL 1 MONTH
        )
    ) = ?
", [$bulan]);
    }),

        ];
    }
}

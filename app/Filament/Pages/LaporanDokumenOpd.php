<?php

namespace App\Filament\Pages;

use App\Models\Document;
use App\Models\Opd;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;

class LaporanDokumenOpd extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Laporan Dokumen per OPD';
    protected static ?string $title = 'Laporan Dokumen per OPD';

    protected static string $view = 'filament.pages.laporan-dokumen-opd';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('pegawai.nama_lengkap')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pegawai.riwayatJabatan.opd.nama_opd')
                    ->label('OPD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('file_name')
                    ->label('File')
                    ->formatStateUsing(fn ($state) => basename($state))
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis'),

                Tables\Columns\TextColumn::make('tanggal_verif')
                    ->label('Tanggal Upload')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('opd')
                    ->label('Filter OPD')
                    ->options(Opd::pluck('nama_opd', 'id'))
                    ->searchable()
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('pegawai.riwayatJabatan', function ($q) use ($data) {
                                $q->where('opd_id', $data['value'])
                                  ->where('status_aktif', 1);
                            });
                        }
                    }),
            ])
            ->defaultSort('tanggal_verif', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(function () {
                    $opdId = $this->tableFilters['opd']['value'] ?? null;

                    return $opdId
                        ? route('laporan.dokumen-opd.pdf', $opdId)
                        : null;
                })
                ->openUrlInNewTab()
                ->disabled(fn () => empty($this->tableFilters['opd']['value'])),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Document::query()
            ->whereHas('pegawai.jabatanAktif', function ($q) {
                $q->where('status_aktif', 1);
            })
            ->with([
                'pegawai.jabatanAktif.opd'
            ]);
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Document;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProgressDokumenPerOpd extends BaseWidget
{
    protected static ?string $heading = 'Progress Dokumen per OPD';

    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Document::query()
            ->join('pegawai', 'documents.nip', '=', 'pegawai.nip')
            ->join('riwayat_jabatan', function ($join) {
                $join->on('pegawai.id', '=', 'riwayat_jabatan.pegawai_id')
                    ->where('riwayat_jabatan.status_aktif', 1)
                    ->whereNull('riwayat_jabatan.tmt_selesai');
            })
            ->join('opds', 'riwayat_jabatan.opd_id', '=', 'opds.id')
            ->selectRaw('
                opds.nama_opd as nama_opd,
                COUNT(documents.id_document) as total,
                SUM(CASE 
                        WHEN documents.status_dokumen = "terima" 
                        THEN 1 
                        ELSE 0 
                    END) as selesai
            ')
            ->groupBy('opds.nama_opd')
            ->orderBy('opds.nama_opd');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('nama_opd')
                ->label('OPD')
                ->searchable(),

            Tables\Columns\TextColumn::make('total')
                ->label('Total'),

            Tables\Columns\TextColumn::make('selesai')
                ->label('Diterima'),

            Tables\Columns\TextColumn::make('progress')
                ->label('Progress')
                ->getStateUsing(function ($record) {
                    if ($record->total == 0) {
                        return 0;
                    }

                    return round(($record->selesai / $record->total) * 100);
                })
                ->badge()
                ->color(function ($state) {
                    if ($state >= 80) return 'success';
                    if ($state >= 60) return 'warning';
                    return 'danger';
                })
                ->formatStateUsing(fn ($state) => $state . '%'),
        ];
    }

    // 🔥 MATIKAN default sorting bawaan Filament
    protected function getDefaultTableSortColumn(): ?string
    {
        return null;
    }

    public function getTableRecordKey($record): string
    {
        return (string) $record->nama_opd;
    }
}

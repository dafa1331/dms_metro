<?php

namespace App\Filament\Resources\PegawaiResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Pangkat;

class RiwayatPangkatRelationManager extends RelationManager
{
    protected static string $relationship = 'riwayatPangkat';
    protected static ?string $recordTitleAttribute = 'Pangkat.pangkat';
    protected static ?string $title = 'Riwayat Pangkat';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('ref_id_pangkat')
                ->label('Pangkat')
                ->options(fn() => Pangkat::where('status_aktif', 1)->pluck('pangkat', 'id'))
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $pangkat = Pangkat::find($state);
                    $set('golongan', $pangkat?->golongan);
                }),

            Forms\Components\TextInput::make('golongan')
                ->label('Golongan')
                ->disabled(),

            Forms\Components\DatePicker::make('tmt_pangkat')
                ->label('TMT Pangkat')
                ->required(),

            Forms\Components\TextInput::make('nomor_sk')
                ->label('Nomor SK'),

            Forms\Components\DatePicker::make('tanggal_sk')
                ->label('Tanggal SK'),

            Forms\Components\FileUpload::make('file_sk')
                ->label('Dokumen SK')
                ->directory('SK_pangkat')
                ->visibility('public')
                ->nullable()
                ->getUploadedFileNameForStorageUsing(function ($file, $state, $livewire) {
                    $pegawai = $livewire->ownerRecord;
                    $pangkat = \App\Models\Pangkat::find($livewire->form->getState()['ref_id_pangkat']);
                    $nip = $pegawai->nip;
                    $tmt = $livewire->form->getState()['tmt_pangkat'] ?? now()->format('Y-m-d');

                    $namaFile = str_replace([' ', '/', ':'], '_', $pangkat?->pangkat . "_$nip" . "_$tmt");

                    return $namaFile . '.' . $file->getClientOriginalExtension();
                }),

            Forms\Components\Textarea::make('keterangan'),

            Forms\Components\Toggle::make('is_aktif')
                ->label('Aktif')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Pangkat.pangkat')->label('Pangkat'),
                Tables\Columns\TextColumn::make('Pangkat.golongan')->label('Golongan'),
                Tables\Columns\TextColumn::make('tmt_pangkat')->date(),
                Tables\Columns\TextColumn::make('nomor_sk'),
                Tables\Columns\TextColumn::make('keterangan'),
                Tables\Columns\IconColumn::make('is_aktif')->boolean(),
                Tables\Columns\TextColumn::make('file_sk')
                    ->label('Dokumen SK')
                    ->formatStateUsing(fn ($state) => $state ? '<a href="'.asset("storage/$state").'" target="_blank">Lihat</a>' : '-')
                    ->html(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('tmt_pangkat', 'desc');
    }
    
}

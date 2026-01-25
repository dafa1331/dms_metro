<?php

namespace App\Filament\Resources\PegawaiResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class RiwayatKepegawaianRelationManager extends RelationManager
{
    protected static string $relationship = 'riwayatKepegawaian';
    protected static ?string $recordTitleAttribute = 'jabatan';

    protected static ?string $title = 'Riwayat Kepegawaian';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status')
                ->label('Status Kepegawaian')
                ->options([
                    'AKTIF' => 'Aktif',
                    'MUTASI_KELUAR' => 'Mutasi Keluar',
                    'PENSIUN' => 'Pensiun',
                ])
                ->required(),

            Forms\Components\DatePicker::make('tmt_status')
                ->label('TMT Status')
                ->required(),

            Forms\Components\TextInput::make('nomor_sk')
                ->label('Nomor SK'),

            Forms\Components\DatePicker::make('tanggal_sk')
                ->label('Tanggal SK'),

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
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('tmt_status')->date(),
                Tables\Columns\TextColumn::make('nomor_sk'),
                Tables\Columns\TextColumn::make('keterangan'),
                Tables\Columns\IconColumn::make('is_aktif')->boolean(),
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
            ->defaultSort('tmt_status', 'desc');
    }

}

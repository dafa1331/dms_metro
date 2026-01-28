<?php

namespace App\Filament\Resources\PegawaiResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RiwayatPendidikanRelationManager extends RelationManager
{
    protected static string $relationship = 'riwayatPendidikan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tingkat_pendidikan')
                    ->label('Tingkat Pendidikan')
                    ->options([
                        'SD' => 'SD',
                        'SMP' => 'SMP',
                        'SMA/SMK' => 'SMA/SMK',
                        'D1' => 'D1',
                        'D2' => 'D2',
                        'D3' => 'D3',
                        'D4' => 'D4',
                        'S1' => 'S1',
                        'S2' => 'S2',
                        'S3' => 'S3',
                    ])
                    ->required()
                    ->searchable(),
                
                    TextInput::make('nama_sekolah')
                    ->label('Nama Sekolah / Perguruan Tinggi')
                    ->required()
                    ->maxLength(150),

                TextInput::make('jurusan')
                    ->label('Jurusan')
                    ->maxLength(100),

                TextInput::make('tahun_masuk')
                    ->label('Tahun Masuk')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(date('Y')),

                TextInput::make('tahun_lulus')
                    ->label('Tahun Lulus')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(date('Y')),

                TextInput::make('nomor_ijazah')
                    ->label('Nomor Ijazah')
                    ->maxLength(100),

                DatePicker::make('tanggal_ijazah')
                    ->label('Tanggal Ijazah')
                    ->displayFormat('d/m/Y'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tingkat_pendidikan')
                    ->label('Tingkat')
                    ->sortable(),

                TextColumn::make('nama_sekolah')
                    ->label('Sekolah / PT')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('jurusan')
                    ->label('Jurusan')
                    ->toggleable(),

                TextColumn::make('tahun_masuk')
                    ->label('Masuk')
                    ->sortable(),

                TextColumn::make('tahun_lulus')
                    ->label('Lulus')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

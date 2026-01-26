<?php

namespace App\Filament\Resources\PegawaiResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Jabatan;
use App\Models\RiwayatJabatan;

class RiwayatJabatanRelationManager extends RelationManager
{
    protected static string $relationship = 'riwayatJabatan';

    public function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Select::make('jenis_jabatan')
                ->label('Jenis Jabatan')
                ->options([
                    'definitif' => 'Definitif',
                    'tambahan'  => 'Tambahan',
                ])
                ->required()
                ->reactive(),

            Forms\Components\Select::make('jabatan_id')
                ->label('Jabatan')
                ->options(
                    Jabatan::query()
                        ->with('jenjangJabatan')
                        ->get()
                        ->mapWithKeys(fn ($j) => [
                            $j->id => 
                                ($j->jenjangJabatan?->nama_jenjang ?? 'Non Fungsional')
                                . ' - ' . $j->nama_jabatan
                        ])
                        ->toArray()
                )
                ->searchable()
                ->required(),

            Forms\Components\Select::make('opd_id')
                ->relationship('opd', 'nama_opd')
                ->required(),

            Forms\Components\Select::make('parent_jabatan_id')
                ->label('Jabatan Definitif Induk')
                ->options(fn () =>
                    RiwayatJabatan::where('pegawai_id', $this->ownerRecord->id)
                        ->where('jenis_jabatan', 'definitif')
                        ->where('status_aktif', 1)
                        ->with('jabatan')
                        ->get()
                        ->pluck('jabatan.nama_jabatan', 'id')
                )
                ->visible(fn ($get) => $get('jenis_jabatan') === 'tambahan')
                ->required(fn ($get) => $get('jenis_jabatan') === 'tambahan'),

            Forms\Components\DatePicker::make('tmt_mulai')
                ->required(),

            Forms\Components\DatePicker::make('tmt_selesai')
                ->visible(fn ($get) => $get('jenis_jabatan') === 'tambahan')
                ->required(fn ($get) => $get('jenis_jabatan') === 'tambahan'),

            Forms\Components\TextInput::make('nomor_sk')
                ->label('Nomor SK'),

            Forms\Components\DatePicker::make('tanggal_sk'),

            Forms\Components\Toggle::make('status_aktif')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('jabatan.nama_jabatan')
                    ->label('Jabatan'),

                Tables\Columns\BadgeColumn::make('jenis_jabatan')
                    ->colors([
                        'success' => 'definitif',
                        'warning' => 'tambahan',
                    ]),

                Tables\Columns\TextColumn::make('opd.nama_opd')
                    ->label('OPD'),

                Tables\Columns\TextColumn::make('tmt_mulai')->date(),

                Tables\Columns\TextColumn::make('tmt_selesai')
                    ->date()
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('status_aktif')
                    ->boolean(),
            ])
            ->defaultSort('tmt_mulai', 'desc')
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

    protected function beforeCreate(): void
    {
        if ($this->data['jenis_jabatan'] === 'definitif') {
            RiwayatJabatan::where('pegawai_id', $this->ownerRecord->id)
                ->where('jenis_jabatan', 'definitif')
                ->where('status_aktif', 1)
                ->update(['status_aktif' => 0]);
        }
    }
}

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
use App\Models\Opd;
use App\Models\RiwayatJabatan;
use App\Models\Pegawai;
use Illuminate\Validation\ValidationException;

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

            // Forms\Components\Select::make('jabatan_id')
            //     ->label('Jabatan')
            //     ->options(
            //         Jabatan::query()
            //             ->with('jenjangJabatan')
            //             ->get()
            //             ->mapWithKeys(fn ($j) => [
            //                 $j->id => 
            //                     ($j->jenjangJabatan?->nama_jenjang ?? 'Non Fungsional')
            //                     . ' - ' . $j->nama_jabatan
            //             ])
            //             ->toArray()
            //     )
            //     ->searchable()
            //     ->required(),
            Forms\Components\Select::make('jabatan_id')
                ->label('Jabatan')
                ->options(function () {
                    return Jabatan::query()
                        ->with('jenjangJabatan')
                        ->where(function ($q) {
                            $q->where('jenis_jabatan', '!=', 'struktural')
                            ->orWhereDoesntHave('riwayatJabatanAktif');
                        })
                        ->get()
                        ->mapWithKeys(fn ($j) => [
                            $j->id =>
                                ($j->jenjangJabatan?->nama_jenjang ?? 'Non Fungsional')
                                . ' - ' . $j->nama_jabatan
                        ])
                        ->toArray();
                })
                ->searchable()
                ->required(),
                // Forms\Components\Hidden::make('is_struktural'),

            // Forms\Components\Select::make('opd_id')
            //     ->relationship('opd', 'nama_opd')
            //     ->searchable()
            //     ->required(),

            Forms\Components\Select::make('opd_id')
                ->label('Unit Kerja')
                ->options(
                    Opd::with('parent.parent')
                        ->where('aktif', 1)
                        ->orderBy('level')
                        ->orderBy('urutan')
                        ->get()
                        ->pluck('label_hierarchy', 'id')
                )
                ->searchable()
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
                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->formatStateUsing(function ($record) {

                        $jabatan = $record->jabatan;

                        if (!$jabatan) {
                            return '-';
                        }

                        if ($jabatan->jenis_jabatan === 'fungsional') {
                            return ($jabatan->jenjangJabatan?->nama_jenjang ?? '') 
                                    . ' - ' . 
                                $jabatan->nama_jabatan;
                        }

                        return $jabatan->nama_jabatan;
                    }),

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
                // Tables\Actions\CreateAction::make(),
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire) {

                        // ⛔ NONAKTIFKAN SEMUA JABATAN AKTIF PEGAWAI
                        if ($data['jenis_jabatan'] === 'definitif') {
                            \App\Models\RiwayatJabatan::where('pegawai_id', $livewire->ownerRecord->id)
                                ->where('status_aktif', 1)
                                ->update([
                                    'status_aktif' => 0,
                                    'tmt_selesai'  => now(),
                                ]);
                        }

                        // ⛳ set default status aktif untuk jabatan baru
                        $data['status_aktif'] = 1;
                        $data['pegawai_id']  = $livewire->ownerRecord->id;

                        return $data;
                    }),
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
        $jabatan = Jabatan::find($this->data['jabatan_id']);

        // 🔒 Validasi jabatan struktural
        if ($jabatan?->jenis_jabatan === 'struktural') {
            $exists = RiwayatJabatan::where('jabatan_id', $jabatan->id)
                ->where('status_aktif', 1)
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'jabatan_id' => 'Jabatan struktural ini sudah diisi oleh pegawai lain.',
                ]);
            }
        $this->data['is_struktural'] = 1;
        } else {
            $this->data['is_struktural'] = 0;
        }

        if ($this->data['jenis_jabatan'] === 'definitif') {
            RiwayatJabatan::where('pegawai_id', $this->ownerRecord->id)
                ->where('status_aktif', 1)
                ->update([
                    'status_aktif' => 0,
                    'tmt_selesai'  => now(),
                ]);
        }
    }

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $jabatan = Jabatan::find($data['jabatan_id']);

    //     $data['is_struktural'] = $jabatan?->jenis_jabatan === 'struktural';

    //     return $data;
    // }
}

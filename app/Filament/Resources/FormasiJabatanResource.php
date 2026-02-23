<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormasiJabatanResource\Pages;
use App\Filament\Resources\FormasiJabatanResource\RelationManagers;
use App\Models\FormasiJabatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Jabatan;
use App\Models\Opd;
use App\Models\RiwayatJabatan;
use App\Models\Pegawai;

class FormasiJabatanResource extends Resource
{
    protected static ?string $model = FormasiJabatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Formasi Jabatan';

    protected static ?string $navigationGroup = 'Peta Jabatan';

    protected static function getOpdHierarchy()
    {
        $opds = \App\Models\Opd::orderBy('urutan')->get();

        $result = [];

        foreach ($opds as $opd) {
            $result[$opd->id] = static::buildFullPath($opd);
        }

        return $result;
    }

    protected static function buildFullPath($opd)
    {
        $names = [$opd->nama_opd];

        while ($opd->parent) {
            $opd = $opd->parent;
            $names[] = $opd->nama_opd;
        }

        return implode(' - ', $names);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('opd_id')
                ->label('Induk OPD')
                ->options(fn () => static::getOpdHierarchy())
                ->searchable()
                ->nullable()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $parent = \App\Models\Opd::find($state);
                        $set('level', $parent->level + 1);
                    } else {
                        $set('level', 1);
                    }
                }),

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

            Forms\Components\TextInput::make('jumlah_formasi')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('dasar_sk')
                ->label('Nomor SK')
                ->maxLength(255),

            Forms\Components\DatePicker::make('tanggal_sk')
                ->label('Tanggal SK'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('opd.full_path')
                    ->label('OPD')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jabatan.nama_jabatan')
                    ->label('Jabatan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('jumlah_formasi')
                    ->label('Formasi')
                    ->sortable(),

                Tables\Columns\TextColumn::make('terisi')
                    ->label('Terisi')
                    ->getStateUsing(function ($record) {
                        return RiwayatJabatan::where('opd_id', $record->opd_id)
                            ->where('jabatan_id', $record->jabatan_id)
                            ->where('status_aktif', 1)
                            ->whereNull('tmt_selesai')
                            ->count();
                    }),

                Tables\Columns\TextColumn::make('kebutuhan')
                    ->label('Kebutuhan')
                    ->getStateUsing(function ($record) {
                        $terisi = RiwayatJabatan::where('opd_id', $record->opd_id)
                            ->where('jabatan_id', $record->jabatan_id)
                            ->where('status_aktif', 1)
                            ->whereNull('tmt_selesai')
                            ->count();

                        return $record->jumlah_formasi - $terisi;
                    })
                    ->color(function ($state) {
                        if ($state > 0) {
                            return 'danger'; // kurang
                        }
                        if ($state < 0) {
                            return 'warning'; // kelebihan
                        }
                        return 'success'; // sesuai
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('opd_id')
                    ->relationship('opd', 'nama_opd')
                    ->label('Filter OPD'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFormasiJabatans::route('/'),
            'create' => Pages\CreateFormasiJabatan::route('/create'),
            'edit' => Pages\EditFormasiJabatan::route('/{record}/edit'),
        ];
    }
}
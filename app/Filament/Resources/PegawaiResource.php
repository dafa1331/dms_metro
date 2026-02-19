<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiResource\Pages;
use App\Filament\Resources\PegawaiResource\RelationManagers;
use App\Models\Pegawai;
use App\Models\RefAgama;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatPendidikan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('statusAktif', function ($q) {
                $q->where('status', 'AKTIF');
            });
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identitas')
                ->schema([
                    Forms\Components\TextInput::make('nip')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->length(18),

                    Forms\Components\TextInput::make('nama_lengkap')
                        ->required(),

                    Forms\Components\TextInput::make('gelar_depan'),
                    Forms\Components\TextInput::make('gelar_belakang'),

                    Forms\Components\TextInput::make('tempat_lahir')
                        ->required(),

                    Forms\Components\DatePicker::make('tanggal_lahir')
                        ->required(),

                    Forms\Components\Select::make('jenis_kelamin')
                        ->options([
                            'L' => 'Laki-laki',
                            'P' => 'Perempuan',
                        ])
                        ->required(),

                    Forms\Components\Select::make('id_agama')
                        ->label('Agama')
                        ->options(
                            RefAgama::where('status_aktif', 1)
                                ->pluck('nama_agama', 'id_agama')
                        )
                        ->searchable(),
                ])->columns(2),

            Forms\Components\Section::make('Kontak')
                ->schema([
                    Forms\Components\TextInput::make('email')->email(),
                    Forms\Components\TextInput::make('no_hp'),
                    Forms\Components\Textarea::make('alamat'),
                ]),

            Forms\Components\Section::make('Status Kepegawaian')
                ->schema([
                    Forms\Components\Select::make('status_pegawai')
                        ->options([
                            'PNS' => 'PNS',
                            'PPPK' => 'PPPK',
                            'PPPKPW' => 'PPPKPW',
                        ])
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
         return $table
            ->columns([
                Tables\Columns\TextColumn::make('nip')->searchable(),
                Tables\Columns\TextColumn::make('nama_lengkap')->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin'),
                Tables\Columns\TextColumn::make('agama.nama_agama')->label('Agama'),
                Tables\Columns\TextColumn::make('status_pegawai'),
            ])
            ->defaultSort('nama_lengkap')
            ->filters([
                //
            ])

            ->headerActions([
            Tables\Actions\Action::make('exportRekon')
                ->label('Export Rekon Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(route('rekon.pegawai.export'))
                ->openUrlInNewTab(),
            ])

            
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),  
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    

    public static function getRelations(): array
    {
        return [
            RelationManagers\RiwayatKepegawaianRelationManager::class,
            RelationManagers\RiwayatPangkatRelationManager::class,
            RelationManagers\RiwayatJabatanRelationManager::class,
            RelationManagers\RiwayatPendidikanRelationManager::class,
            // RelationManagers\RiwayatKeluargaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
            'view' => Pages\ViewPegawai::route('/{record}'), // <- pastikan ini ada
        ];
    }
}


//tes aja 
//kalau ini masuk berati oke
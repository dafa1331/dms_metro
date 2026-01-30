<?php

namespace App\Filament\Kasubbag\Resources;

use App\Filament\Kasubbag\Resources\PegawaiResource\Pages;
use App\Filament\Kasubbag\Resources\PegawaiResource\RelationManagers;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Facades\Filament;
use App\Models\Opd;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        // OPD induk + anak OPD
        $opdIds = Opd::query()
            ->where('id', $user->opd_id)
            ->orWhere('parent_id', $user->opd_id)
            ->pluck('id');

        return parent::getEloquentQuery()
            ->with([
                'jabatanAktif.jabatan',
                'jabatanAktif.jenjangJabatan',
            ]) // ⬅️ penting (hindari N+1)
            ->whereHas('jabatanAktif', function ($q) use ($opdIds) {
                $q->whereIn('opd_id', $opdIds)
                ->where('status_aktif', 1);
            });
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable(),

                TextColumn::make('nama_lengkap')
                    ->label('Nama')
                    ->searchable(),
                
                TextColumn::make('nama_pangkat')
                    ->label('Pangkat')
                    ->wrap(),

                TextColumn::make('nama_jabatan_lengkap')
                    ->label('Jabatan')
                    ->wrap(),

                TextColumn::make('status_pegawai')
                    ->badge(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'view' => Pages\ViewPegawai::route('/{record}'), // <- pastikan ini ada
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatPangkatResource\Pages;
use App\Filament\Resources\RiwayatPangkatResource\RelationManagers;
use App\Models\RiwayatPangkat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RiwayatPangkatResource extends Resource
{
    protected static ?string $model = RiwayatPangkat::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Data Riwayat Kepegawaian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Select::make('pegawai_id')
                ->relationship('pegawai', 'nama_lengkap')
                ->searchable()
                ->required(),

            Forms\Components\Select::make('ref_id_pangkat')
                ->relationship('pangkat', 'pangkat')
                ->searchable()
                ->required(),

            Forms\Components\DatePicker::make('tmt_pangkat')->required(),

            Forms\Components\TextInput::make('nomor_sk'),

            Forms\Components\DatePicker::make('tanggal_sk'),

            Forms\Components\FileUpload::make('dokumen_sk')
                ->directory('riwayat-pangkat')
                ->downloadable(),

            Forms\Components\Textarea::make('keterangan'),

            Toggle::make('is_aktif')
                ->label('Aktif'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->headerActions([
    Tables\Actions\Action::make('import')
        ->label('Import Excel')
        ->icon('heroicon-o-arrow-up-tray')
        ->visible(fn () => auth()->user()?->hasRole('admin') ?? false)
        ->form([
            Forms\Components\FileUpload::make('file')
                ->label('File Excel Riwayat Pangkat')
                ->disk('local') // WAJIB
                ->directory('imports/riwayat-pangkat') // WAJIB
                ->preserveFilenames()
                ->required()
                ->acceptedFileTypes([
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel',
                ]),
        ])
        ->action(function (array $data) {

            \Maatwebsite\Excel\Facades\Excel::import(
                new \App\Imports\RiwayatPangkatImport,
                storage_path('app/' . $data['file'])
            );

        })
        ->successNotificationTitle('Import Riwayat Pangkat berhasil'),
])
            ->columns([
                Tables\Columns\TextColumn::make('pegawai.nama_lengkap')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pangkat.pangkat')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tmt_pangkat')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nomor_sk'),

                Tables\Columns\IconColumn::make('is_aktif')
                    ->boolean(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListRiwayatPangkats::route('/'),
            'create' => Pages\CreateRiwayatPangkat::route('/create'),
            'edit' => Pages\EditRiwayatPangkat::route('/{record}/edit'),
        ];
    }
}

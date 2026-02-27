<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatKepegawaianResource\Pages;
use App\Filament\Resources\RiwayatKepegawaianResource\RelationManagers;
use App\Models\RiwayatKepegawaian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class RiwayatKepegawaianResource extends Resource
{
    protected static ?string $model = RiwayatKepegawaian::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Data Riwayat Kepegawaian';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('pegawai_id')
                ->relationship('pegawai', 'nama_lengkap')
                ->searchable()
                ->required(),

            TextInput::make('jenis_kepegawaian')->required(),

            TextInput::make('status')->required(),

            DatePicker::make('tmt_status')->required(),

            DatePicker::make('tmt_selesai'),

            TextInput::make('periode_kontrak'),

            TextInput::make('nomor_sk'),

            DatePicker::make('tanggal_sk'),

            Textarea::make('keterangan'),

            Toggle::make('is_aktif')->label('Aktif'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pegawai.nama_lengkap')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jenis_kepegawaian')
                    ->sortable(),

                TextColumn::make('status'),

                TextColumn::make('tmt_status')
                    ->date()
                    ->sortable(),

                TextColumn::make('tmt_selesai')
                    ->date(),

                IconColumn::make('is_aktif')
                    ->boolean(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('import')
        ->label('Import Excel')
        ->icon('heroicon-o-arrow-up-tray')
        ->visible(fn () => auth()->user()?->hasRole('admin') ?? false)
        ->form([
            Forms\Components\FileUpload::make('file')
                ->label('File Excel Riwayat Kepegawaian')
                ->disk('local')
                ->directory('imports/riwayat-kepegawaian')
                ->preserveFilenames()
                ->required()
                ->acceptedFileTypes([
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel',
                ]),
        ])
        ->action(function (array $data) {
            \Maatwebsite\Excel\Facades\Excel::import(
                new \App\Imports\RiwayatKepegawaianImport,
                storage_path('app/' . $data['file'])
            );
        })
        ->successNotificationTitle('Import Riwayat Kepegawaian berhasil')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListRiwayatKepegawaians::route('/'),
            'create' => Pages\CreateRiwayatKepegawaian::route('/create'),
            'edit' => Pages\EditRiwayatKepegawaian::route('/{record}/edit'),
        ];
    }
}

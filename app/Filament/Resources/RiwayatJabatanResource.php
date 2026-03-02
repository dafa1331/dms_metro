<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatJabatanResource\Pages;
use App\Models\RiwayatJabatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RiwayatJabatanImport;
use Illuminate\Support\Facades\Storage;

class RiwayatJabatanResource extends Resource
{
    protected static ?string $model = RiwayatJabatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Data Riwayat Kepegawaian';
    protected static ?string $navigationLabel = 'Riwayat Jabatan';
    protected static ?string $modelLabel = 'Riwayat Jabatan';
    protected static ?string $pluralModelLabel = 'Riwayat Jabatan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('pegawai_id')
                    ->relationship('pegawai', 'nip')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('jabatan_id')
                    ->relationship('jabatan', 'nama_jabatan')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('opd_id')
                    ->relationship('opd', 'nama_opd')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('jenis_jabatan')
                    ->options([
                        'definitif' => 'Definitif',
                        'tambahan' => 'Tambahan',
                    ])
                    ->required(),

                Forms\Components\Toggle::make('is_struktural')
                    ->label('Struktural'),

                Forms\Components\Select::make('parent_jabatan_id')
                    ->relationship('parentJabatan', 'nama_jabatan')
                    ->searchable()
                    ->nullable(),

                Forms\Components\DatePicker::make('tmt_mulai')
                    ->required(),

                Forms\Components\DatePicker::make('tmt_selesai')
                    ->nullable(),

                Forms\Components\TextInput::make('nomor_sk')
                    ->maxLength(255),

                Forms\Components\DatePicker::make('tanggal_sk'),

                Forms\Components\Toggle::make('status_aktif')
                    ->default(true),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
   Tables\Actions\Action::make('importExcel')
    ->label('Import Excel')
    ->icon('heroicon-o-arrow-up-tray')
    ->form([
        Forms\Components\FileUpload::make('file')
            ->label('File Excel Riwayat Jabatan')
            ->disk('local')                  // 🔥 pakai local, jangan public
            ->directory('imports/riwayat-jabatan')
            ->preserveFilenames()
            ->required()
            ->acceptedFileTypes([
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel',
            ]),
    ])
    ->action(function (array $data) {

        Excel::import(
            new RiwayatJabatanImport,
            storage_path('app/' . $data['file'])
        );

    })
    ->successNotificationTitle('Import Riwayat Jabatan berhasil')
    ->color('success')
            ])
            ->columns([

                Tables\Columns\TextColumn::make('pegawai.nip')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pegawai.nama_lengkap')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jabatan.nama_jabatan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('opd.nama_opd')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenis_jabatan')
                    ->badge(),

                Tables\Columns\IconColumn::make('status_aktif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('tmt_mulai')
                    ->date(),

                Tables\Columns\TextColumn::make('tmt_selesai')
                    ->date()
                    ->placeholder('Masih Aktif'),

            ])
            ->filters([

                Tables\Filters\SelectFilter::make('opd_id')
                    ->relationship('opd', 'nama_opd'),

                Tables\Filters\SelectFilter::make('status_aktif')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Tidak Aktif',
                    ]),
                Tables\Filters\Filter::make('tmt_mulai')
                    ->form([
                        Forms\Components\DatePicker::make('dari'),
                        Forms\Components\DatePicker::make('sampai'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['dari'],
                                fn ($q) => $q->whereDate('tmt_mulai', '>=', $data['dari']))
                            ->when($data['sampai'],
                                fn ($q) => $q->whereDate('tmt_mulai', '<=', $data['sampai']));
                    }),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([

                Tables\Actions\BulkAction::make('nonaktifkan')
                    ->label('Nonaktifkan')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->update([
                                'status_aktif' => 0,
                                'tmt_selesai' => now(),
                            ]);
                        }
                    })
                    ->requiresConfirmation()
                    ->color('danger'),

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
            'index' => Pages\ListRiwayatJabatans::route('/'),
            'create' => Pages\CreateRiwayatJabatan::route('/create'),
            'edit' => Pages\EditRiwayatJabatan::route('/{record}/edit'),
        ];
    }
}
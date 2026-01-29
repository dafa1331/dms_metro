<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JabatanResource\Pages;
use App\Filament\Resources\JabatanResource\RelationManagers;
use App\Models\Jabatan;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\JabatanImport;
use App\Exports\JabatanExport;

class JabatanResource extends Resource
{
    protected static ?string $model = Jabatan::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Master Data';
    
    public static function form(Form $form): Form
    {
            return $form->schema([
            Forms\Components\TextInput::make('kode_jabatan')
                ->required()
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('nama_jabatan')
                ->required(),

            Forms\Components\Select::make('jenis_jabatan')
                ->label('Jenis Jabatan')
                ->required()
                ->options([
                    'struktural' => 'Struktural',
                    'fungsional' => 'Fungsional',
                    'pelaksana' => 'Pelaksana',
                ])
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state) {
                    if ($state !== 'fungsional') {
                        $set('jenjang_id', null);
                        $set('jenis_fungsional', null);
                    }
                }),

            Forms\Components\Select::make('jenjang_jabatan_id')
                ->label('Jenjang Jabatan')
                ->relationship('JenjangJabatan', 'nama_jenjang') // ambil dari relasi model
                ->visible(fn ($get) => $get('jenis_jabatan') === 'fungsional')
                ->required(fn ($get) => $get('jenis_jabatan') === 'fungsional'),

            Forms\Components\Select::make('jenis_fungsional')
                ->label('Jenis Jabatan fungsional')
                ->options([
                    'guru' => 'Guru',
                    'kesehatan' => 'kesehatan',
                    'fungsional_lainnya' => 'Fungsional Lainnya',
                ])
                ->visible(fn ($get) => $get('jenis_jabatan') === 'fungsional')
                ->required(fn ($get) => $get('jenis_jabatan') === 'fungsional'),

            Forms\Components\TextInput::make('eselon')
                ->nullable(),

            Forms\Components\TextInput::make('kelas_jabatan')
                ->numeric()
                ->nullable(),

            Forms\Components\Toggle::make('aktif')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('import')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->disk('local')
                            ->directory('imports/jabatan')
                            ->required()
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ]),
                    ])
                    ->action(fn (array $data) =>
                        Excel::import(
                            new JabatanImport,
                            storage_path('app/' . $data['file'])
                        )
                    ),

                Action::make('export')
                    ->label('Export Excel')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () =>
                        Excel::download(new JabatanExport, 'jabatan.xlsx')
                    ),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('kode_jabatan')->searchable(),
                Tables\Columns\TextColumn::make('nama_jabatan')->searchable(),
                Tables\Columns\TextColumn::make('jenis_jabatan')->badge(),
                Tables\Columns\TextColumn::make('eselon'),
                Tables\Columns\TextColumn::make('JenjangJabatan.nama_jenjang')
                ->label('Jenjang Jabatan')
                // ->sortable()
                ->searchable(),
                Tables\Columns\IconColumn::make('aktif')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJabatans::route('/'),
            'create' => Pages\CreateJabatan::route('/create'),
            'edit' => Pages\EditJabatan::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpdResource\Pages;
use App\Filament\Resources\OpdResource\RelationManagers;
use App\Models\Opd;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OpdImport;
use App\Exports\OpdExport;


class OpdResource extends Resource
{
    protected static ?string $model = Opd::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Master Data';

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
        return $form
        ->schema([
            Forms\Components\TextInput::make('kode_opd')
                ->required()
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('nama_opd')
                ->required(),

            Forms\Components\Select::make('jenis_opd')
                ->required()
                ->options([
                    'sekretariat_daerah' => 'Sekretariat Daerah',
                    'sekretariat_dprd' => 'Sekretariat DPRD',
                    'dinas' => 'Dinas',
                    'badan' => 'Badan',
                    'sekretariat' => 'Sekretariat',
                    'bidang' => 'Bidang',
                    'subbid' => 'Sub Bidang',
                    'kecamatan' => 'Kecamatan',
                    'kelurahan' => 'Kelurahan',
                    'uptd' => 'UPTD',
                    'bagian' => 'Bagian',
                    'subbag' => 'Sub Bagian',
                ]),

            Forms\Components\Select::make('parent_id')
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

            Forms\Components\TextInput::make('level')
                ->numeric()
                ->disabled()
                ->dehydrated(),

            Forms\Components\TextInput::make('urutan')
                ->numeric()
                ->default(0),

            Forms\Components\Toggle::make('aktif')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $query
                    ->orderByRaw('COALESCE(parent_id, id)')
                    ->orderBy('parent_id')
                    ->orderBy('urutan');
            })

            // 🔥 HEADER ACTIONS (IMPORT & EXPORT)
            ->headerActions([
                Action::make('import')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->visible(fn () => auth()->user()?->hasRole('admin') ?? false)
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('File Excel OPD')
                            ->disk('local')              // 🔥 WAJIB
                            ->directory('imports/opd')   // 🔥 WAJIB
                            ->preserveFilenames()
                            ->required()
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                            ]),
                    ])
                    ->action(function (array $data) {
                        Excel::import(new OpdImport, storage_path('app/' . $data['file']));
                    })
                    ->successNotificationTitle('Import OPD berhasil'),

                Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn () => auth()->user()->hasRole('admin'))
                    ->action(fn () =>
                        Excel::download(new OpdExport, 'opd.xlsx')
                    ),
            ])

            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama OPD')
                    ->searchable(query: function ($query, $search) {
                        $query->where('nama_opd', 'like', "%{$search}%")
                            ->orWhere('kode_opd', 'like', "%{$search}%");
                    }),

                Tables\Columns\TextColumn::make('jenis_opd')
                    ->badge(),

                Tables\Columns\TextColumn::make('kode_opd')
                    ->searchable(),

                Tables\Columns\IconColumn::make('aktif')
                    ->boolean(),
            ])
            ->paginated(true)
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
            'index' => Pages\ListOpds::route('/'),
            'create' => Pages\CreateOpd::route('/create'),
            'edit' => Pages\EditOpd::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}

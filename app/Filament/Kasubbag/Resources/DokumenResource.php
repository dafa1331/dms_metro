<?php

namespace App\Filament\Kasubbag\Resources;

use App\Models\Document;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Kasubbag\Resources\DocumentResource\Pages;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    protected static ?string $navigationLabel = 'Upload Dokumen';
    protected static ?string $pluralLabel = 'Upload Dokumen';

    /* =========================
     * QUERY (OPD SENDIRI)
     * ========================= */
    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        return parent::getEloquentQuery()
            ->when($user, fn ($q) =>
                $q->where('opd_id', $user->opd_id)
            );
    }

    /* =========================
     * FORM UPLOAD
     * ========================= */
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Select::make('opd_id')
                ->label('OPD')
                ->relationship('opd', 'nama_opd')
                ->default(fn () => Filament::auth()->user()?->opd_id)
                ->disabled()
                ->dehydrated()
                ->required(),

            Forms\Components\Select::make('nip')
                ->label('Pegawai')
                ->searchable()
                ->required()
                ->options(function () {
                    $user = Filament::auth()->user();

                    if (! $user) return [];

                    return Pegawai::where('opd_id', $user->opd_id)
                        ->pluck('nama', 'nip');
                })
                ->getOptionLabelUsing(fn ($value) =>
                    Pegawai::where('nip', $value)->value('nama') . " ($value)"
                ),

            Forms\Components\Select::make('type')
                ->label('Jenis Dokumen')
                ->required()
                ->options([
                    'DRH'      => 'DRH',
                    'SK_CPNS'  => 'SK CPNS',
                    'SK_PNS'   => 'SK PNS',
                    'SPMT'     => 'SPMT',
                    'PERTEK'   => 'PERTEK',
                ]),

            Forms\Components\FileUpload::make('file_name')
                ->label('Upload Dokumen (PDF)')
                ->disk('public')
                ->directory(fn ($get) => 'dokumen/' . $get('type'))
                ->acceptedFileTypes(['application/pdf'])
                ->getUploadedFileNameForStorageUsing(fn ($file, $get) =>
                    $get('type') . '_' . $get('nip') . '.' . $file->getClientOriginalExtension()
                )
                ->required(),

            Forms\Components\Hidden::make('status_dokumen')
                ->default('proses'),
        ]);
    }

    /* =========================
     * TABLE VIEW
     * ========================= */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis'),

                Tables\Columns\BadgeColumn::make('status_dokumen')
                    ->label('Status')
                    ->colors([
                        'warning' => 'proses',
                        'success' => 'terima',
                        'danger'  => 'tolak',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    /* =========================
     * PAGES
     * ========================= */
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
        ];
    }
}
